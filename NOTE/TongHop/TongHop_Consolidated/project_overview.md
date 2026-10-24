# Tổng quan dự án

## Nhận diện và giới hạn nguồn

Tài liệu nguồn mô tả T&T Computer là ứng dụng PHP/MySQL tự xây dựng, dùng namespace `App`, PDO, controller/model/view, router và renderer riêng. Tuy nhiên thư mục đầu vào của nhiệm vụ chỉ chứa 69 tài liệu `.md`/`.mmd`, không chứa 38 PHP, 3 SQL hoặc cấu hình dự án mà các tài liệu đó dẫn chiếu. Vì vậy nội dung dưới đây là tổng hợp có truy xuất từ các báo cáo nguồn, chưa phải kiểm chứng độc lập với mã nguồn hay cơ sở dữ liệu.

Các báo cáo có mốc ngày 2026-10-19. Không xác định runtime, phiên bản PHP/MySQL/XAMPP hay môi trường triển khai.

## Chức năng được tài liệu nguồn ghi nhận

- Khách: xem trang chủ/giới thiệu/catalog; đăng ký, đăng nhập, đăng xuất; cập nhật hồ sơ; quản lý giỏ; đặt hàng COD; xem và hủy đơn.
- Quản trị: tìm kiếm, tạo/sửa nhân viên và đổi trạng thái tài khoản; có kiểm tra vai trò quản trị.
- Chưa thấy bằng chứng route tích hợp thanh toán trực tuyến, quản lý đơn phía nhân viên, CRUD sản phẩm/danh mục, quản lý kho độc lập hoặc báo cáo thống kê.
- Bảng `GiaoDichThanhToan` trong schema được báo cáo không tự chứng minh cổng thanh toán đã tích hợp.

## Thành phần được mô tả

| Khu vực | Vai trò được báo cáo |
|---|---|
| `public/index.php`, `routes/web.php` | bootstrap, front controller, khai báo/dispatch route |
| `app/Core` | Router, base Controller, View, kết nối PDO |
| `app/Controllers`, `app/Models` | xử lý request/use case và truy cập dữ liệu |
| `app/views`, `public/css`, `public/js` | HTML phía server, kiểu giao diện, tương tác DOM |
| `database.sql`, migration, fixture | schema khởi tạo, thay đổi schema, dữ liệu mẫu; vai trò cần phân biệt |

## Số liệu kỹ thuật được báo cáo

- 27 khai báo route: 13 GET và 14 POST.
- 11 bảng trong schema được mô tả.
- 60 call site PDO trong bốn model; cách đếm template SQL khác với số call site.
- Báo cáo v4 chốt 17 class, 89 method và một function JavaScript inline; tổng function-like declaration báo là 90.
- Catalog không phân trang. Một số tài liệu đời đầu nói có page/LIMIT/OFFSET; các đính chính v2/v4 bác bỏ nhận định này.

Các con số trên phản ánh tài liệu nguồn; không được đếm lại trên mã dự án vì mã không nằm trong input.

## Đọc tiếp

Bắt đầu với [kiến trúc](system_architecture.md), [module và method](source_code/module_and_method_reference.md), [luồng/route](business_logic/workflows_and_routes.md), [database](database/database_schema_and_queries.md) và [các điểm chưa xác minh](reports/unresolved_issues.md).
