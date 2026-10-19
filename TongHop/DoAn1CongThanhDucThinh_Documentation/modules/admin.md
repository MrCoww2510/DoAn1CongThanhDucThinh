# Module quản trị tài khoản nhân viên

Ngày 19-10-2026. Source: `UserManagementController.php`, `Account.php`, `app/views/admin/users.php`, `public/css/users.css`.

`index()` gọi `requireAdministrator`, lấy danh sách/tìm kiếm và account đang sửa, tạo token nếu cần rồi render view. `createStaff()`/`updateStaff()` yêu cầu admin, CSRF, đọc `staffData()`, validate name/username/email/phone/status, gọi model và chuyển kết quả qua `fail()`/`succeed()` (flash + redirect). `changeStatus()` xác nhận trạng thái cho phép, cấm đổi tài khoản đang đăng nhập và tránh vô hiệu hóa admin active cuối cùng.

Model tìm kiếm/đọc join TaiKhoan-NguoiDung; insert/update hai bảng và uniqueness checks dùng prepared statements. `FOR UPDATE`/transaction bảo vệ cập nhật cạnh tranh ở các đoạn tương ứng. Cần kiểm soát race giữa các transaction và ensure unique DB constraints.

Ưu: quyền và validation server-side, bảo vệ admin cuối. Hạn chế: controller chứa nhiều validation/redirect branches; role/CSRF patterns lặp ở module khác. Gợi ý test ma trận quyền và trạng thái, không sửa source trong đợt này.
