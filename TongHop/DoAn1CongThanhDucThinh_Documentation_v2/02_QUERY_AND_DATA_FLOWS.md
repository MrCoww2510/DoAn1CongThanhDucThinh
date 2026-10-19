# Query và luồng dữ liệu chi tiết

Ngày 19-10-2026. Source: `app/Models/*.php`, `database.sql`; phân tích tĩnh. Không chạy câu lệnh. Các mục nhóm theo mục đích query; không tính số lần thực thi trong vòng lặp như query template riêng.

## Product (`Product.php`)

1. `featured`: `baseQuery` nối SanPham–DanhMuc (INNER) và ChiTietSanPham (LEFT), ORDER BY MaSP DESC, LIMIT 1..24. Không có input DB.
2. `all`: cùng join; điều kiện động tùy keyword LIKE `%keyword%` và MaDM; parameters bind. Sort map giới hạn 5 lựa chọn, fallback mặc định. **Không có LIMIT/OFFSET hay COUNT/page trong method này**, vì vậy danh sách hiện không phân trang. `%`/`_` trong keyword là wildcard SQL, không injection; có thể ảnh hưởng ý nghĩa tìm kiếm.
3. `categories`: SELECT danh mục ORDER BY TenDM.
4. `findById`: SELECT base query WHERE MaSP, LIMIT 1.

## Account (`Account.php`)

- `searchForManagement`: SELECT account/user join; tìm theo chuỗi query; đọc danh sách để admin view.
- `findForManagement`, `findCustomerProfile`, `findForLogin`: lookup theo ID/identity/customer; query login nối TaiKhoan-NguoiDung để lấy password hash và profile.
- `usernameExists`, `emailExists`, `usernameUsedByOther`, `emailUsedByOther`: `SELECT 1 ... LIMIT 1` kiểm uniqueness; kiểm tra riêng trước write có race, cần unique DB constraint.
- `createStaff`, `register`: transaction insert NguoiDung + TaiKhoan; hash mật khẩu; ID tạo trong PHP.
- `updateStaff`: lock account nhân viên, update NguoiDung và username; transaction.
- `updateCustomerProfile`: lock account/current password; kiểm password cũ khi đổi, cập nhật hash nếu cần; update profile; transaction.
- `changeStatus`: lock admin rows/account, bảo vệ admin cuối active; update trạng thái; transaction.

## Cart (`Cart.php`)

- `getOrCreateCart`: lookup MaND, nếu chưa tồn tại insert MaGH; sau duplicate/concurrent failure có nhánh re-read (cần xem exact exception handling trong source).
- `getItems`/`findItems`: SELECT chi tiết nối sản phẩm và optional detail; tổng tiền từ line × price; `getItems` đồng bộ GioHang.TongTien.
- `addItem`: transaction; lock cart/product/current line; xác nhận còn bán/tồn; update quantity hoặc insert line; commit/rollback.
- `updateItem`: lookup dòng theo owner/product và tồn; update với điều kiện ownership/quantity.
- `removeItem`, `clear`: DELETE join với GioHang giới hạn theo MaND; clear đặt TongTien=0.

## Order (`Order.php`)

- `placeCodOrder`: transaction; lock cart và lines; kiểm số lượng/trạng thái/tồn; SUM(GiaBan × quantity); insert DonHang COD/pending/unpaid; mỗi dòng: conditional stock decrement, insert ChiTietDonHang lưu DonGia, insert LichSuKho âm; xóa cart lines/reset total; commit. Exceptions rollback.
- `findForCustomer`: select header theo MaDH + MaND rồi select details join product. Hai query; owner scope trước khi đọc details.
- `findAllForCustomer`: select order list theo MaND, correlated COUNT ChiTietDonHang, order theo NgayTao/MaDH desc. Chưa có pagination.
- `cancelForCustomer`: transaction; owner-scoped order lock; chỉ trạng thái “Chờ xác nhận” được cancel, đã hủy bị báo riêng; lock lines, hoàn tồn, ghi LichSuKho dương, conditional status update; commit/rollback.

Các giá trị đều bind qua PDO. Transaction dùng InnoDB schema. Chưa có EXPLAIN/index profiling. `ChiTietDonHang.DonGia` snapshot giúp giữ giá lịch sử; tên sản phẩm lúc xem lại lấy hiện tại từ SanPham nên đổi tên có thể đổi hiển thị đơn cũ.
