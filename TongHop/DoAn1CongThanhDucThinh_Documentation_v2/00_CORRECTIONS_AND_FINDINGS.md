# Đính chính và phát hiện có bằng chứng

Ngày 19-10-2026. Nguồn chính: `public/index.php`, `routes/web.php`, `config/app.php`, `config/database.php`, `app/Core/*`, `app/Models/Product.php`.

## Đính chính tài liệu v1

- Danh sách route có **27 khai báo** trong `routes/web.php` (dòng 18–57), không phải 27 đường dẫn duy nhất: cùng `/register`, `/login`, `/account`, `/checkout` có GET/POST.
- `Product::all()` hiện **không phân trang**: chữ ký chỉ nhận keyword/category/sort (Product.php dòng 29–33), không có LIMIT/OFFSET ở query; chỉ `featured()` dùng LIMIT. Các tài liệu v1 nói catalog pagination là không chính xác.
- `Product::all()` dùng sort allowlist, bind keyword/category; tốt cho SQL injection. `featured()` nối limit sau khi clamp integer 1–24, không phải input string SQL thô.
- 60 lời gọi `prepare()`/`query()` là số lời gọi trong model, không phải 60 câu SQL duy nhất; nhiều statement nằm trong loop. Cần phân biệt số lần gọi với template query.
- Số PHP toàn dự án ngoài `.git` là 38. Trong app/public/routes trực tiếp đếm được ít hơn vì hai PHP khác nằm ngoài các nhánh đó.

## Cấu hình có ý nghĩa vận hành

- `public/index.php` dòng 12–18 bật `display_errors=1` khi `debug === true`; hiện `config/app.php` khai báo `debug => true`. Rủi ro **High nếu cấu hình này được dùng môi trường có người dùng thật**; xác thực scope triển khai trước khi kết luận có lộ lỗi production. Dòng 42–45 còn escape thông điệp exception trước khi echo.
- `public/index.php` dòng 58 hardcode base path `/DoAn1CongThanhDucThinh/public`; `Router::dispatch` còn xử lý base_url từ config. Cấu hình base URL phải đồng bộ với cách deploy; behavior trên VirtualHost/subdirectory chưa runtime-test.
- `config/database.php` đang chứa host/database/user mặc định và password rỗng. Giá trị không nhạy cảm cụ thể không cần lặp; đây là cấu hình local XAMPP theo comment. Nếu được dùng ngoài local, credential/least privilege cần rà soát. Không đưa secret vào tài liệu.
- Autoloader PSR-4-like tự viết (`public/index.php` dòng 20–32): ánh xạ prefix `App\\` sang `app/`, không dùng Composer.

## Rủi ro schema

`database.sql` đầu file tắt foreign_key_checks, DROP 11 bảng, bật lại rồi CREATE. Đây là script khởi tạo phá hủy nếu chạy trên DB đang có dữ liệu; tuyệt đối không chạy như migration cập nhật. Cột `NguoiDung.DiaChiNhanHang` tồn tại trong schema chính và migration có lệnh ADD COLUMN cùng tên; áp dụng migration trên schema đã có cột có thể lỗi. Cần xác định version/state trước mọi thao tác DB.

## Độ chắc chắn

Các kết luận trên được xác nhận bằng source tĩnh. Chưa biết config nào đang được web server nạp, DB thực tế, Apache DocumentRoot, PHP ini hoặc trạng thái production.
