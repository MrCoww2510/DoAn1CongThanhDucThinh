# 00 — Tổng quan dự án

Ngày 19-10-2026. Phân tích tĩnh, không xác minh phiên bản runtime.

## Nhận diện

T&T Computer là ứng dụng PHP/MySQL tự triển khai, dùng namespace `App`, PDO và các lớp Controller/Model/View. Có cấu trúc MVC một phần nhưng không dùng framework MVC được nhận diện: router, bootstrap và renderer tự viết. Entry point `public/index.php`; route `routes/web.php`; cấu hình `config/app.php`, `config/database.php`; schema `database.sql`, migration địa chỉ tại `database/migrations/20261012_add_customer_address.sql`.

## Chức năng có bằng chứng

- Trang chủ, giới thiệu, catalog/sản phẩm.
- Đăng nhập/đăng ký/đăng xuất, hồ sơ/đổi mật khẩu.
- Giỏ hàng CRUD, checkout COD, xem/hủy đơn.
- Quản trị tài khoản nhân viên.
- Chưa xác minh cổng thanh toán thực tế: schema có `GiaoDichThanhToan`, nhưng luồng code được rà không cho thấy sử dụng.

## Công nghệ và cấu trúc

PHP thuần, HTML/PHP templates, CSS, JavaScript DOM, PDO/MySQL, Apache rewrite, PHP session. Không thấy `composer.json`/`package.json` ở gốc. Không thấy fetch/AJAX; tương tác form server-rendered. Phiên bản XAMPP chưa xác minh.

| Đường dẫn | Vai trò |
|---|---|
| `public/index.php` | Bootstrap/front controller |
| `routes/web.php` | Khai báo route HTTP |
| `app/Core/` | Router, Controller, View, Database |
| `app/Controllers/`, `app/Models/`, `app/views/` | Nghiệp vụ và giao diện |
| `config/`, SQL | Cấu hình, schema, migration |
| `public/css`, `public/js`, `public/images` | Tài nguyên giao diện |

`dulieuao.sql`, `KienThuc.txt`, `rule.txt`, `LaTex_Document/` tồn tại nhưng không đại diện chắc chắn cho database đang chạy.
