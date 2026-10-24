# Phát hiện và tiến độ sau cụm account/cart/auth

Ngày 19-10-2026. V3 đã đọc toàn văn năm file PHP: `app/Models/Account.php`, `app/Models/Cart.php`, `app/Controllers/CartController.php`, `AuthController.php`, `AccountController.php`; bốn view liên quan và CSS account/auth/cart. Phạm vi file này đọc được chia từng đoạn có line number.

## Phát hiện kỹ thuật có bằng chứng

| Ưu tiên | Phát hiện | Bằng chứng / ý nghĩa |
|---|---|---|
| Medium | Email chưa có unique constraint ở schema nhưng có precheck trong app | `database.sql` NguoiDung.Email nullable không UNIQUE; Account model check-then-write. Race có thể tạo email trùng. |
| Medium | `Cart::updateItem` đọc tồn rồi update không transaction/lock | Cart.php 287–318; tồn có thể thay đổi giữa check và write. Checkout kiểm tra lại nên không đủ hàng vẫn bị chặn ở bước đặt đơn, nhưng cart state tạm có thể vượt tồn. |
| Low | GET cart có side effect ghi | Controller index gọi `getItems`; model `getItems` gọi getOrCreate và UPDATE TongTien. GET tạo row/ghi tổng. |
| Low | `Cart::clear` hai write không atomic | Cart.php 342–352; nếu UPDATE tổng lỗi sau DELETE thì tổng lưu có thể lệch. |
| Low | Cart total/format tiền dùng float và hiển thị 0 decimal | Cart.php 95–96; view cart 18. Schema DECIMAL(18,2). Cần chốt rounding/currency rule. |
| Informational | `findItems` không tạo cart, trả null ID/empty items/0 | Cart.php 227–241; khác với getItems, hữu ích cho checkout. |
| Informational | Đăng ký email duplicate query nhưng schema email không unique | Check ứng dụng không thay constraint. Username có UNIQUE. |
| Informational | Auth/account/cart views được rà có escape HTML cho text/attribute | Không khái quát sang các view chưa rà. |

## Coverage của cụm

- 5 source PHP đọc đầy đủ; method được đặc tả: Account 13, Cart 8 (kể constructor), CartController 10, AuthController 4, AccountController 6 = 41 method/constructor.
- 4 view PHP đọc đầy đủ: account, auth/login, auth/register, cart.
- 3 CSS đọc toàn file, selector/breakpoints được nhận diện; 9 CSS khác chưa rà.
- SQL gắn với Account/Cart được nhóm theo query purpose và file line range, chưa xuất danh mục từng literal statement ID riêng biệt.

## Còn lại

Rà các cụm PHP khác (Product, Order, Checkout, Admin, core, controllers, views còn lại), 9 CSS, 15 LaTeX, 3 SQL đầy đủ (đặc biệt fixture), tài liệu phụ trợ; thực hiện matrix output escaping toàn bộ view. Runtime Apache/PHP/MySQL/session không xác minh. Không chạy tests/SQL/scripts.
