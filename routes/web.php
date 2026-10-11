<?php
declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ProductController;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/products', [ProductController::class, 'index']);

$router->get('/product', [ProductController::class, 'show']);