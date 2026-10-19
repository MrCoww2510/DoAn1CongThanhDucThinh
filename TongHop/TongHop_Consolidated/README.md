# T&T Computer — bộ tài liệu tổng hợp

Bộ này hợp nhất các tài liệu kỹ thuật v1–v4 nằm trong đầu vào `C:\xampp\htdocs\TongHop`. Tài liệu nguồn mô tả ứng dụng PHP/MySQL, nhưng thư mục đầu vào không chứa mã nguồn hoặc SQL thực tế; mọi mô tả hệ thống ở đây được gắn nhãn là thông tin từ báo cáo nguồn, chưa xác minh độc lập.

## Mục đích và phạm vi

Tập hợp kiến trúc, module, route/luồng nghiệp vụ, schema/query theo báo cáo, bảo mật, giao diện, yêu cầu và hạn chế; giảm lặp bằng cách giữ một tài liệu chuẩn cho mỗi chủ đề, cùng báo cáo kiểm kê, khử trùng lặp và truy xuất nguồn gốc. Không đưa credential, token, row fixture hay định danh cá nhân vào đây.

## Cấu trúc và nội dung

- [Tổng quan dự án](project_overview.md): chức năng, công nghệ, số liệu đã được báo cáo và giới hạn bằng chứng.
- [Kiến trúc hệ thống](system_architecture.md): request lifecycle, MVC phân lớp, router, view/output và deploy concerns.
- [Module và method](source_code/module_and_method_reference.md): class/method được các tài liệu nguồn ghi nhận.
- [Luồng nghiệp vụ và route](business_logic/workflows_and_routes.md): nhóm route và các use case chính.
- [Schema và truy vấn](database/database_schema_and_queries.md): 11 bảng, quan hệ, query flows, fixture/migration và cảnh báo SQL.
- [Ledger SQL](database/sql_callsite_ledger.md): 60 call site PDO với method/bảng/vị trí theo báo cáo v4.
- [Phân tích bảo mật](security/security_analysis.md): kiểm soát được báo cáo, rủi ro và việc cần xác minh.
- [Ma trận encoding view](security/view_output_encoding_reference.md): 17 view, dữ liệu động, context/encoder và giới hạn.
- [Thiết kế, hiệu năng, UI](design_and_theory/quality_performance_ui.md): mức áp dụng nguyên tắc, giới hạn, CSS/JS và đề xuất kiểm thử.
- [Yêu cầu và vận hành](setup_and_maintenance/requirements_and_operations.md): UC01–UC15, khoảng cách yêu cầu và ghi chú vận hành.
- [Sơ đồ](diagrams/): kiến trúc, request lifecycle, module dependency và quan hệ dữ liệu.
- [Kiểm kê nguồn](reports/document_inventory.md): toàn bộ 69 tệp đầu vào, phân loại, mục đích, liên quan và trạng thái đọc.
- [Báo cáo khử trùng lặp](reports/deduplication_report.md): nhóm lặp/overlap, cách hợp nhất và tên chuẩn hóa.
- [Truy xuất nguồn gốc](reports/source_traceability.md): nguồn đầu vào cho từng đầu ra.
- [Vấn đề chưa xác minh](reports/unresolved_issues.md): mâu thuẫn còn mở, giới hạn và việc cần kiểm tra.

## Thứ tự đọc gợi ý

1. Tổng quan → kiến trúc.
2. Module/method → luồng nghiệp vụ → schema/query.
3. Bảo mật → thiết kế/hiệu năng/UI.
4. Yêu cầu/vận hành → unresolved issues.
5. Dùng inventory, deduplication và traceability khi cần tra ngược nguồn.

## Quy ước

Tên file dùng chữ thường, tiếng Anh không dấu và `_`. Nội dung viết tiếng Việt, giữ nguyên tên class/method/bảng/route. “Theo tài liệu nguồn”, “chưa xác minh”, “chưa thấy bằng chứng” phân biệt rõ với hành vi runtime đã quan sát. Liên kết là tương đối và được kiểm tra trong thư mục đầu ra.

## Giới hạn

Đầu vào có 61 Markdown và 8 Mermaid; không có 38 PHP, 3 SQL, CSS, JS hay dữ liệu ứng dụng mà các tài liệu đầu vào nhắc tới. Không thể kiểm chứng độc lập các nhận định source, phiên bản triển khai, trạng thái DB, behavior browser hoặc số dòng. Báo cáo nguồn có đính chính không đồng nhất; phiên bản mới hơn chỉ được ưu tiên khi chính tài liệu đó nêu rõ đính chính và có lý do/cách đếm. Xem [unresolved issues](reports/unresolved_issues.md).


