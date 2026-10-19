# Yêu cầu, triển khai và bảo trì

## Yêu cầu so với triển khai được báo cáo

Nguồn yêu cầu gồm `rule.txt` và `LaTex_Document/Nhap/BanMoi.tex`; đó là yêu cầu/spec chứ không phải bằng chứng tính năng chạy. Báo cáo v4 lập UC01–UC15 đối chiếu route/controller/model/schema:

| UC | Kết quả theo đối chiếu nguồn |
|---|---|
| UC01–02 đăng ký/đăng nhập | Có bằng chứng route/handler |
| UC03 xem/tìm/lọc sản phẩm | Một phần; có keyword/category/sort, không thấy lọc giá/brand hoặc pagination theo requirement |
| UC04 giỏ | Có |
| UC05 đặt hàng | Có COD |
| UC06 thanh toán | Một phần; không thấy payment gateway/callback |
| UC07 theo dõi/hủy đơn | Có, theo customer/owner/status |
| UC08 tài khoản | Có hồ sơ khách |
| UC09 quản lý đơn bởi nhân viên | Chưa thấy route tương ứng |
| UC10 tra tồn độc lập | Chưa thấy route/controller riêng |
| UC11–12 CRUD sản phẩm/danh mục admin | Chưa thấy route tương ứng |
| UC13 quản lý người dùng | Một phần; quản lý nhân viên |
| UC14 quản lý kho độc lập | Chưa thấy; kho đổi qua đặt/hủy đơn |
| UC15 thống kê/Excel | Chưa thấy route/report |

“Chưa thấy” nghĩa là không có bằng chứng trong snapshot mô tả, không khẳng định không tồn tại ngoài phạm vi.

## Vận hành an toàn

- Xác nhận môi trường/config đang được nạp trước khi triển khai; `debug => true`, hardcoded base path và `base_url` cần kiểm tra theo môi trường.
- `database.sql` có DROP theo báo cáo: chỉ xem là khởi tạo có khả năng phá hủy, không dùng tùy tiện trên DB có dữ liệu.
- Migration thêm `DiaChiNhanHang` có thể trùng với schema snapshot; kiểm tra version/state migration trước khi áp dụng.
- `dulieuao.sql` là fixture, không phải schema và không được xem là tài khoản thật; không đưa row values vào tài liệu.
- Cấu hình cookie/session, HTTPS, log, charset và Apache DocumentRoot chưa xác minh.

## Bối cảnh tài liệu phụ trợ

- `KienThuc.txt`: hướng dẫn học/lần theo luồng, nguồn thứ cấp.
- `rule.txt`, `BanMoi.tex`, PlantUML: yêu cầu và mô hình dự kiến, phải đối chiếu source.
- Template LaTeX chứa placeholder/mẫu; một chương có ví dụ nhà hàng/F&B không khớp cửa hàng máy tính, không dùng làm mô tả hệ thống.
- Các trang LaTeX chứa định danh cá nhân; bản này không sao chép dữ liệu đó.

Không có lệnh cài đặt/chạy đáng tin cậy trong nguồn đủ để viết runbook đầy đủ. Chưa xác minh phiên bản PHP, MySQL, Apache, XAMPP hay dependencies.
