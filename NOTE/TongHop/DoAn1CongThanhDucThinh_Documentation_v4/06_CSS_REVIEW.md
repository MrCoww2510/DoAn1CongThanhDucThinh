# Rà soát CSS

- Ngày phân tích: 2026-10-19.
- Phạm vi: 12 file `public/css/*.css`, đọc toàn bộ; đánh giá tĩnh, không chạy trình duyệt.
- Dòng là vị trí tại snapshot phân tích và có thể đổi khi source cập nhật.

## Danh mục và phạm vi giao diện

| File | Vai trò theo selector/nội dung | Breakpoint hoặc ghi chú tĩnh |
|---|---|---|
| `base.css` | reset, typography, container, nút/utility và nền tảng chung | media tại khoảng dòng 27, 207 |
| `header.css` | header, tìm kiếm, tài khoản, điều hướng và menu di động | nhiều media, khoảng 146, 263, 270, 312, 408, 416 |
| `home.css` | hero, khu vực nội dung trang chủ, thẻ và bố cục | media khoảng 566, 586, 619, 694 |
| `products.css` | danh sách, bộ lọc, thẻ và chi tiết sản phẩm | media khoảng 362, 376, 441, 693, 705, 711, 728, 742 |
| `about.css` | các section giới thiệu, giá trị và CTA | media khoảng 496, 531 |
| `users.css` | trang quản trị người dùng | không thấy `@media` trong file |
| `account.css` | hồ sơ/tài khoản | media khoảng 176 |
| `auth.css` | đăng nhập/đăng ký và form | media khoảng 24, 61, 72, 94, 131, 186, 188, 193; có selector `:has()` |
| `cart.css` | giỏ hàng và dòng sản phẩm | media khoảng 304, 314 |
| `checkout.css` | biểu mẫu/khối checkout | media khoảng 305 |
| `orders.css` | danh sách/chi tiết đơn | media khoảng 378 |
| `errors404.css` | trang lỗi 404 | không thấy `@media` |

Các số dòng CSS là mốc tra cứu sơ bộ; nên dùng từ khóa selector nếu source thay đổi. Các file được tải trong `app/views/layouts/customer.php` không bao gồm `account.css`, `cart.css`, `checkout.css`, `orders.css` và `errors404.css`; các view/module tương ứng có thể nạp riêng hoặc cần kiểm tra lại liên kết giao diện. Không suy ra stylesheet thực sự được tải trên từng route nếu chưa đối chiếu render/runtime.

## Phát hiện về cascade và responsive

- `base.css` định nghĩa font/kiểu nền nhưng selector wildcard áp dụng font về sau; cần kiểm tra cascade khi muốn đổi font toàn cục.
- `header.css` có nhiều khối `.nav-inner` và quy tắc `.header-account` chồng lấn theo thứ tự; đây là nợ cascade có thể làm thay đổi style ngoài dự kiến.
- `auth.css` sử dụng `:has(#confirm_password)`. Trình duyệt không hỗ trợ selector này có thể bỏ quy tắc style liên quan, không làm hỏng server-side validation.
- Một số trang không có breakpoint CSS thấy được (`users.css`, `errors404.css`); điều đó chưa chứng minh giao diện không responsive vì có thể dùng layout co giãn từ rule chung.
- Chưa xác minh tương phản màu, focus-visible, điều hướng bàn phím, zoom, layout thực tế hay hỗ trợ trình duyệt. Cần kiểm tra trực quan và accessibility riêng nếu được yêu cầu.

## Đánh giá

CSS được chia theo nhóm giao diện tương đối rõ, thuận tiện tìm quy tắc theo màn hình. Hạn chế chính là stylesheet theo trang có thể không được liên kết từ layout chung, selector trùng/chồng lấn và chưa có bằng chứng kiểm thử hiển thị. Đề xuất: lập bảng route → stylesheet, giảm trùng selector, tập trung breakpoint/token chung và kiểm tra focus/contrast ở trình duyệt; đây là đề xuất, không thay đổi source.
