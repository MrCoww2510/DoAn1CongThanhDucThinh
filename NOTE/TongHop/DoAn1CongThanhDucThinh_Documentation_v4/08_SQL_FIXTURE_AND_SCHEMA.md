# Database và fixture SQL: phạm vi, cấu trúc, an toàn

- Ngày phân tích: 2026-10-19.
- Phạm vi: `database.sql`, `dulieuao.sql`, `database/migrations/20261012_add_customer_address.sql`; không thực thi SQL.
- Không trích xuất hoặc chép các giá trị dữ liệu fixture vào tài liệu.

## Vai trò các file

| File | Vai trò xác minh từ cú pháp | Lưu ý vận hành |
|---|---|---|
| `database.sql` | schema khởi tạo; có lệnh DROP và CREATE cho 11 bảng theo lượt đọc trước | Có tính phá hủy dữ liệu nếu chạy trên DB đang có. Tuyệt đối không chạy trong phân tích này. |
| `dulieuao.sql` | fixture/dữ liệu mẫu với 5 nhóm `INSERT INTO`: `NguoiDung`, `TaiKhoan`, `DanhMuc`, `SanPham`, `ChiTietSanPham`; không có CREATE TABLE | Phụ thuộc schema có sẵn. Chỉ nhận diện bảng và cấu trúc câu lệnh, không công bố giá trị. |
| `database/migrations/20261012_add_customer_address.sql` | 2 dòng, thêm `DiaChiNhanHang` vào `NguoiDung` | Schema hiện tại trong `database.sql` đã có cột cùng tên; áp dụng migration sau schema có thể lỗi duplicate column. Chưa thực thi để xác minh trạng thái DB. |

## Quan hệ schema với ứng dụng

Tài liệu chi tiết query đã ghi trong [tài liệu module đơn hàng/quản trị](modules/orders_admin.md) và các module Account/Cart tại tài liệu v3. Schema có 11 bảng; entity nghiệp vụ được tham chiếu bởi SQL ứng dụng gồm người dùng/tài khoản, danh mục/sản phẩm/chi tiết sản phẩm, giỏ hàng, đơn hàng/chi tiết đơn, tồn kho/lịch sử tồn và giao dịch thanh toán. `GiaoDichThanhToan` có trong schema nhưng luồng checkout được đọc hiện tại chưa chứng minh tích hợp cổng thanh toán; không kết luận đã thanh toán online.

Sơ đồ quan hệ được xác lập từ khai báo khóa trong `database.sql`, không từ dữ liệu fixture. Nếu cột quan hệ chỉ xuất hiện trong query mà thiếu FK khai báo, cần xem là quan hệ logic, không phải FK đã xác minh. Không dùng giá trị trong `dulieuao.sql` để mô tả tài khoản thật hay thông tin cá nhân.

## Rủi ro và hướng dẫn

- `database.sql` chứa DROP nên người vận hành cần xác nhận database đích và backup trước khi triển khai; không dùng file này như seed an toàn cho môi trường có dữ liệu.
- Fixture INSERT có thể tạo tài khoản/đơn vị dữ liệu thử nghiệm. Không dùng trên môi trường production; chỉ chạy trong DB sandbox sau khi đối chiếu schema.
- Migration không có kiểm tra idempotent thể hiện trong hai dòng. Cần quản lý trạng thái migration và tránh chạy lặp.
- Không khảo sát database đang chạy, không xác nhận charset/collation/runtime indexes ngoài nội dung schema; không chạy SELECT, DDL hay DML.
- Dòng SQL là tham chiếu snapshot, có thể thay đổi khi source được cập nhật.
