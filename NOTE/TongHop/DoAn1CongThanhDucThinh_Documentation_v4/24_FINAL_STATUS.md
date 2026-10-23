# Trạng thái cuối của lượt phân tích tĩnh

Ngày snapshot: 2026-10-19. **Trạng thái:** phần phân tích tĩnh trong phạm vi source đã hoàn thành; runtime chưa xác minh.

## Coverage đã xác minh

Đếm bằng `rg --files --hidden -g '!.git/**'`: 232 file ngoài nội dung `.git`.

- 38 PHP: đọc đầy đủ.
- 12 CSS: đọc đầy đủ.
- 2 JavaScript: đọc đầy đủ.
- 3 SQL: schema/migration/fixture đọc cú pháp; không chạy. Fixture values không được đưa vào báo cáo.
- 15 LaTeX: đọc đầy đủ; `BanMoi.tex` 992 dòng, template 14 file.
- 4 TXT: đọc đầy đủ (`KienThuc`, `rule`, class UML, database UML).
- `AGENTS.md` 195 dòng và hai `.htaccess`: đọc đầy đủ.
- 155 file còn lại chủ yếu tài nguyên ảnh, PDF, ZIP và artifact build; kiểm kê tên/metadata, không mở hoặc trích xuất.

## Thống kê kỹ thuật

- 17 class.
- 89 method và một function JavaScript inline trong layout view.
- 27 route declarations: 13 GET, 14 POST.
- 60 PDO call sites: 54 call site có SQL literal trực tiếp, 49 mẫu literal khác nhau sau chuẩn hóa; 6 call site sử dụng `$sql` được dựng trước. Xem [ledger](13_SQL_CALLSITE_LEDGER.md) và [phương pháp đếm](20_SQL_TEMPLATE_COUNT.md).
- 17/17 view có ma trận dynamic value → HTML context → encoder tại [14_VIEW_ESCAPING_MATRIX.md](14_VIEW_ESCAPING_MATRIX.md).
- 15 UC được đối chiếu với route/controller/model/schema tại [12_REQUIREMENTS_TRACEABILITY.md](12_REQUIREMENTS_TRACEABILITY.md).
- 4 sơ đồ Mermaid mới: [system architecture](diagrams/system_architecture.mmd), [request lifecycle](diagrams/request_lifecycle.mmd), [module dependencies](diagrams/module_dependencies.mmd), [database relationships](diagrams/database_relationships.mmd).
- Trước khi tạo file trạng thái này, output v4 có 24 Markdown + 4 Mermaid; file này đưa tổng hiện tại lên 25 Markdown + 4 Mermaid = **29 file**.

## Phát hiện chính

- Có flow khách hàng: account/auth, product browse/search, cart, checkout COD, order listing/detail/cancel; admin module tập trung vào quản lý tài khoản/nhân viên.
- UC quản lý đơn bởi nhân viên, CRUD sản phẩm/danh mục, quản lý kho độc lập, thống kê/Excel và cổng thanh toán online chưa có route/flow tương ứng trong snapshot.
- Product listing không phân trang; yêu cầu lọc giá/thương hiệu không được thể hiện đầy đủ trong form/controller.
- `database.sql` có DROP và không được thực thi; migration thêm `DiaChiNhanHang` trong khi schema snapshot đã có cột đó.
- Layout output `$content` raw nên view con là ranh giới tin cậy; dynamic image URL được HTML-escape nhưng không thấy allowlist scheme/host.
- `session_regenerate_id(true)` có tại `AuthController.php:45` sau login thành công.
- `AboutController` dùng path có chữ hoa `app/Views` trong khi thư mục thực tế là `app/views`; có rủi ro khác biệt trên filesystem phân biệt hoa thường.
- Không đưa giá trị fixture, credentials hoặc dữ liệu định danh cá nhân từ tài liệu LaTeX vào bộ tài liệu.

## Kiểm tra tính toàn vẹn và giới hạn

- `git status --short` trống ở lần kiểm tra cuối; không có thay đổi source được thực hiện chủ động.
- Không có SHA-256 baseline trước phân tích để đối chiếu checksum; vì vậy không tuyên bố đã xác minh checksum trước/sau.
- Không khởi động Apache/PHP/MySQL, không truy vấn database, không chạy tests/scripts/migration hoặc browser. Runtime/session behavior, phiên bản môi trường, accessibility và hiển thị thực tế còn chưa xác minh.
- Link nội bộ Markdown trong v4 đã được kiểm tra và không có link hỏng ở snapshot cuối.

## Tài liệu nên mở đầu

Bắt đầu từ [chỉ mục](17_DOCUMENTATION_INDEX.md), sau đó đọc ma trận yêu cầu, kiến trúc core, từng module và coverage. Một số tài liệu cũ được giữ nguyên để tuân thủ quy tắc không ghi đè; khi có đính chính, dùng file correction có số thứ tự cao hơn.
