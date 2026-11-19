<?php

// Điều phối quản trị campaign giảm giá và voucher.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Promotion;
use Throwable;

final class PromotionManagementController extends Controller
{
    /**
     * Tải và hiển thị danh sách chiến dịch khuyến mãi trong trang quản trị.
     * @return Không trả về giá trị.
     */
    public function campaigns(): void
    {
        AdminController::authorize();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $model = new Promotion();
        $editId = trim((string) ($_GET['edit'] ?? ''));
        $this->view('admin/promotions', [
            'pageTitle' => 'Quản lý chương trình giảm giá',
            'campaigns' => $model->listCampaigns(),
            'editing' => $editId === '' ? null : $model->findCampaign($editId),
            'products' => (new Product())->allForManagement(),
            'csrfToken' => $_SESSION['_csrf'],
            'error' => (string) ($_SESSION['_promotion_error'] ?? ''),
            'success' => (string) ($_SESSION['_promotion_success'] ?? ''),
        ]);
        unset($_SESSION['_promotion_error'], $_SESSION['_promotion_success']);
    }

    /**
     * Xác thực dữ liệu biểu mẫu rồi tạo mới hoặc cập nhật chiến dịch khuyến mãi.
     * @return Không trả về giá trị.
     */
    public function saveCampaign(): void
    {
        AdminController::authorize();
        if (!$this->validCsrf()) {
            $this->fail('_promotion_error', 'Phiên biểu mẫu không hợp lệ.', 'admin/promotions');
        }
        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'scope' => (string) ($_POST['scope'] ?? ''),
            'type' => (string) ($_POST['type'] ?? ''),
            'value' => trim((string) ($_POST['value'] ?? '')),
            'start' => $this->dateValue((string) ($_POST['start'] ?? '')),
            'end' => $this->dateValue((string) ($_POST['end'] ?? '')),
            'status' => (string) ($_POST['status'] ?? ''),
            'products' => is_array($_POST['products'] ?? null) ? $_POST['products'] : [],
        ];
        $id = trim((string) ($_POST['campaign_id'] ?? ''));
        if ($data['name'] === '' || mb_strlen($data['name']) > 100
            || !in_array($data['scope'], ['ToanTrang', 'SanPham'], true)
            || !in_array($data['type'], ['PhanTram', 'SoTien'], true)
            || !is_numeric($data['value']) || (float) $data['value'] <= 0
            || !in_array($data['status'], ['HoatDong', 'TamDung', 'KetThuc'], true)) {
            $this->fail('_promotion_error', 'Thông tin chương trình giảm giá không hợp lệ.', 'admin/promotions');
        }
        try {
            (new Promotion())->saveCampaign($data, $id !== '' ? $id : null);
            $_SESSION['_promotion_success'] = 'Đã lưu chương trình giảm giá.';
            $this->redirect('admin/promotions', 303);
        } catch (Throwable $exception) {
            error_log('[Campaign save] '.$exception->getMessage());
            $this->fail('_promotion_error', $exception->getMessage(), 'admin/promotions');
        }
    }

    /**
     * Tải và hiển thị danh sách voucher trong trang quản trị.
     * @return Không trả về giá trị.
     */
    public function vouchers(): void
    {
        AdminController::authorize();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $model = new Promotion();
        $editId = trim((string) ($_GET['edit'] ?? ''));
        $this->view('admin/vouchers', [
            'pageTitle' => 'Quản lý voucher',
            'vouchers' => $model->listVouchers(),
            'editing' => $editId === '' ? null : $model->findVoucher($editId),
            'csrfToken' => $_SESSION['_csrf'],
            'error' => (string) ($_SESSION['_voucher_error'] ?? ''),
            'success' => (string) ($_SESSION['_voucher_success'] ?? ''),
        ]);
        unset($_SESSION['_voucher_error'], $_SESSION['_voucher_success']);
    }

    /**
     * Xác thực dữ liệu biểu mẫu rồi tạo mới hoặc cập nhật voucher.
     * @return Không trả về giá trị.
     */
    public function saveVoucher(): void
    {
        AdminController::authorize();
        if (!$this->validCsrf()) {
            $this->fail('_voucher_error', 'Phiên biểu mẫu không hợp lệ.', 'admin/vouchers');
        }
        $type = (string) ($_POST['type'] ?? '');
        $data = [
            'code' => strtoupper(trim((string) ($_POST['code'] ?? ''))),
            'type' => $type,
            'value' => trim((string) ($_POST['value'] ?? '')),
            'start' => $this->dateValue((string) ($_POST['start'] ?? '')),
            'end' => $this->dateValue((string) ($_POST['end'] ?? '')),
            'minimum' => trim((string) ($_POST['minimum'] ?? '0')),
            'max_discount' => trim((string) ($_POST['max_discount'] ?? '')) === '' ? null : trim((string) $_POST['max_discount']),
            'usage_limit' => trim((string) ($_POST['usage_limit'] ?? '')) === '' ? null : trim((string) $_POST['usage_limit']),
            'per_customer' => trim((string) ($_POST['per_customer'] ?? '')) === '' ? null : trim((string) $_POST['per_customer']),
            'status' => (string) ($_POST['status'] ?? ''),
            'id' => trim((string) ($_POST['voucher_id'] ?? '')),
        ];
        if (!preg_match('/\A[A-Z0-9_-]{3,50}\z/', $data['code'])
            || !in_array($type, ['PhanTram', 'SoTien'], true)
            || !is_numeric($data['value']) || (float) $data['value'] <= 0
            || !is_numeric($data['minimum']) || (float) $data['minimum'] < 0
            || ($data['max_discount'] !== null && (!is_numeric($data['max_discount']) || (float) $data['max_discount'] < 0))
            || !$this->validLimit($data['usage_limit']) || !$this->validLimit($data['per_customer'])
            || !in_array($data['status'], ['HoatDong', 'VoHieuHoa'], true)) {
            $this->fail('_voucher_error', 'Thông tin voucher không hợp lệ.', 'admin/vouchers');
        }
        try {
            (new Promotion())->saveVoucher($data, $data['id'] !== '' ? $data['id'] : null);
            $_SESSION['_voucher_success'] = 'Đã lưu voucher.';
            $this->redirect('admin/vouchers', 303);
        } catch (Throwable $exception) {
            error_log('[Voucher save] '.$exception->getMessage());
            $message = str_contains($exception->getMessage(), 'đã được sử dụng')
                ? $exception->getMessage()
                : 'Không thể lưu voucher. Hãy kiểm tra dữ liệu và mã voucher.';
            $this->fail('_voucher_error', $message, 'admin/vouchers');
        }
    }

    /**
     * So sánh token gửi từ biểu mẫu với token trong session để chống giả mạo yêu cầu.
     * @return bool Kết quả kiểm tra hoặc thao tác.
     */
    private function validCsrf(): bool
    {
        $token = (string) ($_POST['_csrf'] ?? '');
        return $token !== '' && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }

    /**
     * Chuẩn hóa giá trị ngày nhận từ biểu mẫu theo định dạng được lưu trong hệ thống.
     * @param $value Giá trị cần chuẩn hóa hoặc kiểm tra.
     * @return string Chuỗi đã chuẩn hóa hoặc mã định danh tương ứng.
     */
    private function dateValue(string $value): string
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value);
        return $date ? $date->format('Y-m-d H:i:s') : '';
    }

    /**
     * Kiểm tra giới hạn sử dụng voucher; giá trị rỗng biểu thị không đặt giới hạn.
     * @param $value Giá trị cần chuẩn hóa hoặc kiểm tra.
     * @return bool Kết quả kiểm tra hoặc thao tác.
     */
    private function validLimit(?string $value): bool
    {
        return $value === null || (ctype_digit($value) && (int) $value > 0 && (float) $value <= 4294967295);
    }

    /**
     * Lưu thông báo lỗi và chuyển về đường dẫn đã chọn mà không tiếp tục thao tác ghi.
     * @param $key Tên khóa session dùng để lưu thông báo lỗi.
     * @param $message Nội dung thông báo cần lưu hoặc gửi.
     * @return Không trả về; phương thức kết thúc yêu cầu.
     */
    private function fail(string $key, string $message, string $path): never
    {
        $_SESSION[$key] = $message;
        $this->redirect($path, 303);
    }
}
