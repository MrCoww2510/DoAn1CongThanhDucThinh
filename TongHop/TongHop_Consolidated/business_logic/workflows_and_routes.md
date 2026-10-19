# Luồng nghiệp vụ và ma trận route

Các luồng dưới đây được ghi nhận qua phân tích tĩnh trong tài liệu nguồn. Không có PHP để xác minh lại trong input.

## Route theo nhóm

| Method | Path | Handler/chức năng | Kiểm soát được báo cáo |
|---|---|---|---|
| GET | `/`, `/products`, `/product`, `/about` | Trang công khai/catalog | Product detail dùng query id |
| GET/POST | `/register`, `/login` | Đăng ký/đăng nhập | POST CSRF, validation/password/status |
| POST | `/logout` | Đăng xuất | POST-only, session và CSRF |
| GET/POST | `/account` | Hồ sơ khách | customer, POST CSRF |
| GET | `/admin/users` | Danh sách/tìm/sửa nhân viên | admin và DB role/status recheck |
| POST | `/admin/users/create-staff`, `/admin/users/update-staff`, `/admin/users/status` | Quản lý nhân viên | admin, CSRF, validation/policy |
| GET | `/cart` | Xem giỏ | customer |
| POST | `/cart/add`, `/cart/buy-now`, `/cart/update`, `/cart/remove`, `/cart/clear` | Thay đổi giỏ | customer, POST-only, CSRF |
| GET/POST | `/checkout` | Form/đặt hàng | customer, POST CSRF, COD validation |
| GET | `/checkout/success` | Biên nhận | customer, truy vấn owner-scoped |
| GET | `/orders` | Danh sách đơn | customer |
| GET | `/orders/{orderId}` | Chi tiết đơn | customer, owner-scoped |
| POST | `/orders/{orderId}/cancel` | Hủy đơn | customer, POST-only, CSRF, owner/status |

Tổng 27 route declarations, 13 GET và 14 POST. Bảng nhóm không liệt kê từng declaration thành 27 dòng; đầy đủ danh sách handler/path nằm ở nguồn `DoAn1CongThanhDucThinh_Documentation_v2/01_ROUTE_AUTH_MATRIX.md`.

## Đăng nhập/đăng ký

Login: form → CSRF/required → lookup username/email → verify password → kiểm trạng thái tài khoản → `session_regenerate_id(true)` theo đính chính v4 → bỏ `MatKhau` khỏi session user → cấp CSRF token mới → redirect. Register kiểm required/độ dài/username/email/password confirm và hash mật khẩu khi tạo hai bản ghi trong transaction. Email có kiểm tra trùng ở ứng dụng nhưng thiếu UNIQUE được báo cáo, nên vẫn có concurrency race.

## Giỏ, checkout COD, hủy đơn

Cart add kiểm customer/CSRF/quantity và tồn; `buy-now` thêm vào giỏ hiện tại rồi chuyển checkout. GET cart có thể tạo cart và cập nhật tổng tiền. Checkout kiểm recipient name/phone/address và chỉ chấp nhận COD; server lấy giá từ DB. Đặt hàng khóa cart/sản phẩm, ghi order/header/detail, trừ tồn, ghi stock history, xóa cart trong transaction. Hủy đơn chỉ cho đơn của customer ở trạng thái pending, hoàn tồn và ghi log trong transaction.

## Hồ sơ và quản trị nhân viên

Hồ sơ khách cho phép sửa tên/email/phone/address; khi đổi password cần password cũ, password mới tối thiểu 8 theo mô tả, xác nhận khớp. Quản trị tạo/sửa nhân viên và thay đổi trạng thái; có quy tắc không vô hiệu hóa admin active cuối cùng và không tự đổi trạng thái account đang đăng nhập.

## Hành vi cạnh

Router được mô tả trả 404 cho method/path không khớp. Một số action tự chặn method khác POST bằng 405. `removeItem` không báo lỗi khi ID không tồn tại; flash vẫn có thể báo thao tác thành công. Các hành vi này dựa vào tài liệu nguồn, chưa runtime-test.
