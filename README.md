# T&T Computer — MVC starter

Đây là bộ khung khởi tạo, chưa phải website hoàn chỉnh. Chưa có module sản phẩm, tài khoản, giỏ hàng, đơn hàng hoặc quản trị.

## Yêu cầu
- XAMPP có Apache, MySQL và PHP 8+
- Database `TTComputer` đã được tạo/import từ file `database.sql` đã chốt

## Cài đặt
1. Giải nén thư mục dự án vào `C:\xampp\htdocs\DoAn1CongThanhDucThinh`.
2. Sao chép các file trong bộ khung vào đúng thư mục dự án, giữ nguyên các file SQL/tài liệu hiện có. Không ghi đè file dự án khác nếu đã tồn tại.
3. Mở `config/database.php`, kiểm tra host, port, database, username và password.
4. Bật Apache và MySQL trong XAMPP.
5. Truy cập `http://localhost/DoAn1CongThanhDucThinh/public/`.

## Ghi chú đường dẫn
Bộ khung có `.htaccess` để điều hướng URL trong `public/`. Nếu dùng URL mặc định ở trên, Apache cần cho phép `mod_rewrite` và `AllowOverride All`.
CSS mặc định dùng đường dẫn `/css/main.css`, phù hợp khi `public/` là DocumentRoot hoặc chạy tại root của host. Khi chạy qua URL con `/DoAn1CongThanhDucThinh/public/`, hãy cấu hình VirtualHost để `public/` làm DocumentRoot (khuyến nghị) hoặc điều chỉnh `base_url` và đường dẫn asset trước khi kiểm thử.

## Kiểm thử nền tảng
- Trang chủ: `/`
- Route chưa khai báo: hiển thị 404
- Kết nối PDO hiện được khởi tạo theo nhu cầu; trang chủ chưa truy vấn database nên trang chủ có thể mở ngay cả khi database chưa kết nối được. Cần kiểm thử kết nối PDO riêng ở bước tiếp theo.
- `debug=true` chỉ dành cho local development; tắt khi triển khai thật.

## An toàn
Không đặt thông tin kết nối trong `public/`. Không dùng route GET cho thao tác ghi dữ liệu. Chưa có CSRF/auth middleware trong bộ khung; phải triển khai trước khi đưa các chức năng ghi dữ liệu và tài khoản vào sử dụng.
