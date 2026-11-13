# Cấu hình Google Login

1. Trong phpMyAdmin, chọn database `TTComputer` và chạy một lần nội dung
   [`database/migrations/20261113_add_google_id.sql`](../database/migrations/20261113_add_google_id.sql).
   Lệnh chỉ thêm cột `TaiKhoan.GoogleID` nullable và unique index, không xóa dữ liệu.
2. Tạo `config/google.local.php` (file được Git bỏ qua) với nội dung:

   ```php
   <?php
   return ['client_id' => 'YOUR_WEB_CLIENT_ID.apps.googleusercontent.com'];
   ```

   Hoặc đặt biến môi trường `GOOGLE_OAUTH_CLIENT_ID`. Chỉ cần Web Client ID;
   GIS không dùng Client Secret ở trình duyệt hay backend cho luồng ID Token này.
3. Authorized JavaScript origin trong Google Cloud phải khớp origin website đang
   mở, ví dụ `http://localhost`. Cấu hình origin không kèm `/public` hay route.
4. Mở trang `/login`, đăng nhập bằng Google. Tài khoản email/mật khẩu đã có cần
   đăng nhập trước bằng cách cũ, sau đó vào `/account` để liên kết Google; email
   Google phải trùng với email hồ sơ.

Không chạy `NOTE/database.sql` để cập nhật database đang có dữ liệu: đó là script
khởi tạo và có các lệnh `DROP TABLE`.
