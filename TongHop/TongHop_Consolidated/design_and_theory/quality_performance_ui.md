# Thiết kế, hiệu năng, giao diện và kiểm thử

## Nguyên tắc thiết kế được nhận định

| Chủ đề | Mức áp dụng được báo cáo | Căn cứ/giới hạn |
|---|---|---|
| MVC/separation | Một phần | Router/controller/model/view riêng; controller gọi model concrete |
| OOP/encapsulation | Một phần | class, namespace, visibility, base controller; abstraction ít |
| SOLID | Một phần | SRP tương đối; DIP yếu; OCP/LSP/ISP chưa đủ bằng chứng |
| DRY | Một phần | layout/helper chung nhưng CSRF/role patterns lặp |
| Prepared statements | Có theo source report | tách SQL value; identifiers vẫn cần allowlist |
| Transactions/ACID | Có trong luồng chính | InnoDB/commit/rollback/row locks được mô tả; concurrency chưa test |
| REST/AJAX | Chưa thấy | form GET/POST, không có JSON API/fetch theo báo cáo |
| PRG | Một phần | redirect sau mutation; một số lỗi render trực tiếp |
| DI | Chưa thấy rõ | concrete model và static PDO làm test/mock khó |

Không suy rộng rằng một framework hoặc design pattern khác đang được sử dụng.

## Hiệu năng và bảo trì

- `Product::all` trả tất cả kết quả phù hợp; không pagination. LIKE `%term%` và offset concern được tài liệu cũ nêu, nhưng offset catalog hiện không có.
- Admin search có LIMIT 200 nhưng không có paging theo báo cáo.
- Dùng `EXPLAIN`/index trên DB staging trước tối ưu; chưa benchmark.
- Cart total lưu dư bên cạnh line items; writer khác có thể làm lệch.
- Row lock giúp bảo vệ order stock, nhưng lock duration/deadlock phụ thuộc workload và DB.
- Controller khởi tạo dependency cụ thể; có thể cân nhắc injection nếu cần unit tests.

## Giao diện và JavaScript

12 CSS được báo cáo chia theo base/header/home/products/about/users/account/auth/cart/checkout/orders/404; không render browser. `auth.css` dùng `:has()`, có thể ảnh hưởng style ở browser không hỗ trợ. CSS không phải kiểm soát bảo mật.

`nav.js` toggles class cho menu khi phần tử có mặt. `order-cancel-dialog.js` dùng native dialog, focus return và `requestSubmit`; server vẫn chịu trách nhiệm quyền/CSRF. Chưa xác minh keyboard, screen reader, focus trap, tương phản hoặc browser support. Một số stylesheet trang có thể không liên kết ở layout chung; cần route→stylesheet runtime/source review.

## Kiểm thử đề xuất, chưa thực hiện

DB test: uniqueness race email, cạnh tranh đặt hàng/tồn, hủy lặp, rollback, deadlock. HTTP: ma trận role/method/CSRF, lỗi 404/405, base path, session/cookie. Browser: output encoding, URL ảnh, responsive/accessibility. Các đề xuất này không đồng nghĩa test đã chạy.
