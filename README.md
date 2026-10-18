# T&T Computer — MVC starter

Đây là ứng dụng MVC bán máy tính đang được phát triển; hiện có các luồng sản phẩm, tài khoản, giỏ hàng và đặt hàng COD cơ bản.

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

## Đặt hàng và thanh toán
- Khách hàng đăng nhập, thêm sản phẩm vào giỏ và chọn **Tiến hành thanh toán**.
- Hiện hỗ trợ thanh toán khi nhận hàng (COD). Trạng thái thanh toán ban đầu là **Chưa thanh toán**.
- Khách hàng có thể theo dõi danh sách, chi tiết và trạng thái đơn hàng của chính mình; chỉ đơn **Chờ xác nhận** mới được hủy. Hủy đơn hoàn trả tồn kho và ghi lịch sử kho trong cùng một transaction.
- Thanh toán trực tuyến chưa khả dụng vì chưa tích hợp cổng thanh toán; ứng dụng không giả lập giao dịch trực tuyến thành công.
- Khi tạo đơn, ứng dụng kiểm tra lại tồn kho, lưu giá tại thời điểm đặt hàng, giữ chỗ tồn kho và ghi lịch sử kho trong cùng một transaction.
- Phí giao hàng hiện chưa được tính trong hệ thống.

## Quản lý tài khoản
- Khách hàng có thể cập nhật họ tên, email, số điện thoại, địa chỉ nhận hàng và đổi mật khẩu tại `/account`.

- Khi đổi mật khẩu cần nhập đúng mật khẩu hiện tại; các cập nhật hồ sơ và mật khẩu được lưu nguyên tử.

## An toàn
Không đặt thông tin kết nối trong `public/`. Không dùng route GET cho thao tác ghi dữ liệu. Chưa có CSRF/auth middleware trong bộ khung; phải triển khai trước khi đưa các chức năng ghi dữ liệu và tài khoản vào sử dụng.
