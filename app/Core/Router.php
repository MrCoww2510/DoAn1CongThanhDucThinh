<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:callable|array}> */
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, callable|array $handler): void
    {
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        $quoted = preg_quote($path, '#');
        $pattern = preg_replace_callback(
            '/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/',
            static fn(array $matches): string => '(?P<' . $matches[1] . '>[^/]+)',
            $quoted
        );

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => '#^' . $pattern . '/?$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        $app = require dirname(__DIR__, 2) . '/config/app.php';
        $baseUrl = rtrim((string) ($app['base_url'] ?? ''), '/');

        if (
            $baseUrl !== ''
            && ($path === $baseUrl || str_starts_with($path, $baseUrl . '/'))
        ) {
            $path = substr($path, strlen($baseUrl)) ?: '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches) !== 1) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $handler = $route['handler'];

            if (is_array($handler) && count($handler) === 2) {
                [$class, $action] = $handler;
                $controller = new $class();
                $controller->{$action}(...array_values($params));
                return;
            }

            $handler(...array_values($params));
            return;
        }

        http_response_code(404);
        $title = 'Không tìm thấy trang';
        $message = 'Đường dẫn bạn truy cập không tồn tại.';
        require dirname(__DIR__) . '/Views/errors/404.php';
    }

    public function __construct() {}
}
