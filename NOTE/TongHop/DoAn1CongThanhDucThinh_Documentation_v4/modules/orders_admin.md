# Checkout, Order và UserManagement

Ngày 19-10-2026. Đọc toàn văn CheckoutController (206 dòng), OrderController (122), UserManagementController (203), Order model (375). Liên hệ v3 Account model.

## CheckoutController

`requireCustomer` lấy session user; kiểm array/MaND/role KhachHang, nếu sai redirect login. `verifyCsrf` cast token POST/session; rỗng/mismatch `hash_equals` →403.

`index`: yêu cầu customer, cấp CSRF nếu thiếu; Cart::findItems (không tạo cart) và Account::findCustomerProfile; dựng form mặc định name/phone từ profile rồi session, address profile, payment COD; render checkout.

`placeOrder`: POST-only; CSRF rồi auth. Thu form recipientName/Phone/address/paymentMethod. Name required/max100; phone chuẩn hóa bỏ whitespace/().-, yêu cầu optional + và 8–15 chữ số, max15 sau normalize; address required/max255; payment phải chính xác COD. Tải cart; rỗng thêm lỗi. Có errors → renderCheckout giữ dữ liệu, không gọi order. Nếu hợp lệ gọi Order::placeCodOrder. Thành công redirect 303 kèm encoded ID. PDO/Throwable log chi tiết và báo chung; RuntimeException render message nghiệp vụ.

`success`: auth; đọc GET id, rỗng→null, nếu có gọi owner-scoped findForCustomer; null→404; else render receipt.
`renderCheckout(cart,form,errors)`: truyền items/total/token/errors/form sang view.

Locals: `$user`, `$cart`, `$profile`, `$form`, `$errors`, `$normalizedPhone`, `$order`, `$exception`, `$orderId`, `$receipt`. Dữ liệu POST cast string; không nhận total/product prices từ client. RuntimeException có thể chứa tên sản phẩm từ DB, view escape cần thiết.

## OrderController

`requireCustomer`, `verifyCsrf`, `postOnly` như Cart; `flash` lưu `_order_flash`. `index`: lấy rồi unset flash; model findAllForCustomer theo MaND; view. `show(orderId)`: owner-scoped query; 404 nếu null; init CSRF; lấy/unset flash; truyền order/items/token. `cancel(orderId)`: POST→CSRF→customer; model cancel; success flash; RuntimeException hiện message nghiệp vụ; Throwable log+generic; 303 quay lại chi tiết với encoded ID.

## Order model

Property `private PDO $db`; constructor lấy Database connection.

- `placeCodOrder(customerId,recipientName,deliveryAddress,recipientPhone):array`: transaction; lock cart by MaND; no cart/empty items throw. Lock detail/product rows; quantity >=1; status null/blank được coi bán được, nếu có thì lowercase phải match allowlist; quantity không vượt stock. Tính COALESCE SUM giá × quantity trong SQL thành string `$total`. Sinh DH ID; insert header COD, pending, unpaid. Mỗi item conditional stock decrement với guard `SoLuongTon>=quantity`, `rowCount===1`; random detail ID, insert ChiTietDonHang giá snapshot; random history ID, insert LichSuKho số âm/XuatKho. Xóa cart details, reset total, commit; return MaDH/TongTien. Throwable rollback/rethrow.
- `findForCustomer(orderId,customerId):?array`: header query scoped cả ID và owner; false→null; nếu có query details join SanPham, calculate line total từ snapshot DonGia; return `{order,items}`. Tên sản phẩm lấy current SanPham, không snapshot.
- `findAllForCustomer(customerId):array`: SELECT header fields + correlated COUNT lines; owner scope, newest-first; no pagination.
- `cancelForCustomer(orderId,customerId):void`: transaction; owner-scoped `FOR UPDATE`; missing/already canceled/non-pending throw; lock order lines; restore each stock and log positive quantity/NhapKho; conditional status update only pending+owner; rowCount must one; commit; Throwable rollback/rethrow.

Transaction protects order header/detail/stock/history/cart atomically under InnoDB. Potential review: cancel line empty list is not rejected before updating status; FK/order business invariant normally means order has lines, but verify data integrity. Decrement conditional + lock is defense in depth. Deadlocks can still occur; callers currently show generic failure.

## UserManagementController

`index`: admin gate (session role, then DB findForManagement self and require QuanTriVien+HoatDong), init CSRF, read GET q/edit, editing only if role NhanVien; search and pass flash strings, then unset flash keys after view.

`createStaff`: admin, CSRF, `staffData`, `validateStaff`, password length 8..255, username/email checks, then Account::createStaff; PDO error log/generic; `fail/succeed` set session flash and redirect.
`updateStaff`: admin+CSRF; account_id nonempty and staffData validation, uniqueness checks excluding current ID; model returns false for no staff row; success/fail; catches PDOException only.
`changeStatus`: admin+CSRF; check ID and status allowlist; cannot change own session MaTK; Account::changeStatus returns `last_admin`, `missing`, `updated`; map to flash.
`requireAdministrator`: non-admin returns 401 if guest/redirect login, else 403 exit; then database recheck of current account and active status, clears stale session if invalid.
`validCsrf`: POST token nonempty and hash_equals. `staffData`: trim four text fields. `validateStaff`: required name/username/email; max lengths; username ASCII regex; filter_var email; returns error string or ''. Phone length only, no numeric normalization. `fail/succeed`: session keys and redirect.

Notable side effects/edge cases: flash unset occurs after view include; if rendering throws, unset may not run. The admin status method trusts HTTP route POST but does not separately call `postOnly`; router exposes only POST, so route mapping provides method gate. create/update/status all CSRF check. Admin self role/status is refreshed each management request.
