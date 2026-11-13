<?php

// Khai báo các tuyến HTTP và ánh xạ chúng tới controller.

declare(strict_types=1);

use App\Controllers\AboutController;
use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CatalogManagementController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\InventoryController;
use App\Controllers\OrderController;
use App\Controllers\OrderManagementController;
use App\Controllers\PaymentController;
use App\Controllers\ProductController;
use App\Controllers\StatisticsController;
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
$router->post('/auth/google/login', [AuthController::class, 'googleLogin']);
$router->post('/auth/google/link', [AuthController::class, 'linkGoogle']);

// Tài khoản khách hàng
$router->get('/account', [AccountController::class, 'index']);
$router->post('/account', [AccountController::class, 'update']);
$router->post('/account/addresses/add', [AccountController::class, 'addAddress']);
$router->post('/account/addresses/update', [AccountController::class, 'updateAddress']);
$router->post('/account/addresses/delete', [AccountController::class, 'deleteAddress']);

// Quản lý người dùng (chỉ quản trị viên; các thao tác ghi dùng POST và CSRF).
$router->get('/admin/users', [UserManagementController::class, 'index']);
$router->post('/admin/users/create-staff', [UserManagementController::class, 'createStaff']);
$router->post('/admin/users/update-staff', [UserManagementController::class, 'updateStaff']);
$router->post('/admin/users/status', [UserManagementController::class, 'changeStatus']);
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/products', [CatalogManagementController::class, 'products']);
$router->post('/admin/products/save', [CatalogManagementController::class, 'saveProduct']);
$router->post('/admin/products/status', [CatalogManagementController::class, 'productStatus']);
$router->get('/admin/categories', [CatalogManagementController::class, 'categories']);
$router->post('/admin/categories/save', [CatalogManagementController::class, 'saveCategory']);
$router->post('/admin/categories/status', [CatalogManagementController::class, 'categoryStatus']);
$router->post('/admin/categories/delete', [CatalogManagementController::class, 'deleteCategory']);
$router->get('/admin/inventory', [InventoryController::class, 'index']);
$router->post('/admin/inventory/update', [InventoryController::class, 'update']);
$router->get('/admin/statistics', [StatisticsController::class, 'index']);

// Giỏ hàng
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/buy-now', [CartController::class, 'buyNow']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);

// Đặt hàng và thanh toán khi nhận hàng.
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout/address', [CheckoutController::class, 'addAddress']);
$router->post('/checkout', [CheckoutController::class, 'placeOrder']);
$router->get('/checkout/success', [CheckoutController::class, 'success']);

$router->get('/payments/sepay/start', [PaymentController::class, 'start']);
$router->get('/payments/sepay/success', [PaymentController::class, 'success']);
$router->get('/payments/sepay/error', [PaymentController::class, 'error']);
$router->get('/payments/sepay/cancel', [PaymentController::class, 'cancel']);
$router->post('/payments/sepay/ipn', [PaymentController::class, 'ipn']);

$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/{orderId}', [OrderController::class, 'show']);
$router->post('/orders/{orderId}/cancel', [OrderController::class, 'cancel']);

$router->get('/admin/orders', [OrderManagementController::class, 'index']);
$router->get('/admin/orders/{orderId}', [OrderManagementController::class, 'show']);
$router->post('/admin/orders/{orderId}/status', [OrderManagementController::class, 'updateStatus']);
$router->post('/admin/orders/{orderId}/return', [OrderManagementController::class, 'confirmReturn']);

$router->get('/about', [AboutController::class, 'index']);
