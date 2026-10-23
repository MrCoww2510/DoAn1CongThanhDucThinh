# Coverage snapshot mới nhất

Ngày phân tích: 2026-10-19. Tài liệu này là trạng thái mới nhất, thay cho số liệu cũ trong `10_ANALYSIS_COVERAGE.md`; file cũ được giữ nguyên theo quy tắc không ghi đè.

## Đếm file

Kiểm kê dùng `rg --files --hidden -g '!.git/**'`: **232 file** ngoài nội dung nội bộ `.git`.

| Loại | Phát hiện | Đọc nội dung |
|---|---:|---|
| PHP | 38 | 38 đọc đầy đủ |
| CSS | 12 | 12 đọc đầy đủ |
| JavaScript | 2 | 2 đọc đầy đủ |
| SQL | 3 | 3 đọc cú pháp/schema; fixture values không được đưa vào báo cáo |
| LaTeX `.tex` | 15 | 15 đọc đầy đủ (992 dòng use-case + 14 file template) |
| TXT | 4 | 4 đọc đầy đủ: `KienThuc.txt`, `rule.txt`, `codeumlClass.txt`, `codeumldatabase.txt` |
| `AGENTS.md` | 1 | 195 dòng đọc đầy đủ; nội dung safety instructions cũng được cung cấp trong hội thoại |
| `.htaccess` | 2 | root và `public/.htaccess` đọc đầy đủ |
| Còn lại | 155 | Chủ yếu hình ảnh, PDF, ZIP và artifact build; chỉ kiểm kê tên/metadata, không mở/trích xuất nội dung. `storage/logs/.gitkeep` là marker rỗng. |

Phép cộng 232: 38+12+2+3+15+4+1+2+155. File count mặc định từ `rg --files` bỏ qua dotfiles trả 229; khi bật `--hidden` có hai `.htaccess` và `.gitkeep`, tổng là 232.

## Trạng thái phân tích kỹ thuật

- 17 class, 89 method, 1 function JavaScript inline trong view.
- 27 route declaration (13 GET, 14 POST).
- 60 PDO call sites; 49 direct SQL literal templates sau chuẩn hóa và 6 call sites lấy SQL từ biến `$sql`; xem [chi tiết đếm SQL](20_SQL_TEMPLATE_COUNT.md) và [ledger](13_SQL_CALLSITE_LEDGER.md).
- Ma trận output động cho 17 view tại [14_VIEW_ESCAPING_MATRIX.md](14_VIEW_ESCAPING_MATRIX.md).
- 4 sơ đồ Mermaid mới ở `diagrams/`; xem [đính chính chỉ mục sơ đồ](18_DIAGRAM_INDEX_CORRIGENDUM.md) và [ghi chú cardinality](19_DIAGRAM_CARDINALITY_NOTES.md).
- Tài liệu yêu cầu UC01–UC15 đối chiếu tại [12_REQUIREMENTS_TRACEABILITY.md](12_REQUIREMENTS_TRACEABILITY.md).
- 18 tài liệu Markdown v4 hiện tại? Không tính lại trong nội dung này; số file có thể thay đổi khi tài liệu được bổ sung. Dùng filesystem inventory của thư mục output để kiểm tra.

## Giới hạn còn lại

- Không chạy ứng dụng, PHP, SQL, migration, tests, hoặc browser; runtime Apache/PHP/MySQL/session chưa xác minh.
- Không có SHA-256 baseline trước phân tích để đối chiếu sau phân tích. `git status --short` trống ở lần kiểm tra cuối; đây không phải bằng chứng checksum.
- Các ảnh/PDF/ZIP/artifact không được nội dung hóa; nếu cần xác minh bản báo cáo LaTeX đã biên dịch thì phải đọc PDF trong tác vụ riêng.
