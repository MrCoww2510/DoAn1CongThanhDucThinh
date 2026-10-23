# 13 — Ma trận truy vết

Ngày 19-10-2026; ánh xạ source tĩnh.

| Nghiệp vụ/UI | Controller | Model/SQL | Bảng |
|---|---|---|---|
| Trang chủ | HomeController | Product::featured | SanPham, DanhMuc, ChiTietSanPham |
| Tìm/chi tiết | ProductController | Product::all/findById | catalog tables |
| Auth | AuthController | Account::register/findForLogin | NguoiDung, TaiKhoan |
| Hồ sơ | AccountController | findCustomerProfile/updateCustomerProfile | NguoiDung, TaiKhoan |
| Giỏ | CartController | Cart methods | GioHang, ChiTietGioHang, SanPham |
| Đặt COD | CheckoutController | Order::placeCodOrder | DonHang, ChiTietDonHang, SanPham, LichSuKho |
| Đơn | OrderController | Order read/cancel | DonHang, ChiTietDonHang, LichSuKho |
| Quản trị nhân viên | UserManagementController | Account management methods | TaiKhoan, NguoiDung |

Route chi tiết ở `routes/web.php`; database diagram [đây](diagrams/database_relationships.mmd). Ánh xạ có thể đổi cùng source.
