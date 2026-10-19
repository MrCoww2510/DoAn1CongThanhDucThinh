# Home và product views; output encoding

Ngày 19-10-2026. Đọc toàn văn năm PHP views: `home/index.php` (224 dòng), `products/index.php` (125), `products/show.php` (136), `products/not-found.php` (8), `products/_card.php` (149). Đối chiếu dynamic output trực tiếp.

## Home

`home/index.php` default `$featuredProducts/$categories` về array; dựng hero, liên kết, category grid, featured cards và banner. URL base được `htmlspecialchars`; category ID trước `rawurlencode` rồi escape toàn URL; category name escape. Product card include trong loop với `$product` từ model. Empty arrays có empty-state. Phần lớn nội dung còn lại literal HTML/text/SVG.

## Products index

Default sort. GET form có `q`, category options, sort options tĩnh, reset link; keyword/category label được escape. selected chỉ in literal attribute dựa so sánh. Result count dùng `count($products)`; empty/list branch. Mỗi row include `_card.php`. Không có điều khiển page vì backend cũng không pagination.

## Card

Tính image path giống cart; stock int; lấy CSRF session default empty; product URL dùng rawurlencode MaSP; add/buy-now URLs nội bộ; `$e` htmlspecialchars UTF-8. Escape image, alt/name/category/id/url/token. Price format float 0 decimals; stock int; literal buttons. Hai POST forms gửi token/ID/quantity=1. CSRF token chưa được khởi tạo riêng bởi HomeController/ProductController cho visitor guest; nếu session chưa có token, form có value rỗng, POST cart controller xác thực CSRF trước yêu cầu customer và trả 403. Đây là lỗi luồng UX có thể thấy theo source, không phải bypass CSRF. Người dùng đăng nhập đã nhận token từ login.

## Detail

Chuẩn hóa image URL (http(s), project prefix, `/images/`, `public/`, relative); escaping khi đưa vào src. Escape tên/category/ID/brand/type, URL action, CSRF, mô tả/thông số (`htmlspecialchars` rồi `nl2br`). Numeric price cast float, stock cast int. Stock >0 mới hiển thị add form; max=stock; stock<=0 disabled. Token có thể rỗng nếu session chưa init, cùng vấn đề card. Description null/empty fallback. `$image`, `$stock`, `$csrfToken` locals.

## Not-found

Nội dung tĩnh; link baseUrl được escape.

## Escaping matrix trong cụm

| View | Text output | Attribute/URL | Numeric output | Ghi chú |
|---|---|---|---|---|
| home/index | TenDM htmlspecialchars | href encode ID rồi escape | — | product card dùng partial |
| products/index | keyword/category name escape | action/reset/option values escape | `count` integer | selected literals |
| products/_card | product/category/alt escape | URLs, image, ID, CSRF escape | price float format, stock int | CSRF có thể blank guest |
| products/show | fields/long text escaped | src/action/token/IDs escaped | price float, stock int | `nl2br` chạy sau escape |
| products/not-found | tĩnh | link escape | — | — |

Escaping HTML an toàn cho text/quoted attributes; URL escaping không xác nhận scheme an toàn nếu data source đổi, nhưng image logic chỉ giữ http(s) hoặc tạo local path. `HinhAnh` external host có thể theo dõi người xem. Numeric cast giảm injection nhưng không làm round policy đúng nghiệp vụ.
