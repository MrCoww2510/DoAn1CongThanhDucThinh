# 01 — Kiến trúc hệ thống

Ngày 19-10-2026; phân tích tĩnh, chưa xác minh Apache/PHP runtime. Sơ đồ: [kiến trúc](diagrams/system_architecture.mmd), [request](diagrams/request_lifecycle.mmd), [module](diagrams/module_dependencies.mmd).

## Request/response

Apache phục vụ file thật; `public/.htaccess` rewrite đường dẫn còn lại tới `index.php` (dòng 4–13). `.htaccess` gốc tắt directory listing và chặn `app|config|routes|storage`. Tác dụng phụ thuộc mod_rewrite/AllowOverride chưa kiểm tra.

`public/index.php` khởi tạo hằng đường dẫn/session và nạp router/routes. `routes/web.php` khai báo GET/POST. `Router::dispatch` so method/path và gọi handler. Controller kiểm tra input/session/quyền, gọi model, rồi render hoặc redirect. Model gọi PDO qua `Database::connection()`. `View` include template PHP; layout phát HTML/CSS/JS.

Đây là MVC phân lớp mức ứng dụng, không phải framework đầy đủ. Controller khởi tạo concrete model trực tiếp; không thấy DI container. Renderer dùng include/output buffering, không thấy template engine ngoài.

## Session, bảo mật, lỗi

`AuthController` lưu user trong session, regenerate ID lúc login, tạo CSRF token. Một số controller kiểm vai trò và token. Không có middleware riêng được xác nhận. POST thường redirect/flash hoặc render lỗi; Router xử lý 404/405. Cấu hình hiển thị lỗi, cookie flags và log deployment chưa xác minh.

## Phụ thuộc

Controller → Model → Database/PDO → MySQL; Controller → View → template; template → CSS/JS. Quan hệ bảng tại [database spec](06_DATABASE_SPECIFICATION.md).
