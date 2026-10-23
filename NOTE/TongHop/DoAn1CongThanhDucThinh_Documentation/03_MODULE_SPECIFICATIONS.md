# 03 — Đặc tả module

Ngày 19-10-2026; bằng chứng source tĩnh. Chi tiết: [core](modules/core.md), [tài khoản](modules/accounts_auth.md), [sản phẩm](modules/products.md), [giỏ hàng/đơn](modules/cart_orders.md), [quản trị](modules/admin.md), [giao diện](modules/presentation.md).

| Module | Thành phần | Trách nhiệm |
|---|---|---|
| Core/routing | `public/index.php`, `routes/web.php`, `app/Core/*` | bootstrap, match route, render/redirect, PDO |
| Auth/account | `AuthController`, `AccountController`, `Account` | xác thực, đăng ký, hồ sơ, quản lý nhân sự |
| Catalog | `ProductController`, `Product` | lọc, sắp xếp, phân trang, chi tiết |
| Cart | `CartController`, `Cart` | CRUD giỏ và tổng tiền |
| Orders | `CheckoutController`, `OrderController`, `Order` | checkout COD, xem/hủy đơn, kho |
| Presentation | `app/views`, `public/css`, `public/js` | server-rendered HTML, styling, DOM |

Controller gọi trực tiếp model cụ thể; không thấy DI/service layer.
