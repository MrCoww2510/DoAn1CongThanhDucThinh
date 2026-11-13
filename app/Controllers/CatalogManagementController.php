<?php
// Controller quản lý sản phẩm và danh mục trong khu vực quản trị.


declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

final class CatalogManagementController extends Controller
{
    // Lấy dữ liệu sản phẩm phục vụ trang quản trị.
    public function products(): void
    {
        AdminController::authorize();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $model = new Product();
        $id = trim((string) ($_GET['edit'] ?? ''));
        $this->view('admin/products', [
            'pageTitle' => 'Quản lý sản phẩm', 'products' => $model->allForManagement(trim((string) ($_GET['q'] ?? ''))),
            'categories' => $model->categoriesForManagement(), 'editing' => $id !== '' ? $model->findManaged($id) : null,
            'query' => trim((string) ($_GET['q'] ?? '')), 'csrfToken' => $_SESSION['_csrf'],
            'error' => (string) ($_SESSION['_catalog_error'] ?? ''), 'success' => (string) ($_SESSION['_catalog_success'] ?? ''),
        ]);
        unset($_SESSION['_catalog_error'], $_SESSION['_catalog_success']);
    }

    // Kiểm tra dữ liệu gửi lên rồi lưu thông tin sản phẩm.
    public function saveProduct(): void
    {
        AdminController::authorize();
        if (!$this->validCsrf()) $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')), 'category' => trim((string) ($_POST['category'] ?? '')),
            'price' => trim((string) ($_POST['price'] ?? '')), 'type' => trim((string) ($_POST['type'] ?? '')),
            'brand' => trim((string) ($_POST['brand'] ?? '')), 'specs' => trim((string) ($_POST['specs'] ?? '')),
            'image' => trim((string) ($_POST['image'] ?? '')), 'description' => trim((string) ($_POST['description'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'DangKinhDoanh'),
        ];
        $id = trim((string) ($_POST['product_id'] ?? ''));
        if ($data['name'] === '' || mb_strlen($data['name']) > 200 || $data['category'] === '' || !is_numeric($data['price']) || (float) $data['price'] < 0 || mb_strlen($data['image']) > 500 || !in_array($data['status'], ['DangKinhDoanh', 'NgungKinhDoanh'], true)) {
            $this->fail('Thông tin sản phẩm chưa hợp lệ. Kiểm tra tên, danh mục, giá, ảnh và trạng thái.');
        }
        try {
            $model = new Product();
            if (!$model->categoryIsActive($data['category'])) {
                $this->fail('Chọn một danh mục đang kinh doanh.');
            }
            $model->saveManaged($data, $id !== '' ? $id : null);
            $this->succeed('Đã lưu sản phẩm.');
        } catch (\Throwable $exception) {
            error_log('[Catalog saveProduct] '.$exception->getMessage());
            $this->fail('Không thể lưu sản phẩm. Kiểm tra danh mục và thông tin đã nhập.');
        }
    }

    // Cập nhật trạng thái kinh doanh của sản phẩm.
    public function productStatus(): void
    {
        AdminController::authorize();
        if (!$this->validCsrf()) $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        $id = trim((string) ($_POST['product_id'] ?? ''));
        $status = (string) ($_POST['status'] ?? '');
        if ($id === '' || !in_array($status, ['DangKinhDoanh', 'NgungKinhDoanh'], true) || !(new Product())->changeProductStatus($id, $status)) $this->fail('Không thể cập nhật trạng thái sản phẩm.');
        $this->succeed('Đã cập nhật trạng thái sản phẩm.');
    }

    // Lấy danh sách danh mục phục vụ trang hiện tại.
    public function categories(): void
    {
        AdminController::authorize();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $this->view('admin/categories', [
            'pageTitle' => 'Quản lý danh mục', 'categories' => (new Product())->categoriesForManagement(),
            'csrfToken' => $_SESSION['_csrf'], 'error' => (string) ($_SESSION['_catalog_error'] ?? ''),
            'success' => (string) ($_SESSION['_catalog_success'] ?? ''),
        ]);
        unset($_SESSION['_catalog_error'], $_SESSION['_catalog_success']);
    }

    // Tạo mới hoặc cập nhật tên danh mục.
    public function saveCategory(): void
    {
        AdminController::authorize();
        if (!$this->validCsrf()) $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        $id = trim((string) ($_POST['category_id'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 100) $this->fail('Tên danh mục bắt buộc và tối đa 100 ký tự.');
        try {
            (new Product())->saveCategory($name, $id !== '' ? $id : null);
            $this->succeed('Đã lưu danh mục.');
        } catch (\Throwable $exception) {
            error_log('[Catalog saveCategory] '.$exception->getMessage());
            $this->fail('Không thể lưu danh mục. Tên danh mục có thể đã được sử dụng.');
        }
    }

    // Cập nhật trạng thái kinh doanh của danh mục.
    public function categoryStatus(): void
    {
        AdminController::authorize();
        if (!$this->validCsrf()) $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        $id = trim((string) ($_POST['category_id'] ?? ''));
        $status = (string) ($_POST['status'] ?? '');
        if ($id === '' || !in_array($status, ['DangKinhDoanh', 'NgungKinhDoanh'], true) || !(new Product())->changeCategoryStatus($id, $status)) $this->fail('Không thể cập nhật trạng thái danh mục.');
        $this->succeed('Đã cập nhật trạng thái danh mục.');
    }

    // Xóa danh mục sau khi kiểm tra điều kiện nghiệp vụ.
    public function deleteCategory(): void
    {
        AdminController::authorize();
        if (!$this->validCsrf()) $this->fail('Phiên biểu mẫu không hợp lệ. Hãy tải lại trang.');
        if (!(new Product())->deleteCategory(trim((string) ($_POST['category_id'] ?? '')))) $this->fail('Danh mục có sản phẩm liên kết hoặc không tồn tại; hãy ngừng kinh doanh thay vì xóa.');
        $this->succeed('Đã xóa danh mục không còn sản phẩm liên kết.');
    }

    // So sánh mã CSRF trong yêu cầu với mã lưu trong phiên.
    private function validCsrf(): bool
    {
        $token = (string) ($_POST['_csrf'] ?? '');
        return $token !== '' && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }

    // Lưu thông báo lỗi và chuyển hướng về trang phù hợp.
    private function fail(string $message): never
    {
        $_SESSION['_catalog_error'] = $message;
        $this->redirect('admin/products');
    }

    // Lưu thông báo thành công và chuyển hướng về trang phù hợp.
    private function succeed(string $message): never
    {
        $_SESSION['_catalog_success'] = $message;
        $this->redirect((str_contains($_SERVER['HTTP_REFERER'] ?? '', '/admin/categories') ? 'admin/categories' : 'admin/products'), 303);
    }
}
