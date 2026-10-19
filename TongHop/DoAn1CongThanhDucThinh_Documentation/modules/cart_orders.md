# Module giỏ hàng, checkout và đơn hàng

Ngày 19-10-2026. Source: Cart/Checkout/Order controllers, models và views; bảng GioHang, ChiTietGioHang, DonHang, ChiTietDonHang, SanPham, LichSuKho.

## Cart

Controller yêu cầu customer, POST-only/CSRF cho mutation và dùng flash. `add`/`buyNow` validate product/quantity; update/remove/clear chuyển ID/số lượng tới model rồi redirect. Model `getOrCreateCart` lấy hoặc tạo giỏ; `getItems/findItems` join dòng cart với sản phẩm/chi tiết và tính/cập nhật tổng. `addItem` transaction, khóa cart/product/current line, kiểm tồn và insert/update. `updateItem` chặn quantity vượt tồn; remove/clear xóa các dòng thuộc đúng user.

## Checkout/Order

`CheckoutController::placeOrder` kiểm customer/POST/CSRF, validate thông tin giao và render lại lỗi hoặc gọi `Order::placeCodOrder`. Model transaction khóa cart/items, lấy giá/tồn, tính tổng authoritative, ghi đơn và dòng đơn, trừ tồn, ghi lịch sử kho, xóa dòng cart/reset tổng, commit; exception rollback. `success` chỉ truy vấn đơn của người dùng hiện hành.

`OrderController` liệt kê và xem đơn theo khách; `cancel` POST+CSRF, flash và redirect. `Order::cancelForCustomer` khóa order/status/details, hoàn tồn và ghi stock history rồi cập nhật trạng thái; điều kiện hủy là business rule trong model.

Transaction và row lock là bảo vệ nhất quán đáng kể; cần test deadlock/concurrency, hủy lặp và rollback. Tổng giỏ lưu trữ có thể lệch nếu ngoài luồng này có writer khác. `GiaoDichThanhToan` chưa thấy tham gia; COD là luồng có bằng chứng.
