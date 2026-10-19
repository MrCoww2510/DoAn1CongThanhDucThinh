# 08 — Lý thuyết và nguyên tắc

Ngày 19-10-2026; mức áp dụng dựa trên source tĩnh.

| Chủ đề | Mức | Bằng chứng/đánh đổi |
|---|---|---|
| MVC/separation | Một phần | Router/controller/model/view tách lớp; controller khởi tạo model trực tiếp |
| OOP/đóng gói | Một phần | class, namespace, visibility, kế thừa base controller; ít abstraction |
| SOLID | Một phần | SRP tương đối; DIP yếu, các nguyên tắc khác chưa đủ bằng chứng |
| DRY | Một phần | layout/base controller chung; CSRF/role logic lặp |
| Prepared statements | Có | prepare/execute tách value khỏi SQL; identifiers cần allowlist |
| Authentication | Có | password_hash/verify, trạng thái account, regenerate session |
| Authorization | Một phần | role checks trong controller; cần ma trận route coverage |
| Session/CSRF | Một phần | user/token session, hash_equals; cookie policy chưa xác minh |
| Output encoding | Chưa audit toàn diện | một số view có escape helper; cần kiểm sink theo context |
| Validation | Một phần | server side auth/profile/admin và constraint DB |
| Quan hệ/CRUD | Có | PK/FK/unique/check và truy vấn CRUD |
| Transaction/ACID | Có trong luồng chính | InnoDB, begin/commit/rollback, row locks; chưa test concurrency |
| HTTP/REST | HTTP form routing; REST API chưa thấy | GET/POST, không thấy JSON API |
| PRG | Một phần | redirect sau mutation; lỗi đôi khi render trực tiếp |
| DI/DIP | Chưa thấy rõ | concrete model/static PDO gây khó mock |
| Pagination/search | Có | catalog và user search |
| AJAX/fetch | Chưa thấy | JS chỉ thao tác DOM |

Prepared statement là tách SQL khỏi value; transaction là commit/rollback đơn vị; CSRF token ràng POST với session; PRG tránh gửi lại form khi refresh. “Có” không thay thế kiểm thử runtime.
