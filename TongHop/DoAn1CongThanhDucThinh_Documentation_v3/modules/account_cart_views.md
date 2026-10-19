# View hồ sơ, auth, giỏ hàng và CSS

Ngày 19-10-2026. Đọc toàn văn: `app/views/account/index.php` (149 dòng), `auth/login.php` (51), `auth/register.php` (69), `cart/index.php` (332); CSS account/auth/cart lần lượt 196/205/347 dòng. Các template được include bởi `View`; variables đến từ controller.

## `account/index.php`

Tạo `$escape` bằng htmlspecialchars `(string)value, ENT_QUOTES, UTF-8`; đọc app config và base URL. Render flash, errors, profile username, form name/email/phone/address, CSRF, password fields và logout form. Các dynamic outputs được escape; textarea content cũng encode. Hidden CSRF escape attribute. Password fields không có value, đúng nguyên tắc không echo password. Phone input maxlength=20 trong HTML nhưng server cho chuẩn hóa tối đa 15; HTML chỉ là tiện ích, server validation là authority. Form update/logout POST.

## Auth views

`auth/login.php`: render success flag `_register_success` rồi unset trong view (side effect session), error/identity/CSRF/base URL đều htmlspecialchars UTF-8. Password không giữ lại.

`auth/register.php`: error, csrf, name, username, email, phone và link/action đều escaped; password/confirm không được echo. `required`, maxlength/minlength cải thiện client UX nhưng không thay server validation.

Cả hai view dựa `$baseUrl` do `View::render` scope cung cấp. CSS `auth.css` dùng `:has(#confirm_password)` để đổi kiểu register; trình duyệt không hỗ trợ `:has` có thể chỉ mất styling phân biệt, HTML vẫn render.

## `cart/index.php`

Đầu file lấy items default `[]`, total cast float, tạo `$csrf`, `$formatMoney`, `$escape`, config/baseUrl/escaped. Empty/non-empty branch. Non-empty loop tạo biến cho image/path, MaSP/TenSP/price/quantity/stock/line total, max quantity và sanitized DOM ID. Render image (fallback placeholder), details, update/remove forms, summary/checkout.

Encoding: text, image URL, product ID, CSRF, label/id, form action/baseUrl được escape HTML attribute/context. Numeric values (`max`, quantity, stock, count, formatted money) được cast từ PDO rows hoặc controller và in không escape; source values numeric theo schema INT/DECIMAL nhưng khuyến nghị cast/clamp tại sink để phòng dữ liệu ngoài giả định. Flash class chỉ chọn hai literal dựa so sánh exact type; không echo type tùy ý. Money format dùng number_format 0 decimal, dấu phân cách kiểu Việt; làm tròn số lẻ vì schema cho phép DECIMAL(18,2), cần xác nhận đây là quy tắc nghiệp vụ mong muốn.

Ảnh: chấp nhận URL http(s) từ DB, project-prefixed path, `/images/`, `public/`, hoặc relative path. Attribute được escape, nhưng URL ngoài có thể gửi request tới host thứ ba/track người xem; không phải XSS tự động. Nguồn dữ liệu `HinhAnh` và quyền quản trị sản phẩm cần được kiểm soát.

Form clear/remove có confirm JavaScript; server vẫn bắt buộc POST+CSRF/auth. `maxQuantity=max(1, stock)` cho phép UI value 1 khi tồn 0 để HTML min<=max hợp lệ; server model vẫn từ chối qua add/checkout stock checks. Empty cart không render checkout.

## CSS

`account.css`: layout/card/fields/notices/buttons; breakpoint 620px chuyển field/actions phù hợp mobile. `auth.css`: card/forms/feedback và breakpoint 600/480px; selector `:has` nêu trên. `cart.css`: cart two-column, product rows, summary/empty states; breakpoint 900/600px chuyển layout. CSS chỉ presentational, không là kiểm soát bảo mật.

## Kết quả rà output

Trong bốn view này, dynamic text/attribute đều qua htmlspecialchars helper hoặc explicit call ngoại trừ numeric/currency output được cast/format; flash type trở thành literal class; không thấy raw user string render trong ba auth/account templates hoặc cart. Đây là kết luận giới hạn bốn file, không đại diện toàn bộ 17 PHP views.
