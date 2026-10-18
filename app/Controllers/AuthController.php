<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;

final class AuthController extends Controller
{
    // Xác thực thông tin đăng nhập và tạo phiên người dùng.
    public function login(): void
    {
        if (isset($_SESSION['user'])) {
            $this->redirectByRole($_SESSION['user']['VaiTro']);
        }

        $error = '';
        $identity = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $identity = trim((string) ($_POST['identity'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $csrf = (string) ($_POST['_csrf'] ?? '');

            if (
                !isset($_SESSION['_csrf'])
                || !hash_equals($_SESSION['_csrf'], $csrf)
            ) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Hãy tải lại trang.';
            } elseif ($identity === '' || $password === '') {
                $error = 'Vui lòng nhập tên đăng nhập/email và mật khẩu.';
            } else {
                $account = (new Account())->findForLogin($identity);

                if (
                    $account === null
                    || !password_verify($password, $account['MatKhau'])
                ) {
                    $error = 'Thông tin đăng nhập không chính xác.';
                } elseif ($account['TrangThai'] !== 'HoatDong') {
                    $error = 'Tài khoản hiện không hoạt động.';
                } else {
                    session_regenerate_id(true);

                    unset($account['MatKhau']);

                    $_SESSION['user'] = $account;

                    // Tao token CSRF moi sau khi dang nhap thanh cong.
                    // Nut dang xuat trong layout can token nay.
                    $_SESSION['_csrf'] = bin2hex(random_bytes(32));

                    $this->redirectByRole($account['VaiTro']);
                }
            }
        }

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        $this->view('auth/login', [
            'pageTitle' => 'Đăng nhập',
            'error' => $error,
            'identity' => $identity,
            'csrfToken' => $_SESSION['_csrf'],
        ]);
    }

    // Kiểm tra dữ liệu và tạo tài khoản khách hàng mới.
    public function register(): void
    {
        if (isset($_SESSION['user'])) {
            $this->redirectByRole($_SESSION['user']['VaiTro']);
        }

        $error = '';
        $name = '';
        $username = '';
        $email = '';
        $phone = '';

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $username = trim((string) ($_POST['username'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');
            $csrf = (string) ($_POST['_csrf'] ?? '');

            if (
                !isset($_SESSION['_csrf'])
                || !hash_equals($_SESSION['_csrf'], $csrf)
            ) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Hãy tải lại trang.';
            } elseif (
                $name === ''
                || $username === ''
                || $email === ''
                || $password === ''
            ) {
                $error = 'Vui lòng điền đầy đủ các trường bắt buộc.';
            } elseif (
                mb_strlen($name) > 100
                || mb_strlen($username) > 50
                || mb_strlen($email) > 100
                || mb_strlen($phone) > 15
            ) {
                $error = 'Một hoặc nhiều trường vượt quá độ dài cho phép.';
            } elseif (
                !preg_match('/\A[a-zA-Z0-9_.-]{4,50}\z/', $username)
            ) {
                $error = 'Tên đăng nhập cần từ 4 ký tự, chỉ gồm chữ, số, dấu chấm, gạch ngang hoặc gạch dưới.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Địa chỉ email không hợp lệ.';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } elseif (strlen($password) < 8) {
                $error = 'Mật khẩu phải có ít nhất 8 ký tự.';
            } else {
                $model = new Account();

                if ($model->usernameExists($username)) {
                    $error = 'Tên đăng nhập đã được sử dụng.';
                } elseif ($model->emailExists($email)) {
                    $error = 'Email đã được sử dụng.';
                } else {
                    try {
                        $model->register(
                            $name,
                            $username,
                            $email,
                            $phone,
                            $password
                        );

                        unset($_SESSION['_csrf']);
                        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
                        $_SESSION['_register_success'] = true;

                        $this->redirect('login');
                    } catch (\PDOException $exception) {
                        error_log('[Register] '.$exception->getMessage());
                        $error = 'Không thể tạo tài khoản. Hãy kiểm tra dữ liệu hoặc thử lại.';
                    } catch (\Throwable $exception) {
                        error_log('[Register] '.$exception->getMessage());
                        $error = 'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.';
                    }
                }
            }
        }

        $this->view('auth/register', [
            'pageTitle' => 'Đăng ký tài khoản',
            'error' => $error,
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'csrfToken' => $_SESSION['_csrf'],
        ]);
    }

    // Kết thúc phiên đăng nhập sau khi kiểm tra yêu cầu và CSRF.
    public function logout(): void
    {
        // Chỉ chấp nhận POST
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        // Chỉ coi là đã đăng nhập khi session có thông tin tài khoản hợp lệ.
        if (
            !isset($_SESSION['user'])
            || !is_array($_SESSION['user'])
            || empty($_SESSION['user']['MaTK'])
        ) {
            unset($_SESSION['user']);

            $config = require BASE_PATH.'/config/app.php';
            $baseUrl = rtrim($config['base_url'] ?? '', '/');

            header('Location: '.$baseUrl.'/login');
            exit;
        }

        // Kiểm tra CSRF khi đang đăng nhập
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

        // Xóa thông tin đăng nhập
        unset($_SESSION['user']);

        // Tạo token mới cho session hiện tại
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));

        $config = require BASE_PATH.'/config/app.php';
        $baseUrl = rtrim($config['base_url'] ?? '', '/');

        header('Location: '.$baseUrl.'/');
        exit;
    }

    // Chuyển người dùng sau đăng nhập theo vai trò tài khoản.
    private function redirectByRole(string $role): never
    {
        if ($role === 'QuanTriVien' || $role === 'NhanVien') {
            // Chưa chuyển vào trang quản trị khi module quản trị chưa được xây dựng.
            $this->redirect('');
        }

        $this->redirect('');
    }
}
