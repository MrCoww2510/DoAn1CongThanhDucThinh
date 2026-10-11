<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'customer'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path, int $statusCode = 302): never
    {
        $app = require dirname(__DIR__, 2) . '/config/app.php';
        $baseUrl = rtrim((string) ($app['base_url'] ?? ''), '/');
        header('Location: ' . $baseUrl . '/' . ltrim($path, '/'), true, $statusCode);
        exit;
    }
}
