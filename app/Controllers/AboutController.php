<?php

declare(strict_types=1);

namespace App\Controllers;

class AboutController
{
    // Hiển thị trang giới thiệu cửa hàng.
    public function index(): void
    {
        $appConfig = require BASE_PATH.'/config/app.php';
        $baseUrl = rtrim($appConfig['base_url'] ?? '', '/');

        ob_start();
        require BASE_PATH.'/app/Views/pages/about.php';
        $content = ob_get_clean();

        require BASE_PATH.'/app/Views/layouts/customer.php';
    }
}
