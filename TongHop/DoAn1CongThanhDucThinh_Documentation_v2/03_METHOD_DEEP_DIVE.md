# Phân tích sâu method trọng yếu

Ngày 19-10-2026. Chữ ký và nhánh dưới đây được đối chiếu code tại ngày khảo sát; line number là chỉ dẫn tương đối, có thể đổi.

## `Router::add(string $path, callable|array $handler): void`

Chuẩn hóa slash, `preg_quote` static path, thay `{identifier}` bằng named capture `[^/]+`, lưu method uppercase, regex anchor cho path và optional trailing slash. Tham số path do code route cung cấp. Không kiểm trùng route; dispatch lấy match đầu tiên. Params được lọc theo key string rồi truyền positional. Route handler dạng `[class, action]` tạo controller mới mỗi request.

## `Router::dispatch(string $method, string $uri): void`

Tách URI path, rawurldecode, đọc app config/base_url rồi cắt prefix nếu khớp; duyệt route theo thứ tự, yêu cầu method trùng, preg_match path. Gọi action và return. Không có route thì 404 + template. Path hợp lệ nhưng method sai cũng đi tới 404, không phát 405. Cần kiểm thứ tự decode/prefix và encoded slash behavior khi thay đổi router.

## `View::render(string $view, array $data = [], string $layout = 'customer'): void`

Tạo path dưới `app/views`, kiểm file view/layout, nạp config/baseUrl, `extract($data, EXTR_SKIP)`, buffer view làm `$content`, include layout. `EXTR_SKIP` tránh ghi đè biến đã có. `view/layout` là chuỗi nội bộ nhưng method không canonicalize path; chỉ controller nội bộ truyền vào hiện thấy. Template chịu trách nhiệm output encoding.

## `Product::all(string $keyword='', string $categoryId='', string $sort='default'): array`

Tạo base SELECT JOIN; thêm WHERE theo điều kiện tồn tại; bind `:keyword`, `:category`; chọn ORDER BY từ map (default/name asc/name desc/price asc/price desc), execute và fetchAll. Output là list associative arrays theo PDO. Không phân trang; LIKE wildcard không escape; count không được query. Rủi ro chính là hiệu năng/list quá lớn khi catalog tăng, không phải identifier injection trong sort hiện tại.

## `CheckoutController::placeOrder(): void`

Yêu cầu request POST trước; xác minh CSRF; lấy customer; trim người nhận, điện thoại, địa chỉ, paymentMethod; validate name <=100, số đã chuẩn hóa theo regex 8–15 số có thể có +, address <=255, chỉ COD; đọc giỏ để phát hiện rỗng. Có lỗi thì render checkout giữ form; không lỗi thì gọi `Order::placeCodOrder`; redirect 303 sang success. PDO/Throwable log chi tiết server và hiển thị tổng quát; RuntimeException hiển thị message nghiệp vụ. Tác dụng phụ chỉ xảy ra qua model transaction/session/response.

## `Order::placeCodOrder(customerId, recipientName, deliveryAddress, recipientPhone): array`

Bắt đầu transaction. Lock cart; fail nếu không có. Lock cart item rows/product, kiểm quantity >=1, trạng thái sản phẩm nếu có phải trong allowlist, tồn đủ. Tính total từ giá DB hiện thời; sinh ID ngẫu nhiên dạng DH + hex; insert DonHang; mỗi item conditional decrement bảo vệ tồn và rowCount=1, ghi ChiTietDonHang với price snapshot, ghi LichSuKho loại XuatKho; xóa cart lines/reset total; commit và trả MaDH/TongTien. Bất kỳ Throwable rollback rồi ném lại. Isolation/lock behavior phụ thuộc DB engine/runtime; schema chỉ định InnoDB.

## `Order::cancelForCustomer(orderId, customerId): void`

Transaction; lock đơn theo cả ID và owner; trả RuntimeException nếu không có, đã hủy, hoặc trạng thái không đúng pending. Lock chi tiết; tăng tồn cho từng dòng, rowCount check, ghi LichSuKho loại NhapKho. Conditional update status theo owner và pending; rowCount=1, commit. Exception rollback. Khóa đơn ngăn hai yêu cầu hủy đồng thời cùng xử lý thành công.

## `UserManagementController::requireAdministrator(): void`

Kiểm role trong session; trả 401/403, redirect guest. Sau đó đọc tài khoản hiện tại từ DB và yêu cầu role QuanTriVien + status HoatDong; nếu không còn hợp lệ xóa session user rồi redirect login. Đây là tái xác thực đặc quyền mạnh hơn các controller customer đang dùng session role.
