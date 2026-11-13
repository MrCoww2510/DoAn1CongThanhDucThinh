<?php

// Controller xử lý quy trình đặt hàng và thanh toán tại bước checkout.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Services\SePayPayment;

final class CheckoutController extends Controller
{
    // Yêu cầu đăng nhập và lấy thông tin khách đang đặt hàng.
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

    // Kiểm tra token CSRF của biểu mẫu thanh toán.
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

    // Hiển thị biểu mẫu đặt hàng cùng nội dung giỏ hàng.
    public function index(): void
    {
        $user = $this->requireCustomer();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        $cart = (new Cart())->findItems((string) $user['MaND']);
        $profile = (new Account())->findCustomerProfile((string) $user['MaND']);
        $addresses = $this->findDeliveryAddresses(
            (string) $user['MaND'],
            (string) ($profile['DiaChiNhanHang'] ?? '')
        );
        $selectedAddressId = (string) (
            $_SESSION['_checkout_selected_address_id']
                ?? ($addresses[0]['id'] ?? '')
        );
        unset($_SESSION['_checkout_selected_address_id']);

        $this->view('checkout/index', [
            'pageTitle' => 'Thanh toán',
            'items' => $cart['items'],
            'total' => $cart['total'],
            'csrfToken' => $_SESSION['_csrf'],
            'errors' => [],
            'addresses' => $addresses,
            'form' => [
                'recipientName' => (string) ($profile['HoTen'] ?? $user['HoTen'] ?? ''),
                'recipientPhone' => (string) ($profile['SDT'] ?? $user['SDT'] ?? ''),
                'deliveryAddressId' => $selectedAddressId,
                'newAddress' => '',
                'paymentMethod' => 'COD',
            ],
        ]);
    }

    // Lưu địa chỉ giao hàng mới trong quá trình thanh toán.
    public function addAddress(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        $this->verifyCsrf();
        $user = $this->requireCustomer();
        $address = trim((string) ($_POST['newAddress'] ?? ''));
        $cart = (new Cart())->findItems((string) $user['MaND']);
        $form = [
            'recipientName' => trim((string) ($_POST['recipientName'] ?? '')),
            'recipientPhone' => trim((string) ($_POST['recipientPhone'] ?? '')),
            'deliveryAddressId' => trim((string) ($_POST['deliveryAddressId'] ?? '')),
            'newAddress' => $address,
            'paymentMethod' => trim((string) ($_POST['paymentMethod'] ?? 'COD')),
        ];

        if ($address === '' || mb_strlen($address) > 255) {
            $this->renderCheckout(
                $cart,
                $form,
                ['Địa chỉ mới là bắt buộc và không được vượt quá 255 ký tự.'],
                (string) $user['MaND']
            );

            return;
        }

        try {
            $addressId = (new DeliveryAddress())->addForCustomer(
                (string) $user['MaND'],
                $address
            );
            $profile = (new Account())->findCustomerProfile((string) $user['MaND']);
            $_SESSION['_checkout_selected_address_id'] =
                (string) ($profile['DiaChiNhanHang'] ?? '') === $address
                    ? 'profile'
                    : (string) $addressId;
            $this->redirect('checkout', 303);
        } catch (\PDOException $exception) {
            error_log('[Checkout address] '.$exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể lưu địa chỉ lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        } catch (\RuntimeException $exception) {
            $this->renderCheckout(
                $cart,
                $form,
                [$exception->getMessage()],
                (string) $user['MaND']
            );
        } catch (\Throwable $exception) {
            error_log('[Checkout address] '.$exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể lưu địa chỉ lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        }
    }

    // Kiểm tra dữ liệu nhận hàng và tạo đơn hàng theo phương thức đã chọn.
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
            'deliveryAddressId' => trim((string) ($_POST['deliveryAddressId'] ?? '')),
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

        $profile = (new Account())->findCustomerProfile((string) $user['MaND']);
        $deliveryAddress = $this->resolveDeliveryAddress(
            (string) $user['MaND'],
            $form['deliveryAddressId'],
            (string) ($profile['DiaChiNhanHang'] ?? '')
        );

        if ($deliveryAddress === null) {
            $errors[] = 'Vui lòng chọn địa chỉ giao hàng hợp lệ hoặc thêm địa chỉ mới.';
        }

        if (!in_array($form['paymentMethod'], ['COD', 'Online'], true)) {
            $errors[] = 'Phương thức thanh toán không hợp lệ.';
        } elseif (
            $form['paymentMethod'] === 'Online'
            && !(new SePayPayment())->isConfigured()
        ) {
            $errors[] = 'SePay Sandbox chưa được cấu hình đầy đủ. Vui lòng thử lại sau.';
        }

        $cart = (new Cart())->findItems((string) $user['MaND']);

        if ($cart['items'] === []) {
            $errors[] = 'Giỏ hàng đang trống. Hãy chọn sản phẩm trước khi thanh toán.';
        }

        if ($errors !== []) {
            $this->renderCheckout($cart, $form, $errors, (string) $user['MaND']);

            return;
        }

        try {
            $order = (new Order())->placeOrder(
                (string) $user['MaND'],
                $form['recipientName'],
                (string) $deliveryAddress,
                $form['recipientPhone'],
                $form['paymentMethod']
            );

            if ($form['paymentMethod'] === 'Online') {
                $this->redirect(
                    'payments/sepay/start?id='.rawurlencode($order['MaDH']),
                    303
                );
            }

            $this->redirect(
                'checkout/success?id='.rawurlencode($order['MaDH']),
                303
            );
        } catch (\PDOException $exception) {
            error_log('[Checkout] '.$exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể tạo đơn hàng lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        } catch (\RuntimeException $exception) {
            $this->renderCheckout(
                $cart,
                $form,
                [$exception->getMessage()],
                (string) $user['MaND']
            );
        } catch (\Throwable $exception) {
            error_log('[Checkout] '.$exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể tạo đơn hàng lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        }
    }

    // Hiển thị kết quả đặt hàng thành công.
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

    // Dựng lại trang thanh toán với dữ liệu và lỗi đã có.
    private function renderCheckout(
        array $cart,
        array $form,
        array $errors,
        string $customerId
    ): void {
        $profile = (new Account())->findCustomerProfile($customerId);

        $this->view('checkout/index', [
            'pageTitle' => 'Thanh toán',
            'items' => $cart['items'],
            'total' => $cart['total'],
            'csrfToken' => (string) ($_SESSION['_csrf'] ?? ''),
            'errors' => $errors,
            'addresses' => $this->findDeliveryAddresses(
                $customerId,
                (string) ($profile['DiaChiNhanHang'] ?? '')
            ),
            'form' => $form,
        ]);
    }

    /**
     * @return array<int, array{id: string, address: string, isDefault: bool}>
     */
    private function findDeliveryAddresses(string $customerId, string $profileAddress): array
    {
        $addresses = [];

        if (trim($profileAddress) !== '') {
            $addresses[] = [
                'id' => 'profile',
                'address' => $profileAddress,
                'isDefault' => true,
            ];
        }

        foreach ((new DeliveryAddress())->findForCustomer($customerId) as $address) {
            if (
                trim($profileAddress) !== ''
                && (string) $address['DiaChi'] === $profileAddress
            ) {
                continue;
            }

            $addresses[] = [
                'id' => (string) $address['MaDiaChi'],
                'address' => (string) $address['DiaChi'],
                'isDefault' => $addresses === [],
            ];
        }

        return $addresses;
    }

    // Xác định địa chỉ giao hàng theo lựa chọn và dữ liệu gửi lên.
    private function resolveDeliveryAddress(
        string $customerId,
        string $addressId,
        string $profileAddress
    ): ?string {
        if ($addressId === 'profile') {
            return trim($profileAddress) === '' ? null : $profileAddress;
        }

        if ($addressId === '' || !ctype_digit($addressId)) {
            return null;
        }

        return (new DeliveryAddress())->findForCustomerById(
            (int) $addressId,
            $customerId
        );
    }
}