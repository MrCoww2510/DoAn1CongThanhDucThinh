<?php

declare(strict_types=1);

use App\Controllers\AboutController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Core\Router;

/* @var Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/products', [ProductController::class, 'index']);

$router->get('/product', [ProductController::class, 'show']);
// Đăng ký/nhập/xuất
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// Giỏ hàng
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/buy-now', [CartController::class, 'buyNow']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);

// Đặt hàng và thanh toán khi nhận hàng.
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'placeOrder']);
$router->get('/checkout/success', [CheckoutController::class, 'success']);

$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/{orderId}', [OrderController::class, 'show']);
$router->post('/orders/{orderId}/cancel', [OrderController::class, 'cancel']);

$router->get('/about', [AboutController::class, 'index']);
