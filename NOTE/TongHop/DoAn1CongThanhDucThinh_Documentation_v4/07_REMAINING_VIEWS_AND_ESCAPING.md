# View còn lại và ma trận output escaping

- Ngày phân tích: 2026-10-19.
- Phạm vi bổ sung: `app/views/layouts/customer.php`, `app/views/errors/404.php`, `app/views/pages/about.php`, đọc toàn bộ.
- Kiểm tra encoding/output tĩnh; dòng có thể đổi khi source cập nhật.

## `app/views/layouts/customer.php`

Layout đọc `config/app.php`, chuẩn hóa `base_url` vào `$baseUrl`, dựng HTML dùng chung và nhúng nội dung con ở dòng 151. Tiêu đề, base URL, truy vấn tìm kiếm, lời chào session, CSRF field và URL href/src đều qua `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` ở các vị trí tương ứng (10–18, 29, 43, 51–55, 81–89, 94, 103, 108, 114, 117). Giá trị tìm kiếm cast sang string trước khi escape. Link/menu được điều kiện hóa theo `$_SESSION['user']['VaiTro']` (79–120, 139–145).

`$content` được echo nguyên trạng (151), không HTML-escape. Đây là cách render HTML của view con, nhưng tạo trust boundary: mọi view được truyền vào phải mã hóa dữ liệu không tin cậy trước khi tạo markup. Một view con có lỗi escaping có thể dẫn đến XSS ở toàn layout.

JS inline lấy phần tử `backToTop`, cập nhật hiển thị theo scroll và gọi `window.scrollTo` (210–228). Không có dữ liệu người dùng được nội suy vào script. Nút thiếu khai báo `type`/nhãn truy cập rõ ràng là điểm UX/accessibility cần kiểm tra.

Các nội dung footer là hằng tĩnh; một số link `#` chỉ là placeholder. Source hiển thị mojibake trong terminal ở chuỗi tiếng Việt; chưa xác định đây là encoding lưu file hay vấn đề hiển thị terminal nên không kết luận dữ liệu source đã hỏng.

## `app/views/errors/404.php`

View độc lập, đọc config và in tiêu đề/thông điệp mặc định hoặc truyền vào. `$title`, `$message`, `$baseUrl` đều được escape HTML ở dòng 16–18. Không thấy form hoặc dữ liệu đầu vào khác. Phản hồi HTTP status phụ thuộc controller/router; template một mình không chứng minh status 404.

## `app/views/pages/about.php`

Trang giới thiệu gần như markup tĩnh. Closure `$escape` ở dòng 7 cast string và gọi `htmlspecialchars` UTF-8; được dùng cho `$baseUrl` ở link/style (12, 36, 44, 181, 191, 200, 209, 218, 244). Nội dung còn lại là literal HTML, không lấy dữ liệu database. Không có output dynamic chưa escape được nhận diện.

## Ma trận escaping toàn bộ nhóm view đã đọc trong các lượt trước

| Nhóm view | Kiểm soát đầu ra quan sát được | Ghi chú |
|---|---|---|
| `account/index.php` | dữ liệu hồ sơ/form qua helper/escape theo ngữ cảnh | xem tài liệu v3; không đánh giá lại mọi dòng trong tài liệu này |
| `auth/login.php`, `auth/register.php` | thông báo/giá trị form được escape | xem tài liệu v3; CSRF token được escape khi render |
| `cart/index.php` | tên/ảnh/giá và giá trị form được escape hoặc ép kiểu phù hợp | xem tài liệu v3 |
| `home/index.php`, `products/*` | văn bản/attribute qua escaping; URL ảnh được encode thành phần và escape; số được cast/format | xem `modules/catalog_views.md` |
| `checkout/*`, `orders/*`, `admin/users.php` | nội dung động được escape, số cast/format; class trạng thái lấy từ nhánh/giá trị kiểm soát | xem `modules/order_views_and_encoding.md` |
| `layouts/customer.php` | dynamic href, title, query, session greeting, CSRF được escape; `$content` raw | tin cậy đầu ra view con |
| `errors/404.php` | title/message/base URL escape | HTTP status chưa chứng minh tại view |
| `pages/about.php` | base URL escape qua closure; nội dung khác literal | không có dữ liệu DB dynamic |

Đây là kiểm tra tĩnh trên các view đã đọc. Không có browser/runtime test, không bảo đảm an toàn tuyệt đối với mọi route; HTML context khác như JavaScript/CSS/URL đặc biệt cần encoder chuyên biệt nếu dữ liệu động được thêm vào.
