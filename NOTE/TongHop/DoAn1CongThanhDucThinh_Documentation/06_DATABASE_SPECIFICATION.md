# 06 — Database và SQL

Ngày 19-10-2026. Phân tích file SQL/source בלבד; không kết nối DB. `database.sql` dòng 16–26 có DROP TABLE, không được chạy như một truy vấn khám phá. Migration địa chỉ thêm cột đã có trong schema hiện tại: cần xác định version trước deploy.

## Schema xác nhận

| Bảng | Khóa/quan hệ |
|---|---|
| `NguoiDung` | PK MaND; cha của account, cart, orders, stock history |
| `TaiKhoan` | PK MaTK, unique MaND/TenDangNhap; FK NguoiDung |
| `DanhMuc` | PK MaDM, unique TenDM; 1:n SanPham |
| `SanPham` | PK MaSP, FK DanhMuc; 1:1 ChiTietSanPham |
| `ChiTietSanPham` | PK MaCTSP, unique/FK MaSP |
| `GioHang` | PK MaGH, unique/FK MaND |
| `ChiTietGioHang` | PK MaCTGH, FK cart/product, unique MaGH+MaSP |
| `DonHang` | PK MaDH, FK người mua/nhân viên tới NguoiDung |
| `ChiTietDonHang` | PK MaCTDH, FK đơn/sản phẩm, giá snapshot |
| `GiaoDichThanhToan` | PK MaGD, FK DonHang; chưa thấy dùng trong controller/model luồng hiện diện |
| `LichSuKho` | PK MaLS, FK SanPham/NguoiDung |

InnoDB, CHECK/UNIQUE/FK được khai báo. CHECK behavior tùy phiên bản MySQL/MariaDB; chưa xác minh runtime.

## Truy vấn theo model

- `Product`: SELECT nối SanPham/DanhMuc và LEFT JOIN ChiTietSanPham; lọc, sort, LIMIT/OFFSET; danh mục và chi tiết.
- `Account`: SELECT/INSERT/UPDATE người dùng/tài khoản; login/profile join; existence checks; transaction và `FOR UPDATE` khi sửa hồ sơ/trạng thái/tạo nhân viên.
- `Cart`: lấy/tạo giỏ, join dòng với sản phẩm, tính `SUM(GiaBan*SoLuong)`, thêm/sửa/xóa dòng; khóa khi thêm.
- `Order`: đặt COD transaction: khóa giỏ/sản phẩm, tính tổng, insert đơn/chi tiết, giảm tồn, ghi LichSuKho, clear cart. Hủy: khóa đơn/chi tiết, hoàn tồn, log kho, đổi trạng thái. Đọc danh sách/chi tiết theo khách.

Các model dùng prepared statements cho values; identifiers sort phải allowlist. Transaction/row locks tăng nhất quán kho; cần test concurrency. Tổng tiền giỏ lưu dư có nguy cơ lệch nếu có writer khác. Pagination lớn có thể tốn offset; index cần EXPLAIN trên DB staging.

Bằng chứng: `database.sql` dòng 35–320; migration dòng 1–2; model `Product.php`, `Account.php`, `Cart.php`, `Order.php`. Số dòng có thể đổi.
