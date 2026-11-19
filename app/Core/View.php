<?php

// Nạp view, truyền dữ liệu và kết xuất nội dung cùng layout.

declare(strict_types=1);

namespace App\Core;

final class View
{
    // Tìm tệp giao diện, truyền dữ liệu và ghép nội dung vào layout.
    public static function render(string $view, array $data = [], string $layout = 'customer'): void
    {
        $viewsRoot = dirname(__DIR__).'/views/';
        $viewPath = $viewsRoot.trim($view, '/').'.php';
        $layoutPath = $viewsRoot.'layouts/'.$layout.'.php';

        if (!is_file($viewPath)) {
            throw new \RuntimeException('Không tìm thấy View: '.$view);
        }
        if (!is_file($layoutPath)) {
            throw new \RuntimeException('Không tìm thấy layout: '.$layout);
        }

        $appConfig = require dirname(__DIR__, 2).'/config/app.php';
        $baseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
        $requestPath = (string) ($_SERVER['APP_REQUEST_PATH'] ?? '/');

        // Chỉ tải stylesheet dành riêng cho view đang render.
        $pageCss = [
            'home/index' => ['home.css'],
            'auth/login' => ['auth.css'],
            'auth/register' => ['auth.css'],
            'products/index' => ['products.css'],
            'products/show' => ['products.css'],
            'products/not-found' => ['products.css'],
            'pages/about' => ['about.css'],
            'pages/support' => ['support.css'],
            'cart/index' => ['cart.css'],
            'checkout/index' => ['checkout.css'],
            'checkout/success' => ['checkout.css'],
            'orders/index' => ['orders.css'],
            'orders/show' => ['orders.css'],
            'account/index' => ['account.css'],
            'errors/404' => ['errors404.css'],
            'admin/dashboard' => ['admin.css'],
            'admin/products' => ['admin.css'],
            'admin/categories' => ['admin.css'],
            'admin/orders' => ['admin.css'],
            'admin/order-detail' => ['admin.css'],
            'admin/inventory' => ['admin.css'],
            'admin/promotions' => ['admin.css'],
            'admin/vouchers' => ['admin.css'],
            'admin/statistics' => ['admin.css'],
            'admin/users' => ['admin.css', 'users.css'],
        ];
        $pageCss = $pageCss[trim($view, '/')] ?? [];

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = (string) ob_get_clean();

        require $layoutPath;
    }

    // Ngăn tạo đối tượng vì giao diện được render qua phương thức tĩnh.
    private function __construct()
    {
    }
}
