# Cart model và controller

Ngày 19-10-2026. Files `app/Models/Cart.php` (354 dòng), `app/Controllers/CartController.php` (229 dòng). Model giữ `private PDO $db`, set một lần trong constructor. Customer ID luôn lấy từ session ở controller, không nhận trực tiếp từ form.

## Cart model

- `__construct(): void` (14–17): gán PDO singleton.
- `getOrCreateCart(string $maND): string` (22–63): SELECT cart ID. Nếu tồn tại trả string. Nếu không sinh `GH` + 16 hex uppercase và INSERT. PDOException khi insert làm re-read; nếu concurrent request đã tạo thì trả ID đó, nếu không thì rethrow. Không có transaction riêng.
- `getItems(string $maND): array` (68–112): gọi getOrCreate (GET có thể INSERT); SELECT line join sản phẩm + LEFT JOIN chi tiết, tính từng ThanhTien và sắp line ID desc; tổng cộng bằng float trong PHP; UPDATE GioHang.TongTien; trả `{maGH:string,items:list,total:number}`. Đây là read có side effect ghi/tạo giỏ. GET lỗi DB có thể throw.
- `addItem(string $maND,string $maSP,int $quantity): void` (117–222): reject quantity <1. Transaction; lấy/tạo giỏ; khóa cart; select product/stock/status FOR UPDATE; thiếu → RuntimeException; status rỗng chấp nhận, status khác allowlist lowercase bị từ chối; lock existing line; cộng quantity hiện tại; reject vượt tồn; update hoặc insert dòng ID `CTGH` + random hex; commit. Throwable rollback/rethrow. Khóa product giúp serialize thêm cùng sản phẩm trên InnoDB.
- `findItems(string $maND): array` (227–273): SELECT cart hiện có; nếu không trả `maGH=null, items=[], total=0` và không tạo row. Nếu có SELECT lines như getItems, tổng float tính lại nhưng **không update** `GioHang.TongTien`.
- `updateItem(string $maND,string $maSP,int $quantity): void` (278–319): reject <1; select line/stock thông qua join scoped owner; thiếu → RuntimeException; vượt stock → RuntimeException; UPDATE joined row. Không transaction và SELECT không `FOR UPDATE`, do đó tồn có thể đổi giữa check/update. Không tự refresh `GioHang.TongTien`; caller/cart GET có thể đồng bộ sau đó.
- `removeItem(string $maND,string $maSP): void` (324–335): DELETE join scoped owner; xóa 0 dòng không báo lỗi. Tổng chưa cập nhật tại đây.
- `clear(string $maND): void` (340–353): DELETE tất cả dòng theo owner rồi UPDATE tổng 0; hai câu không trong transaction nên có thể không đồng bộ nếu câu thứ hai lỗi sau khi delete.

SQL group: cart SELECT/INSERT/reread; line SELECT joins; aggregate PHP sum; total UPDATE; transaction locking selects; line UPDATE/INSERT; find-only select; owner-scoped item lookup and joined UPDATE; owner-scoped joined DELETE; clear UPDATE. Không có SQL parameter interpolation từ user.

### Biến model

`$this->db`: PDO property. `$stmt`: statement. `$maGH`: cart ID/false-return normalization. `$items`: rows. `$total`: float tổng. `$item/$product`: fetched associative row hoặc false. `$status`: chuỗi trim, NULL→''. `$current`: fetchColumn hoặc false; `$newQuantity`: int. `$quantity`: int parameter. `$e`: Throwable/PDOException. Mã IDs sinh từ `random_bytes`, không dựa auto increment.

## CartController

- `requireCustomer(): array` (13–26): lấy `$_SESSION['user'] ?? null`; thiếu/không array/thiếu MaND/sai role → redirect login. Return user array.
- `verifyCsrf(): void` (29–42): POST/session token cast string; thiếu/rỗng hoặc hash_equals false → 403 exit.
- `postOnly(): void` (45–51): method không POST → 405 exit.
- `flash(string $message,string $type='success'): void` (54–60): lưu `_cart_flash` message/type.
- `index(): void` (63–79): require customer; init CSRF nếu vắng; `getItems` (side-effect tạo cart/recompute total); lấy flash và unset; render cart.
- `add(): void` (82–114): postOnly→CSRF→auth; trim maSP; filter quantity default 1 bằng FILTER_VALIDATE_INT. Empty/invalid/<1 flash lỗi+redirect; model add; RuntimeException đưa message; Throwable log và báo chung; cuối redirect cart.
- `buyNow(): void` (117–150): cùng validate; addItem; RuntimeException/Throwable flash và redirect cart ngay; thành công redirect checkout. Sản phẩm được cộng vào giỏ hiện tại, không thay thế các dòng.
- `update(): void` (153–185): cùng gate; quantity thiếu default null (khác add default 1); validate; model update; lỗi nghiệp vụ hiển thị; lỗi khác log+generic; redirect.
- `remove(): void` (188–210): gate, validate maSP; model remove; Throwable log/generic; redirect.
- `clear(): void` (213–229): gate; model clear; Throwable log/generic; redirect.

Trong các handler, `$user` là session array; `$maSP` string; `$quantity` integer hoặc false; exceptions nhánh nghiệp vụ/runtime. Redirect kết thúc request. Form không giữ giá trị lỗi quantity vì mọi kết quả quay về cart. `removeItem` không báo khi ID không tồn tại nên UI vẫn có thông báo xóa thành công.

### Đánh giá

Ranh giới owner được áp vào SQL bằng MaND; controller không tin owner ID từ client. Add có transaction/locks. Rủi ro: update stock check race; getItems gây write trong GET; clear hai writes không atomic; float dùng cho tiền (DECIMAL DB cast sang số có thể mất chính xác ở giá trị lớn/định dạng). Cân nhắc integer minor units/decimal-safe math và transaction phù hợp; chỉ là đề xuất.
