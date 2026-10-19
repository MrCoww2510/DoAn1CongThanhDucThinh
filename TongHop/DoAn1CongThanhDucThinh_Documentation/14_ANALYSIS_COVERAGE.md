# 14 — Độ bao phủ và trạng thái

Ngày 19-10-2026. Kiểm kê read-only ngoài `.git`: 232 file; SHA-256 đã tính cho 232/232 trước khi soạn. `git status --short` ban đầu không có output. Đối chiếu cuối sẽ được báo trong hội thoại; hash không lưu vào project.

## Số lượng theo loại

PHP 38; CSS 12; JS 2; SQL 3; JPG 125; PNG 5; TEX 15; PDF 2; TXT 4; XML 1; ZIP 1; AUX 2; BBL 1; BCF 1; FDB_LATEXMK 2; FLS 2; LOF 1; LOG 2; LOT 1; OUT 2; TOC 1; SVG 1; HTACCESS 2; GITKEEP 1; GZ 2; PY 1.

## Trạng thái

- PHP: đã rà để dựng kiến trúc/method inventory; phần đọc tổng hợp bị cắt bởi giới hạn output nên không thể khẳng định mọi file đã đọc line-by-line. Ứng dụng code được mô tả ở mức đủ mục đích, nhưng cần phiên rà module chia trang để hoàn tất từng method/biến.
- CSS 12: đọc một phần. JS 2: đọc đủ.
- SQL 3: schema chính và migration đọc; fixture `dulieuao.sql` chưa đọc nội dung đầy đủ.
- `AGENTS.md`, KienThuc/rule và 15 TEX: một phần. Binary JPG/PNG/PDF/ZIP/GZ/SVG và LaTeX artifacts: metadata, không đọc nội dung đầy đủ.
- `.git` internals, database runtime, session/log/upload: bỏ qua có chủ đích (không phải source cần thiết/có thể nhạy cảm).

Tổng module mô tả: core, auth/account, catalog, cart/orders, admin, presentation. Lớp inventory: 4 core + 9 controller + 4 model = 17. Method inventory khoảng 65; số SQL statement tuyệt đối chưa thống kê chính xác do output công cụ bị cắt. Không tuyên bố đã đặc tả mọi local variable/mọi nhánh chi tiết.

## Giới hạn và tiếp tục

Không chạy PHP lint/test, app hoặc SQL; không truy cập DB/XAMPP config. Các sơ đồ và nhận định chưa xác minh được ghi riêng. Số dòng thay đổi khi source được cập nhật.
