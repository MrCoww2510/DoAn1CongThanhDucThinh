# Đối chiếu yêu cầu nghiệp vụ với source

Ngày phân tích: 2026-10-19. Nguồn yêu cầu: `rule.txt` và `LaTex_Document/Nhap/BanMoi.tex`; nguồn thực thi ưu tiên: `routes/web.php`, controller/model/view, `database.sql`. Chỉ kết luận có/không có bằng chứng trong snapshot tĩnh; không xác nhận runtime.

| UC | Phạm vi yêu cầu | Kết quả đối chiếu source | Bằng chứng / giới hạn |
|---|---|---|---|
| UC01 | Đăng ký | Có | `routes/web.php:24-25`, AuthController và Account model. |
| UC02 | Đăng nhập | Có | `routes/web.php:26-28`, AuthController. |
| UC03 | Xem/tìm/lọc sản phẩm | Một phần | `routes/web.php:20-22`, ProductController/Product. Có tìm kiếm và lọc danh mục/sort; chưa thấy lọc giá/brand/phân trang như yêu cầu `rule.txt:218-232`. |
| UC04 | Quản lý giỏ | Có | `/cart` GET và POST add/buy-now/update/remove/clear ở `routes/web.php:41-46`; Cart model. |
| UC05 | Đặt hàng | Có, COD | Checkout POST và `Order::placeCodOrder`; giao dịch tạo đơn, chi tiết, giữ kho và dọn cart trong transaction theo `modules/orders_admin.md`. |
| UC06 | Thanh toán | Một phần | Checkout chọn COD; không thấy cổng trực tuyến/callback. `GiaoDichThanhToan` trong schema không chứng minh tích hợp. |
| UC07 | Theo dõi/hủy đơn | Có | GET `/orders`, GET `/orders/{orderId}`, POST cancel tại `routes/web.php:53-55`; model giới hạn đơn theo khách và điều kiện trạng thái. |
| UC08 | Quản lý tài khoản | Có cho hồ sơ khách | `/account` GET/POST ở dòng 31-32; Account model. |
| UC09 | Quản lý đơn bởi nhân viên/admin | Chưa thấy triển khai | Không có route nghiệp vụ quản trị đơn trong 27 route khai báo; controller OrderController là luồng khách hàng. |
| UC10 | Tra tồn kho bởi nhân viên | Chưa thấy triển khai | Không có route/controller tra kho độc lập; tồn kho chỉ được đọc/điều chỉnh trong luồng giỏ/đặt/hủy đơn. |
| UC11 | Quản lý sản phẩm admin | Chưa thấy triển khai | Không có route CRUD sản phẩm; ProductController là đọc danh sách/chi tiết. |
| UC12 | Quản lý danh mục admin | Chưa thấy triển khai | Không có route CRUD danh mục; model chỉ truy vấn danh sách. |
| UC13 | Quản lý người dùng | Một phần/có phần quản trị nhân viên | `routes/web.php:35-38`, UserManagementController tạo/sửa nhân viên, đổi trạng thái; không chứng minh mọi thao tác quản trị tài khoản khách theo UC. |
| UC14 | Quản lý kho admin | Chưa thấy triển khai | Không có route nhập/điều chỉnh kho riêng. Lịch sử kho được ghi tự động bởi luồng đặt/hủy đơn. |
| UC15 | Thống kê | Chưa thấy triển khai | Không có route/controller báo cáo hoặc xuất Excel trong route inventory. |

## Mâu thuẫn/khác biệt đáng chú ý

- Các use case UC09–UC12, UC14–UC15 mô tả chức năng chưa thấy route triển khai. Đây là thiếu bằng chứng trong source hiện tại, không phải khẳng định rằng chức năng không thể tồn tại ngoài snapshot.
- Yêu cầu `rule.txt:455-463` nói session regeneration sau đăng nhập. Trong phần AuthController đã đọc, cần xác nhận riêng việc gọi `session_regenerate_id`; route/source hiện có đăng nhập nhưng không thể suy luận biện pháp này chỉ từ yêu cầu.
- UC03 và `rule.txt:218-232` nêu lọc khoảng giá, thương hiệu, phân trang; code hiện được đặc tả ở `modules/catalog.md` không có phân trang, và form không cung cấp đủ bộ lọc đó.
- Kho được trừ trong lúc đặt COD, đồng thời giao diện/đặc tả có khái niệm payment; flow được đọc không có thanh toán online.
- `DonHang` có CHECK trạng thái tại `database.sql:214-224`; đối chiếu nguyên văn trạng thái được đặc tả với CHECK và code cần lưu ý trạng thái “Giao hàng thất bại” không nằm trong CHECK theo kiểm tra trước đó. Không chạy SQL.
- `rule.txt` là prompt/yêu cầu và template LaTeX chứa mô hình/Use Case thiết kế. Chúng không được coi là bằng chứng tính năng đang chạy.

Các nhận định trên là đối chiếu tĩnh, được dẫn tới route/schema/module để người đọc tra cứu. Đề xuất triển khai, nếu có, thuộc tài liệu khuyến nghị và không được thực hiện tại source.
