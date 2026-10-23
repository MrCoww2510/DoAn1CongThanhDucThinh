# Kiểm tra chéo tài liệu và tiến độ bổ sung

Ngày phân tích: 2026-10-19. Đây là bản bổ sung trạng thái, không thay thế/ghi đè tài liệu v4 đã tồn tại.

## Rà tài liệu yêu cầu và LaTeX

- Đọc toàn văn `rule.txt` (678 dòng thực tế) và `LaTex_Document/Nhap/BanMoi.tex` (992 dòng thực tế; 15 UC và 3 hình tổng quát).
- Đọc toàn văn 14 file `.tex` trong template LaTeX; `main.tex` khai báo gói và input chương, phần đầu/cuối và danh mục tham khảo; các chương 1–3 còn template placeholder; Chương 4 chứa nội dung mẫu thuộc bài toán nhà hàng/F&B, không khớp dự án cửa hàng máy tính. Không dùng nội dung template làm kết quả dự án.
- Các file trang bìa/đồng thuận/cam đoan/cảm ơn có thông tin định danh cá nhân. Tài liệu này cố ý không sao chép tên, mã số sinh viên hay dữ liệu cá nhân.
- `BanMoi.tex` mô tả UC01–UC15; đối chiếu với source được lập ở [ma trận yêu cầu](12_REQUIREMENTS_TRACEABILITY.md). Các sơ đồ class/database/use-case là hình tài liệu, không đồng nhất với cấu trúc code/schema thực tế.
- Mojibake hiển thị trong PowerShell có thể do encoding terminal; không kết luận lỗi bytes của file nếu chưa kiểm tra encoding bằng công cụ phù hợp.

## Đếm symbol trong source PHP

Dò 38 file PHP bằng pattern khai báo class/function, không thực thi PHP:

- **17 class** trong `app/Core`, `app/Models`, `app/Controllers`.
- **90 function-like declarations** tổng cộng: **89 method/class member** và **1 function JavaScript nhúng** `scrollToTop()` trong `app/views/layouts/customer.php:222`.
- Không thấy top-level named PHP function riêng ngoài phần `<script>` trong view. Đây là đếm cú pháp khai báo trong snapshot; closure/arrow function và callback không được tính là named function.
- **60 call site** PDO `prepare()`/`query()` ở 4 model, được ID hóa tại [SQL callsite ledger](13_SQL_CALLSITE_LEDGER.md). Đây là call site, không phải số literal SQL duy nhất.
- Route inventory có **27 khai báo**: 13 GET và 14 POST (xem `routes/web.php:18-57`).

## Link và bằng chứng tài liệu

Các file bổ sung mới tham chiếu module file hiện có bằng đường dẫn tương đối. Trong lần kiểm tra này không dựng lại các sơ đồ cũ và không ghi vào folder diagrams; v4 hiện là gói tiếp nối và không chứa đầy đủ sơ đồ mục tiêu. README v4 là entrypoint ngắn; các tài liệu trước đó vẫn là bộ phân tích các nhóm account/cart/auth. Đường dẫn qua phiên bản cần giữ nguyên để không nhầm v4 là toàn bộ bộ tài liệu duy nhất.

Danh sách cần đối chiếu cuối nếu đóng gói một bản hoàn chỉnh: links tương đối xuyên v1–v4; sơ đồ Mermaid trong bản gốc; tất cả dòng dẫn chứng; sự khớp giữa tổng method 41 ở cụm cũ và 89 toàn dự án; các số liệu table/route/SQL. Không sửa tài liệu hiện hữu theo quy tắc không ghi đè; các khác biệt được báo cáo ở đây.

## Giới hạn còn lại

- Runtime Apache/PHP/MySQL/session, browser behavior, tương phản và accessibility chưa xác minh có chủ đích.
- `dulieuao.sql` được nhận diện cấu trúc fixture, không đưa row values vào báo cáo; chưa lập thống kê số tuple an toàn cho từng INSERT.
- Sơ đồ database Mermaid trong bộ tài liệu hiện có cần kiểm chéo lại từng cạnh với `database.sql` nếu phát hành bản chốt.
- Không có checksum baseline trước phân tích để đối chiếu SHA-256. `git status --short` trống ở lần kiểm tra gần nhất là bằng chứng working tree Git sạch tại thời điểm đó, không thay thế checksum đầy đủ.
