# Module catalog và sản phẩm

Ngày 19-10-2026. Source: `ProductController.php`, `Product.php`, product views, `HomeController.php`; schema SanPham/DanhMuc/ChiTietSanPham.

`HomeController::index()` yêu cầu Product model lấy danh sách featured rồi truyền vào home view. `ProductController::index()` nhận filter/sort/page từ GET, chuẩn hóa và gọi `Product::all`; truyền categories, products và trạng thái lọc sang view. `show()` đọc định danh sản phẩm; thiếu/sai hoặc không tìm thấy đi `notFound()`, còn lại render detail.

`Product::featured(limit)` dùng base query và giới hạn kết quả. `all(...)` tạo filter, sort được chọn trong code, LIMIT/OFFSET và truy vấn danh sách. `categories()` lấy danh mục sắp xếp. `findById(id)` truy vấn một sản phẩm. `baseQuery()` SELECT nối SanPham–DanhMuc và LEFT JOIN ChiTietSanPham để tạo dữ liệu card/detail.

Prepared values giảm injection; sort identifiers phải allowlist. Không đủ bằng chứng để khẳng định mọi edge case pagination/input đều an toàn. Đánh đổi: join cung cấp view model đủ dùng nhưng kéo dữ liệu detail vào list; xem query plan/index trên dữ liệu thực trước tối ưu. Template `_card.php`, `index.php`, `show.php` là HTML phía server; kiểm output encoding theo context.
