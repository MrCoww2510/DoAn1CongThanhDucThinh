# Trạng thái tiếp tục và coverage phiên bản 2

Ngày 19-10-2026. V2 bổ sung đối chiếu route, bootstrap/config, Product query, Order/Checkout và sửa nhận định v1. Dự án gốc vẫn được đọc ở chế độ chỉ đọc.

## Đã xác minh thêm

- 27 khai báo route: 15 GET và 12 POST (một số URL có cả hai method); route matrix đầy đủ tại `01_ROUTE_AUTH_MATRIX.md`.
- 11 bảng trong `database.sql`.
- Entry bootstrap: BASE_PATH, timezone, debug/error policy, autoloader App→app, session, exception handler, base path hardcoded, dispatch.
- Router dynamic path, query/path normalization, first-match and 404 behavior.
- Product::all không phân trang; sort allowlist; featured limit clamp.
- Order COD/cancel transaction branches và owner/status checks.
- `config/app.php` có debug=true ở file hiện tại; mức độ ảnh hưởng production phụ thuộc config đang được deploy.

## Chưa hoàn thành

1. Rà line-by-line toàn bộ 38 PHP, đặc biệt templates và nhánh từng controller/model.
2. Đặc tả mọi biến cục bộ/property cùng trạng thái null/error cho từng method.
3. Đếm danh mục SQL theo template query và gắn vị trí chính xác cho từng câu (đếm call sites không tương đương câu SQL duy nhất).
4. Đọc đủ 3 SQL, bao gồm fixture `dulieuao.sql`, phân biệt dữ liệu mẫu và schema.
5. Rà đủ 12 CSS, 15 TEX và tài liệu phụ trợ; kiểm mọi output escaping.
6. Xác minh config/session/Apache/DB runtime không thực hiện trong phạm vi phân tích tĩnh.

## Bước kế tiếp

Tiếp tục theo cụm file, mỗi lần đọc có giới hạn output: Account model; Cart model/controller; auth/account controllers; từng view group. Với mỗi cụm, ghi chữ ký, input/output, nhánh, query, side effects, null/error cases và line references. Không chạy SQL/scripts. Nếu phát hiện destination hiện tại tồn tại, tạo version directory khác thay vì overwrite.
