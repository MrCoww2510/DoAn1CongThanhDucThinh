<?php

// Controller khởi tạo và tiếp nhận kết quả thanh toán SePay.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Services\SePayPayment;

final class PaymentController extends Controller
{
    // Khởi tạo quy trình thanh toán cho đơn hàng.
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
        } catch (\RuntimeException $exception) {
            $this->setOrderFlash($exception->getMessage(), 'error');
            $this->redirect('orders/'.rawurlencode($orderId), 303);
        }

        header('Content-Type: text/html; charset=UTF-8');
        echo $sepay->renderCheckoutForm($fields);
    }

    // Hiển thị kết quả đặt hàng thành công.
    public function success(): void
    {
        $this->handleReturn('success');
    }

    // Hiển thị hoặc xử lý kết quả thanh toán thất bại.
    public function error(): void
    {
        $this->handleReturn('error');
    }

    // Xử lý yêu cầu hủy giao dịch hoặc đơn hàng.
    public function cancel(): void
    {
        $this->handleReturn('cancel');
    }

    // Tiếp nhận và xác minh thông báo thanh toán từ cổng thanh toán.
    public function ipn(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->sendJson(405, ['error' => 'Method not allowed']);
        }

        $sepay = new SePayPayment();
        $ipnSecret = $sepay->ipnSecretKey();
        if ($ipnSecret === '') {
            $this->sendJson(503, ['error' => 'IPN authentication is not configured']);
        }

        $providedSecret = (string) ($_SERVER['HTTP_X_SECRET_KEY'] ?? '');
        if ($providedSecret === '' || !hash_equals($ipnSecret, $providedSecret)) {
            $this->sendJson(401, ['error' => 'Unauthorized']);
        }

        $rawBody = file_get_contents('php://input');
        if (is_string($rawBody) && strlen($rawBody) > 65536) {
            $this->sendJson(413, ['error' => 'IPN payload is too large']);
        }

        $payload = is_string($rawBody) ? json_decode($rawBody, true) : null;
        if (!is_array($payload)) {
            $this->sendJson(400, ['error' => 'Invalid JSON payload']);
        }

        $notificationType = $payload['notification_type'] ?? '';
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
            || !SePayPayment::amountsMatch($orderAmount, $transactionAmount)
        ) {
            $this->sendJson(422, ['error' => 'Payment notification did not pass validation']);
        }

        try {
            $processed = (new Order())->markOnlineOrderPaid($invoice, $transactionAmount);
        } catch (\Throwable $exception) {
            error_log('[SePay IPN] Order '.$invoice.': '.$exception->getMessage());
            $this->sendJson(422, ['error' => 'Payment could not be applied to the order']);
        }

        if (!$processed) {
            error_log('[SePay IPN] Ignored payment for ineligible order '.$invoice);
        }

        $this->sendJson(200, ['received' => true, 'processed' => $processed]);
    }

    // Xử lý kết quả trả về từ cổng thanh toán.
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

    // Đảm bảo yêu cầu được gửi bởi khách hàng đã đăng nhập.
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

    // Lấy mã đơn hàng từ tham số yêu cầu hợp lệ.
    private function queryOrderId(): string
    {
        $value = $_GET['id'] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    // Lưu thông báo gắn với đơn hàng để hiển thị sau chuyển hướng.
    private function setOrderFlash(string $message, string $type): void
    {
        $_SESSION['_order_flash'] = [
            'message' => $message,
            'type' => $type,
        ];
    }

    // Trả phản hồi JSON với mã trạng thái HTTP được chỉ định.
    private function sendJson(int $status, array $body): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE);
        exit;
    }
}