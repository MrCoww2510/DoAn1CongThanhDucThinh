<?php
declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;

/** @var Router $router */
$router->get('/', [HomeController::class, 'index']);

// Các route module sẽ được thêm vào đây khi triển khai từng chức năng.
