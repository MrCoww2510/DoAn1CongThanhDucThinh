<?php

// Dịch vụ tạo và xác minh dữ liệu thanh toán qua SePay.

declare(strict_types=1);

namespace App\Services;

final class SePayPayment
{
    private const CHECKOUT_URL = 'https://pay-sandbox.sepay.vn/v1/checkout/init';

    /** @var array<string, string> */
    private array $config;

    // Đọc thông tin cấu hình cần thiết cho tích hợp SePay.
    public function __construct()
    {
        $config = require dirname(__DIR__, 2).'/config/sepay.php';
        $this->config = $config;
    }

    // Kiểm tra các thông tin bắt buộc của SePay đã được cấu hình.
    public function isConfigured(): bool
    {
        return $this->config['merchant_id'] !== ''
            && $this->config['secret_key'] !== ''
            && $this->config['ipn_secret_key'] !== ''
            && $this->publicBaseUrl() !== null;
    }

    // Lấy khóa dùng để xác minh thông báo thanh toán.
    public function ipnSecretKey(): string
    {
        return $this->config['ipn_secret_key'];
    }

    /**
     * @param array{MaDH:mixed, TongTien:mixed} $order
     *
     * @return array<string, string>
     */
    public function createCheckoutFields(array $order): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('SePay Sandbox chưa được cấu hình đầy đủ. Hãy cấu hình thông tin trong config/sepay.local.php.');
        }

        $orderId = (string) ($order['MaDH'] ?? '');
        if (preg_match('/\A[A-Za-z0-9_-]{1,100}\z/', $orderId) !== 1) {
            throw new \RuntimeException('Mã đơn hàng không hợp lệ để gửi sang SePay.');
        }

        $amount = $this->amountInVnd((string) ($order['TongTien'] ?? ''));
        $baseUrl = (string) $this->publicBaseUrl();
        $orderQuery = '?id='.rawurlencode($orderId);

        $fields = [
            'merchant' => $this->config['merchant_id'],
            'currency' => 'VND',
            'order_amount' => (string) $amount,
            'operation' => 'PURCHASE',
            'order_description' => 'Thanh toan don hang '.$orderId,
            'payment_method' => 'BANK_TRANSFER',
            'order_invoice_number' => $orderId,
            'success_url' => $baseUrl.'/payments/sepay/success'.$orderQuery,
            'error_url' => $baseUrl.'/payments/sepay/error'.$orderQuery,
            'cancel_url' => $baseUrl.'/payments/sepay/cancel'.$orderQuery,
        ];

        $signedFields = [];
        $signableNames = [
            'merchant',
            'env',
            'operation',
            'payment_method',
            'order_amount',
            'currency',
            'order_invoice_number',
            'order_description',
            'customer_id',
            'agreement_id',
            'agreement_name',
            'agreement_type',
            'agreement_payment_frequency',
            'agreement_amount_per_payment',
            'success_url',
            'error_url',
            'cancel_url',
        ];

        foreach ($fields as $name => $value) {
            if (in_array($name, $signableNames, true)) {
                $signedFields[] = $name.'='.$value;
            }
        }

        $fields['signature'] = base64_encode(
            hash_hmac(
                'sha256',
                implode(',', $signedFields),
                $this->config['secret_key'],
                true
            )
        );

        return $fields;
    }

    /**
     * @param array<string, string> $fields
     */
    public function renderCheckoutForm(array $fields): string
    {
        $html = '<!doctype html><html lang="vi"><head><meta charset="utf-8">'
            .'<meta name="viewport" content="width=device-width, initial-scale=1">'
            .'<title>Đang chuyển đến SePay</title></head><body>'
            .'<p>Đang chuyển đến cổng thanh toán SePay...</p>'
            .'<form id="sepay-checkout-form" method="post" action="'
            .htmlspecialchars(self::CHECKOUT_URL, ENT_QUOTES, 'UTF-8').'">';

        foreach ($fields as $name => $value) {
            $html .= '<input type="hidden" name="'
                .htmlspecialchars($name, ENT_QUOTES, 'UTF-8')
                .'" value="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'">';
        }

        return $html
            .'<button type="submit">Tiếp tục thanh toán</button></form>'
            .'<script>document.getElementById("sepay-checkout-form").submit();</script>'
            .'</body></html>';
    }

    // So sánh hai số tiền dưới dạng giá trị VND chuẩn hóa.
    public static function amountsMatch(string $left, string $right): bool
    {
        $normalize = static function (string $amount): ?string {
            if (preg_match('/\A([0-9]+)(?:\.([0-9]+))?\z/', trim($amount), $matches) !== 1) {
                return null;
            }

            $whole = ltrim($matches[1], '0');
            $fraction = rtrim($matches[2] ?? '', '0');

            return ($whole === '' ? '0' : $whole)
                .($fraction === '' ? '' : '.'.$fraction);
        };

        $normalizedLeft = $normalize($left);
        $normalizedRight = $normalize($right);

        return $normalizedLeft !== null
            && $normalizedRight !== null
            && hash_equals($normalizedLeft, $normalizedRight);
    }

    // Chuẩn hóa chuỗi số tiền thành số nguyên VND.
    private function amountInVnd(string $amount): int
    {
        if (
            preg_match('/\A([0-9]+)(?:\.([0-9]+))?\z/', trim($amount), $matches) !== 1
            || rtrim($matches[2] ?? '', '0') !== ''
        ) {
            throw new \RuntimeException('Tổng tiền đơn hàng không hợp lệ với thanh toán VND.');
        }

        $whole = ltrim($matches[1], '0');
        $whole = $whole === '' ? '0' : $whole;
        $max = (string) PHP_INT_MAX;

        if (
            strlen($whole) > strlen($max)
            || (strlen($whole) === strlen($max) && strcmp($whole, $max) > 0)
        ) {
            throw new \RuntimeException('Tổng tiền đơn hàng vượt quá giới hạn của SePay.');
        }

        $amountInVnd = (int) $whole;
        if ($amountInVnd < 1) {
            throw new \RuntimeException('Tổng tiền đơn hàng phải lớn hơn 0.');
        }

        return $amountInVnd;
    }

    // Lấy địa chỉ gốc công khai cần thiết cho callback thanh toán.
    private function publicBaseUrl(): ?string
    {
        $url = trim($this->config['public_base_url']);
        $parts = $url === '' ? false : parse_url($url);

        if (
            $parts === false
            || ($parts['scheme'] ?? '') !== 'https'
            || empty($parts['host'])
            || isset($parts['query'])
            || isset($parts['fragment'])
            || isset($parts['user'])
            || isset($parts['pass'])
        ) {
            return null;
        }

        return rtrim($url, '/');
    }
}