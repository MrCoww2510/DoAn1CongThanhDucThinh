# Account model — đặc tả method và SQL

Ngày 19-10-2026. File: `app/Models/Account.php` (413 dòng). Class `final`, không property; mỗi phương thức lấy PDO qua `Database::connection()`. Kiểu row là associative array do PDO fetch mode. Tất cả values động truyền qua prepared statement.

## Tra cứu

- `searchForManagement(string $query=''): array` (16–39): `$sql` bắt đầu SELECT tài khoản join người dùng; `$params=[]`; query không rỗng tạo 3 LIKE với `%query%`; ORDER BY role/name, LIMIT 200. Trả 0..200 rows. `%` và `_` do user gõ được hiểu wildcard, không SQL injection. Không pagination.
- `findForManagement(string $accountId): ?array` (42–55): select account/profile theo MaTK, fetch false → null.
- `usernameUsedByOther(string $username,string $accountId=''): bool` (145–153): SELECT 1 với MaTK khác ID. Mặc định ID rỗng nghĩa mọi username trùng bị coi là của account khác.
- `emailUsedByOther(string $email,string $accountId=''): bool` (156–166): join user/account, exclude MaTK. Return bool từ fetchColumn.
- `findCustomerProfile(string $userId): ?array` (169–182): role-gated join khách, lấy MaND/name/phone/email/address; không có kết quả → null.
- `findForLogin(string $identity): ?array` (307–336): username OR email, lấy password hash và role/status; controller phải verify hash và loại MatKhau khỏi session. Không tự lọc trạng thái; controller làm bước đó.
- `usernameExists(string $username): bool` (339–347), `emailExists(string $email): bool` (350–358): existence checks trước register.

## Ghi/transaction

- `createStaff(name,username,email,phone,password): void` (58–96): bắt đầu transaction; sinh ND/TK ID bằng random bytes; insert NguoiDung (phone rỗng thành NULL), insert TaiKhoan role NhanVien/status HoatDong, hash PASSWORD_DEFAULT; commit. Throwable → rollback nếu transaction còn mở → rethrow.
- `updateStaff(accountId,name,username,email,phone): bool` (99–142): transaction; SELECT MaND cho MaTK + VaiTro NhanVien FOR UPDATE. Không có account → rollback/false. Nếu có → update profile, username, commit/true; Throwable rollback/rethrow. Không cập nhật role/password/status.
- `updateCustomerProfile(userId,name,email,phone,address,currentPassword=null,newPassword=null): void` (185–256): lock account customer/hash; thiếu account → RuntimeException. Kiểm email khác MaND; duplicate → RuntimeException. Nếu một trong password args non-null, yêu cầu cả hai non-null và verify mật khẩu hiện tại; nếu không đạt throw. Hash password mới rồi update TaiKhoan. Update NguoiDung; phone/address rỗng thành NULL; commit. Throwable rollback/rethrow. Không xác nhận kiểm email toàn cục atomically do schema thiếu unique Email.
- `changeStatus(accountId,status): string` (259–304): transaction; query tất admin FOR UPDATE, khóa target. Không thấy target → rollback/`missing`. Nếu target là admin đang active và status đích khác active: lọc `$admins` theo HoatDong; chỉ còn <=1 thì rollback/`last_admin`. Còn lại update status, commit/`updated`. Throwable rollback/rethrow. Validity status được controller kiểm trước; method công khai không tự validate.
- `register(name,username,email,phone,password): void` (361–412): transaction tạo user/account khách HoatDong; ID ngẫu nhiên; phone rỗng NULL; password_hash; commit; Throwable rollback/rethrow.

## Biến, nhánh và đánh giá

`$pdo` là local PDO; `$stmt` PDOStatement được tái gán cho từng query; `$userId/$accountId` ID string; `$account/$target` fetch row hoặc false; `$admins` list; `$activeAdmins` array_filter; `$status` string domain; `$exception` Throwable. Giá trị null có chủ đích cho phone/address/password optional; fetch false chuyển về null ở lookup.

Ưu điểm: prepared values, hash API, transaction, rollback, row locks cho các mutation nhạy cảm. Hạn chế/rủi ro: precheck unique không ngăn race; schema `TaiKhoan.TenDangNhap` unique nhưng `NguoiDung.Email` không unique (database.sql 35–41, 48–58); do đó hai đăng ký/cập nhật có thể trùng email dưới concurrency. `changeStatus` query row lock toàn bộ admin set có thể gây lock rộng. `$phone ?: null` cũng coi chuỗi "0" là rỗng, dù validation thường loại kiểu đó. Nên bảo đảm validation trong mọi caller và kiểm tra constraint/transaction trên DB test.

SQL templates: search, account lookup, 2 inserts staff, select-for-update/update profile/update username, username/email check, customer profile, lock account/email duplicate/password update/profile update, admin lock/target lock/status update, login query, username/email existence, 2 register inserts. Vị trí từng nhóm nêu trên; xem source tại ngày phân tích.
