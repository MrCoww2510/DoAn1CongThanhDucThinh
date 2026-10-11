<?php
declare(strict_types=1);

use App\Core\Router;

define('BASE_PATH', dirname(__DIR__));

$appConfig = require BASE_PATH . '/config/app.php';
date_default_timezone_set($appConfig['timezone'] ?? 'Asia/Ho_Chi_Minh');

if (($appConfig['debug'] ?? false) === true) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

set_exception_handler(static function (Throwable $exception) use ($appConfig): void {
    error_log((string) $exception);

    http_response_code(500);
    if (($appConfig['debug'] ?? false) === true) {
        echo '<h1>Lỗi ứng dụng (chế độ phát triển)</h1><pre>'
            . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8')
            . '</pre>';
    } else {
        echo '<h1>Đã xảy ra lỗi</h1><p>Vui lòng thử lại sau.</p>';
    }
});

$router = new Router();

require BASE_PATH . '/routes/web.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';

// Loại bỏ tiền tố thư mục dự án khi chạy trên XAMPP.
$basePath = '/DoAn1CongThanhDucThinh/public';

if ($requestPath === $basePath) {
    $requestPath = '/';
} elseif (str_starts_with($requestPath, $basePath . '/')) {
    $requestPath = substr($requestPath, strlen($basePath));
}

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $requestPath
);
