# Module xác thực, tài khoản và hồ sơ

Ngày 19-10-2026. Source: `AuthController.php`, `AccountController.php`, `UserManagementController.php`, `Account.php` và views liên quan. SQL: `NguoiDung`, `TaiKhoan`.

## AuthController

`login()` đọc identity/password/CSRF; kiểm token bằng hash_equals, kiểm rỗng, `Account::findForLogin`, `password_verify`, status `HoatDong`; khi thành công regenerate session ID, bỏ hash khỏi user, gán `$_SESSION['user']`, tái cấp CSRF và redirect theo role. Lỗi sai thông tin tổng quát.

`register()` kiểm required/độ dài/username pattern/email/confirm/password >=8; kiểm username/email đã dùng rồi gọi model. Model lưu hash và tạo người dùng/tài khoản; redirect login. Precheck unique có race, DB uniqueness là lớp bảo vệ cuối.

`logout()` chỉ POST, yêu cầu session hợp lệ và CSRF; xóa user, cấp token mới, redirect. `redirectByRole()` điều hướng theo role, fallback trang mặc định.

## AccountController

`requireCustomer()` kiểm MaND và role KhachHang. `verifyCsrf()` trả 403 nếu thiếu/sai token. `index()` nạp profile, flash rồi tạo form DTO. `update()` yêu cầu POST, CSRF, role; trim/validate name/email/phone/address. Đổi mật khẩu chỉ khi một trong ba password fields có dữ liệu; yêu cầu current, min 8, confirm. Gọi model, cập nhật session/flash và 303. PDO/runtime/throwable được phân nhánh. `renderForm()` nạp profile và render lỗi.

## Account model

Quản lý login, uniqueness, hồ sơ, nhân viên và status. Prepared statements cho user values. Transaction/`FOR UPDATE` xuất hiện trong create/update/profile/status. `changeStatus` có bảo vệ admin cuối cùng đang active. Class gộp nhiều use case account nhưng cùng domain; transaction rollback khi lỗi là ưu điểm.

Rủi ro/cơ hội: quyền session sau khi account bị vô hiệu hóa cần xác minh; test uniqueness race; cân nhắc chia repository/service nếu module phát triển. Template escape phải kiểm từng trường.
