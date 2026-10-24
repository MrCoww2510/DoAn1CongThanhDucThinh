# 04 — Tham chiếu function/method

Ngày 19-10-2026. Đây là chỉ mục method theo source; luồng/nhánh trọng yếu mô tả trong `modules/*.md`.

- `Router`: `get`, `post`, `add`, `dispatch` — đăng ký và dispatch route.
- `Controller`: `view`, `redirect`; `View::render`; `Database::connection`.
- `HomeController::index`, `AboutController::index`.
- `ProductController::index/show/notFound`; `Product::featured/all/categories/findById/baseQuery`.
- `AuthController::login/register/logout/redirectByRole`.
- `AccountController::requireCustomer/verifyCsrf/index/update/renderForm`.
- `Account`: `searchForManagement`, `findForManagement`, `createStaff`, `updateStaff`, `usernameUsedByOther`, `emailUsedByOther`, `findCustomerProfile`, `updateCustomerProfile`, `changeStatus`, `findForLogin`, `usernameExists`, `emailExists`, `register`.
- `CartController`: `requireCustomer`, `verifyCsrf`, `postOnly`, `flash`, `index`, `add`, `buyNow`, `update`, `remove`, `clear`.
- `Cart`: `getOrCreateCart`, `getItems`, `addItem`, `findItems`, `updateItem`, `removeItem`, `clear`.
- `CheckoutController`: `requireCustomer`, `verifyCsrf`, `index`, `placeOrder`, `success`, `renderCheckout`.
- `OrderController`: `requireCustomer`, `verifyCsrf`, `postOnly`, `flash`, `index`, `show`, `cancel`.
- `Order`: `placeCodOrder`, `findForCustomer`, `findAllForCustomer`, `cancelForCustomer`.
- `UserManagementController`: `index`, `createStaff`, `updateStaff`, `changeStatus`, `requireAdministrator`, `validCsrf`, `staffData`, `validateStaff`, `fail`, `succeed`.

Chữ ký trả về được khai báo trong source (`void`, `array`, `?array`, `string`, `bool`, `never`). SQL/tác dụng phụ tại module và [database spec](06_DATABASE_SPECIFICATION.md). Vị trí dòng thay đổi theo phiên bản source.
