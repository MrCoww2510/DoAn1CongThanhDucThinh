<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use RuntimeException;
use Throwable;

final class OrderController extends Controller
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

    private function postOnly(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }
    }

    private function flash(string $message, string $type = 'success'): void
    {
        $_SESSION['_order_flash'] = [
            'message' => $message,
            'type' => $type,
        ];
    }

    public function index(): void
    {
        $user = $this->requireCustomer();
        $flash = $_SESSION['_order_flash'] ?? null;
        unset($_SESSION['_order_flash']);

        $this->view('orders/index', [
            'pageTitle' => 'Theo dõi đơn hàng',
            'orders' => (new Order())->findAllForCustomer((string) $user['MaND']),
            'flash' => $flash,
        ]);
    }

    public function show(string $orderId): void
    {
        $user = $this->requireCustomer();
        $receipt = (new Order())->findForCustomer(
            $orderId,
            (string) $user['MaND']
        );

        if ($receipt === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy đơn hàng',
                'title' => 'Không tìm thấy đơn hàng',
                'message' => 'Đơn hàng không tồn tại hoặc không thuộc tài khoản của bạn.',
            ]);
            return;
        }

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['_order_flash'] ?? null;
        unset($_SESSION['_order_flash']);

        $this->view('orders/show', [
            'pageTitle' => 'Chi tiết đơn hàng',
            'order' => $receipt['order'],
            'items' => $receipt['items'],
            'flash' => $flash,
            'csrfToken' => $_SESSION['_csrf'],
        ]);
    }

    public function cancel(string $orderId): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $user = $this->requireCustomer();

        try {
            (new Order())->cancelForCustomer($orderId, (string) $user['MaND']);
            $this->flash('Đơn hàng đã được hủy. Số lượng sản phẩm đã được hoàn lại kho.');
        } catch (RuntimeException $exception) {
            $this->flash($exception->getMessage(), 'error');
        } catch (Throwable $exception) {
            error_log('[Order cancellation] ' . $exception->getMessage());
            $this->flash('Không thể hủy đơn hàng lúc này. Vui lòng thử lại.', 'error');
        }

        $this->redirect('orders/' . rawurlencode($orderId), 303);
    }
}
