<?php
// Cấu hình tích hợp thanh toán SePay.


declare(strict_types=1);

$localConfigPath = __DIR__.'/sepay.local.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
if (!is_array($localConfig)) {
    throw new RuntimeException('config/sepay.local.php phải trả về một mảng cấu hình.');
}

$readValue = static function (string $key, string $environmentName) use ($localConfig): string {
    $value = $localConfig[$key] ?? getenv($environmentName);

    if ($value === false || $value === null) {
        return '';
    }
    if (!is_string($value)) {
        throw new RuntimeException('Giá trị '.$key.' trong cấu hình SePay phải là chuỗi.');
    }

    return trim($value);
};

return [
    'merchant_id' => $readValue('merchant_id', 'SEPAY_MERCHANT_ID'),
    'secret_key' => $readValue('secret_key', 'SEPAY_SECRET_KEY'),
    'ipn_secret_key' => $readValue('ipn_secret_key', 'SEPAY_IPN_SECRET_KEY'),
    'public_base_url' => $readValue('public_base_url', 'SEPAY_PUBLIC_BASE_URL'),
];
