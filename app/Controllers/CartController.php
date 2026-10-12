<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;

final class CartController extends Controller
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
        $_SESSION['_cart_flash'] = [
            'message' => $message,
            'type' => $type,
        ];
    }

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
        } catch (RuntimeException $exception) {
            $this->flash($exception->getMessage(), 'error');
            $this->redirect('cart');
        } catch (\Throwable $exception) {
            error_log('[Cart buy now] ' . $exception->getMessage());
            $this->flash('Không thể thêm sản phẩm. Vui lòng thử lại.', 'error');
            $this->redirect('cart');
        }

        $this->redirect('checkout');
    }

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
