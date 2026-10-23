# Đếm call site và mẫu SQL

Ngày phân tích: 2026-10-19. Đây là đính chính cách đếm cho [SQL callsite ledger](13_SQL_CALLSITE_LEDGER.md); không sửa file ledger.

## Kết quả tĩnh

| Thước đo | Số | Cách hiểu |
|---|---:|---|
| PDO `prepare()`/`query()` call site trong PHP | 60 | Mỗi điểm gọi tại source có ID riêng trong ledger. |
| Call site có chuỗi SQL trực tiếp trong lời gọi | 54 | SQL được truyền bằng string literal, có thể ghép nhiều chuỗi literal. |
| Mẫu literal chuẩn hóa | 49 | Ghép fragments, chuẩn hóa whitespace, so sánh không phân biệt hoa/thường; các mẫu trùng nhau chỉ tính một. |
| Call site dùng biến `$sql` | 6 | Product: 4; Account: 2. Phần query được dựng trước lời gọi PDO. |

Do đó có **55 đơn vị định nghĩa query ở source** theo quy ước “mỗi literal template khác nhau + mỗi biến query được truyền vào PDO”; không đồng nhất với số câu SQL runtime duy nhất.

## Giới hạn phương pháp

- Dò tĩnh trên 4 model bằng vị trí `prepare()`/`query()`; không chạy PHP, không log PDO runtime.
- 49 mẫu được deduplicate bằng whitespace normalization và case-insensitive compare; đây là quy ước so sánh cho tài liệu, không phải AST parse SQL.
- Sáu `$sql` gồm query builder có nhánh lọc/sort hoặc ghép base query. Một template có thể sinh nhiều SQL string cuối cùng tùy input; số biến thể runtime chính xác phụ thuộc các nhánh/giá trị hợp lệ nên không gán một số cố định.
- SQL DDL trong `database.sql`, fixture DML trong `dulieuao.sql`, và migration `ALTER TABLE` được thống kê riêng, không thuộc 60 call site ứng dụng.
- SQL literals chỉ chứa câu lệnh/placeholder; fixture rows và thông tin đăng nhập không được chép vào tài liệu.
