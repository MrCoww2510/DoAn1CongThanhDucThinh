# Vấn đề chưa xác minh và mâu thuẫn

## Giới hạn bằng chứng trọng yếu

Đầu vào có đúng 69 file tài liệu (`.md`/`.mmd`) trong bốn thư mục documentation. PHP, SQL, CSS, JS, config, Apache files và database không nằm trong đầu vào. Do đó tài liệu tổng hợp không xác minh code/runtime; mọi kỹ thuật được quy về nhận định nguồn.

## Mâu thuẫn và cách xử lý

| Chủ đề | Nhận định không khớp | Xử lý trong bộ này | Còn cần xác minh |
|---|---|---|---|
| Catalog pagination | Tài liệu v1 `modules/products.md`, `03_MODULE_SPECIFICATIONS.md`, `06_DATABASE_SPECIFICATION.md`, `08_THEORY_AND_DESIGN_PRINCIPLES.md`, `10_PERFORMANCE_AND_MAINTAINABILITY.md` có mô tả page/LIMIT/OFFSET. V2 `00_CORRECTIONS_AND_FINDINGS.md`, `02_QUERY_AND_DATA_FLOWS.md`, v4 `modules/catalog.md` nói `Product::all()` không có phân trang | Theo correction v2 + đặc tả method v4: catalog listing không phân trang; giữ nội dung cũ trong inventory/dedup như nhận định lỗi thời | Đọc `Product.php`, `ProductController.php` tại snapshot phù hợp |
| Route count | V2 `04_CONTINUATION_STATUS.md` ghi 15 GET/12 POST. `05_ROUTE_COUNT_CORRIGENDUM.md` thay bằng 13 GET/14 POST. Ma trận route và v4 `22/24` nhất quán số sau | Dùng 27 total, 13 GET, 14 POST; correction nêu cách đếm | Đối chiếu `routes/web.php` thực tế |
| Method totals | V3 `05_FINDINGS_AND_STATUS.md` ghi 41; `06_COVERAGE_CORRIGENDUM.md` chốt 40 cho cụm auth/cart/account. V4 `04_STATUS.md` 19 bị `11_STATUS_CORRIGENDUM.md` sửa thành 20; v4 final báo 89 method toàn dự án | Không cộng các subtotal mâu thuẫn thành số mới; chỉ nêu số v4 final với tư cách báo cáo nguồn | Đếm lại source theo định nghĩa thống nhất |
| Session regeneration | V4 `12_REQUIREMENTS_TRACEABILITY.md` ban đầu yêu cầu xác nhận; `16_PROGRESS_CORRECTIONS.md` xác nhận `session_regenerate_id(true)` sau login | Dùng correction v4 làm kết luận tài liệu; runtime cookie vẫn chưa biết | Xem source/config runtime nếu cần |
| Coverage SQL/LaTeX/TXT | V4 `10_ANALYSIS_COVERAGE.md` còn ghi LaTeX/rule chưa đọc đầy đủ; `15_DOCUMENTATION_CROSSCHECK.md`, `16_PROGRESS_CORRECTIONS.md`, `22_FINAL_COVERAGE_CORRECTION.md`, `24_FINAL_STATUS.md` cập nhật đã đọc đủ 15 TEX và 4 TXT | Dùng final/correction mới hơn; lưu trạng thái cũ ở inventory | Mã/dữ liệu thực không có để kiểm lại kết quả đọc trước |
| Sơ đồ ER | Sơ đồ gốc và v4 khác cardinality/chi tiết; v4 `19_DIAGRAM_CARDINALITY_NOTES.md` giải thích constraint vật lý vs business rule; `18_DIAGRAM_INDEX_CORRIGENDUM.md` sửa vị trí sơ đồ | Sơ đồ tổng hợp giản lược; không xem cardinality tối thiểu là constraint | Đối chiếu toàn bộ FK/UNIQUE với `database.sql` |
| Encoding và path case | Terminal mojibake có thể do console. `AboutController` dùng `Views` còn thư mục được báo cáo là `views`, rủi ro trên Linux | Không kết luận file encoding hỏng hoặc chắc chắn lỗi deploy | Kiểm bytes, filesystem và triển khai mục tiêu |
| Schema/migration | `database.sql` được nói đã có `DiaChiNhanHang`; migration lại ADD cột này | Nêu nguy cơ áp dụng lặp/trùng; không chọn trạng thái DB | Kiểm migration ledger/schema runtime |
| Ràng buộc trạng thái order | V4 requirements traceability ghi nhận trạng thái “Giao hàng thất bại” có thể không nằm trong CHECK | Giữ là điểm cần đối chiếu, chưa xác nhận lỗi | So nguyên văn enum/check/transition với schema/model |

## Vấn đề kỹ thuật chưa xác minh

- `debug => true`, hardcoded base path và config database: trạng thái triển khai không rõ.
- Apache mod_rewrite/AllowOverride/DocumentRoot; PHP/MySQL version, CHECK behavior, cookie flags, HTTPS, timeout/logging.
- Customer session có bị thu hồi ngay khi account bị disable hay không.
- Output encoding review tĩnh có bao phủ chính xác snapshot nào; URL ảnh được escape nhưng policy scheme/host chưa xác định.
- `AboutController` path capitalization và stylesheet load per route.
- Email duplicate race, cart update stock race, cart clear non-atomic, concurrency/deadlock trong order.
- Rounding/tiền: DECIMAL(18,2) vs float/format 0 decimals.
- Không có benchmark/EXPLAIN/browser/accessibility runtime.

## Cần người bảo trì xác nhận

1. Source snapshot/branch và commit tương ứng với các báo cáo v1–v4.
2. DB hiện tại đã áp dụng schema/migration nào; policy backup/seed fixture.
3. Catalog có thực sự cần pagination và filter giá/brand theo yêu cầu.
4. Quy tắc trạng thái order và thanh toán được chấp nhận.
5. Môi trường deploy dự kiến và cấu hình an toàn (debug, base URL, session, HTTPS).

Không có dữ liệu đủ để tự giải quyết các điểm này.
