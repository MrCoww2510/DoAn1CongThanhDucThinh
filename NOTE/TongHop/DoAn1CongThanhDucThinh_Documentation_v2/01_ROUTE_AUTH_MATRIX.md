# Ma trận route, method và kiểm soát truy cập

Ngày 19-10-2026. Nguồn `routes/web.php` dòng 18–57. “Auth” mô tả kiểm tra trong action/controller, không phải middleware. CSRF chỉ áp dụng POST theo code action; token field tên `_csrf`.

| # | Method/path | Handler | Bằng chứng kiểm soát |
|---:|---|---|---|
| 1 | GET `/` | HomeController::index | public read |
| 2 | GET `/products` | ProductController::index | public read |
| 3 | GET `/product` | ProductController::show | public read, id query |
| 4–5 | GET/POST `/register` | AuthController::register | guest redirect nếu đã login; POST CSRF + validation |
| 6–7 | GET/POST `/login` | AuthController::login | POST CSRF + password/status checks |
| 8 | POST `/logout` | AuthController::logout | POST-only + logged-in session + CSRF |
| 9–10 | GET/POST `/account` | AccountController::index/update | customer; POST-only + CSRF |
| 11 | GET `/admin/users` | UserManagementController::index | admin + DB role/status recheck |
| 12 | POST `/admin/users/create-staff` | createStaff | admin + CSRF + validation |
| 13 | POST `/admin/users/update-staff` | updateStaff | admin + CSRF + validation |
| 14 | POST `/admin/users/status` | changeStatus | admin + CSRF + status policy |
| 15 | GET `/cart` | CartController::index | customer |
| 16–20 | POST `/cart/add`, `/cart/buy-now`, `/cart/update`, `/cart/remove`, `/cart/clear` | CartController matching action | customer + POST-only + CSRF |
| 21 | GET `/checkout` | CheckoutController::index | customer |
| 22 | POST `/checkout` | CheckoutController::placeOrder | customer + POST-only + CSRF + COD validation |
| 23 | GET `/checkout/success` | CheckoutController::success | customer + order ownership query |
| 24 | GET `/orders` | OrderController::index | customer |
| 25 | GET `/orders/{orderId}` | OrderController::show | customer + owner-scoped query |
| 26 | POST `/orders/{orderId}/cancel` | OrderController::cancel | customer + POST-only + CSRF + owner/status checks |
| 27 | GET `/about` | AboutController::index | public read |

Router returns 404 on unmatched route; code does not establish a separate 405 for a known path with wrong method. Admin controller re-reads own account role/status; customer actions generally trust session role/ID. Session revocation on customer account disable is not established. Recheck matrix whenever routes change.
