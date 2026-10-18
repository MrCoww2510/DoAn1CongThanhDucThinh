<?php

declare(strict_types=1);

use App\Controllers\AboutController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\UserManagementController;
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

// Quản lý người dùng (chỉ quản trị viên; các thao tác ghi dùng POST và CSRF).
$router->get('/admin/users', [UserManagementController::class, 'index']);
$router->post('/admin/users/create-staff', [UserManagementController::class, 'createStaff']);
$router->post('/admin/users/update-staff', [UserManagementController::class, 'updateStaff']);
$router->post('/admin/users/status', [UserManagementController::class, 'changeStatus']);

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

$router->get('/about', [AboutController::class, 'index']);
