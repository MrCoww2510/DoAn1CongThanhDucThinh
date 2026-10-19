# Danh mục call site SQL trong PHP

Ngày phân tích: 2026-10-19. Danh mục này dùng **ID cho mỗi call site `prepare()`/`query()`**, không đồng nhất số call site với số câu SQL literal duy nhất. Các query được ghép trong biến, lặp cấu trúc hoặc cùng mục đích có thể làm số literal duy nhất khác 60. Mỗi ID dẫn đến file/dòng chính xác tại snapshot. Nội dung nghiệp vụ, tham số, nhánh transaction, rủi ro và schema tham chiếu trong tài liệu model tương ứng. Không SQL nào được thực thi.

Định dạng ID: `SQL-<Model>-NN`. Tất cả 60 call site ứng dụng được đếm trong 4 model.

| ID | File và dòng call site | Method theo source | Đối tượng/schema cần đối chiếu |
|---|---|---|---|
| SQL-ACC-01 | `app/Models/Account.php:35` | searchForManagement | TaiKhoan, NguoiDung |
| SQL-ACC-02 | `app/Models/Account.php:44` | findForManagement | TaiKhoan, NguoiDung |
| SQL-ACC-03 | `app/Models/Account.php:71` | createStaff | NguoiDung |
| SQL-ACC-04 | `app/Models/Account.php:78` | createStaff | TaiKhoan |
| SQL-ACC-05 | `app/Models/Account.php:110` | updateStaff | TaiKhoan |
| SQL-ACC-06 | `app/Models/Account.php:122` | updateStaff | NguoiDung |
| SQL-ACC-07 | `app/Models/Account.php:129` | updateStaff | TaiKhoan |
| SQL-ACC-08 | `app/Models/Account.php:147` | usernameUsedByOther | TaiKhoan |
| SQL-ACC-09 | `app/Models/Account.php:158` | emailUsedByOther | NguoiDung |
| SQL-ACC-10 | `app/Models/Account.php:171` | findCustomerProfile | NguoiDung, TaiKhoan |
| SQL-ACC-11 | `app/Models/Account.php:198` | updateCustomerProfile | TaiKhoan |
| SQL-ACC-12 | `app/Models/Account.php:211` | updateCustomerProfile | NguoiDung |
| SQL-ACC-13 | `app/Models/Account.php:229` | updateCustomerProfile | TaiKhoan |
| SQL-ACC-14 | `app/Models/Account.php:236` | updateCustomerProfile | NguoiDung |
| SQL-ACC-15 | `app/Models/Account.php:265` | changeStatus | TaiKhoan, đếm admin active |
| SQL-ACC-16 | `app/Models/Account.php:269` | changeStatus | TaiKhoan, khóa bản ghi |
| SQL-ACC-17 | `app/Models/Account.php:293` | changeStatus | TaiKhoan |
| SQL-ACC-18 | `app/Models/Account.php:327` | findForLogin | TaiKhoan, NguoiDung |
| SQL-ACC-19 | `app/Models/Account.php:341` | usernameExists | TaiKhoan |
| SQL-ACC-20 | `app/Models/Account.php:352` | emailExists | NguoiDung |
| SQL-ACC-21 | `app/Models/Account.php:376` | register | NguoiDung |
| SQL-ACC-22 | `app/Models/Account.php:388` | register | TaiKhoan |
| SQL-CART-01 | `app/Models/Cart.php:24` | getOrCreateCart | GioHang |
| SQL-CART-02 | `app/Models/Cart.php:37` | getOrCreateCart | GioHang |
| SQL-CART-03 | `app/Models/Cart.php:49` | getOrCreateCart | GioHang |
| SQL-CART-04 | `app/Models/Cart.php:72` | getItems | ChiTietGioHang, SanPham |
| SQL-CART-05 | `app/Models/Cart.php:99` | getItems | GioHang |
| SQL-CART-06 | `app/Models/Cart.php:131` | addItem | GioHang, khóa dòng |
| SQL-CART-07 | `app/Models/Cart.php:136` | addItem | ChiTietGioHang, SanPham |
| SQL-CART-08 | `app/Models/Cart.php:168` | addItem | SanPham |
| SQL-CART-09 | `app/Models/Cart.php:188` | addItem | ChiTietGioHang |
| SQL-CART-10 | `app/Models/Cart.php:201` | addItem | ChiTietGioHang |
| SQL-CART-11 | `app/Models/Cart.php:229` | findItems | GioHang |
| SQL-CART-12 | `app/Models/Cart.php:243` | findItems | ChiTietGioHang, SanPham |
| SQL-CART-13 | `app/Models/Cart.php:287` | updateItem | ChiTietGioHang, SanPham |
| SQL-CART-14 | `app/Models/Cart.php:308` | updateItem | ChiTietGioHang |
| SQL-CART-15 | `app/Models/Cart.php:326` | removeItem | ChiTietGioHang |
| SQL-CART-16 | `app/Models/Cart.php:342` | clear | ChiTietGioHang |
| SQL-CART-17 | `app/Models/Cart.php:349` | clear | GioHang |
| SQL-ORD-01 | `app/Models/Order.php:29` | placeCodOrder | GioHang, khóa/đọc cart |
| SQL-ORD-02 | `app/Models/Order.php:43` | placeCodOrder | ChiTietGioHang, SanPham |
| SQL-ORD-03 | `app/Models/Order.php:88` | placeCodOrder | tính tổng từ giỏ và giá hiện tại |
| SQL-ORD-04 | `app/Models/Order.php:98` | placeCodOrder | INSERT DonHang |
| SQL-ORD-05 | `app/Models/Order.php:122` | placeCodOrder | UPDATE SanPham/tồn kho |
| SQL-ORD-06 | `app/Models/Order.php:139` | placeCodOrder | INSERT ChiTietDonHang |
| SQL-ORD-07 | `app/Models/Order.php:154` | placeCodOrder | INSERT LichSuKho |
| SQL-ORD-08 | `app/Models/Order.php:169` | placeCodOrder | DELETE ChiTietGioHang |
| SQL-ORD-09 | `app/Models/Order.php:174` | placeCodOrder | UPDATE GioHang tổng tiền |
| SQL-ORD-10 | `app/Models/Order.php:197` | findForCustomer | DonHang |
| SQL-ORD-11 | `app/Models/Order.php:222` | findForCustomer | ChiTietDonHang, SanPham |
| SQL-ORD-12 | `app/Models/Order.php:244` | findAllForCustomer | DonHang, tổng hợp số mặt hàng |
| SQL-ORD-13 | `app/Models/Order.php:271` | cancelForCustomer | DonHang, khóa trạng thái |
| SQL-ORD-14 | `app/Models/Order.php:300` | cancelForCustomer | ChiTietDonHang/SanPham |
| SQL-ORD-15 | `app/Models/Order.php:314` | cancelForCustomer | UPDATE SanPham/tồn kho |
| SQL-ORD-16 | `app/Models/Order.php:331` | cancelForCustomer | INSERT LichSuKho |
| SQL-ORD-17 | `app/Models/Order.php:346` | cancelForCustomer | UPDATE DonHang trạng thái |
| SQL-PROD-01 | `app/Models/Product.php:22` | featured | SELECT dựng trong `$sql` |
| SQL-PROD-02 | `app/Models/Product.php:71` | all | SELECT sản phẩm và điều kiện lọc |
| SQL-PROD-03 | `app/Models/Product.php:89` | categories | SELECT DanhMuc |
| SQL-PROD-04 | `app/Models/Product.php:101` | findById | SELECT sản phẩm theo ID |

## Cách dùng ledger và giới hạn

- Tổng: Account 22, Cart 17, Order 17, Product 4 = **60 call site** được nhận diện bằng regex trên PHP model.
- Các vị trí `query()`/`prepare()` ở đây là điểm bắt đầu gọi PDO. Query SQL nhiều dòng hoặc tạo trong biến có thể bắt đầu ở dòng khác; xem phần thân method và module để biết literal đầy đủ.
- Tham số thay thế `:name`/`?` cần đối chiếu ngay trong method; danh mục này không lặp lại secret/fixture value.
- `database.sql` xác nhận 11 bảng/khóa/constraint tại các dòng được mô tả trong `08_SQL_FIXTURE_AND_SCHEMA.md`. Query không đồng nghĩa có FK; chỉ constraint khai báo mới được gọi là FK.
- Các query schema/fixture/migration trong SQL file không nằm trong 60 call site PHP và không được thực thi.
