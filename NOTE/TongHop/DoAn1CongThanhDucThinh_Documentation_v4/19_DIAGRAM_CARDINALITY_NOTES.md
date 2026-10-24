# Ghi chú cardinality trong sơ đồ ER

Ngày phân tích: 2026-10-19. Sơ đồ [database_relationships.mmd](diagrams/database_relationships.mmd) kết hợp quan hệ vật lý trong schema với quy tắc nghiệp vụ được thể hiện ở model. Các dấu cardinality vì vậy cần đọc theo bảng dưới, không coi mọi bội số là constraint do MySQL cưỡng chế.

| Quan hệ | Cơ sở schema | Quy tắc/giới hạn |
|---|---|---|
| NguoiDung–TaiKhoan | `TaiKhoan.MaND` là FK, NOT NULL, UNIQUE (`database.sql:48-58`) | Mỗi tài khoản trỏ đúng một người dùng; schema không bắt buộc mọi người dùng phải có tài khoản. Registration/staff create tạo cặp trong transaction. |
| NguoiDung–GioHang | `GioHang.MaND` FK NOT NULL UNIQUE (`database.sql:137-144`) | Mỗi giỏ có một chủ; người dùng có thể chưa có giỏ cho tới khi model tạo. |
| DanhMuc–SanPham | `SanPham.MaDM` FK | Mỗi sản phẩm thuộc một danh mục; danh mục có thể chưa có sản phẩm. |
| SanPham–ChiTietSanPham | `ChiTietSanPham.MaSP` FK NOT NULL UNIQUE (`database.sql:117-129`) | Mỗi detail trỏ một sản phẩm; một sản phẩm tối đa một detail, nhưng schema không bắt buộc detail phải tồn tại. |
| GioHang–ChiTietGioHang / SanPham–ChiTietGioHang | FK tại `database.sql:155-174` | Mỗi dòng thuộc một giỏ và một sản phẩm; giỏ/sản phẩm có thể không có dòng liên kết. Unique `(MaGH, MaSP)` giới hạn một dòng cho một sản phẩm trong một giỏ. |
| NguoiDung–DonHang | `DonHang.MaND` FK NOT NULL (`database.sql:182-198`) | Mỗi đơn thuộc một khách hàng; khách có thể chưa đặt đơn. |
| TaiKhoan–DonHang (người xử lý) | FK `DonHang.MaNV` tới `TaiKhoan`; cột có thể nullable (`database.sql:182-201`) | Đơn có thể chưa gán nhân viên; tài khoản có thể xử lý nhiều đơn. |
| DonHang–ChiTietDonHang | FK từ chi tiết sang đơn (`database.sql:239-252`) | Schema cho phép đơn 0 dòng; luồng `placeCodOrder` từ giỏ không rỗng tạo chi tiết trong transaction. Mức tối thiểu 1 là quy tắc ứng dụng, không phải FK. |
| SanPham–ChiTietDonHang | FK tại `database.sql:239-252` | Mỗi dòng chi tiết tham chiếu một sản phẩm; sản phẩm có thể chưa được bán. |
| DonHang–GiaoDichThanhToan | FK tại `database.sql:267-277` | Schema cho phép không giao dịch hoặc nhiều giao dịch; không có bằng chứng tích hợp cổng thanh toán ở route flow hiện tại. |
| SanPham/NguoiDung–LichSuKho | FK tại `database.sql:296-309` | Mỗi log tham chiếu sản phẩm và người thực hiện theo schema; lịch sử có thể có nhiều dòng. |

Các liên kết `NguoiDung`–`TaiKhoan` và `NguoiDung`–`GioHang` được source nghiệp vụ sử dụng như 1:1 tối đa. Schema không bảo đảm sự tồn tại bắt buộc ở phía NguoiDung. Tương tự, bội số tối thiểu 1 ở chi tiết đơn được bảo đảm bởi luồng tạo đơn, không bởi constraint. Chưa xác minh schema runtime; `database.sql` là nguồn bằng chứng tĩnh.
