<?php
// Controller xử lý nghiệp vụ điều chỉnh và xem tồn kho.


declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Inventory;

final class InventoryController extends Controller
{
    // Chuẩn bị dữ liệu và hiển thị trang tương ứng.
    public function index(): void
    {
        AdminController::authorize();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $model = new Inventory();
        $this->view('admin/inventory', [
            'pageTitle' => 'Quản lý kho', 'products' => $model->products(), 'history' => $model->history(),
            'csrfToken' => $_SESSION['_csrf'], 'error' => (string) ($_SESSION['_inventory_error'] ?? ''),
            'success' => (string) ($_SESSION['_inventory_success'] ?? ''),
        ]);
        unset($_SESSION['_inventory_error'], $_SESSION['_inventory_success']);
    }

    // Cập nhật dữ liệu sau khi kiểm tra các điều kiện nghiệp vụ.
    public function update(): void
    {
        AdminController::authorize();
        $token = (string) ($_POST['_csrf'] ?? '');
        if ($token === '' || !isset($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $token)) {
            $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        }
        $productId = trim((string) ($_POST['product_id'] ?? ''));
        $operation = (string) ($_POST['operation'] ?? '');
        $rawQuantity = trim((string) ($_POST['quantity'] ?? ''));
        if ($productId === '' || !in_array($operation, ['NhapKho', 'DieuChinh'], true) || !ctype_digit($rawQuantity) || (float) $rawQuantity > 2147483647) {
            $this->fail('Thông tin cập nhật kho không hợp lệ.');
        }
        try {
            (new \App\Models\Inventory())->update($productId, $operation, (int) $rawQuantity, (string) $_SESSION['user']['MaND']);
            $_SESSION['_inventory_success'] = $operation === 'NhapKho' ? 'Đã nhập kho và ghi lịch sử.' : 'Đã điều chỉnh tồn kho và ghi lịch sử.';
            $this->redirect('admin/inventory', 303);
        } catch (\Throwable $exception) {
            error_log('[Inventory update] '.$exception->getMessage());
            $this->fail($exception instanceof \InvalidArgumentException ? $exception->getMessage() : 'Không thể cập nhật tồn kho. Kiểm tra sản phẩm và thử lại.');
        }
    }

    // Lưu thông báo lỗi và chuyển hướng về trang phù hợp.
    private function fail(string $message): never
    {
        $_SESSION['_inventory_error'] = $message;
        $this->redirect('admin/inventory', 303);
    }
}
