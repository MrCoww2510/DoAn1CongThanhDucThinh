# Báo cáo khử trùng lặp

## A. Thống kê

| Chỉ số | Số | Cách hiểu |
|---|---:|---|
| File phát hiện | 69 | 61 Markdown + 8 Mermaid |
| Đọc đầy đủ | 69 | Văn bản đã đọc; không kiểm chứng code được trích dẫn |
| Đọc một phần | 0 | Không có file văn bản nào chỉ đọc một phần |
| Không đọc được | 0 | Không thấy định dạng không hỗ trợ trong inventory |
| Nhóm trùng hoàn toàn | 0 | So sánh nội dung sau chuẩn hóa whitespace và không phân biệt hoa/thường; không tệp nào cùng hash |
| Nhóm gần trùng lặp | 4 | Nhóm nội dung có cùng phần lớn thông tin nền nhưng khác mức chi tiết/đính chính; nhóm không đồng nghĩa file trùng |
| Nhóm trùng một phần | 4 | Cùng tài liệu có các đoạn/nhận định lặp, cộng với thông tin riêng phải giữ |
| Nhóm trùng ý nghĩa | 1 | Khái niệm/chức năng được diễn đạt ở nhiều tài liệu chủ đề |
| Nhóm liên quan nhưng không trùng | 3 | Spec, báo cáo trạng thái, sơ đồ, hướng dẫn học và kết quả triển khai cùng chủ đề nhưng mục đích khác |
| Tài liệu đầu ra | 19 | 15 Markdown + 4 Mermaid |

Các nhóm gần/trùng một phần/ý nghĩa/liên quan là **nhóm chủ đề biên tập**, có thể giao nhau vì cùng file nói nhiều chủ đề; các con số không phải phân hoạch độc lập trên từng đoạn. Phân tích semantical là đọc đối chiếu nội dung, không dùng similarity model tự động. So sánh hash chỉ phát hiện trùng toàn file sau whitespace/case normalization, không chứng minh không có bản sao ở cấp đoạn.

## B. Nhóm phát hiện và phương án

| ID | Nguồn liên quan | Mức | Phần giao nhau | Nội dung riêng được bảo toàn | Đầu ra / phương án |
|---|---|---|---|---|---|
| D-01 | v1 `00_PROJECT_OVERVIEW`, `README`; v2–v4 README/status | Gần trùng | Nhận diện hệ thống, mục đích và giới hạn | Coverage và phát hiện mới theo từng đời | `project_overview.md`; gộp nền, giữ lịch sử/correction trong reports |
| D-02 | v1 `01_ARCHITECTURE`, core diagrams; v2 router deep dive; v4 core/diagrams | Gần trùng | Request path, MVC layers, router | Normalization, error, base path, view trust boundary | `system_architecture.md` và 3 sơ đồ; hợp nhất chi tiết, không lặp README |
| D-03 | v1 `03_MODULE_SPECIFICATIONS`, `04_FUNCTION_METHOD_REFERENCE`; v2 deep dive; v3/v4 modules | Gần trùng | Trách nhiệm class/method | Method signatures, variables, edge cases theo module | `source_code/module_and_method_reference.md`; giữ unique behaviors |
| D-04 | v1 `06_DATABASE_SPECIFICATION`; v2 query flows; v4 schema/fixture/ledger/cardinality | Gần trùng | Bảng, query, transaction | Fixture counts, SQL callsite IDs, cardinality, migration caveats | `database/database_schema_and_queries.md`; gộp schema/query và nêu giới hạn |
| D-05 | v1 `07_BUSINESS_FLOWS`, trace matrix; v2 route matrix/query flows; v4 requirements traceability | Một phần | Đăng nhập, cart, COD, order | HTTP method/auth/CSRF details và UC gaps | `business_logic/workflows_and_routes.md`; requirements riêng ở setup |
| D-06 | v1 account/admin/cart modules; v3 Account/Cart/auth deep dives | Một phần | Account/cart use cases | Race conditions, side effects, validation branch | Module reference + security; không bỏ caveat v3 |
| D-07 | v1 security/theory; v2 auth matrix; v3 findings; v4 final/escaping matrix | Ý nghĩa | CSRF/session/escaping/prepared SQL | Context-specific output matrix và deployment unknowns | `security/security_analysis.md`; hợp nhất findings, không khẳng định pentest |
| D-08 | v1 theory/performance/presentation; v3 view/CSS; v4 CSS/JS/view reports | Một phần | MVC/DRY, responsive, escape/accessibility | Selector/breakpoint, dialog behavior, exact view coverage | `design_and_theory/quality_performance_ui.md` |
| D-09 | v1 coverage; v2 status/count correction; v3 coverage correction; v4 coverage/status corrections | Một phần | Trạng thái, counts, work remaining | Mỗi correction có hiệu lực và lý do riêng | `reports/unresolved_issues.md`; ưu tiên correction có căn cứ, không xóa lịch sử |
| D-10 | v1 ER diagram vs v4 ER + cardinality notes | Liên quan, không hoàn toàn trùng | Entities và FK relationships | Optionality vs application invariants, staff handler edge | `diagrams/database_relationships.mmd` + DB doc; giản lược và ghi caveat |
| D-11 | `rule.txt`/`BanMoi.tex` traceability vs source-focused docs | Liên quan, không trùng | Use case names/domain | Yêu cầu dự kiến khác chức năng có route | `setup_and_maintenance/requirements_and_operations.md`; phân biệt requirement/evidence |
| D-12 | `KienThuc.txt`, UML, LaTeX template vs modules | Liên quan, không trùng | Giải thích thuật ngữ/mô hình | Hướng dẫn học, thiết kế dự kiến, template F&B | Chỉ tóm tắt trong setup/inventory; không lấy làm source truth |

## C. Bảng tên mới và ánh xạ nguồn

Mỗi dòng inventory ghi một file nguồn, chủ đề, tệp liên quan và tài liệu đầu ra tiếp nhận. Bảng mapping chi tiết 69:1 được giữ ở [document_inventory.md](document_inventory.md) để tránh lặp bảng dài tại đây.

| Nhóm file nguồn | Tài liệu đầu ra | Lý do |
|---|---|---|
| Tổng quan/README các đời | `project_overview.md` | Một điểm vào, không giữ tên version chung chung |
| Architecture/core/router và sơ đồ tổng quan | `system_architecture.md`, `diagrams/*` | Tên phản ánh kiến trúc/request/dependency |
| Module/method/class | `source_code/module_and_method_reference.md` | Tách tham chiếu implementation |
| Route/business/use case | `business_logic/workflows_and_routes.md` | Nhóm theo luồng thực tế |
| Schema/query/fixture/SQL ledger | `database/database_schema_and_queries.md` | Tên cụ thể phạm vi schema + SQL |
| Security/view escaping findings | `security/security_analysis.md` | Gộp kiểm soát và rủi ro bảo mật |
| CSS/JS/views/design/performance | `design_and_theory/quality_performance_ui.md` | Một tài liệu presentation/quality, giữ chi tiết đặc thù |
| Requirements/setup/operations | `setup_and_maintenance/requirements_and_operations.md` | Tách yêu cầu thiết kế khỏi bằng chứng triển khai |
| Coverage/status/corrections | `reports/unresolved_issues.md`, `reports/source_traceability.md` | Giữ mâu thuẫn, trạng thái và đường dẫn nguồn |

Không xóa hoặc sửa bất kỳ file nguồn nào. Nội dung lặp chỉ được rút gọn sau khi được giữ ở một tài liệu đầu ra phù hợp.

## D. Chưa thể kết luận

- Không có PHP/SQL gốc để xác định liệu các báo cáo cùng snapshot hay có bản source khác nhau.
- Nội dung semantically tương tự có thể khác về scope hoặc độ tin cậy; không tự hợp nhất những nhận định mâu thuẫn.
- Không kiểm tra toàn bộ markdown paragraph-level similarity bằng công cụ chuyên biệt. Exact full-file compare không tìm thấy duplicate; các nhóm khác là editorial judgement.
- Các correction được nguồn nêu có lý do/cách đếm được ưu tiên, nhưng vẫn cần đối chiếu source trước khi dùng cho vận hành.

### Mapping từng file nguồn

| File/đường dẫn nguồn | Tài liệu đầu ra | Lý do |
|---|---|---|
| `DoAn1CongThanhDucThinh_Documentation\00_PROJECT_OVERVIEW.md` | `project_overview.md` | Tổng quan/chỉ mục |
| `DoAn1CongThanhDucThinh_Documentation\01_ARCHITECTURE.md` | `system_architecture.md` | Kiến trúc/Core |
| `DoAn1CongThanhDucThinh_Documentation\02_FILE_CATALOG.md` | `project_overview.md` | Tổng quan/chỉ mục |
| `DoAn1CongThanhDucThinh_Documentation\03_MODULE_SPECIFICATIONS.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation\04_FUNCTION_METHOD_REFERENCE.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation\05_VARIABLE_CLASS_REFERENCE.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation\06_DATABASE_SPECIFICATION.md` | `database/database_schema_and_queries.md` | Database/SQL |
| `DoAn1CongThanhDucThinh_Documentation\07_BUSINESS_FLOWS.md` | `business_logic/workflows_and_routes.md` | Luồng/route |
| `DoAn1CongThanhDucThinh_Documentation\08_THEORY_AND_DESIGN_PRINCIPLES.md` | `design_and_theory/quality_performance_ui.md` | Thiết kế/hiệu năng |
| `DoAn1CongThanhDucThinh_Documentation\09_SECURITY_AUDIT.md` | `security/security_analysis.md` | Bảo mật/output |
| `DoAn1CongThanhDucThinh_Documentation\10_PERFORMANCE_AND_MAINTAINABILITY.md` | `design_and_theory/quality_performance_ui.md` | Thiết kế/hiệu năng |
| `DoAn1CongThanhDucThinh_Documentation\11_LIMITATIONS_AND_RECOMMENDATIONS.md` | `design_and_theory/quality_performance_ui.md` | Thiết kế/hiệu năng |
| `DoAn1CongThanhDucThinh_Documentation\12_GLOSSARY.md` | `design_and_theory/quality_performance_ui.md` | Thiết kế/hiệu năng |
| `DoAn1CongThanhDucThinh_Documentation\13_TRACEABILITY_MATRIX.md` | `business_logic/workflows_and_routes.md` | Luồng/route |
| `DoAn1CongThanhDucThinh_Documentation\14_ANALYSIS_COVERAGE.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation\diagrams\database_relationships.mmd` | `diagrams/database_relationships.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation\diagrams\module_dependencies.mmd` | `diagrams/module_dependencies.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation\diagrams\request_lifecycle.mmd` | `diagrams/request_lifecycle.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation\diagrams\system_architecture.mmd` | `diagrams/system_architecture.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation\modules\accounts_auth.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation\modules\admin.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation\modules\cart_orders.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation\modules\core.md` | `system_architecture.md` | Kiến trúc/Core |
| `DoAn1CongThanhDucThinh_Documentation\modules\presentation.md` | `design_and_theory/quality_performance_ui.md` | Giao diện/JS/encoding |
| `DoAn1CongThanhDucThinh_Documentation\modules\products.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation\README.md` | `project_overview.md` | Tổng quan/chỉ mục |
| `DoAn1CongThanhDucThinh_Documentation_v2\00_CORRECTIONS_AND_FINDINGS.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v2\01_ROUTE_AUTH_MATRIX.md` | `business_logic/workflows_and_routes.md` | Luồng/route |
| `DoAn1CongThanhDucThinh_Documentation_v2\02_QUERY_AND_DATA_FLOWS.md` | `database/database_schema_and_queries.md` | Database/SQL |
| `DoAn1CongThanhDucThinh_Documentation_v2\03_METHOD_DEEP_DIVE.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation_v2\04_CONTINUATION_STATUS.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v2\05_ROUTE_COUNT_CORRIGENDUM.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v2\README.md` | `project_overview.md` | Tổng quan/chỉ mục |
| `DoAn1CongThanhDucThinh_Documentation_v3\05_FINDINGS_AND_STATUS.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v3\06_COVERAGE_CORRIGENDUM.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v3\modules\account_cart_views.md` | `design_and_theory/quality_performance_ui.md` | Giao diện/JS/encoding |
| `DoAn1CongThanhDucThinh_Documentation_v3\modules\account_model.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation_v3\modules\auth_account_controllers.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation_v3\modules\cart.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation_v3\README.md` | `project_overview.md` | Tổng quan/chỉ mục |
| `DoAn1CongThanhDucThinh_Documentation_v4\04_STATUS.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\06_CSS_REVIEW.md` | `design_and_theory/quality_performance_ui.md` | Giao diện/JS/encoding |
| `DoAn1CongThanhDucThinh_Documentation_v4\07_REMAINING_VIEWS_AND_ESCAPING.md` | `design_and_theory/quality_performance_ui.md` | Giao diện/JS/encoding |
| `DoAn1CongThanhDucThinh_Documentation_v4\08_SQL_FIXTURE_AND_SCHEMA.md` | `database/database_schema_and_queries.md` | Database/SQL |
| `DoAn1CongThanhDucThinh_Documentation_v4\09_AUXILIARY_DOCUMENTS_AND_JS.md` | `design_and_theory/quality_performance_ui.md` | Giao diện/JS/encoding |
| `DoAn1CongThanhDucThinh_Documentation_v4\10_ANALYSIS_COVERAGE.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\11_STATUS_CORRIGENDUM.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\12_REQUIREMENTS_TRACEABILITY.md` | `setup_and_maintenance/requirements_and_operations.md` | Yêu cầu |
| `DoAn1CongThanhDucThinh_Documentation_v4\13_SQL_CALLSITE_LEDGER.md` | `database/database_schema_and_queries.md` | Database/SQL |
| `DoAn1CongThanhDucThinh_Documentation_v4\14_VIEW_ESCAPING_MATRIX.md` | `security/security_analysis.md` | Bảo mật/output |
| `DoAn1CongThanhDucThinh_Documentation_v4\15_DOCUMENTATION_CROSSCHECK.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\16_PROGRESS_CORRECTIONS.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\17_DOCUMENTATION_INDEX.md` | `project_overview.md` | Tổng quan/chỉ mục |
| `DoAn1CongThanhDucThinh_Documentation_v4\18_DIAGRAM_INDEX_CORRIGENDUM.md` | `reports/unresolved_issues.md` | Báo cáo/chủ đề khác |
| `DoAn1CongThanhDucThinh_Documentation_v4\19_DIAGRAM_CARDINALITY_NOTES.md` | `database/database_schema_and_queries.md` | Database/SQL |
| `DoAn1CongThanhDucThinh_Documentation_v4\20_SQL_TEMPLATE_COUNT.md` | `database/database_schema_and_queries.md` | Database/SQL |
| `DoAn1CongThanhDucThinh_Documentation_v4\22_FINAL_COVERAGE_CORRECTION.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\23_FINAL_COUNT_CORRECTION.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\24_FINAL_STATUS.md` | `reports/unresolved_issues.md` | Kiểm kê/coverage/đính chính |
| `DoAn1CongThanhDucThinh_Documentation_v4\diagrams\database_relationships.mmd` | `diagrams/database_relationships.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation_v4\diagrams\module_dependencies.mmd` | `diagrams/module_dependencies.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation_v4\diagrams\request_lifecycle.mmd` | `diagrams/request_lifecycle.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation_v4\diagrams\system_architecture.mmd` | `diagrams/system_architecture.mmd` | Sơ đồ |
| `DoAn1CongThanhDucThinh_Documentation_v4\modules\catalog.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation_v4\modules\catalog_views.md` | `design_and_theory/quality_performance_ui.md` | Giao diện/JS/encoding |
| `DoAn1CongThanhDucThinh_Documentation_v4\modules\core.md` | `system_architecture.md` | Kiến trúc/Core |
| `DoAn1CongThanhDucThinh_Documentation_v4\modules\order_views_and_encoding.md` | `design_and_theory/quality_performance_ui.md` | Giao diện/JS/encoding |
| `DoAn1CongThanhDucThinh_Documentation_v4\modules\orders_admin.md` | `source_code/module_and_method_reference.md` | Module/method |
| `DoAn1CongThanhDucThinh_Documentation_v4\README.md` | `project_overview.md` | Tổng quan/chỉ mục |

