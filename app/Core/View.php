<?php

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
