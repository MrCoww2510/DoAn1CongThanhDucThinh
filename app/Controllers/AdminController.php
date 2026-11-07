<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\Product;

final class AdminController extends Controller
{
    public function index(): void
    {
        $this->authorize();
        $pdo = \App\Core\Database::connection();
        $this->view('admin/dashboard', [
            'pageTitle' => 'Quản trị hệ thống',
            'productCount' => (int) $pdo->query('SELECT COUNT(*) FROM SanPham')->fetchColumn(),
            'categoryCount' => (int) $pdo->query('SELECT COUNT(*) FROM DanhMuc')->fetchColumn(),
            'userCount' => (int) $pdo->query('SELECT COUNT(*) FROM TaiKhoan')->fetchColumn(),
            'categories' => (new Product())->categoriesForManagement(),
        ], 'customer');
    }

    public static function authorize(): void
    {
        if (($_SESSION['user']['VaiTro'] ?? '') !== 'QuanTriVien') {
            http_response_code(isset($_SESSION['user']) ? 403 : 401);
            if (!isset($_SESSION['user'])) {
                $app = require dirname(__DIR__, 2).'/config/app.php';
                header('Location: '.rtrim((string) ($app['base_url'] ?? ''), '/').'/login');
                exit;
            }
            exit('Bạn không có quyền truy cập chức năng này.');
        }
        $id = (string) ($_SESSION['user']['MaTK'] ?? '');
        $account = $id !== '' ? (new Account())->findForManagement($id) : null;
        if (($account['VaiTro'] ?? '') !== 'QuanTriVien' || ($account['TrangThai'] ?? '') !== 'HoatDong') {
            unset($_SESSION['user']);
            $app = require dirname(__DIR__, 2).'/config/app.php';
            header('Location: '.rtrim((string) ($app['base_url'] ?? ''), '/').'/login');
            exit;
        }
    }
}
