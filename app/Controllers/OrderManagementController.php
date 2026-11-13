<?php
// Controller xử lý đơn hàng trong khu vực quản trị.


declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\Order;
use RuntimeException;
use Throwable;

final class OrderManagementController extends Controller
{
    private const ORDER_STATUSES = [
        'Chờ xác nhận',
        'Đang chuẩn bị hàng',
        'Đang giao hàng',
        'Hoàn thành',
        'Giao hàng thất bại',
        'Đã hủy',
    ];

    // Chuẩn bị dữ liệu và hiển thị trang tương ứng.
    public function index(): void
    {
        $this->requireOrderManager();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        $queryInput = $_GET['q'] ?? '';
        $statusInput = $_GET['status'] ?? '';
        $query = is_string($queryInput) ? trim($queryInput) : '';
        $status = is_string($statusInput) ? trim($statusInput) : '';
        $error = '';

        if (!is_string($queryInput)) {
            $error = 'Từ khóa tìm kiếm không hợp lệ.';
        }
        if (mb_strlen($query) > 100) {
            $query = '';
            $error = 'Từ khóa tìm kiếm không được vượt quá 100 ký tự.';
        }
        if (!is_string($statusInput)) {
            $error = 'Trạng thái lọc không hợp lệ.';
        }
        if ($status !== '' && !in_array($status, self::ORDER_STATUSES, true)) {
            $status = '';
            $error = 'Trạng thái lọc không hợp lệ.';
        }

        $this->view('admin/orders', [
            'pageTitle' => 'Quản lý đơn hàng',
            'orders' => (new Order())->findAllForManagement($status, $query),
            'statuses' => self::ORDER_STATUSES,
            'statusFilter' => $status,
            'query' => $query,
            'csrfToken' => $_SESSION['_csrf'],
            'error' => $error ?: (string) ($_SESSION['_order_management_error'] ?? ''),
            'success' => (string) ($_SESSION['_order_management_success'] ?? ''),
        ]);
        unset($_SESSION['_order_management_error'], $_SESSION['_order_management_success']);
    }

    // Tìm và hiển thị thông tin chi tiết của đối tượng được yêu cầu.
    public function show(string $orderId): void
    {
        $this->requireOrderManager();
        $receipt = (new Order())->findForManagement($orderId);

        if ($receipt === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy đơn hàng',
                'title' => 'Không tìm thấy đơn hàng',
                'message' => 'Đơn hàng không tồn tại.',
            ]);
            return;
        }

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $flash = [
            'error' => (string) ($_SESSION['_order_management_error'] ?? ''),
            'success' => (string) ($_SESSION['_order_management_success'] ?? ''),
        ];
        unset($_SESSION['_order_management_error'], $_SESSION['_order_management_success']);

        $this->view('admin/order-detail', [
            'pageTitle' => 'Chi tiết đơn hàng',
            'order' => $receipt['order'],
            'items' => $receipt['items'],
            'csrfToken' => $_SESSION['_csrf'],
            'error' => $flash['error'],
            'success' => $flash['success'],
        ]);
    }

    // Kiểm tra quyền và cập nhật trạng thái theo yêu cầu.
    public function updateStatus(string $orderId): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $manager = $this->requireOrderManager();
        $statusInput = $_POST['status'] ?? '';
        $targetStatus = is_string($statusInput) ? trim($statusInput) : '';

        if (!in_array($targetStatus, self::ORDER_STATUSES, true)) {
            $this->fail($orderId, 'Trạng thái đơn hàng không hợp lệ.');
        }

        try {
            (new Order())->updateStatusForManagement(
                $orderId,
                (string) $manager['MaND'],
                $targetStatus
            );
            $_SESSION['_order_management_success'] = 'Đã cập nhật trạng thái đơn hàng.';
        } catch (RuntimeException $exception) {
            $this->fail($orderId, $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('[Order management status] '.$exception->getMessage());
            $this->fail($orderId, 'Không thể cập nhật đơn hàng lúc này. Vui lòng thử lại.');
        }

        $this->redirect('admin/orders/'.rawurlencode($orderId), 303);
    }

    // Xác nhận hàng hoàn về kho cho đơn hàng.
    public function confirmReturn(string $orderId): void
    {
        $this->postOnly();
        $this->verifyCsrf();
        $manager = $this->requireOrderManager();

        try {
            (new Order())->confirmReturnForManagement(
                $orderId,
                (string) $manager['MaND']
            );
            $_SESSION['_order_management_success'] = 'Đã xác nhận hàng hoàn về kho và cập nhật tồn kho.';
        } catch (RuntimeException $exception) {
            $this->fail($orderId, $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('[Order management return] '.$exception->getMessage());
            $this->fail($orderId, 'Không thể xác nhận hàng hoàn về kho lúc này. Vui lòng thử lại.');
        }

        $this->redirect('admin/orders/'.rawurlencode($orderId), 303);
    }

    // Kiểm tra vai trò trước khi cho phép quản lý đơn hàng.
    private function requireOrderManager(): array
    {
        $sessionUser = $_SESSION['user'] ?? null;
        $roleValue = is_array($sessionUser) ? ($sessionUser['VaiTro'] ?? '') : '';
        $role = is_string($roleValue) ? $roleValue : '';

        if (!is_array($sessionUser) || $role === '') {
            $this->redirect('login');
        }
        if (!in_array($role, ['NhanVien', 'QuanTriVien'], true)) {
            http_response_code(403);
            exit('Bạn không có quyền truy cập chức năng này.');
        }

        $accountId = is_string($sessionUser['MaTK'] ?? null)
            ? $sessionUser['MaTK']
            : '';
        $account = $accountId !== '' ? (new Account())->findForManagement($accountId) : null;
        if (
            !in_array($account['VaiTro'] ?? '', ['NhanVien', 'QuanTriVien'], true)
            || ($account['TrangThai'] ?? '') !== 'HoatDong'
        ) {
            unset($_SESSION['user']);
            $this->redirect('login');
        }

        return $account;
    }

    // Từ chối yêu cầu nếu mã CSRF không khớp với phiên.
    private function verifyCsrf(): void
    {
        $csrf = $_POST['_csrf'] ?? '';
        $sessionCsrf = $_SESSION['_csrf'] ?? '';

        if (
            !is_string($csrf)
            || !is_string($sessionCsrf)
            || $csrf === ''
            || $sessionCsrf === ''
            || !hash_equals($sessionCsrf, $csrf)
        ) {
            http_response_code(403);
            exit('Phiên không hợp lệ. Hãy tải lại trang rồi thử lại.');
        }
    }

    // Chỉ cho phép thao tác khi yêu cầu dùng phương thức POST.
    private function postOnly(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }
    }

    // Lưu thông báo lỗi và chuyển hướng về trang phù hợp.
    private function fail(string $orderId, string $message): never
    {
        $_SESSION['_order_management_error'] = $message;
        $this->redirect('admin/orders/'.rawurlencode($orderId), 303);
    }
}
