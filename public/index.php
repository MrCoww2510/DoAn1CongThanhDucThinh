<?php
// Điểm vào HTTP: khởi tạo ứng dụng, nạp route và điều phối yêu cầu.


declare(strict_types=1);

use App\Core\Router;

define('BASE_PATH', dirname(__DIR__));

$appConfig = require BASE_PATH.'/config/app.php';
date_default_timezone_set($appConfig['timezone'] ?? 'Asia/Ho_Chi_Minh');

if (($appConfig['debug'] ?? false) === true) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

/** Tự động nạp lớp trong namespace App từ thư mục app theo tên lớp. */
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH.'/app/'.str_replace('\\', '/', $relativeClass).'.php';

    if (is_file($file)) {
        require $file;
    }
});

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Bảo vệ cookie session; chỉ bật Secure khi request hiện tại chạy qua HTTPS.
    $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $https,
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

/** Ghi log ngoại lệ và trả trang lỗi phù hợp với chế độ debug đang bật hay tắt. */
set_exception_handler(static function (Throwable $exception) use ($appConfig): void {
    error_log((string) $exception);

    http_response_code(500);
    if (($appConfig['debug'] ?? false) === true) {
        echo '<h1>Lỗi ứng dụng (chế độ phát triển)</h1><pre>'
            .htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8')
            .'</pre>';
    } else {
        echo '<h1>Đã xảy ra lỗi</h1><p>Vui lòng thử lại sau.</p>';
    }
});

$router = new Router();

require BASE_PATH.'/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
