<?php

declare(strict_types=1);

// Ưu tiên cấu hình riêng trên máy; file này được Git bỏ qua để mỗi môi trường tự cấu hình.
$localConfigPath = __DIR__.'/google.local.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
if (!is_array($localConfig)) {
    throw new RuntimeException('config/google.local.php phải trả về một mảng cấu hình.');
}

// Chỉ chứa Client ID công khai. Không đặt Client Secret trong ứng dụng GIS.
return [
    // Cho phép cấu hình qua biến môi trường nếu không có google.local.php.
    'client_id' => trim((string) ($localConfig['client_id'] ?? getenv('GOOGLE_OAUTH_CLIENT_ID') ?: '807719654360-j30si1h948no3442d01jj9kuheecete7.apps.googleusercontent.com')),
];
