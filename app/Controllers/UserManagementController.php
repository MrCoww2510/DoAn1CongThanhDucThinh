<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;

final class UserManagementController extends Controller
{
    // Hiển thị danh sách tài khoản và biểu mẫu quản lý nhân viên.
    public function index(): void
    {
        $this->requireAdministrator();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $model = new Account();
        $query = trim((string) ($_GET['q'] ?? ''));
        $editId = trim((string) ($_GET['edit'] ?? ''));
        $editing = $editId !== '' ? $model->findForManagement($editId) : null;
        if (($editing['VaiTro'] ?? null) !== 'NhanVien') {
            $editing = null;
        }

        $this->view('admin/users', [
            'pageTitle' => 'Quản lý người dùng',
            'accounts' => $model->searchForManagement($query),
            'query' => $query,
            'editing' => $editing,
            'error' => (string) ($_SESSION['_user_management_error'] ?? ''),
            'success' => (string) ($_SESSION['_user_management_success'] ?? ''),
            'csrfToken' => $_SESSION['_csrf'],
        ]);
        unset($_SESSION['_user_management_error'], $_SESSION['_user_management_success']);
    }

    // Kiểm tra thông tin rồi tạo tài khoản nhân viên mới.
    public function createStaff(): void
    {
        $this->requireAdministrator();
        if (!$this->validCsrf()) {
            $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        }

        $data = $this->staffData();
        $password = (string) ($_POST['password'] ?? '');
        $error = $this->validateStaff($data);
        if ($error === '' && (strlen($password) < 8 || strlen($password) > 255)) {
            $error = 'Mật khẩu nhân viên phải có từ 8 đến 255 ký tự.';
        }
        $model = new Account();
        if ($error === '' && $model->usernameUsedByOther($data['username'])) {
            $error = 'Tên đăng nhập đã được sử dụng.';
        }
        if ($error === '' && $model->emailUsedByOther($data['email'])) {
            $error = 'Email đã được sử dụng.';
        }
        if ($error !== '') {
            $this->fail($error);
        }

        try {
            $model->createStaff($data['name'], $data['username'], $data['email'], $data['phone'], $password);
            $this->succeed('Đã tạo tài khoản nhân viên.');
        } catch (\PDOException $exception) {
            error_log('[UserManagement createStaff] '.$exception->getMessage());
            $this->fail('Không thể tạo tài khoản. Hãy kiểm tra thông tin rồi thử lại.');
        }
    }

    // Kiểm tra và lưu thông tin mới cho tài khoản nhân viên.
    public function updateStaff(): void
    {
        $this->requireAdministrator();
        if (!$this->validCsrf()) {
            $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        }

        $accountId = trim((string) ($_POST['account_id'] ?? ''));
        $data = $this->staffData();
        $error = $accountId === '' ? 'Tài khoản không hợp lệ.' : $this->validateStaff($data);
        $model = new Account();
        if ($error === '' && $model->usernameUsedByOther($data['username'], $accountId)) {
            $error = 'Tên đăng nhập đã được sử dụng.';
        }
        if ($error === '' && $model->emailUsedByOther($data['email'], $accountId)) {
            $error = 'Email đã được sử dụng.';
        }
        if ($error !== '') {
            $this->fail($error);
        }

        try {
            if (!$model->updateStaff($accountId, $data['name'], $data['username'], $data['email'], $data['phone'])) {
                $this->fail('Chỉ có thể cập nhật tài khoản nhân viên hiện có.');
            }
            $this->succeed('Đã cập nhật thông tin nhân viên.');
        } catch (\PDOException $exception) {
            error_log('[UserManagement updateStaff] '.$exception->getMessage());
            $this->fail('Không thể cập nhật tài khoản. Hãy kiểm tra thông tin rồi thử lại.');
        }
    }

    // Thay đổi trạng thái hoạt động của một tài khoản.
    public function changeStatus(): void
    {
        $this->requireAdministrator();
        if (!$this->validCsrf()) {
            $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        }
        $accountId = trim((string) ($_POST['account_id'] ?? ''));
        $status = (string) ($_POST['status'] ?? '');
        if ($accountId === '' || !in_array($status, ['HoatDong', 'VoHieuHoa', 'KhongConHoatDong'], true)) {
            $this->fail('Trạng thái tài khoản không hợp lệ.');
        }
        if ($accountId === (string) ($_SESSION['user']['MaTK'] ?? '')) {
            $this->fail('Bạn không thể thay đổi trạng thái tài khoản đang đăng nhập.');
        }

        $result = (new Account())->changeStatus($accountId, $status);
        if ($result === 'last_admin') {
            $this->fail('Không thể vô hiệu hóa quản trị viên cuối cùng đang hoạt động.');
        }
        if ($result === 'missing') {
            $this->fail('Không tìm thấy tài khoản.');
        }
        $this->succeed('Đã cập nhật trạng thái tài khoản.');
    }

    // Chỉ cho phép quản trị viên đang hoạt động dùng các chức năng quản lý.
    private function requireAdministrator(): void
    {
        if (($_SESSION['user']['VaiTro'] ?? '') !== 'QuanTriVien') {
            http_response_code(isset($_SESSION['user']) ? 403 : 401);
            if (!isset($_SESSION['user'])) {
                $this->redirect('login');
            }
            exit('Bạn không có quyền truy cập chức năng này.');
        }

        $accountId = (string) ($_SESSION['user']['MaTK'] ?? '');
        $account = $accountId !== '' ? (new Account())->findForManagement($accountId) : null;
        if (($account['VaiTro'] ?? '') !== 'QuanTriVien' || ($account['TrangThai'] ?? '') !== 'HoatDong') {
            unset($_SESSION['user']);
            $this->redirect('login');
        }
    }

    // Kiểm tra token CSRF để bảo vệ các biểu mẫu gửi bằng POST.
    private function validCsrf(): bool
    {
        $csrf = (string) ($_POST['_csrf'] ?? '');

        return isset($_SESSION['_csrf']) && $csrf !== '' && hash_equals($_SESSION['_csrf'], $csrf);
    }

    // Lấy và làm sạch thông tin nhân viên từ biểu mẫu.
    /** @return array{name:string,username:string,email:string,phone:string} */
    private function staffData(): array
    {
        return [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'username' => trim((string) ($_POST['username'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
        ];
    }

    // Kiểm tra các trường thông tin nhân viên có hợp lệ không.
    /** @param array{name:string,username:string,email:string,phone:string} $data */
    private function validateStaff(array $data): string
    {
        if ($data['name'] === '' || $data['username'] === '' || $data['email'] === '') {
            return 'Vui lòng nhập họ tên, tên đăng nhập và email.';
        }
        if (mb_strlen($data['name']) > 100 || mb_strlen($data['username']) > 50
            || mb_strlen($data['email']) > 100 || mb_strlen($data['phone']) > 15) {
            return 'Một hoặc nhiều trường vượt quá độ dài cho phép.';
        }
        if (!preg_match('/\A[a-zA-Z0-9_.-]{4,50}\z/', $data['username'])) {
            return 'Tên đăng nhập cần từ 4 ký tự và chỉ gồm chữ, số, dấu chấm, gạch ngang hoặc gạch dưới.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Địa chỉ email không hợp lệ.';
        }

        return '';
    }

    // Lưu thông báo lỗi rồi quay lại trang quản lý tài khoản.
    private function fail(string $message): never
    {
        $_SESSION['_user_management_error'] = $message;
        $this->redirect('admin/users');
    }

    // Lưu thông báo thành công rồi quay lại trang quản lý tài khoản.
    private function succeed(string $message): never
    {
        $_SESSION['_user_management_success'] = $message;
        $this->redirect('admin/users');
    }
}
