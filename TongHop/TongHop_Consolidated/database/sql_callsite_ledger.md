# Ledger 60 call site SQL

Nguồn: v4 `13_SQL_CALLSITE_LEDGER.md`; đây là vị trí call `prepare()`/`query()` theo snapshot được báo cáo, không phải toàn bộ SQL literal hoặc số query runtime. Dòng có thể đổi. SQL không được thực thi và file model gốc không có trong input hiện tại.

| ID | File/dòng theo nguồn | Method | Bảng/mục đích |
|---|---|---|---|
| SQL-ACC-01 | `Account.php:35` | `searchForManagement` | TaiKhoan, NguoiDung |
| SQL-ACC-02 | `Account.php:44` | `findForManagement` | TaiKhoan, NguoiDung |
| SQL-ACC-03 | `Account.php:71` | `createStaff` | INSERT NguoiDung |
| SQL-ACC-04 | `Account.php:78` | `createStaff` | INSERT TaiKhoan |
| SQL-ACC-05 | `Account.php:110` | `updateStaff` | Khóa/đọc TaiKhoan |
| SQL-ACC-06 | `Account.php:122` | `updateStaff` | UPDATE NguoiDung |
| SQL-ACC-07 | `Account.php:129` | `updateStaff` | UPDATE TaiKhoan |
| SQL-ACC-08 | `Account.php:147` | `usernameUsedByOther` | Kiểm tra username |
| SQL-ACC-09 | `Account.php:158` | `emailUsedByOther` | Kiểm tra email |
| SQL-ACC-10 | `Account.php:171` | `findCustomerProfile` | NguoiDung, TaiKhoan |
| SQL-ACC-11 | `Account.php:198` | `updateCustomerProfile` | Khóa TaiKhoan |
| SQL-ACC-12 | `Account.php:211` | `updateCustomerProfile` | Kiểm tra NguoiDung/email |
| SQL-ACC-13 | `Account.php:229` | `updateCustomerProfile` | UPDATE TaiKhoan/password |
| SQL-ACC-14 | `Account.php:236` | `updateCustomerProfile` | UPDATE NguoiDung |
| SQL-ACC-15 | `Account.php:265` | `changeStatus` | Khóa/đếm admin active |
| SQL-ACC-16 | `Account.php:269` | `changeStatus` | Khóa account đích |
| SQL-ACC-17 | `Account.php:293` | `changeStatus` | UPDATE trạng thái |
| SQL-ACC-18 | `Account.php:327` | `findForLogin` | TaiKhoan, NguoiDung |
| SQL-ACC-19 | `Account.php:341` | `usernameExists` | Kiểm tra username |
| SQL-ACC-20 | `Account.php:352` | `emailExists` | Kiểm tra email |
| SQL-ACC-21 | `Account.php:376` | `register` | INSERT NguoiDung |
| SQL-ACC-22 | `Account.php:388` | `register` | INSERT TaiKhoan |
| SQL-CART-01 | `Cart.php:24` | `getOrCreateCart` | SELECT GioHang |
| SQL-CART-02 | `Cart.php:37` | `getOrCreateCart` | INSERT GioHang |
| SQL-CART-03 | `Cart.php:49` | `getOrCreateCart` | Đọc lại GioHang sau lỗi insert/concurrency |
| SQL-CART-04 | `Cart.php:72` | `getItems` | ChiTietGioHang, SanPham |
| SQL-CART-05 | `Cart.php:99` | `getItems` | UPDATE GioHang.TongTien |
| SQL-CART-06 | `Cart.php:131` | `addItem` | Khóa/tìm GioHang |
| SQL-CART-07 | `Cart.php:136` | `addItem` | ChiTietGioHang, SanPham |
| SQL-CART-08 | `Cart.php:168` | `addItem` | Khóa/đọc SanPham |
| SQL-CART-09 | `Cart.php:188` | `addItem` | Đọc dòng giỏ hiện tại |
| SQL-CART-10 | `Cart.php:201` | `addItem` | UPDATE/INSERT ChiTietGioHang |
| SQL-CART-11 | `Cart.php:229` | `findItems` | SELECT GioHang |
| SQL-CART-12 | `Cart.php:243` | `findItems` | ChiTietGioHang, SanPham |
| SQL-CART-13 | `Cart.php:287` | `updateItem` | Đọc dòng/tồn theo owner |
| SQL-CART-14 | `Cart.php:308` | `updateItem` | UPDATE ChiTietGioHang |
| SQL-CART-15 | `Cart.php:326` | `removeItem` | DELETE ChiTietGioHang theo owner |
| SQL-CART-16 | `Cart.php:342` | `clear` | DELETE ChiTietGioHang |
| SQL-CART-17 | `Cart.php:349` | `clear` | UPDATE GioHang.TongTien |
| SQL-ORD-01 | `Order.php:29` | `placeCodOrder` | Khóa/đọc GioHang |
| SQL-ORD-02 | `Order.php:43` | `placeCodOrder` | ChiTietGioHang, SanPham |
| SQL-ORD-03 | `Order.php:88` | `placeCodOrder` | Tính tổng từ giá và quantity |
| SQL-ORD-04 | `Order.php:98` | `placeCodOrder` | INSERT DonHang |
| SQL-ORD-05 | `Order.php:122` | `placeCodOrder` | Trừ tồn SanPham có điều kiện |
| SQL-ORD-06 | `Order.php:139` | `placeCodOrder` | INSERT ChiTietDonHang, snapshot giá |
| SQL-ORD-07 | `Order.php:154` | `placeCodOrder` | INSERT LichSuKho âm/XuatKho |
| SQL-ORD-08 | `Order.php:169` | `placeCodOrder` | DELETE ChiTietGioHang |
| SQL-ORD-09 | `Order.php:174` | `placeCodOrder` | Reset GioHang.TongTien |
| SQL-ORD-10 | `Order.php:197` | `findForCustomer` | DonHang theo ID + owner |
| SQL-ORD-11 | `Order.php:222` | `findForCustomer` | ChiTietDonHang, SanPham |
| SQL-ORD-12 | `Order.php:244` | `findAllForCustomer` | DonHang + count lines |
| SQL-ORD-13 | `Order.php:271` | `cancelForCustomer` | Khóa DonHang theo owner/status |
| SQL-ORD-14 | `Order.php:300` | `cancelForCustomer` | Đọc ChiTietDonHang/SanPham |
| SQL-ORD-15 | `Order.php:314` | `cancelForCustomer` | Cộng tồn SanPham |
| SQL-ORD-16 | `Order.php:331` | `cancelForCustomer` | INSERT LichSuKho dương/NhapKho |
| SQL-ORD-17 | `Order.php:346` | `cancelForCustomer` | UPDATE DonHang status |
| SQL-PROD-01 | `Product.php:22` | `featured` | SELECT từ `$sql` base query |
| SQL-PROD-02 | `Product.php:71` | `all` | SELECT catalog + filter động |
| SQL-PROD-03 | `Product.php:89` | `categories` | SELECT DanhMuc |
| SQL-PROD-04 | `Product.php:101` | `findById` | SELECT sản phẩm theo ID |

## Cách hiểu số liệu

- Account 22 + Cart 17 + Order 17 + Product 4 = 60 call sites.
- Báo cáo đếm 54 callsite với SQL trực tiếp; 49 mẫu literal sau chuẩn hóa whitespace/case; 6 callsite gọi biến `$sql`. Theo quy ước đó có 55 query-definition units; đây không phải số câu query runtime duy nhất.
- DDL schema, fixture DML, migration không nằm trong 60 call sites.
- Dùng [schema/query summary](database_schema_and_queries.md) để hiểu transaction và nghiệp vụ; bảng này chỉ là ledger tra cứu.
