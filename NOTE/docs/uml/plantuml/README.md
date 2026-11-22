# Bộ sơ đồ PlantUML T&T COMPUTER

Bộ này mô tả hiện trạng source tại thời điểm khảo sát. Nguồn chính là `app/`, `routes/web.php`, `public/index.php`, `NOTE/database.sql` cùng hai migration dưới `database/migrations/`; `NOTE/LaTex_Document/Nhap/BanMoi.pdf` chỉ dùng làm đối chiếu nghiệp vụ. PDF mang ngày 15/10/2026 và liệt kê UC01–UC18; mã nguồn hiện tại là căn cứ cho các sơ đồ hiện trạng.

## Danh mục

- `01_UseCase_TongQuat.puml`, `01A_UseCase_KhachHang.puml`, `01B_UseCase_QuanTri.puml`, `01C_UseCase_NhanVien.puml`: actor và chức năng đã kiểm tra trong route/controller.
- `02_Class_TongQuat.puml`, `02A_Class_Model_Entity.puml`, `02B_Class_Controller_Service.puml`, `02C_Class_QuanHeNghiepVu.puml`: class PHP thực tế và phụ thuộc trực tiếp.
- `02D_Class_Diagram_Full.puml`: sơ đồ lớp tổng hợp theo mô hình nghiệp vụ, tham khảo bố cục class diagram được cung cấp.
- `03_Database_ERD_TongQuat.puml`, `03A_Database_ERD_Order.puml`, `03B_Database_ERD_Product.puml`, `03C_Database_ERD_User.puml`: schema `NOTE/database.sql` và migration.
- `04_FunctionalDecomposition_TongQuat.puml`, `04A_FunctionalDecomposition_KhachHang.puml`, `04B_FunctionalDecomposition_QuanTri.puml`: phân rã chức năng triển khai.
- `sequence/05A_Sequence_DangKy.puml` đến `sequence/05N_Sequence_ThongKe.puml`: các luồng theo controller/model thực tế.

## Trạng thái so với tài liệu

Đã triển khai: đăng ký, đăng nhập thường/Google, tra cứu sản phẩm, giỏ hàng, checkout COD/Online SePay, theo dõi/hủy đơn, hồ sơ/địa chỉ, quản lý đơn và hoàn kho đơn giao thất bại, sản phẩm/danh mục, tài khoản nhân viên, điều chỉnh kho, thống kê, campaign giảm giá, voucher và preview voucher lúc checkout.

PDF liệt kê UC01–UC18, nhưng không thấy chức năng đặt đơn tự động hủy do hết hạn được thực thi bởi ứng dụng. UC10 kiểm tra tồn xuất hiện trong bước xác nhận đơn, không có UC độc lập. Source có tích hợp Google Identity và SePay. Không thấy upload ảnh qua form quản trị; ảnh là dữ liệu chi tiết sản phẩm. Các sequence được giới hạn theo hành vi controller/model.

## Mở và xuất

Mở `.puml` bằng PlantUML extension trong VS Code/IntelliJ hoặc PlantUML local. Khi đã có PlantUML và Java: `java -jar plantuml.jar -tsvg docs/uml/plantuml/**/*.puml`; dùng `-tpng` để xuất PNG. PDF có thể tạo từ SVG/PNG bằng công cụ xuất tài liệu. Môi trường khảo sát có Java nhưng không tìm thấy executable/JAR PlantUML; không cài đặt và chưa render.

Thứ tự đề xuất: class tổng quát → use case → phân rã chức năng → ERD tổng quát/chuyên đề → sequence đăng nhập, tìm kiếm, đặt hàng, thanh toán, hủy đơn, xử lý đơn. Các sơ đồ là hiện trạng IMPLEMENTATION; phần chỉ có trong PDF phải ghi là yêu cầu tài liệu.
