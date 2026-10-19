<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use PDOException;
use RuntimeException;
use Throwable;

final class AccountController extends Controller
{
    private function requireAccountUser(): array
    {
        $user = $_SESSION['user'] ?? null;

        if (
            !is_array($user)
            || empty($user['MaND'])
            || !in_array($user['VaiTro'] ?? '', ['KhachHang', 'QuanTriVien'], true)
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
        $user = $this->requireAccountUser();
        $role = (string) $user['VaiTro'];
        $profile = (new Account())->findCustomerProfile((string) $user['MaND'], $role);

        if ($profile === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy tài khoản',
                'title' => 'Không tìm thấy tài khoản',
                'message' => 'Không tìm thấy thông tin tài khoản khách hàng.',
            ]);
            return;
        }

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['_account_flash'] ?? null;
        unset($_SESSION['_account_flash']);

        $form = [
            'name' => (string) $profile['HoTen'],
            'email' => (string) ($profile['Email'] ?? ''),
            'phone' => (string) ($profile['SDT'] ?? ''),
            'address' => (string) ($profile['DiaChiNhanHang'] ?? ''),
        ];

        $this->view('account/index', [
            'pageTitle' => 'Quản lý tài khoản',
            'profile' => $profile,
            'form' => $form,
            'flash' => $flash,
            'csrfToken' => $_SESSION['_csrf'],
        ]);
    }

    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        $this->verifyCsrf();
        $user = $this->requireAccountUser();
        $role = (string) $user['VaiTro'];
        $form = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'address' => trim((string) ($_POST['address'] ?? '')),
        ];
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
        $errors = [];

        if ($form['name'] === '' || mb_strlen($form['name']) > 100) {
            $errors[] = 'Họ tên là bắt buộc và không được vượt quá 100 ký tự.';
        }

        if (
            mb_strlen($form['email']) > 100
            || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $errors[] = 'Địa chỉ email không hợp lệ.';
        }

        $normalizedPhone = preg_replace('/[\s().-]+/', '', $form['phone']);
        if (
            !is_string($normalizedPhone)
            || (
                $normalizedPhone !== ''
                && (
                    strlen($normalizedPhone) > 15
                    || preg_match('/\A\+?[0-9]{8,15}\z/', $normalizedPhone) !== 1
                )
            )
        ) {
            $errors[] = 'Số điện thoại cần gồm 8–15 chữ số, có thể bắt đầu bằng dấu +.';
        } else {
            $form['phone'] = $normalizedPhone;
        }

        if (mb_strlen($form['address']) > 255) {
            $errors[] = 'Địa chỉ nhận hàng không được vượt quá 255 ký tự.';
        }

        $changingPassword = $currentPassword !== ''
            || $newPassword !== ''
            || $confirmPassword !== '';

        if ($changingPassword) {
            if ($currentPassword === '') {
                $errors[] = 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu.';
            }
            if (strlen($newPassword) < 8) {
                $errors[] = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
            }
        }

        if ($errors !== []) {
            $this->renderForm($user, $form, $errors);
            return;
        }

        try {
            (new Account())->updateCustomerProfile(
                (string) $user['MaND'],
                $form['name'],
                $form['email'],
                $form['phone'],
                $form['address'],
                $changingPassword ? $currentPassword : null,
                $changingPassword ? $newPassword : null,
                $role
            );

            $_SESSION['user']['HoTen'] = $form['name'];
            $_SESSION['user']['Email'] = $form['email'];
            $_SESSION['user']['SDT'] = $form['phone'];
            $_SESSION['_account_flash'] = [
                'type' => 'success',
                'message' => 'Thông tin tài khoản đã được cập nhật.',
            ];
            $this->redirect('account', 303);
        } catch (PDOException $exception) {
            error_log('[Account update] ' . $exception->getMessage());
            $this->renderForm(
                $user,
                $form,
                ['Không thể cập nhật tài khoản. Vui lòng kiểm tra thông tin và thử lại.']
            );
        } catch (RuntimeException $exception) {
            $this->renderForm($user, $form, [$exception->getMessage()]);
        } catch (Throwable $exception) {
            error_log('[Account update] ' . $exception->getMessage());
            $this->renderForm(
                $user,
                $form,
                ['Đã xảy ra lỗi khi cập nhật tài khoản. Vui lòng thử lại.']
            );
        }
    }

    private function renderForm(array $user, array $form, array $errors): void
    {
        $profile = (new Account())->findCustomerProfile(
            (string) $user['MaND'],
            (string) $user['VaiTro']
        );

        if ($profile === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy tài khoản',
                'title' => 'Không tìm thấy tài khoản',
                'message' => 'Không tìm thấy thông tin tài khoản khách hàng.',
            ]);
            return;
        }

        $this->view('account/index', [
            'pageTitle' => 'Quản lý tài khoản',
            'profile' => $profile,
            'form' => $form,
            'errors' => $errors,
            'csrfToken' => (string) ($_SESSION['_csrf'] ?? ''),
        ]);
    }
}
