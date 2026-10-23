# Ma trận encoding dữ liệu động trong view

Nguồn: v4 `14_VIEW_ESCAPING_MATRIX.md`, `07_REMAINING_VIEWS_AND_ESCAPING.md`, v3 view notes. Review tĩnh, không xác nhận exploit/runtime. `htmlspecialchars(ENT_QUOTES, UTF-8)` phù hợp HTML text/quoted attribute; URL scheme policy và JavaScript/CSS context cần xử lý riêng.

| View | Dữ liệu động chính | Kiểm soát được báo cáo | Ghi chú |
|---|---|---|---|
| `account/index.php` | profile, form, errors, flash, CSRF, base URL | helper escape cho text/attribute/URL | password fields không echo; textarea escaped |
| `admin/users.php` | accounts, editing, query, role/status, flash | escape text/value/href/action; MaTK rawurlencode rồi escape | class/status theo nhánh/tập giá trị |
| `auth/login.php` | error, CSRF, identity, flash, base URL | htmlspecialchars trong text/attribute | password không giữ lại |
| `auth/register.php` | error, CSRF, name/username/email/phone, URLs | htmlspecialchars | password/confirm không echo |
| `cart/index.php` | item/name/image/quantity/price/CSRF/flash/URLs | helper escape; số cast/format | URL ảnh HTML-escaped nhưng không allowlist host/scheme; tiền format 0 decimals |
| `checkout/index.php` | recipient, errors, items, total, CSRF/URLs | escape strings; quantity int, money format | payment value so sánh literal; không JS interpolation |
| `checkout/success.php` | order/items/status/date/link | escape, integer cast, money/date format, ID rawurlencode | item rows được trình bày hai lần trong markup |
| `errors/404.php` | title/message/base URL | htmlspecialchars text và URL attr | template không tự chứng minh status HTTP |
| `home/index.php` | featured products, category/name/ID/base URL | escape; category ID rawurlencode | product card ở partial riêng |
| `layouts/customer.php` | title/query/session/CSRF/URLs/content | escape dynamic values; role dùng điều kiện literal | `$content` echo raw; view child phải an toàn |
| `orders/index.php` | orders/status/date/count/payment/total/flash | escape, cast, date/money formatter, ID rawurlencode | status class literal theo branch |
| `orders/show.php` | order/items/status/flash/CSRF/URLs | escape, cast, format, ID rawurlencode | cancel dialog JS không thay server check |
| `pages/about.php` | base URL | closure escape URL | phần còn lại tĩnh theo báo cáo |
| `products/_card.php` | name/category/id/image/stock/price/CSRF/URL | escape HTML; ID rawurlencode; number format | external image host có thể tạo tracking request |
| `products/index.php` | query/category/sort/categories/products/count | htmlspecialchars; selected literal | không có page control; backend không pagination |
| `products/not-found.php` | base URL | escape href | phần còn lại tĩnh |
| `products/show.php` | product descriptions/spec/brand/stock/image/CSRF | escape; description escape trước `nl2br`; numeric cast | URL image escaping không đồng nghĩa scheme allowlist |

## Kết luận và giới hạn

Báo cáo v4 nói đã lập ma trận cho 17/17 view. Không phát hiện `srcset`; dynamic `src` ở product card/detail và cart. Inline handler được ghi nhận là literal tĩnh. Layout `$content` là trust boundary. Đây là nhận định tài liệu v4, không được kiểm chứng với file view gốc vì source code không nằm trong input. Xem thêm [phân tích bảo mật](security_analysis.md) và [UI/JS](../design_and_theory/quality_performance_ui.md).
