<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

final class ProductController extends Controller
{
    public function index(): void
    {
        $model = new Product();

        $keyword = trim((string) ($_GET['q'] ?? ''));
        $categoryId = trim((string) ($_GET['category'] ?? ''));

        $this->view('products/index', [
            'pageTitle' => 'Sản phẩm',
            'products' => $model->all($keyword, $categoryId),
            'categories' => $model->categories(),
            'keyword' => $keyword,
            'categoryId' => $categoryId,
        ]);
    }

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

    private function notFound(): void
    {
        http_response_code(404);

        $this->view('products/not-found', [
            'pageTitle' => 'Không tìm thấy sản phẩm',
        ]);
    }
}