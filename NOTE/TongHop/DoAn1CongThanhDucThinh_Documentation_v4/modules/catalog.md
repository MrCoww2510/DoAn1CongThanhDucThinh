# Catalog và Product

Ngày 19-10-2026. Đọc toàn văn `ProductController.php` (90 dòng), `Product.php` (135), `HomeController.php` (23), `AboutController.php` (21). Schema tables: SanPham, DanhMuc, ChiTietSanPham.

## ProductController

`index():void`: tạo Product; đọc GET `q/category/sort`, cast string + trim; allowlist 5 sort values; invalid fallback default; gọi `all(keyword,category,sort)` và `categories`; render products/index với title/data/filter state. Không có page/per-page input.
`show():void`: đọc GET id; rỗng → notFound + return; gọi findById; null → notFound + return; render detail. `notFound():void` set 404 và view products/not-found.
Variables `$model`, `$keyword`, `$categoryId`, `$sort`, `$allowedSorts`, `$products`, `$categories`, `$id`, `$product`. ID invalid syntax vẫn query prepared, không có regex format check. DB exception lên global exception handler.

## Product model

- `featured(int $limit=8):array`: clamp 1..24; append numeric limit vào SQL static; query/fetchAll. Không phải SQL injection vector theo code vì integer typed/clamped. Sắp theo MaSP DESC; “featured” không dùng cột đánh dấu nổi bật.
- `all(string $keyword='',string $categoryId='',string $sort='default'):array`: base select; optional keyword `TenSP LIKE :keyword` dùng `%...%`, optional category `MaDM=:category`, conditions AND; sort allowlist/fallback; prepare execute; fetchAll. Không LIMIT/OFFSET, không COUNT/pages. Wildcard `%`/`_` trong keyword giữ ý nghĩa LIKE; kết quả có thể rộng hơn chuỗi literal.
- `categories():array`: SELECT MaDM/TenDM ORDER BY TenDM.
- `findById(string $id):?array`: base select WHERE MaSP LIMIT1, fetch false→null.
- `baseQuery():string`: chọn product id/category/name/price/stock + category name + detail fields; INNER JOIN category (sản phẩm orphan không hiện), LEFT JOIN details (thiếu detail vẫn hiện).

No mutable properties. PDO fetch rows associative. Query read-only. Performance: list all matching results không phân trang; keyword contains pattern thường hạn chế index prefix; cần search strategy/benchmark khi catalog lớn. Price schema DECIMAL; view casts float và format 0 decimals.

## Home/About

`HomeController::index()` tạo Product; view data featured 8 + categories. `AboutController::index()` khác kiến trúc: không kế thừa Controller, trực tiếp load app config/baseUrl, output-buffer include pages/about, rồi include customer layout. Cả hai public GET. About dùng chữ hoa `Views` trong path include; Windows XAMPP case-insensitive, deploy Linux case-sensitive có thể gặp lỗi do thư mục thực tế `app/views` chữ thường; xác minh deploy platform trước khi kết luận.
