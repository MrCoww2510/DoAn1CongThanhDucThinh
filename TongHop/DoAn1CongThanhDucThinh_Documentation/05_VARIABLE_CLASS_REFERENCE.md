# 05 — Biến và class

Ngày 19-10-2026. Tham chiếu biến quan trọng xuyên module; biến tạm từng nhánh mô tả ở tài liệu module.

| Biến | Nguồn/phạm vi | Ý nghĩa/rủi ro |
|---|---|---|
| `$_SERVER['REQUEST_METHOD']` | superglobal HTTP | điều khiển nhánh GET/POST; cần kiểm tra method cho mutation |
| `$_SERVER['REQUEST_URI']` | superglobal HTTP | path router; query cần tách chuẩn hóa |
| `$_GET`, `$_POST` | input client | thường là string/array, không tin kiểu; cast và validate trước dùng |
| `$_SESSION['user']` | session server-side | ID/role và thông tin user sau login; không giữ hash mật khẩu |
| `$_SESSION['_csrf']` | session | token random so với POST bằng `hash_equals` |
| `$_SESSION` flash keys | session | thông báo tạm sau redirect rồi được tiêu thụ |
| `$pdo`, `$this->db` | PDO | truy vấn/transaction; credential không được ghi tài liệu |
| `$form`, `$errors`, `$profile`, `$cart`, `$order` | local/controller-view array | trạng thái form/dữ liệu hiển thị; output phải escape |
| `$baseUrl` | config app | tạo link/redirect, phụ thuộc deploy prefix |

Kiểu là kiểu động PHP; chỉ ghi kiểu khi chữ ký/cast/SQL chứng minh. Ví dụ giả lập: quantity `2`, identity `demo_user`.

## Class

`Router`: route table/dispatch. `Controller`: base helper. `View`: include templates. `Database`: PDO connection singleton tĩnh. Controller: HTTP/use-case. Model: SQL/domain persistence. Có tách trách nhiệm theo lớp nhưng concrete construction và static singleton làm coupling/testability yếu. Không thấy interface/trait; không khẳng định pattern ngoài singleton connection có bằng chứng.

SRP được áp dụng một phần qua controller/model/view. DIP yếu do controller phụ thuộc concrete model/static PDO. OCP/LSP/ISP chưa đủ bằng chứng do thiếu abstraction rõ.
