# Đính chính tiến độ và cấu trúc fixture

Ngày phân tích: 2026-10-19. Tài liệu bổ sung này có hiệu lực cùng các file v4 trước đó và không ghi đè nội dung cũ.

## Đính chính về xác thực

`15_DOCUMENTATION_CROSSCHECK.md` nói cần xác nhận `session_regenerate_id` riêng. Đã xác nhận lời gọi `session_regenerate_id(true)` có trong `app/Controllers/AuthController.php:45` sau khi thông tin đăng nhập hợp lệ. Vì vậy yêu cầu session ID rotation sau login có bằng chứng trong source. Điều này không thay thế kiểm tra cờ cookie/session runtime.

## Cấu trúc `dulieuao.sql` đã xác nhận mà không lộ row values

File có năm bảng đích fixture:

| Bảng | Cách nạp | Số dòng được xác định tĩnh | Ghi chú |
|---|---|---:|---|
| `NguoiDung` | `INSERT ... VALUES` | 20 tuple | Không sao chép nội dung từng tuple. |
| `TaiKhoan` | `INSERT ... VALUES` | 20 tuple | Giá trị đăng nhập/hash không được trích vào tài liệu. |
| `DanhMuc` | `INSERT ... VALUES` | 10 tuple | Giá trị tên không trích. |
| `SanPham` | `INSERT ... VALUES` | 120 tuple | Giá trị sản phẩm/giá không trích. |
| `ChiTietSanPham` | `INSERT ... SELECT` từ `SanPham` | biểu thức tạo tối đa theo tập nguồn 120 sản phẩm | Không chạy SELECT/INSERT; số tuple đầu vào cho SELECT đến từ block fixture 120 dòng. |

Các dòng SELECT thống kê ở cuối fixture cũng không được thực thi. Các con số trên đếm cú pháp/biểu thức trong file, không phải kết quả kiểm kê database runtime.

## Đính chính phạm vi tài liệu LaTeX

- `LaTex_Document/Nhap/BanMoi.tex`: 992 dòng, đọc toàn văn; UC01–UC15 và 3 hình sơ đồ tổng quát.
- Template LaTeX: 14 file `.tex`, đọc toàn văn; `main.tex` 402 dòng. Nội dung một số chương còn placeholder/mẫu. Chương 4 có ví dụ ngành nhà hàng/F&B và không thể dùng làm bằng chứng kết quả T&T Computer.
- Tổng cộng: 15 file `.tex`, đọc đầy đủ. `rule.txt` đọc đủ 678 dòng.
- Các trang bìa/phê duyệt có thông tin định danh cá nhân; không đưa vào bộ tài liệu phân tích.

## Lưu ý trạng thái cũ

`10_ANALYSIS_COVERAGE.md` và `09_AUXILIARY_DOCUMENTS_AND_JS.md` được tạo trước khi hoàn tất lượt đọc LaTeX, nên phần “còn lại”/mức coverage trong đó đã cũ. Giữ nguyên chúng theo quy tắc không ghi đè; dùng file này và `15_DOCUMENTATION_CROSSCHECK.md` làm trạng thái mới hơn. Runtime, Apache/PHP/MySQL/session và browser behavior vẫn chưa xác minh.
