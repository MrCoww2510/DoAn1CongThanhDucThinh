<?php

// Controller hiển thị danh sách và chi tiết sản phẩm.

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
        $minPrice = trim((string) ($_GET['min_price'] ?? ''));
        $maxPrice = trim((string) ($_GET['max_price'] ?? ''));
        $filters = $model->catalogFilterOptions();
        $brand = trim((string) ($_GET['brand'] ?? ''));
        $type = trim((string) ($_GET['type'] ?? ''));

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
        if ($minPrice !== '' && (!is_numeric($minPrice) || (float) $minPrice < 0 || (float) $minPrice > 9999999999999999)) {
            $minPrice = '';
        }
        if ($maxPrice !== '' && (!is_numeric($maxPrice) || (float) $maxPrice < 0 || (float) $maxPrice > 9999999999999999)) {
            $maxPrice = '';
        }
        if ($minPrice !== '' && $maxPrice !== '' && (float) $minPrice > (float) $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }
        if (!in_array($brand, $filters['brands'], true)) {
            $brand = '';
        }
        if (!in_array($type, $filters['types'], true)) {
            $type = '';
        }
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

        // Lấy dữ liệu sản phẩm và danh mục
        $result = $model->searchCatalog([
            'keyword' => mb_substr($keyword, 0, 100), 'category' => $categoryId,
            'sort' => $sort, 'minPrice' => $minPrice, 'maxPrice' => $maxPrice,
            'brand' => $brand, 'type' => $type,
        ], $page);

        $categories = $model->categories();

        // Truyền dữ liệu sang View
        $this->view('products/index', [
            'pageTitle' => 'Sản phẩm',
            'products' => $result['items'],
            'totalProducts' => $result['total'],
            'currentPage' => $result['page'],
            'totalPages' => $result['totalPages'],
            'categories' => $categories,
            'keyword' => $keyword,
            'categoryId' => $categoryId,
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'brand' => $brand,
            'type' => $type,
            'brands' => $filters['brands'],
            'types' => $filters['types'],
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
