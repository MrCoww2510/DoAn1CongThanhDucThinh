# 12 — Thuật ngữ

- Front controller: entry point nhận URL động và chuyển cho router.
- Router: ánh xạ HTTP method/path tới handler.
- Controller: xử lý request và điều phối use case.
- Model: truy cập dữ liệu/nghiệp vụ persistence.
- View: template dựng giao diện.
- PDO: API truy cập DB của PHP.
- Prepared statement: SQL chuẩn bị tách khỏi dữ liệu đầu vào.
- CSRF: giả mạo yêu cầu liên trang; token ràng request với session.
- Authentication: xác minh danh tính; authorization: quyền được làm gì.
- Transaction: nhóm thao tác commit hoặc rollback cùng nhau.
- `FOR UPDATE`: khóa bản ghi trong transaction.
- Foreign key: ràng buộc tham chiếu giữa bảng.
- PRG: POST-Redirect-GET.
- XSS: script độc hại được trình duyệt chạy do output không an toàn.
- InnoDB: engine MySQL hỗ trợ transaction và row locks.
