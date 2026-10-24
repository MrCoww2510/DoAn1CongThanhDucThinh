# Core: Router, Controller, View, Database

Ngày 19-10-2026. Đọc toàn văn 4 file `app/Core/*.php`.

## Router (`Router.php`, 97 dòng)

Property `private array $routes=[]`: list `{method,pattern,handler}`; type PHPDoc. Constructor rỗng.
- `get(path,handler):void`, `post(...)`: gọi `add` với verb cố định.
- `add(method,path,handler):void`: trim slash, thêm slash đầu; root đặc biệt `/`; preg_quote path, thay token `{name}` hợp lệ bằng named group `[^/]+`; lưu method uppercase, anchored regex có trailing slash optional và handler. Các route tin cậy do source cấu hình.
- `dispatch(method,uri):void`: parse path, fallback `/`; rawurldecode; load app config/base_url; cắt base prefix nếu khớp; scan thứ tự route; method uppercase match; preg_match; chỉ giữ keys string (bao gồm named captures), lấy handler. Array hai phần tử → instantiate class, gọi action với params positional; callable khác gọi trực tiếp. Không match → 404, include error view. Method sai cũng 404, không phát Allow/405. Duplicate routes first-match.

Locals: `$path` decoded URI path; `$app/$baseUrl` config; `$route` entry; `$matches` preg result; `$params` key-filtered matches; `$handler`, `$class`, `$action`, `$controller`. Query string bỏ khỏi routing. Decode trước regex có thể làm encoded slash thành separator; cần test nếu IDs có `%2F` (hiện ID schema không dự kiến chứa slash).

## Controller (`Controller.php`, 23 dòng)

Abstract helper. `view(view,data=[],layout='customer'):void` forward static `View::render`. `redirect(path,statusCode=302):never`: load config/base_url, trim slash, `header(Location)` rồi exit. Không validate redirect target ngoài việc caller thường truyền route nội bộ; nếu path user-controlled, cần rà open redirect/header behavior. Hiện controller call sites dùng chuỗi nội bộ, có thêm rawurlencode order ID.

## View (`View.php`, 39 dòng)

`render(view,data=[],layout='customer'):void`: dựng `$viewsRoot`, `$viewPath`, `$layoutPath`; kiểm `is_file` và throw RuntimeException nếu thiếu; config/base URL; `extract($data,EXTR_SKIP)`; output buffer view thành `$content`; include layout. `EXTR_SKIP` tránh overwrite local names. Không escape tự động; view author chịu trách nhiệm. `$view` được trim nhưng không canonicalize; callers nội bộ hiện truyền literal. Nếu method trở thành callable với input ngoài, traversal cần chặn.

## Database (`Database.php`, 46 dòng)

Static nullable PDO `$connection`; `connection():PDO` return cache nếu PDO sẵn, nếu không load database config, dựng MySQL DSN host/port/db/charset, new PDO với ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false. PDOException được log phía server và bọc RuntimeException tổng quát. Private constructor. Đây là singleton connection có bằng chứng; lợi ích tái dùng/cấu hình đơn; nhược global dependency, test isolation/mock khó. Credentials không chép vào tài liệu.

## Đánh giá

Strict types ở file, visibility và các return types giúp hợp đồng. Error handler/bootstrap nằm front controller, không trong Core. Không thấy interface/DI; controller/model phụ thuộc static/concrete. Cần review route order/404, header status, output buffering khi template throw và kiểm thử path normalization.
