# Phân tích bảo mật tĩnh

Đây là tổng hợp các nhận xét trong tài liệu nguồn, không phải pentest hay xác nhận khai thác.

Ma trận từng view có tại [view output encoding reference](view_output_encoding_reference.md).`n`n## Kiểm soát được báo cáo có mặt

- Password được lưu bằng `password_hash` và kiểm bằng `password_verify`.
- Session ID được rotate sau login thành công; CSRF token ngẫu nhiên và so sánh `hash_equals` được dùng trong các POST mutation đã rà.
- Vai trò/ownership được kiểm ở controller/model; admin controller đọc lại quyền/trạng thái tài khoản từ DB.
- PDO prepared statements dùng cho values; sort identifiers có allowlist.
- Order/create/cancel và một số account/cart mutation dùng transaction/row locks.
- Các view được rà có `htmlspecialchars`/encoder hoặc cast/format số; layout echo `$content` thô nên tin cậy vào encoding của view con.

## Điểm cần rà tiếp

| Ưu tiên theo nguồn | Vấn đề | Giới hạn / hành động xác minh |
|---|---|---|
| Cao nếu debug bật ngoài local | `debug => true` và `display_errors` theo config hiện tại được báo cáo | Xác minh config deploy/ini; không kết luận đã lộ lỗi production |
| Cao về vận hành dữ liệu | Schema init có DROP | Xác minh quy trình triển khai, không dùng như migration trên DB có dữ liệu |
| Trung bình | Role/CSRF logic lặp trong controller | Duy trì route-method-auth-CSRF matrix; chưa có route cụ thể thiếu kiểm soát được chứng minh |
| Trung bình | Email uniqueness precheck nhưng không UNIQUE DB | Cân nhắc constraint và xử lý duplicate race |
| Trung bình | Dynamic image URL được HTML-escape, nhưng không có allowlist scheme/host được ghi nhận | Xác định nguồn dữ liệu và URL policy; HTML escaping không thay thế policy URL |
| Thấp/chưa xác minh | Cookie flags, timeout, HTTPS, headers, session revoke | Kiểm cấu hình runtime |
| Chưa xác minh | Toàn bộ output sinks/upload/DocumentRoot | Review source/runtime riêng |

## Rủi ro tính nhất quán liên quan bảo mật

- `Cart::updateItem` được mô tả đọc tồn rồi cập nhật không transaction/lock. Checkout kiểm tra tồn lần nữa, nhưng trạng thái giỏ trung gian có thể vượt tồn.
- `Cart::clear` gồm DELETE và UPDATE tổng không atomic.
- `getItems` tạo giỏ/cập nhật tổng trong GET.
- Float/currency formatting 0 chữ số có thể gây sai lệch nghiệp vụ; schema được mô tả DECIMAL(18,2).
- Session customer sau khi tài khoản bị vô hiệu hóa chưa được xác định có bị revoke tức thời.

Mức độ trong báo cáo gốc là ưu tiên xem xét, không phải mức độ exploit đã xác minh. Không có credential, token hay dữ liệu fixture trong bản tổng hợp.

