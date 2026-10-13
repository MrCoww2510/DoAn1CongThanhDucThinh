<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\Cart;
use App\Models\Order;
use RuntimeException;
use Throwable;

final class CheckoutController extends Controller
{
    private function requireCustomer(): array
    {
        $user = $_SESSION['user'] ?? null;

        if (
            !is_array($user)
            || empty($user['MaND'])
            || ($user['VaiTro'] ?? '') !== 'KhachHang'
        ) {
            $this->redirect('login');
        }

        return $user;
    }

    private function verifyCsrf(): void
    {
        $csrf = (string) ($_POST['_csrf'] ?? '');
        $sessionCsrf = (string) ($_SESSION['_csrf'] ?? '');

        if (
            $csrf === ''
            || $sessionCsrf === ''
            || !hash_equals($sessionCsrf, $csrf)
        ) {
            http_response_code(403);
            exit('Phiên không hợp lệ. Hãy tải lại trang rồi thử lại.');
        }
    }

    public function index(): void
    {
        $user = $this->requireCustomer();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        $cart = (new Cart())->findItems((string) $user['MaND']);
        $profile = (new Account())->findCustomerProfile((string) $user['MaND']);

        $this->view('checkout/index', [
            'pageTitle' => 'Thanh toán',
            'items' => $cart['items'],
            'total' => $cart['total'],
            'csrfToken' => $_SESSION['_csrf'],
            'errors' => [],
            'form' => [
                'recipientName' => (string) ($profile['HoTen'] ?? $user['HoTen'] ?? ''),
                'recipientPhone' => (string) ($profile['SDT'] ?? $user['SDT'] ?? ''),
                'deliveryAddress' => (string) ($profile['DiaChiNhanHang'] ?? ''),
                'paymentMethod' => 'COD',
            ],
        ]);
    }

    public function placeOrder(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        $this->verifyCsrf();
        $user = $this->requireCustomer();

        $form = [
            'recipientName' => trim((string) ($_POST['recipientName'] ?? '')),
            'recipientPhone' => trim((string) ($_POST['recipientPhone'] ?? '')),
            'deliveryAddress' => trim((string) ($_POST['deliveryAddress'] ?? '')),
            'paymentMethod' => trim((string) ($_POST['paymentMethod'] ?? '')),
        ];
        $errors = [];

        if ($form['recipientName'] === '') {
            $errors[] = 'Vui lòng nhập tên người nhận.';
        } elseif (mb_strlen($form['recipientName']) > 100) {
            $errors[] = 'Tên người nhận không được vượt quá 100 ký tự.';
        }

        $normalizedPhone = preg_replace(
            '/[\s().-]+/',
            '',
            $form['recipientPhone']
        );

        if (
            !is_string($normalizedPhone)
            || strlen($normalizedPhone) > 15
            || preg_match('/\A\+?[0-9]{8,15}\z/', $normalizedPhone) !== 1
        ) {
            $errors[] = 'Số điện thoại cần gồm 8–15 chữ số, có thể bắt đầu bằng dấu +; tối đa 15 ký tự sau chuẩn hóa.';
        } else {
            $form['recipientPhone'] = $normalizedPhone;
        }

        if ($form['deliveryAddress'] === '') {
            $errors[] = 'Vui lòng nhập địa chỉ giao hàng.';
        } elseif (mb_strlen($form['deliveryAddress']) > 255) {
            $errors[] = 'Địa chỉ giao hàng không được vượt quá 255 ký tự.';
        }

        if ($form['paymentMethod'] !== 'COD') {
            $errors[] = 'Thanh toán trực tuyến chưa được tích hợp. Vui lòng chọn thanh toán khi nhận hàng (COD).';
        }

        $cart = (new Cart())->findItems((string) $user['MaND']);

        if ($cart['items'] === []) {
            $errors[] = 'Giỏ hàng đang trống. Hãy chọn sản phẩm trước khi thanh toán.';
        }

        if ($errors !== []) {
            $this->renderCheckout($cart, $form, $errors);
            return;
        }

        try {
            $order = (new Order())->placeCodOrder(
                (string) $user['MaND'],
                $form['recipientName'],
                $form['deliveryAddress'],
                $form['recipientPhone']
            );

            $this->redirect(
                'checkout/success?id=' . rawurlencode($order['MaDH']),
                303
            );
        } catch (\PDOException $exception) {
            error_log('[Checkout] ' . $exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể tạo đơn hàng lúc này. Vui lòng thử lại.']
            );
        } catch (RuntimeException $exception) {
            $this->renderCheckout($cart, $form, [$exception->getMessage()]);
        } catch (Throwable $exception) {
            error_log('[Checkout] ' . $exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể tạo đơn hàng lúc này. Vui lòng thử lại.']
            );
        }
    }

    public function success(): void
    {
        $user = $this->requireCustomer();
        $orderId = trim((string) ($_GET['id'] ?? ''));
        $receipt = $orderId === ''
            ? null
            : (new Order())->findForCustomer(
                $orderId,
                (string) $user['MaND']
            );

        if ($receipt === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy đơn hàng',
                'title' => 'Không tìm thấy đơn hàng',
                'message' => 'Đơn hàng không tồn tại hoặc không thuộc tài khoản của bạn.',
            ], 'customer');
            return;
        }

        $this->view('checkout/success', [
            'pageTitle' => 'Đặt hàng thành công',
            'order' => $receipt['order'],
            'items' => $receipt['items'],
        ]);
    }

    private function renderCheckout(array $cart, array $form, array $errors): void
    {
        $this->view('checkout/index', [
            'pageTitle' => 'Thanh toán',
            'items' => $cart['items'],
            'total' => $cart['total'],
            'csrfToken' => (string) ($_SESSION['_csrf'] ?? ''),
            'errors' => $errors,
            'form' => $form,
        ]);
    }
}
