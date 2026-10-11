<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'customer'): void
    {
        $viewsRoot = dirname(__DIR__) . '/Views/';
        $viewPath = $viewsRoot . trim($view, '/') . '.php';
        $layoutPath = $viewsRoot . 'layouts/' . $layout . '.php';

        if (!is_file($viewPath)) {
            throw new RuntimeException('Không tìm thấy View: ' . $view);
        }
        if (!is_file($layoutPath)) {
            throw new RuntimeException('Không tìm thấy layout: ' . $layout);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = (string) ob_get_clean();

        require $layoutPath;
    }

    private function __construct() {}
}
