# Truy xuất nguồn gốc

Tất cả đường dẫn nguồn tương đối với `C:\xampp\htdocs\TongHop`. Nguồn là tài liệu phân tích trước đó; mã PHP/SQL gốc không có trong input. Tên tiêu đề được dùng thay cho số dòng khi nguồn không cung cấp vị trí ổn định.

| Đầu ra | Nguồn chính | Nội dung dùng/hợp nhất | Mâu thuẫn/giới hạn |
|---|---|---|---|
| `project_overview.md` | v1 `00_PROJECT_OVERVIEW.md`, `README.md`; v4 `24_FINAL_STATUS.md`, `22_FINAL_COVERAGE_CORRECTION.md` | Nhận diện, chức năng, công nghệ, counts | Chỉ là báo cáo nguồn; input thiếu code |
| `system_architecture.md` | v1 `01_ARCHITECTURE.md`, `modules/core.md`; v2 `00_CORRECTIONS_AND_FINDINGS.md`, `03_METHOD_DEEP_DIVE.md`; v4 `modules/core.md`, `07_REMAINING_VIEWS_AND_ESCAPING.md`, diagram system architecture | Request path, layering, Router/View, config, output trust boundary | Chưa runtime; v1/v4 mô tả bổ sung khác mức chi tiết |
| `source_code/module_and_method_reference.md` | v1 `03_MODULE_SPECIFICATIONS.md`, `04_FUNCTION_METHOD_REFERENCE.md`, module docs; v2 `03_METHOD_DEEP_DIVE.md`; v3 `modules/*`; v4 `modules/*` | Tên class/method, trách nhiệm và tham số khái quát | Pagination v1 sai theo v2 correction/v4 catalog; chưa verify source |
| `business_logic/workflows_and_routes.md` | v1 `07_BUSINESS_FLOWS.md`, `13_TRACEABILITY_MATRIX.md`; v2 `01_ROUTE_AUTH_MATRIX.md`, `02_QUERY_AND_DATA_FLOWS.md`; v4 `12_REQUIREMENTS_TRACEABILITY.md` | Route groups, auth/cart/order/profile workflows | Route count 15/12 cũ bị correction thành 13/14 |
| `database/database_schema_and_queries.md` | v1 `06_DATABASE_SPECIFICATION.md`; v2 `02_QUERY_AND_DATA_FLOWS.md`; v3 Account/Cart modules; v4 `08_SQL_FIXTURE_AND_SCHEMA.md`, `13_SQL_CALLSITE_LEDGER.md`, `19_DIAGRAM_CARDINALITY_NOTES.md`, `20_SQL_TEMPLATE_COUNT.md` | 11 tables, query purposes, SQL counts, fixture/migration, cardinality | Không có SQL thật trong input; không chạy DB |
| `database/sql_callsite_ledger.md` | v4 `13_SQL_CALLSITE_LEDGER.md`, `20_SQL_TEMPLATE_COUNT.md` | Danh sách 60 call sites, method, dòng snapshot, bảng/mục đích, count conventions | Line refs theo snapshot, chưa đối chiếu PHP gốc |
| `security/security_analysis.md` | v1 `09_SECURITY_AUDIT.md`; v2 `00_CORRECTIONS_AND_FINDINGS.md`, `01_ROUTE_AUTH_MATRIX.md`; v3 `05_FINDINGS_AND_STATUS.md`; v4 `14_VIEW_ESCAPING_MATRIX.md`, `24_FINAL_STATUS.md` | Controls and review risks | Ưu tiên tĩnh, không pentest; coverage chỉ theo reports |
| `security/view_output_encoding_reference.md` | v3 `modules/account_cart_views.md`; v4 `07_REMAINING_VIEWS_AND_ESCAPING.md`, `14_VIEW_ESCAPING_MATRIX.md` | 17-view matrix, dynamic values, encoder, URL/layout trust boundary | Dựa vào review nguồn, không kiểm tra view code trong input |
| `design_and_theory/quality_performance_ui.md` | v1 `08_THEORY_AND_DESIGN_PRINCIPLES.md`, `10_PERFORMANCE_AND_MAINTAINABILITY.md`; v3 view/CSS modules; v4 `06_CSS_REVIEW.md`, `09_AUXILIARY_DOCUMENTS_AND_JS.md`, `14_VIEW_ESCAPING_MATRIX.md` | SOLID/MVC assessment, performance, CSS/JS/accessibility | Không benchmark/browser test |
| `setup_and_maintenance/requirements_and_operations.md` | v1 `11_LIMITATIONS_AND_RECOMMENDATIONS.md`; v4 `08_SQL_FIXTURE_AND_SCHEMA.md`, `09_AUXILIARY_DOCUMENTS_AND_JS.md`, `12_REQUIREMENTS_TRACEABILITY.md`, `15_DOCUMENTATION_CROSSCHECK.md`, `16_PROGRESS_CORRECTIONS.md` | UC gap, deploy cautions, supporting docs | Requirement/spec không chứng minh implementation |
| `diagrams/system_architecture.mmd` | v1/v4 system architecture diagrams; v4 `18_DIAGRAM_INDEX_CORRIGENDUM.md` | Unified request dependency path | Apache behavior chưa runtime-verified |
| `diagrams/request_lifecycle.mmd` | v1/v4 request lifecycle diagrams | Request sequence | Simplified summary |
| `diagrams/module_dependencies.mmd` | v1/v4 module dependency diagrams | Layer/module dependencies | Summarized, no code graph generated |
| `diagrams/database_relationships.mmd` | v1/v4 ER diagrams; v4 `19_DIAGRAM_CARDINALITY_NOTES.md` | Main FK domains | Simplified cardinality; optionality nuances not fully diagrammed |
| `reports/document_inventory.md` | Filesystem inventory of all 69 `.md`/`.mmd`; each file heading and content read | Relative paths, type, actual title/topic, purpose, related documents, read status | “Không đọc được” = none among found text files; no binary input found |
| `reports/deduplication_report.md` | Pairwise normalized content comparison, all 69 headings/content, corrections across v1–v4 | Exact duplicate result, overlap groups, source/output mapping | Human semantic grouping is qualitative; no automated paraphrase oracle |
| `reports/unresolved_issues.md` | v2 corrections; v3 corrigendum; v4 corrections/status/traceability | Conflicts and unknowns | Cannot inspect source implementation from current input |

## Mốc/tiêu đề nguồn nên tra

- **v1:** `00_PROJECT_OVERVIEW`, `01_ARCHITECTURE`, `03_MODULE_SPECIFICATIONS`, `04_FUNCTION_METHOD_REFERENCE`, `06_DATABASE_SPECIFICATION`, `07_BUSINESS_FLOWS`, `09_SECURITY_AUDIT`, `14_ANALYSIS_COVERAGE`; six `modules/*.md`; four diagrams.
- **v2:** `00_CORRECTIONS_AND_FINDINGS`, `01_ROUTE_AUTH_MATRIX`, `02_QUERY_AND_DATA_FLOWS`, `03_METHOD_DEEP_DIVE`, `04_CONTINUATION_STATUS`, `05_ROUTE_COUNT_CORRIGENDUM`.
- **v3:** `05_FINDINGS_AND_STATUS`, `06_COVERAGE_CORRIGENDUM`, four modules: Account, Cart, auth/account controllers, account/cart views.
- **v4:** `06_CSS_REVIEW` through `24_FINAL_STATUS` (including corrections), four module docs and four Mermaid diagrams. File inventory chính xác tại [document inventory](document_inventory.md).

## Database schema and relationships

Cardinality phụ thuộc constraint: `TaiKhoan.MaND` unique FK, `GioHang.MaND` unique FK; điều đó cho phép tối đa một bản ghi phía con, không buộc mọi người dùng phải có con. `ChiTietSanPham.MaSP` unique FK nhưng detail có thể thiếu. Schema có thể cho phép order không detail, trong khi use case tạo order từ cart không rỗng dự kiến tạo các detail. `GiaoDichThanhToan` có thể 0..n bản ghi theo mô tả FK; không chứng minh gateway.

