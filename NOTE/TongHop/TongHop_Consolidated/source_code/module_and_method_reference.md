# Module, class và method

Thông tin này được tổng hợp từ các tài liệu module v1–v4. Tên/chi tiết method là trích dẫn tài liệu thứ cấp; xác minh trên file PHP tương ứng trước khi dùng như API contract.

## Core

- `Router`: `get`, `post`, `add`, `dispatch`; giữ danh sách route và gọi handler.
- `Controller`: `view`, `redirect`; helper render và redirect.
- `View::render`: nạp view/layout, truyền data, buffer output.
- `Database::connection`: tạo/tái sử dụng PDO qua static property.

## Catalog

- `HomeController::index`; `AboutController::index`.
- `ProductController::index/show/notFound`.
- `Product::featured/all/categories/findById/baseQuery`.
- `featured` clamp limit 1–24 theo tài liệu. `all` lọc keyword/category, chọn sort từ allowlist; không có LIMIT/OFFSET hoặc page/count. `featured` sắp theo mã sản phẩm, không dùng flag nổi bật.
- Tài liệu đời đầu v1 ghi `index()` nhận page và `all()` dùng pagination. Đính chính v2 xác nhận mô tả này sai; module v4 khớp với đính chính. Giữ nội dung cũ ở đây như lịch sử mâu thuẫn, không xem là behavior hiện hành.

## Auth, account, admin

- `AuthController`: `login/register/logout/redirectByRole`.
- `AccountController`: `requireCustomer/verifyCsrf/index/update/renderForm`.
- `Account` model: tìm kiếm/lookup account, `createStaff/updateStaff`, uniqueness checks, `findCustomerProfile/updateCustomerProfile`, `changeStatus`, `findForLogin`, `usernameExists/emailExists`, `register`.
- `UserManagementController`: `index/createStaff/updateStaff/changeStatus/requireAdministrator/validCsrf/staffData/validateStaff/fail/succeed`.
- Các báo cáo cho biết login dùng `password_verify`, session ID rotation, loại password hash khỏi session; ghi mật khẩu dùng `password_hash`. Quản trị viên được kiểm lại vai trò/trạng thái từ DB trong controller quản trị.

## Cart và orders

- `CartController`: `requireCustomer/verifyCsrf/postOnly/flash/index/add/buyNow/update/remove/clear`.
- `Cart`: `getOrCreateCart/getItems/addItem/findItems/updateItem/removeItem/clear`.
- `CheckoutController`: `requireCustomer/verifyCsrf/index/placeOrder/success/renderCheckout`.
- `OrderController`: `requireCustomer/verifyCsrf/postOnly/flash/index/show/cancel`.
- `Order`: `placeCodOrder/findForCustomer/findAllForCustomer/cancelForCustomer`.

Các thao tác đặt/hủy đơn được mô tả dùng transaction, row lock và lịch sử kho; chi tiết [database và query](../database/database_schema_and_queries.md), [business flows](../business_logic/workflows_and_routes.md).

## Cấu trúc dữ liệu/controller thường gặp

- Input: `$_GET`, `$_POST`, `$_SERVER['REQUEST_METHOD']`, `$_SERVER['REQUEST_URI']`; phải coi là dữ liệu không tin cậy.
- Session: `$_SESSION['user']`, CSRF `_csrf`, flash keys. Không lưu password hash trong user session theo tài liệu.
- Model/controller-view: `$pdo`, `$this->db`, `$form`, `$errors`, `$profile`, `$cart`, `$order`; dữ liệu output cần escape theo context.
- `$baseUrl`: cấu hình tạo URL/redirect, phụ thuộc base path deploy.

Không có danh sách toàn bộ local variable đáng tin cậy trong input; tài liệu cũ nói rõ phần này không được đặc tả đầy đủ.
