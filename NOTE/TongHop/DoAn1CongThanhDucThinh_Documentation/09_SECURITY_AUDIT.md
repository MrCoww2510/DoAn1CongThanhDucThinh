# 09 — Kiểm toán bảo mật tĩnh

Ngày 19-10-2026. Không penetration test, không HTTP runtime/DB. Mức độ là ưu tiên xem xét, không phải tuyên bố khai thác.

| Mức | Nhận xét/bằng chứng | Hướng xử lý |
|---|---|---|
| Medium | CSRF/role check được đặt trong controller và có lặp ở Account/Cart/Order/Admin. | Đối chiếu từng route-method với yêu cầu auth/CSRF để tránh bỏ sót khi thêm action. Chưa kết luận route cụ thể thiếu. |
| Medium | Schema init `database.sql` chứa DROP TABLE IF EXISTS dòng 16–26. | Có thể phá dữ liệu nếu chạy nhầm; chỉ sử dụng theo quy trình cài mới có backup, không chạy trong audit. |
| Low | Cookie flags/session timeout chưa xác định từ source đã khảo sát. | Xác minh PHP/XAMPP/HTTPS config (`Secure`, `HttpOnly`, `SameSite`, lifetime). |
| Chưa xác minh | Escape mọi template, upload, security headers, deployment DocumentRoot. | Audit runtime/config và từng output sink trước kết luận. |
| Tích cực | Prepared SQL, password_hash/verify, session_regenerate_id, hash_equals có bằng chứng. | Kiểm tra coverage và cấu hình thực tế. |

Vai trò lưu trong TaiKhoan và được đọc từ session; cần xác minh session cũ sau khi account bị vô hiệu hóa có bị thu hồi hay không. Prepared statements bảo vệ value, không tự bảo vệ identifier. Unique precheck có race; constraint/exception vẫn quan trọng. Không ghi credential vào tài liệu.
