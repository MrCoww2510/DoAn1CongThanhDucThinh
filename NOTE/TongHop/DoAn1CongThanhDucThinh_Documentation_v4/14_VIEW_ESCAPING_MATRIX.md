# Ma trận dữ liệu động và output escaping của 17 view

Ngày phân tích: 2026-10-19. Mỗi dòng dưới đây là view PHP. `htmlspecialchars` hoặc helper tương đương áp dụng cho HTML text/attribute; cast/format chỉ được coi là an toàn phù hợp khi dữ liệu thực sự là số. Đây là đánh giá tĩnh, không runtime test.

| View | Nguồn dữ liệu động chính | Context / encoder thấy trong code | Ghi chú/rủi ro |
|---|---|---|---|
| `account/index.php` | `$profile`, `$form`, `$errors`, `$flash`, `$csrfToken`, `$baseUrl` | helper `$escape` trong text/attribute/URL; CSRF cũng escape | Email/phone/address được trình bày trong input/textarea; không đưa vào JS. |
| `admin/users.php` | `$accounts`, `$editing`, `$query`, errors/success, role/status | `$escape` trong HTML text, value, href/action; MaTK URL query `rawurlencode` rồi escape | `statusClass`/nhánh role nên vẫn lấy từ tập giá trị code. |
| `auth/login.php` | `$error`, `$csrfToken`, `$identity`, register flash, base URL | `htmlspecialchars(... ENT_QUOTES, UTF-8)` trong text/attribute | Password input không được echo lại. |
| `auth/register.php` | `$error`, `$csrfToken`, name/username/email/phone, base URL | `htmlspecialchars` trong text/attribute | Trường lỗi được escape; không echo mật khẩu. |
| `cart/index.php` | cart items, names, image, quantity, price, csrf, flash, URLs | `$escape`/`$baseUrlEscaped`; quantity cast int, giá format float; image escaped | URL ảnh chỉ HTML-escape, không thấy scheme allowlist. Có inline `onsubmit` nhưng literal tĩnh. |
| `checkout/index.php` | form recipient data/payment, errors, cart items/total, csrf, URLs | `$escape` cho text/input/textarea/URL; quantity int, tiền format | Payment method phản ánh literal/so sánh; không có nội suy JS. |
| `checkout/success.php` | order/header/items/status/date, links | `$escape`, int cast, `$formatMoney`; order ID `rawurlencode` trong path | Danh sách mặt hàng được hiển thị lặp ở hai cấu trúc (redundancy, không phải XSS). |
| `errors/404.php` | `$title`, `$message`, `$baseUrl` | `htmlspecialchars(ENT_QUOTES, UTF-8)` trong text và URL attr | Status HTTP không thể kết luận từ template. |
| `home/index.php` | `$featuredProducts`, `$categories`, tên/danh mục/ID, `$baseUrl` | `htmlspecialchars`; category ID `rawurlencode`; card render riêng | Kiểm tra tiếp `_card.php` cho từng product. |
| `layouts/customer.php` | page title, GET q, session user/greeting/role, CSRF, base URL, `$content` | `htmlspecialchars` cho title/query/session/URL/CSRF; role dùng điều kiện literal | `$content` echo raw ở dòng 151 là HTML render của view con: cần bảo đảm từng view con escape mọi dữ liệu. Inline JS không nội suy dữ liệu. |
| `orders/index.php` | orders/status/date/quantity/payment/total/flash | `$escape`, cast int, date format, `$formatMoney`, ID `rawurlencode` | `statusClass` xuất theo nhánh code với class literal. |
| `orders/show.php` | order fields/items/status/flash/cancel token, URL | `$escape`, int, date format, tiền formatter, ID `rawurlencode` | Nhánh cancel dựa status; JS external deferred. |
| `pages/about.php` | `$baseUrl` | closure `$escape` trên URL href/style | Phần còn lại là literal HTML; không có DB dynamic. |
| `products/_card.php` | product name/category/id/image/stock/price, CSRF, URL | `$e` trong text/attributes; `rawurlencode` product ID; stock int, price number format | `HinhAnh` được HTML-escape nhưng không thấy giới hạn scheme/host; khả năng URL ngoài ngoài ý muốn cần policy xác minh. |
| `products/index.php` | q/category/sort/categories/products/count | `htmlspecialchars`; selected condition xuất literal; product chuyển `_card` | Chưa thấy srcset/inline script. |
| `products/not-found.php` | `$baseUrl` | `htmlspecialchars` trong href | Nội dung còn lại tĩnh. |
| `products/show.php` | product name/description/spec/brand/stock/image/csrf | `htmlspecialchars`; mô tả/spec escape trước `nl2br`; số cast; URL base escape | URL ảnh chỉ escape; cần policy scheme/host nếu dữ liệu không đáng tin cậy. |

## Kiểm kê URL, ảnh và script trong toàn bộ view

- Không phát hiện thuộc tính `srcset` trong `app/views` qua tìm kiếm tĩnh.
- Dynamic `src` được thấy ở `products/_card.php`, `products/show.php`, `cart/index.php`; text ảnh đã escape nhưng không thấy allowlist scheme/path.
- Dynamic href/action chủ yếu ghép từ `base_url` cấu hình rồi escape HTML; tham số ID được `rawurlencode` ở một số link order/admin/product. Escaping HTML không thay thế xác thực scheme/URL policy.
- Inline handlers chỉ thấy trong cart confirm và layout scroll-to-top; code là literal tĩnh, không chèn dữ liệu người dùng.
- `orders/show.php` nạp `order-cancel-dialog.js` external; script này submit form, server mới là nơi kiểm soát quyền/CSRF.
- `customer.php` có `$content` raw là điểm tin cậy xuyên suốt: dữ liệu động an toàn hay không phụ thuộc encoding ở view child.

Đây là review source snapshot, không chứng minh exploit đang khai thác được. Một URL ảnh có scheme không mong muốn là rủi ro do thiếu policy allowlist, cần xem nguồn kiểm soát cột ảnh và cách browser xử lý trong môi trường mục tiêu.
