<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;

final class CartController extends Controller
{
    // Yêu cầu đăng nhập và lấy mã người dùng hiện tại.
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

    // Dừng xử lý nếu token CSRF trong biểu mẫu không hợp lệ.
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

    // Chỉ cho phép các thao tác thay đổi giỏ hàng qua POST.
    private function postOnly(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }
    }

    // Lưu thông báo tạm thời để hiển thị sau khi chuyển hướng.
    private function flash(string $message, string $type = 'success'): void
    {
        $_SESSION['_cart_flash'] = [
            'message' => $message,
            'type' => $type,
        ];
    }

    // Lấy giỏ hàng và hiển thị nội dung cho khách.
    public function index(): void
    {
        $user = $this->requireCustomer();

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        $cart = (new Cart())->getItems((string) $user['MaND']);
        $flash = $_SESSION['_cart_flash'] ?? null;
        unset($_SESSION['_cart_flash']);

        $this->view('cart/index', [
            'pageTitle' => 'Giỏ hàng',
            'cart' => $cart,
            'flash' => $flash,
            'csrfToken' => $_SESSION['_csrf'],
        ]);
    }

    // Thêm sản phẩm được chọn vào giỏ hàng.
    public function add(): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $user = $this->requireCustomer();

        $maSP = trim((string) ($_POST['maSP'] ?? ''));
        $quantity = filter_var(
            $_POST['quantity'] ?? 1,
            FILTER_VALIDATE_INT
        );

        if ($maSP === '' || $quantity === false || $quantity < 1) {
            $this->flash('Thông tin sản phẩm hoặc số lượng không hợp lệ.', 'error');
            $this->redirect('cart');
        }

        try {
            (new Cart())->addItem(
                (string) $user['MaND'],
                $maSP,
                $quantity
            );
            $this->flash('Đã thêm sản phẩm vào giỏ hàng.');
        } catch (\RuntimeException $e) {
            $this->flash($e->getMessage(), 'error');
        } catch (\Throwable $e) {
            error_log('[Cart add] '.$e->getMessage());
            $this->flash('Không thể thêm sản phẩm. Vui lòng thử lại.', 'error');
        }

        $this->redirect('cart');
    }

    // Thêm sản phẩm vào giỏ rồi chuyển tiếp sang bước thanh toán.
    public function buyNow(): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $user = $this->requireCustomer();

        $maSP = trim((string) ($_POST['maSP'] ?? ''));
        $quantity = filter_var(
            $_POST['quantity'] ?? 1,
            FILTER_VALIDATE_INT
        );

        if ($maSP === '' || $quantity === false || $quantity < 1) {
            $this->flash('Thông tin sản phẩm hoặc số lượng không hợp lệ.', 'error');
            $this->redirect('cart');
        }

        try {
            (new Cart())->addItem(
                (string) $user['MaND'],
                $maSP,
                $quantity
            );
        } catch (\RuntimeException $e) {
            $this->flash($e->getMessage(), 'error');
            $this->redirect('cart');
        } catch (\Throwable $e) {
            error_log('[Cart buy now] '.$e->getMessage());
            $this->flash('Không thể thêm sản phẩm. Vui lòng thử lại.', 'error');
            $this->redirect('cart');
        }

        $this->redirect('checkout');
    }

    // Cập nhật số lượng sản phẩm trong giỏ.
    public function update(): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $user = $this->requireCustomer();

        $maSP = trim((string) ($_POST['maSP'] ?? ''));
        $quantity = filter_var(
            $_POST['quantity'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($maSP === '' || $quantity === false || $quantity < 1) {
            $this->flash('Số lượng cập nhật không hợp lệ.', 'error');
            $this->redirect('cart');
        }

        try {
            (new Cart())->updateItem(
                (string) $user['MaND'],
                $maSP,
                $quantity
            );
            $this->flash('Đã cập nhật giỏ hàng.');
        } catch (\RuntimeException $e) {
            $this->flash($e->getMessage(), 'error');
        } catch (\Throwable $e) {
            error_log('[Cart update] '.$e->getMessage());
            $this->flash('Không thể cập nhật giỏ hàng.', 'error');
        }

        $this->redirect('cart');
    }

    // Xóa một sản phẩm khỏi giỏ hàng.
    public function remove(): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $user = $this->requireCustomer();

        $maSP = trim((string) ($_POST['maSP'] ?? ''));

        if ($maSP === '') {
            $this->flash('Mã sản phẩm không hợp lệ.', 'error');
            $this->redirect('cart');
        }

        try {
            (new Cart())->removeItem((string) $user['MaND'], $maSP);
            $this->flash('Đã xóa sản phẩm khỏi giỏ hàng.');
        } catch (\Throwable $e) {
            error_log('[Cart remove] '.$e->getMessage());
            $this->flash('Không thể xóa sản phẩm.', 'error');
        }

        $this->redirect('cart');
    }

    // Xóa toàn bộ sản phẩm trong giỏ hàng.
    public function clear(): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $user = $this->requireCustomer();

        try {
            (new Cart())->clear((string) $user['MaND']);
            $this->flash('Đã xóa toàn bộ giỏ hàng.');
        } catch (\Throwable $e) {
            error_log('[Cart clear] '.$e->getMessage());
            $this->flash('Không thể làm trống giỏ hàng.', 'error');
        }

        $this->redirect('cart');
    }
}
