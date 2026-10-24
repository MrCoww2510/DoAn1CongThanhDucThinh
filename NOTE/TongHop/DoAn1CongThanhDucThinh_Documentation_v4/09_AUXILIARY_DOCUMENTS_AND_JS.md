# JavaScript và tài liệu phụ trợ

- Ngày phân tích: 2026-10-19.
- Đọc toàn bộ 2 file JavaScript; LaTeX được kiểm kê và đọc outline/heading, chưa đọc toàn bộ nội dung.

## JavaScript

### `public/js/nav.js`

Đợi `DOMContentLoaded`, tìm `.mobile-menu-toggle` và `#mainMenu`; chỉ khi cả hai tồn tại mới đăng ký click để toggle class `active` (dòng 1–13). Không có request mạng, dữ liệu người dùng, hay thao tác lưu trữ. Tác dụng phụ là đổi class DOM. Menu phụ thuộc CSS xử lý `.active`; cần kiểm tra bàn phím và cập nhật `aria-expanded` đồng bộ để hỗ trợ accessibility.

### `public/js/order-cancel-dialog.js`

IIFE lấy dialog/form/nút theo data attribute, kiểm tra đúng loại DOM element và dừng nếu markup không đủ (dòng 1–16). Nút mở gọi `showModal`; nút đóng gọi `close` rồi focus nút mở; xác nhận đóng dialog và gọi `form.requestSubmit` (18–30); sự kiện `close` trả focus về nút mở (32–34). JavaScript chỉ điều khiển giao diện; endpoint server vẫn phải xác minh quyền, CSRF và điều kiện hủy. Tính tương thích `HTMLDialogElement`/`requestSubmit` cần được kiểm tra theo browser mục tiêu; chưa runtime test.

## Tài liệu phụ trợ và bản thiết kế

- `KienThuc.txt` (đọc đủ 161 dòng): tài liệu hướng dẫn lần theo request, route, module, luồng nghiệp vụ và khái niệm PHP/MySQL. Nội dung hữu ích làm chỉ dẫn học tập nhưng vẫn là tài liệu thứ cấp; khi khác biệt, source code được ưu tiên.
- `rule.txt` (484 dòng): prompt/đặc tả yêu cầu xây dựng ứng dụng, có kiến trúc mong muốn, UI, use case, nghiệp vụ, database, bảo mật và nghiệm thu. Đây là yêu cầu/định hướng, không phải bằng chứng tính năng đã triển khai. Đối chiếu route/source cho thấy phạm vi thực tế hẹp hơn một số nghiệp vụ quản trị, kho, báo cáo, thanh toán được nêu trong tài liệu.
- `LaTex_Document/Nhap/codeumlClass.txt` (186 dòng) và `codeumldatabase.txt` (147 dòng): PlantUML dạng text cho class/database; nội dung thể hiện mô hình dự kiến. Không dùng chúng thay cho schema hoặc class PHP thực tế khi xác nhận quan hệ.
- `LaTex_Document/Nhap/BanMoi.tex` (580 dòng): tài liệu use case, có đặc tả UC01–UC15 cùng luồng nghiệp vụ, class/database diagrams. Đây là đặc tả thiết kế; các use case quản lý kho/thanh toán/thống kê cần đối chiếu khả năng hiện thực trong source.
- `LaTex_Document/DATN__Template_LaTexStudio/*.tex` (14 file): template báo cáo gồm bìa, phần mở đầu, chương 0–4, phụ lục và danh mục. Outline đã nhận diện qua các lệnh `\\input`, `\\chapter`, `\\section`; phần thân chưa được đọc đủ để audit nội dung.
- Có PDF, ảnh, ZIP và các artifact biên dịch LaTeX (`.aux`, `.log`, `.synctex.gz`, `.fls`, v.v.). Không giải nén ZIP hay trích xuất nội dung PDF trong lượt này; chỉ xem metadata/đuôi file, vì mục tiêu ưu tiên source có thể kiểm chứng.

## Đối chiếu đặc tả với triển khai

Không coi nhãn “MVC” trong prompt/tài liệu phụ trợ là bằng chứng tự thân. Source có front controller, router, controller, model, view riêng nên có phân tách theo vai trò; tuy vậy cần đọc [đặc tả kiến trúc](modules/core.md) và các tài liệu module để thấy đường đi thật, thay vì giả định MVC textbook. Tương tự, các UC thanh toán online, quản lý kho độc lập, quản lý sản phẩm/danh mục admin và báo cáo trong tài liệu chưa được xác nhận là route/flow chạy được.
