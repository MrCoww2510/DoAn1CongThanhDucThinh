<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

final class ProductController extends Controller
{
    // Hiển thị danh sách sản phẩm theo từ khóa, danh mục và sắp xếp.
    public function index(): void
    {
        $model = new Product();

        // Lấy dữ liệu từ URL
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $categoryId = trim((string) ($_GET['category'] ?? ''));
        $sort = trim((string) ($_GET['sort'] ?? 'default'));

        // Danh sách kiểu sắp xếp được phép
        $allowedSorts = [
            'default',
            'name_asc',
            'name_desc',
            'price_asc',
            'price_desc',
        ];

        // Kiểm tra kiểu sắp xếp
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'default';
        }

        // Lấy dữ liệu sản phẩm và danh mục
        $products = $model->all(
            $keyword,
            $categoryId,
            $sort
        );

        $categories = $model->categories();

        // Truyền dữ liệu sang View
        $this->view('products/index', [
            'pageTitle' => 'Sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'keyword' => $keyword,
            'categoryId' => $categoryId,
            'sort' => $sort,
        ]);
    }

    // Hiển thị thông tin chi tiết của sản phẩm được chọn.
    public function show(): void
    {
        $id = trim((string) ($_GET['id'] ?? ''));

        if ($id === '') {
            $this->notFound();

            return;
        }

        $product = (new Product())->findById($id);

        if ($product === null) {
            $this->notFound();

            return;
        }

        $this->view('products/show', [
            'pageTitle' => $product['TenSP'],
            'product' => $product,
        ]);
    }

    // Trả về trang không tìm thấy sản phẩm.
    private function notFound(): void
    {
        http_response_code(404);

        $this->view('products/not-found', [
            'pageTitle' => 'Không tìm thấy sản phẩm',
        ]);
    }
}
