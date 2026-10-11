<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

final class HomeController extends Controller
{
    public function index(): void
    {
        $model = new Product();

        $this->view('home/index', [
            'pageTitle' => 'Trang chủ',
            'products' => $model->featured(8),
        ]);
    }
}
