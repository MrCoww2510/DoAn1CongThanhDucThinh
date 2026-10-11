<?php
declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\AuthController;
/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/products', [ProductController::class, 'index']);

$router->get('/product', [ProductController::class, 'show']);

$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);