# AuthController và AccountController

Ngày 19-10-2026. Files lần lượt 227 và 212 dòng. Dependency concrete: Account model, base Controller, PHP session.

## AuthController

`login(): void` (13–68): nếu session user đã tồn tại thì redirect role. Khởi tạo `$error=''`, `$identity=''`. POST đọc identity trim, password raw, CSRF; kiểm token tồn tại/hash_equals, required; gọi `findForLogin`; null hoặc password_verify sai cho thông báo chung; trạng thái khác HoatDong báo inactive. Thành công regenerate session ID, unset MatKhau, lưu account vào `$_SESSION['user']`, cấp CSRF 32 random bytes hex, redirectByRole. GET/lỗi khởi tạo CSRF nếu cần rồi render. `$account` array|null. Hash chỉ tồn tại tạm trong biến trước khi unset.

`register(): void` (71–165): session user hiện hữu redirect. Khởi tạo error/name/username/email/phone rỗng, đảm bảo CSRF. POST trim text fields; password/confirm giữ nguyên. Lần lượt kiểm CSRF, required, độ dài name100/user50/email100/phone15, username regex 4–50 ASCII, email filter, password confirm, min8. Sau đó gọi usernameExists/emailExists; trùng báo tương ứng. Model register trong try; thành công xóa rồi tái cấp token, set `_register_success`, redirect login. PDOException/Throwable log chi tiết và message chung. Lỗi render giữ text values; không đưa password vào view. Lưu ý admin account nếu login redirect root (không có trang admin landing riêng trong redirect).

`logout(): void` (168–215): chỉ POST; nếu session user không array/thiếu MaTK thì unset và redirect login (nhánh này trước CSRF). Nếu logged-in, so CSRF; sai →403. Đúng thì unset user, cấp CSRF mới, redirect root. Không gọi `session_destroy`; session vẫn sống cho flash/token.

`redirectByRole(string $role): never` (218–226): QuanTriVien/NhanVien và nhánh còn lại đều redirect root. Role hiện không thay đổi đích.

## AccountController

`requireCustomer(): array` (15–28): `$_SESSION['user'] ?? null`; kiểm array, MaND truthy và role KhachHang; redirect login khi sai.

`verifyCsrf(): void` (30–43): token POST/session cast string; rỗng hoặc mismatch hash_equals → HTTP403 + exit.

`index(): void` (45–78): xác thực; lấy profile; null →404 view; init csrf; đọc flash `_account_flash`, unset; tạo `$form` từ profile với cast string và fallback empty; render profile/form/flash/token.

`update(): void` (80–188): POST-only 405; CSRF trước customer check; trim name/email/phone/address, lấy password fields raw, `$errors=[]`. Validate name required/max100; email max100/valid; normalize phone xóa whitespace/().- và cho blank hoặc 8–15 digits optional +; address max255. `$changingPassword` true nếu bất kỳ password field không rỗng; yêu cầu current, new>=8, confirm match. Errors → renderForm return. Nếu pass, model call truyền current/new hoặc null; update session name/email/phone, set success flash, redirect 303. PDOException/Throwable log + generic error; RuntimeException nghiệp vụ hiển thị exception message. Lưu ý API model chỉ kiểm `null`/non-null, nên controller map empty fields sang null.

`renderForm(array $user,array $form,array $errors): void` (190–211): reload profile; missing →404. Render profile/form/errors/token. Controller không truyền `$flash` ở nhánh lỗi update, nên flash success không áp dụng.

Biến: `$currentPassword/$newPassword/$confirmPassword` không được render hay log; `$normalizedPhone` có thể preg_replace string/false (code guard). `$profile` null/array. Errors list string. Side effects: session/token/flash, HTTP status/redirect, model writes.

## Nhận định

Password hashing/verify, regenerate session, generic login failure, CSRF compare an toàn có bằng chứng. Đăng ký email duplicate precheck nhưng schema không UNIQUE Email nên có race; Session flags/cookie settings nằm ngoài các controller. Xác nhận `AuthController::logout` nhánh invalid session không cần CSRF là hành vi rõ trong source; chưa đánh giá tác động exploit thực tế.
