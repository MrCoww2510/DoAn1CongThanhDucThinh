<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

final class HomeController extends Controller
{
    // Lấy sản phẩm nổi bật và hiển thị trang chủ.
    public function index(): void
    {
        $model = new Product();

        $this->view('home/index', [
            'pageTitle' => 'Trang chủ',
            'featuredProducts' => $model->featured(8),
            'categories' => $model->categories(),
        ]);
    }
}
