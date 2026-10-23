# Chỉ mục bộ tài liệu phân tích v4

**Snapshot:** 2026-10-19. Đây là chỉ mục bổ sung cho README v4 đã tồn tại (không chỉnh sửa README cũ). Đọc file này khi muốn điều hướng toàn bộ các tài liệu của phiên tiếp tục.

## Đọc theo thứ tự

1. [Trạng thái và đính chính gần nhất](16_PROGRESS_CORRECTIONS.md) — trạng thái mới, fixture, LaTeX và điểm đính chính session.
2. [Phạm vi phân tích](10_ANALYSIS_COVERAGE.md) — inventory nhóm file và giới hạn; phần còn lại cần đọc cùng corrections vì file gốc được giữ nguyên.
3. [Đối chiếu yêu cầu với source](12_REQUIREMENTS_TRACEABILITY.md) — UC01–UC15, chức năng có/thiếu bằng chứng.
4. [Core/router](modules/core.md), [catalog](modules/catalog.md), [catalog views](modules/catalog_views.md), [order/admin](modules/orders_admin.md), [order views/encoding](modules/order_views_and_encoding.md).
5. [Ma trận output escaping của 17 view](14_VIEW_ESCAPING_MATRIX.md) và [ledger 60 SQL call site](13_SQL_CALLSITE_LEDGER.md).
6. [Kiến trúc CSS](06_CSS_REVIEW.md), [view bổ sung](07_REMAINING_VIEWS_AND_ESCAPING.md), [schema/fixture](08_SQL_FIXTURE_AND_SCHEMA.md), [JavaScript/tài liệu phụ trợ](09_AUXILIARY_DOCUMENTS_AND_JS.md).
7. [Kiểm tra chéo toàn bộ v4](15_DOCUMENTATION_CROSSCHECK.md), [đính chính phép đếm operation](11_STATUS_CORRIGENDUM.md), [trạng thái core/catalog ban đầu](04_STATUS.md).

## Các tài liệu theo phiên bản

- v4 là phần bổ sung cho các tài liệu phân tích trước, không phải bản sao đầy đủ toàn bộ nội dung lịch sử.
- README v4 liên kết tới [README v3 về account/cart](../DoAn1CongThanhDucThinh_Documentation_v3/README.md).
- Với thông tin trùng nhau, ưu tiên tài liệu có ngày/snapshot mới hơn; các file cũ được giữ nguyên để không ghi đè.
- Các sơ đồ tổng quan Mermaid được tạo ở các phiên bản trước. V4 hiện bổ sung ma trận/đối chiếu/ledger, không tạo bản sơ đồ trùng lặp. Cần mở tài liệu v3 để xem sơ đồ nếu liên kết của phiên trước còn tồn tại.

## Phạm vi chưa xác minh

Không chạy ứng dụng, PHP, SQL, tests, migration hay dịch vụ. Runtime Apache/PHP/MySQL/session, hiển thị browser, accessibility thực tế và trạng thái database đang chạy chưa được kiểm tra. Không có checksum baseline trước phân tích để xác nhận SHA-256 trước/sau.
