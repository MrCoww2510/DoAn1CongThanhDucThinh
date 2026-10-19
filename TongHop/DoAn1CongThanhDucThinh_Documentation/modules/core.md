# Module Core và routing

Ngày 19-10-2026. Source: `public/index.php`, `routes/web.php`, `app/Core/{Controller,Database,Router,View}.php`, `.htaccess` files. Phân tích tĩnh.

- `public/index.php`: front controller; khởiởi tạo BASE_PATH/session, nạp route và dispatch. Input là HTTP server vars; output response. Tác dụng phụ: session/header.
- `Router::get/post/add`: route method/path/handler được thêm vào danh sách. `dispatch`: chuẩn hóa request path, tìm route, lấy biến động, gọi callable/class method; method mismatch/không thấy route trả lỗi HTTP.
- `Controller::view`: gọi `View::render`; `redirect`: tạo Location dựa config và kết thúc luồng (`never`).
- `View::render`: chuẩn bị data cho template và include view/layout; template chạy trong PHP scope, vì vậy escape output là trách nhiệm template.
- `Database::connection`: tạo/tái sử dụng PDO qua static connection; constructor private. Pattern singleton có bằng chứng. Ưu: một điểm cấu hình/tái sử dụng; nhược: trạng thái global khó test/mock.

`public/.htaccess` ưu tiên phục vụ file thật rồi rewrite sang entry point. Root `.htaccess` chặn thư mục nội bộ. Cần cấu hình Apache cho phép rewrite; chưa kiểm chứng runtime. Router/Controller/View tự viết làm app nhẹ nhưng phải tự duy trì lỗi method/path, encoding URL, lỗi và dependency.
