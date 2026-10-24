# 07 — Luồng nghiệp vụ

Ngày 19-10-2026; suy từ controller/model tĩnh. Xem sơ đồ [request](diagrams/request_lifecycle.mmd), [database](diagrams/database_relationships.mmd).

## Đăng nhập/đăng ký

Login: GET/POST `/login` → CSRF/required validation → Account::findForLogin(username/email) → password_verify → kiểm trạng thái → regenerate session ID → bỏ MatKhau → lưu user và token mới → redirect theo role. Register: validate trường/username/email/password → kiểm trùng → model tạo NguoiDung/TaiKhoan trong transaction và hash mật khẩu → redirect login. Unique DB vẫn cần xử lý race.

## Cart và COD

Product detail → POST cart add → kiểm customer/CSRF/quantity → model kiểm tồn và ghi giỏ → checkout nạp giỏ/profile → POST order validate người nhận/phương thức → transaction khóa giỏ/sản phẩm, tính tổng theo giá, ghi DonHang/ChiTietDonHang, giảm tồn và ghi LichSuKho, dọn giỏ, commit → redirect success. Không có bằng chứng gọi cổng thanh toán.

## Hủy đơn

Dialog JS xác nhận form POST → kiểm quyền sở hữu/CSRF/method/trạng thái → transaction khóa đơn/chi tiết, hoàn tồn, ghi lịch sử kho, cập nhật trạng thái → redirect flash.

## Quản trị/hồ sơ

Admin list GET; tạo/sửa/trạng thái POST với role, CSRF, validation. Có ràng buộc không vô hiệu hóa admin cuối còn hoạt động và không đổi trạng thái tài khoản đang dùng. Hồ sơ khách: GET profile; POST validate, nếu đổi password yêu cầu mật khẩu cũ + confirm, model update transaction, session/flash rồi 303 redirect.
