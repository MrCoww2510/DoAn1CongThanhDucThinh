<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Services\SePayPayment;
use RuntimeException;
use Throwable;

final class PaymentController extends Controller
{
    /**
     * Khởi tạo yêu cầu thanh toán SePay cho đơn hàng hợp lệ và chuyển khách tới biểu mẫu thanh toán.
     * @return Không trả về giá trị.
     */
    public function start(): void
    {
        $user = $this->requireCustomer();
        $orderId = $this->queryOrderId();
        $order = $orderId === ''
            ? null
            : (new Order())->findForCustomer($orderId, (string) $user['MaND']);

        if ($order === null) {
            $this->redirect('orders', 303);
        }

        $details = $order['order'];
        if (
            $details['PhuongThucTT'] !== 'Online'
            || $details['TrangThaiDH'] === 'Đã hủy'
            || $details['TrangThaiTT'] !== 'Chưa thanh toán'
        ) {
            $this->setOrderFlash('Đơn hàng không còn đủ điều kiện để thanh toán.', 'error');
            $this->redirect('orders/'.rawurlencode($orderId), 303);
        }

        $sepay = new SePayPayment();
        if (!$sepay->isConfigured()) {
            $this->setOrderFlash(
                'SePay Sandbox chưa được cấu hình. Hãy hoàn tất cấu hình cục bộ trước khi thanh toán.',
                'error'
            );
            $this->redirect('orders/'.rawurlencode($orderId), 303);
        }

        try {
            $fields = $sepay->createCheckoutFields($details);
        } catch (RuntimeException $exception) {
            $this->setOrderFlash($exception->getMessage(), 'error');
            $this->redirect('orders/'.rawurlencode($orderId), 303);
        }

        header('Content-Type: text/html; charset=UTF-8');
        echo $sepay->renderCheckoutForm($fields);
    }

    /**
     * Xử lý URL trả về thành công từ cổng thanh toán và xác minh trạng thái đơn hàng.
     * @return Không trả về giá trị.
     */
    public function success(): void
    {
        $this->handleReturn('success');
    }

    /**
     * Hiển thị kết quả lỗi khi cổng thanh toán trả khách về.
     * @return Không trả về giá trị.
     */
    public function error(): void
    {
        $this->handleReturn('error');
    }

    /**
     * Xử lý trường hợp khách hủy hoặc rời khỏi luồng thanh toán.
     * @return Không trả về giá trị.
     */
    public function cancel(): void
    {
        $this->handleReturn('cancel');
    }

    /**
     * Nhận và xác minh thông báo IPN từ SePay trước khi cập nhật trạng thái thanh toán.
     * @return Không trả về giá trị.
     */
    public function ipn(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->sendJson(405, ['error' => 'Method not allowed']);
        }

        // Chỉ chấp nhận IPN có khóa bí mật hợp lệ trước khi đọc và xử lý nội dung.
        $sepay = new SePayPayment();
        $ipnSecret = $sepay->ipnSecretKey();
        if ($ipnSecret === '') {
            $this->sendJson(503, ['error' => 'IPN authentication is not configured']);
        }

        $providedSecret = (string) ($_SERVER['HTTP_X_SECRET_KEY'] ?? '');
        if ($providedSecret === '' || !hash_equals($ipnSecret, $providedSecret)) {
            $this->sendJson(401, ['error' => 'Unauthorized']);
        }

        // Giới hạn kích thước payload trước khi giải mã JSON để tránh xử lý nội dung quá lớn.
        $rawBody = file_get_contents('php://input');
        if (is_string($rawBody) && strlen($rawBody) > 65536) {
            $this->sendJson(413, ['error' => 'IPN payload is too large']);
        }

        $payload = is_string($rawBody) ? json_decode($rawBody, true) : null;
        if (!is_array($payload)) {
            $this->sendJson(400, ['error' => 'Invalid JSON payload']);
        }

        $notificationType = $payload['notification_type'] ?? '';
        // Bỏ qua loại thông báo không thể hiện thanh toán thành công.
        if ($notificationType === 'TRANSACTION_VOID') {
            $this->sendJson(200, ['received' => true, 'processed' => false]);
        }
        if ($notificationType !== 'ORDER_PAID') {
            $this->sendJson(200, ['received' => true, 'processed' => false]);
        }

        $order = $payload['order'] ?? null;
        $transaction = $payload['transaction'] ?? null;
        if (!is_array($order) || !is_array($transaction)) {
            $this->sendJson(400, ['error' => 'Missing order or transaction data']);
        }

        $invoice = $order['order_invoice_number'] ?? null;
        $orderAmount = $order['order_amount'] ?? null;
        $transactionAmount = $transaction['transaction_amount'] ?? null;
        $transactionId = $transaction['transaction_id'] ?? null;

        // Đối chiếu trạng thái, tiền tệ và số tiền của cả đơn lẫn giao dịch trước khi ghi nhận thanh toán.
        if (
            !is_string($invoice)
            || $invoice === ''
            || ($order['order_status'] ?? '') !== 'CAPTURED'
            || ($order['order_currency'] ?? '') !== 'VND'
            || ($transaction['transaction_status'] ?? '') !== 'APPROVED'
            || ($transaction['transaction_type'] ?? '') !== 'PAYMENT'
            || ($transaction['transaction_currency'] ?? '') !== 'VND'
            || !is_string($orderAmount)
            || !is_string($transactionAmount)
            || (!is_string($transactionId) && !is_int($transactionId))
            || !SePayPayment::amountsMatch($orderAmount, $transactionAmount)
        ) {
            $this->sendJson(422, ['error' => 'Payment notification did not pass validation']);
        }

        try {
            $processed = (new Order())->markOnlineOrderPaid($invoice, $transactionAmount, (string) $transactionId);
        } catch (Throwable $exception) {
            error_log('[SePay IPN] Order '.$invoice.': '.$exception->getMessage());
            $this->sendJson(422, ['error' => 'Payment could not be applied to the order']);
        }

        if (!$processed) {
            error_log('[SePay IPN] Ignored payment for ineligible order '.$invoice);
        }

        $this->sendJson(200, ['received' => true, 'processed' => $processed]);
    }

    /**
     * Xử lý kết quả trả về từ cổng thanh toán, kiểm tra quyền sở hữu đơn và hiển thị trạng thái tương ứng.
     * @param $result Kết quả trả về từ cổng thanh toán.
     * @return Không trả về giá trị.
     */
    private function handleReturn(string $result): void
    {
        $user = $this->requireCustomer();
        $orderId = $this->queryOrderId();
        $receipt = $orderId === ''
            ? null
            : (new Order())->findForCustomer($orderId, (string) $user['MaND']);

        if ($receipt === null) {
            $this->redirect('orders', 303);
        }

        $paymentStatus = (string) $receipt['order']['TrangThaiTT'];
        $orderStatus = (string) $receipt['order']['TrangThaiDH'];
        if ($paymentStatus === 'Đã thanh toán') {
            $message = 'SePay đã xác nhận thanh toán thành công.';
            $type = 'success';
        } elseif ($orderStatus === 'Đã hủy') {
            $message = 'Đơn hàng đã bị hủy và không thể thanh toán.';
            $type = 'error';
        } elseif ($result === 'success') {
            $message = 'Đã quay lại từ SePay. Hệ thống đang chờ IPN xác nhận; trạng thái chưa thanh toán sẽ được giữ nguyên cho đến khi nhận được xác nhận hợp lệ.';
            $type = 'error';
        } elseif ($result === 'cancel') {
            $message = 'Bạn đã hủy thao tác thanh toán. Đơn hàng vẫn chưa được thanh toán và có thể thử lại.';
            $type = 'error';
        } else {
            $message = 'SePay không hoàn tất giao dịch. Đơn hàng vẫn chưa được thanh toán và có thể thử lại.';
            $type = 'error';
        }

        $this->setOrderFlash($message, $type);
        $this->redirect('orders/'.rawurlencode($orderId), 303);
    }

    /**
     * Kiểm tra session để chỉ cho phép khách hàng truy cập các thao tác thanh toán.
     * @return array Mảng dữ liệu theo cấu trúc được xây dựng trong phương thức.
     */
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

    /**
     * Đọc và chuẩn hóa mã đơn hàng từ tham số truy vấn của yêu cầu hiện tại.
     * @return string Chuỗi đã chuẩn hóa hoặc mã định danh tương ứng.
     */
    private function queryOrderId(): string
    {
        $value = $_GET['id'] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    /**
     * Lưu thông báo kết quả thanh toán vào session để hiển thị sau khi chuyển hướng.
     * @param $message Nội dung thông báo cần lưu hoặc gửi.
     * @param $type Phân loại thông báo được lưu cùng nội dung.
     * @return Không trả về giá trị.
     */
    private function setOrderFlash(string $message, string $type): void
    {
        $_SESSION['_order_flash'] = [
            'message' => $message,
            'type' => $type,
        ];
    }

    /**
     * Gửi phản hồi JSON với mã HTTP và nội dung đã cung cấp, sau đó kết thúc yêu cầu.
     * @param $status Mã trạng thái HTTP của phản hồi.
     * @param $body Dữ liệu JSON cần gửi về trình duyệt.
     * @return Không trả về; phương thức kết thúc yêu cầu.
     */
    private function sendJson(int $status, array $body): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
