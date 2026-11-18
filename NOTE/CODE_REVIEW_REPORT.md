# Báo cáo rà soát mã nguồn T&T Computer

**Ngày rà soát:** 18/11/2026  
**Căn cứ yêu cầu chính:** `NOTE/LaTex_Document/Nhap/BanMoi.pdf` và nguồn LaTeX `NOTE/LaTex_Document/Nhap/BanMoi.tex`.  
**Phạm vi source:** mã ứng dụng, cấu hình, route, view, SQL/migration và tài liệu UML trong project; loại trừ toàn bộ `vendor/` theo yêu cầu.

> Đây là rà soát tĩnh. Không chạy ứng dụng, không kết nối MySQL, không chạy SQL/migration hay kiểm thử tích hợp. Trạng thái phản ánh bằng chứng trong snapshot source, không xác nhận triển khai thực tế trên môi trường.

## PHẦN 1: TỔNG QUAN

### Kiến trúc và cấu trúc

Ứng dụng PHP tự tổ chức theo MVC nhẹ: `public/index.php` khởi tạo session/autoload và dispatch; `routes/web.php` ánh xạ URL; `app/Controllers` điều phối; `app/Models` truy vấn PDO; `app/views` render HTML; `app/Core` chứa Router, Database, Controller, View; `app/Services/SePayPayment.php` đóng gói tích hợp SePay. SQL thiết kế nằm tại `NOTE/database.sql`, fixture ở `NOTE/dulieuao.sql`, migration trong `database/migrations/`.

Các nhóm chức năng thấy trong route/source: đăng ký/đăng nhập (kể cả Google), duyệt catalog, giỏ, checkout COD/online, đơn hàng khách và quản trị, hồ sơ/địa chỉ, quản lý tài khoản nhân viên, sản phẩm/danh mục, tồn kho, thống kê. Các khuyến mãi và voucher được mô tả ở UC16–UC18 nhưng không thấy triển khai.

### Tài liệu đã kiểm tra

- Tài liệu gốc `NOTE/LaTex_Document/Nhap/BanMoi.pdf`; đối chiếu nội dung qua LaTeX đi kèm `BanMoi.tex` (18 UC, actor, luồng, quy tắc nghiệp vụ).
- Sơ đồ Use Case, Class, Database dạng ảnh: `UseCase.png`, `class.png`, `database.png`; mã sơ đồ `codeumlClass.txt`, `codeumldatabase.txt`.
- `NOTE/database.sql`, `NOTE/dulieuao.sql`, các migration trong `database/migrations/`.
- Các tài liệu mô tả cấu trúc/traceability trong `NOTE/TongHop/DoAn1CongThanhDucThinh_Documentation_v4/` và tài liệu consolidated trong `NOTE/TongHop/TongHop_Consolidated/` được tham khảo chéo. Chúng không thay thế tài liệu gốc; một số snapshot cũ ghi nhận trạng thái trước khi các module quản trị và SePay xuất hiện.
- `NOTE/AGENTS.md`, `NOTE/rule.txt`, `NOTE/note.txt`, `NOTE/BaoCao.txt`, các PDF hướng dẫn/báo cáo khác trong `NOTE/` được nhận diện; không coi chúng là đặc tả nghiệp vụ ưu tiên khi mâu thuẫn với BanMoi.

Không tìm thấy tài liệu ngoài `NOTE/` định nghĩa thêm use case chuẩn. Các file CSS/JS/assets được kiểm kê; kiểm tra sâu tập trung vào luồng nghiệp vụ PHP/SQL. Không kiểm tra hành vi runtime, cấu hình server thực tế, dữ liệu database đang chạy, hoặc callback thật từ Google/SePay.

### Cách ước lượng tương thích

Chấm mỗi UC theo mức: hoàn thành = 1; một phần = 0,5; sai nghiệp vụ = 0; chưa triển khai = 0. Chia tổng điểm cho 18 UC. Với phân loại tại Phần 3, kết quả là **10 UC hoàn thành + 4 UC một phần + 4 UC chưa triển khai = 12/18 = 66,7%**. Đây là tỷ lệ bao phủ chức năng theo tài liệu, không phải tỷ lệ chất lượng hay xác nhận nghiệm thu; một UC “hoàn thành” vẫn có thể có rủi ro bảo mật/chất lượng riêng.

## PHẦN 2: ĐÁNH GIÁ KIẾN TRÚC MVC

### Điểm phù hợp

- Router và front controller riêng; route được khai báo tập trung tại `routes/web.php`.
- Phần lớn Controller gọi Model rồi render View; truy vấn dữ liệu chủ yếu đặt trong Models. Ví dụ `ProductController` → `Product`, `CartController` → `Cart`, `CheckoutController` → `Order`.
- View templates tập trung trong `app/views`; escape HTML được áp dụng ở nhiều dynamic output.
- `Database` cung cấp kết nối PDO tập trung. Hầu hết truy vấn có tham số sử dụng prepared statements; các đoạn sort động của catalog lấy giá trị từ allowlist trong `app/Models/Product.php`.
- Tích hợp SePay có lớp Service riêng (`app/Services/SePayPayment.php`).

### Sai lệch và bảo trì

- `app/Controllers/AboutController.php:18,21` tự gọi `require` vào `app/Views/...` với chữ hoa; thư mục thực tế là `app/views` chữ thường. Trên filesystem phân biệt hoa/thường trang có thể lỗi; đồng thời controller bỏ qua `View`/base Controller và nhân đôi render/layout.
- `app/Controllers/StatisticsController.php` truy vấn PDO trực tiếp trong Controller (các SELECT thống kê), trái với ranh giới Model; phần lớn module khác đặt SQL trong Model.
- `NOTE/LaTex_Document/Nhap/codeumlClass.txt` là sơ đồ lớp thiết kế, không phản ánh đầy đủ source hiện hành (các class Payment, Inventory, Statistics, CatalogManagement, OrderManagement và các method Google/địa chỉ/quản trị mới thiếu hoặc khác). Cần cập nhật UML để tránh hiểu nhầm kiến trúc.
- Sinh mã định danh theo kiểu lấy mã lớn nhất rồi tăng (ví dụ helper trong `Product.php`) có thể va chạm khi nhiều thao tác đồng thời; cần xác nhận tất cả đường tạo ID và xử lý unique violation.
- Controller lặp lại một số quy tắc auth/CSRF/redirect giữa module. Chưa thấy lớp middleware/filter tập trung; thay đổi chính sách có thể thiếu đồng nhất.

Không thấy SQL trực tiếp trong View. View có logic trình bày và một số định dạng/escape; chưa đủ căn cứ để kết luận có trùng chức năng nghiêm trọng ngoài các điểm cụ thể nêu trên.

## PHẦN 3: BẢNG ĐỐI CHIẾU USE CASE

| Mã UC | Tên Use Case | Actor theo tài liệu | Trạng thái | File liên quan (View → Controller → Model/DB) | Nhận xét |
|---|---|---|---|---|---|
| UC01 | Đăng ký | Khách vãng lai | Đã hoàn thành | `auth/register.php` → `AuthController::register` → `Account::register`; `NguoiDung`, `TaiKhoan` | Route GET/POST có; kiểm tra CSRF, dữ liệu và mật khẩu hash; tạo user/account trong transaction. |
| UC02 | Đăng nhập | Khách vãng lai, Google Identity | Đã hoàn thành | `auth/login.php`, `js/google-auth.js` → `AuthController::login/googleLogin` → `Account`; `TaiKhoan` | Có đăng nhập username/email, trạng thái tài khoản, Google token verification; regenerate session sau login. Cần môi trường OAuth để xác minh vận hành. |
| UC03 | Xem, tìm kiếm và lọc sản phẩm | Khách vãng lai, khách hàng | Hoàn thành một phần | `products/index.php`, `show.php`, `_card.php` → `ProductController` → `Product`; `SanPham`, `DanhMuc`, `ChiTietSanPham` | Có tìm kiếm tên, lọc danh mục, sort tên/giá và chi tiết. Đặc tả nêu lọc giá/brand/loại/thuộc tính; form/query hiện chưa có lọc khoảng giá, thương hiệu hoặc phân trang. |
| UC04 | Quản lý giỏ hàng | Khách hàng | Đã hoàn thành | `cart/index.php`, product views → `CartController` → `Cart`; `GioHang`, `ChiTietGioHang`, `SanPham` | Có thêm/sửa/xóa/dọn giỏ, kiểm tồn khi thao tác; server-side customer ownership và CSRF. Giá trị `TongTien` có vẻ được đồng bộ bởi model nhưng cần xác minh mọi nhánh cập nhật. |
| UC05 | Đặt hàng | Khách hàng | Hoàn thành một phần | `checkout/index.php` → `CheckoutController::placeOrder` → `Order::placeOrder`; `DonHang`, `ChiTietDonHang`, `SanPham`, `LichSuKho`, giỏ | Tạo đơn, khóa giỏ/chi tiết, trừ tồn và ghi lịch sử trong transaction; giá được chụp vào chi tiết. Tuy nhiên đặc tả cho voucher không được triển khai và giảm giá chưa có; UC05 phụ thuộc UC18 theo đặc tả nhưng không có luồng voucher. |
| UC06 | Thanh toán | Khách hàng, cổng thanh toán | Hoàn thành một phần | checkout/success → `CheckoutController`, `PaymentController` → `Order`, `SePayPayment`; `DonHang`, `GiaoDichThanhToan` | COD và SePay online/IPN có route. IPN kiểm tra secret và số tiền; model cập nhật trạng thái đơn đã thanh toán. Không thấy ghi nhận dòng giao dịch vào bảng `GiaoDichThanhToan`, nên lịch sử giao dịch đặc tả/ERD không được hiện thực đầy đủ. Chưa xác minh gateway thật. |
| UC07 | Theo dõi và hủy đơn hàng | Khách hàng | Đã hoàn thành | `orders/index.php`, `show.php` → `OrderController` → `Order`; `DonHang`, `ChiTietDonHang`, `SanPham`, `LichSuKho` | Truy vấn đơn theo chủ sở hữu; hủy giới hạn trạng thái và có hoàn kho/ghi sổ trong transaction. |
| UC08 | Quản lý tài khoản | Khách hàng | Đã hoàn thành | `account/index.php` → `AccountController` → `Account`, `DeliveryAddress`; `NguoiDung`, `TaiKhoan`, `DiaChiNhanHang` | Có sửa hồ sơ/đổi mật khẩu và quản lý địa chỉ giao hàng; thao tác địa chỉ giới hạn theo khách hiện tại. |
| UC09 | Quản lý đơn hàng | Nhân viên, quản trị viên | Đã hoàn thành | `admin/orders.php`, `order-detail.php` → `OrderManagementController` → `Order`; `DonHang`, `ChiTietDonHang`, `LichSuKho` | Có danh sách, chi tiết, chuyển trạng thái và xác nhận hoàn hàng. Controller cho phép NhanVien/QuanTriVien; model thực thi transition và hoàn kho một lần. |
| UC10 | Kiểm tra tồn kho | Nhân viên | Hoàn thành một phần | `admin/inventory.php` → `InventoryController` → `Inventory`; `SanPham`, `LichSuKho` | Có trang xem tồn và lịch sử, nhưng route gọi `AdminController::authorize()` nên chỉ quản trị viên truy cập. Actor nhân viên trong đặc tả bị chặn. Tồn thấp hiển thị qua thống kê, không phải quyền tra cứu riêng cho nhân viên. |
| UC11 | Quản lý sản phẩm | Quản trị viên | Đã hoàn thành | `admin/products.php` → `CatalogManagementController` → `Product`; `SanPham`, `ChiTietSanPham`, `DanhMuc` | Có tạo/sửa/trạng thái và giới hạn cập nhật tồn kho ngoài module này. Chưa có hình thức upload; quản trị nhập đường dẫn ảnh. Không hỗ trợ khuyến mãi UC16. |
| UC12 | Quản lý danh mục | Quản trị viên | Đã hoàn thành | `admin/categories.php` → `CatalogManagementController` → `Product`; `DanhMuc`, `SanPham` | Có thêm/sửa/đổi trạng thái/xóa có điều kiện. ERD/schema gốc không đồng nhất hoàn toàn với code: trạng thái danh mục tồn tại trong SQL nhưng không có trong code UML. |
| UC13 | Quản lý người dùng | Quản trị viên | Hoàn thành một phần | `admin/users.php` → `UserManagementController` → `Account`; `NguoiDung`, `TaiKhoan` | Quản lý tài khoản nhân viên (tạo/sửa/trạng thái), không thấy chỉnh sửa/khóa/khôi phục đầy đủ tài khoản khách như phạm vi UC13 mô tả. Không xóa cứng. |
| UC14 | Quản lý kho | Quản trị viên | Đã hoàn thành | `admin/inventory.php` → `InventoryController` → `Inventory`; `SanPham`, `LichSuKho` | Có nhập/điều chỉnh/xuất và ghi người, thời điểm, delta; cập nhật tồn và history trong transaction. Quyền admin phù hợp UC14. |
| UC15 | Xem thống kê | Quản trị viên | Đã hoàn thành | `admin/statistics.php` → `StatisticsController` → SQL trong Controller; `DonHang`, `ChiTietDonHang`, `SanPham`, `DanhMuc` | Có chọn ngày, trạng thái đơn, top sản phẩm/doanh thu hoàn thành và tồn thấp. SQL nằm trong Controller; doanh thu cần xem cách tính theo dòng hàng khi có hủy/hoàn và chênh lệch so với tổng đơn. |
| UC16 | Quản lý chương trình giảm giá | Quản trị viên | Chưa triển khai | Không có View/route/controller/model/bảng chương trình | Không tìm thấy triển khai; không có khái niệm campaign, phạm vi áp dụng, thời hạn hay giá hiệu lực. |
| UC17 | Quản lý voucher | Quản trị viên | Chưa triển khai | Không có View/route/controller/model/bảng voucher | Không có mã voucher, giới hạn sử dụng tổng/theo khách, hay ghi nhận lượt dùng. |
| UC18 | Áp dụng voucher khi đặt hàng | Khách hàng | Chưa triển khai | Checkout hiện không có trường/route/model voucher; không có bảng sử dụng | Không kiểm tra điều kiện voucher, không tái tính tổng, không lưu voucher/giảm giá thực tế với đơn. |

**Tổng trạng thái:** 10 hoàn thành, 5 một phần, 3 chưa triển khai. Không có UC nào kết luận “sai nghiệp vụ” toàn bộ; các sai lệch cụ thể được đánh dấu một phần và phân tích tiếp ở Phần 5.

## PHẦN 4: ĐỐI CHIẾU DATABASE

Nguồn schema được kiểm tra là `NOTE/database.sql`; đây là SQL thiết kế/khởi tạo, không phải bằng chứng cấu trúc database đang chạy. File có 12 bảng: `NguoiDung`, `DiaChiNhanHang`, `TaiKhoan`, `DanhMuc`, `SanPham`, `ChiTietSanPham`, `GioHang`, `ChiTietGioHang`, `DonHang`, `ChiTietDonHang`, `GiaoDichThanhToan`, `LichSuKho`.

### Khớp ở mức chính

- Các nhóm thực thể chính trên sơ đồ database phù hợp SQL và Model: tài khoản/người dùng, danh mục/sản phẩm/chi tiết, giỏ/chi tiết giỏ, đơn/chi tiết đơn, giao dịch, lịch sử kho.
- Khóa ngoại và UNIQUE quan trọng đã khai báo: tài khoản–người dùng 1:1; giỏ–người dùng 1:1; sản phẩm–chi tiết sản phẩm 1:0..1; giỏ–sản phẩm duy nhất theo từng giỏ; đơn–chi tiết 1:N; danh mục–sản phẩm 1:N.
- Có CHECK cho giá/tổng/số lượng dương, vai trò/trạng thái (mức thực thi phụ thuộc phiên bản MySQL/MariaDB).
- `Order::placeOrder` dùng bảng đơn/chi tiết, sản phẩm và lịch sử kho; `Cart`, `Account`, `DeliveryAddress`, `Inventory` sử dụng các bảng tương ứng.

### Thiếu, thừa, khác sơ đồ và ràng buộc

- **Thiếu theo nghiệp vụ:** không có bảng chương trình giảm giá, voucher, lịch sử/lượt sử dụng voucher, hoặc các cột snapshot voucher/discount trên đơn/chi tiết. Đây là thiếu schema tương ứng UC16–UC18.
- **Bảng thêm so với sơ đồ UML gốc:** `DiaChiNhanHang` không xuất hiện trong `codeumldatabase.txt` nhưng có trong SQL và được dùng bởi `DeliveryAddress`. `NguoiDung.DiaChiNhanHang` vẫn còn, nên đồng thời lưu địa chỉ mặc định và sổ nhiều địa chỉ; phải duy trì đồng bộ giữa hai dạng.
- **ERD ảnh** cũng mô tả 11 bảng, không có `DiaChiNhanHang`; một số kiểu dữ liệu/thuộc tính khác SQL như `NVARCHAR` so với `VARCHAR`, và thiếu `GoogleID`, `TrangThai` danh mục, `DaHoanKho`. **Use Case diagram ảnh** chỉ thể hiện UC01–UC15, trong khi phần danh sách/đặc tả cùng tài liệu có UC16–UC18; đây là mâu thuẫn nội bộ tài liệu cần sửa.
- **Cột không đồng nhất:** SQL có `TaiKhoan.GoogleID`, `TaiKhoan.TrangThai`, `DanhMuc.TrangThai`, `DonHang.DaHoanKho`; mã UML database bỏ qua một số trường này. `SanPham` lưu `TrangThai` ở `ChiTietSanPham`, không ở bảng `SanPham`; cần hiểu đúng khi truy vấn/đổi trạng thái.
- `GiaoDichThanhToan` có FK tới đơn, nhưng luồng IPN hiện chỉ cập nhật `DonHang.TrangThaiTT`; chưa thấy INSERT/UPDATE giao dịch vào bảng. Quan hệ một-nhiều giao dịch trên ERD vì vậy chưa được tận dụng cho lịch sử/thử lại.
- `DonHang` FK `MaND` và `MaNV` cùng tham chiếu `NguoiDung`; trong code lấy actor nhân viên từ account và dùng user ID. Cần xác minh tính toàn vẹn vai trò vì FK không bảo đảm `MaNV` thật sự là nhân viên.
- Khóa ngoại sản phẩm trong `ChiTietDonHang` giữ tham chiếu sản phẩm hiện tại; không xóa sản phẩm được giao dịch là hợp lý, nhưng snapshot tên/thuộc tính chưa có. Giá đơn giá được snapshot.
- Nhiều giá trị trạng thái là chuỗi rời rạc; CHECK của `TrangThaiDH` dùng `HEX()` các chuỗi tiếng Việt. Khả năng tương thích cần kiểm tra trên phiên bản DB thực tế, đặc biệt CHECK chỉ được thực thi từ các phiên bản MySQL nhất định.
- `NOTE/database.sql:16–27` DROP toàn bộ bảng trước khi tạo lại; chạy nhầm vào DB đã có dữ liệu sẽ mất dữ liệu. Không thực thi trong lượt rà soát.
- Migration địa chỉ và migration trạng thái/hàng hoàn cần đối chiếu thứ tự với schema gốc; schema hiện có thể đã chứa một số cột/bảng nên chạy lặp có nguy cơ duplicate. Trạng thái migration trong môi trường không xác minh được.
- Không thấy bảng promotion/voucher và không thấy bảng giao nhận/đối tác vận chuyển; vận chuyển phức tạp nằm ngoài phạm vi BanMoi.

## PHẦN 5: ĐÁNH GIÁ NGHIỆP VỤ

### Luồng tương đối đầy đủ

- **Đăng ký/đăng nhập:** kiểm tra trường, tài khoản, mật khẩu; password hash/verify; tài khoản không hoạt động bị chặn; Google Identity tích hợp ở mức source. Đăng nhập regenerate session.
- **Giỏ và checkout:** kiểm tra customer/session, CSRF, tồn kho và dữ liệu người nhận; tạo đơn, chi tiết, trừ kho, ghi sổ kho và xóa giỏ trong transaction. Có khóa hàng/điều kiện cập nhật tồn để hạn chế oversell.
- **Hủy và xử lý đơn:** khách chỉ hủy đơn đủ điều kiện; trạng thái admin được kiểm tra theo transition; hoàn kho có cờ `DaHoanKho` chống hoàn nhiều lần và history.
- **Kho:** biến động kho có transaction và lịch sử actor/delta/time; phân biệt tồn khả dụng trong `SoLuongTon` với tồn kho thực tế được ghi nhận trong hệ thống. Không suy ra có nghiệp vụ quản lý lô/serial.
- **Quản trị:** các POST thay đổi trạng thái có CSRF; controller kiểm tra vai trò và trạng thái account ở nhiều module.

### Thiếu/sai lệch có thể ảnh hưởng nghiệm thu

- **Voucher/khuyến mãi:** UC16–18 hoàn toàn thiếu, ảnh hưởng chức năng và tổng giá checkout theo tài liệu.
- **Bộ lọc catalog:** đặc tả yêu cầu lọc theo thuộc tính/brand/giá; triển khai hiện tìm tên, category, sort giá/tên, chưa phải lọc khoảng giá/brand/loại. Không có phân trang; với catalog lớn sẽ tải toàn bộ kết quả.
- **UC10 actor:** đặc tả giao quyền kiểm tra tồn cho nhân viên; route tồn kho yêu cầu admin nên nhân viên không thể thực hiện đúng vai trò.
- **UC13:** quản trị user hiện tập trung tạo/sửa/trạng thái nhân viên; đặc tả bao quát quản lý người dùng. Cần xác định rõ có bao gồm khách hàng hay không trước nghiệm thu.
- **Online payment:** gateway IPN cập nhật trạng thái thanh toán đơn nhưng không tạo audit row trong `GiaoDichThanhToan`; callback/return không thay thế xác minh giao dịch production. Nếu khách thanh toán ngoài hệ thống sau khi đơn bị hủy, xử lý hoàn tiền/đối soát không được thể hiện trong source.
- **Giao hàng thất bại/hoàn:** model có bước xác nhận hoàn kho; trạng thái “Giao hàng thất bại” không đồng nghĩa tự động hoàn kho cho đến khi thao tác xác nhận. UI/quy trình này cần được phản ánh rõ trong tài liệu.
- **Giá đơn:** model tính tiền từ `SanPham.GiaBan` tại lúc tạo đơn, lưu `DonGia`; không có discount. Mọi thay đổi giá trước checkout được tính lại (phù hợp snapshot khi đặt, nhưng không đạt yêu cầu giá khuyến mãi thống nhất do campaign không tồn tại).
- **Cạnh tranh đồng thời:** tồn kho được khóa/cập nhật có điều kiện; tuy nhiên cấp ID dạng max+1 nếu có trong module quản trị là điểm dễ va chạm đồng thời; UUID-like mã đơn giảm rủi ro cho đơn hàng.
- **Địa chỉ:** sổ địa chỉ và địa chỉ profile cùng tồn tại; tạo/sửa/xóa có logic cập nhật liên quan, cần kiểm thử các trường hợp xóa địa chỉ đang là địa chỉ profile, trùng địa chỉ, và race với checkout.

## PHẦN 6: ĐÁNH GIÁ BẢO MẬT

| Mức | Nguy cơ / bằng chứng | Đánh giá |
|---|---|---|
| High | `NOTE/database.sql:16–27` có DROP TABLE không điều kiện (trong script khởi tạo). | Rủi ro vận hành mất dữ liệu nếu chạy nhầm trên DB có dữ liệu. Đây không phải lỗ hổng request runtime; cần quy trình backup/môi trường rõ ràng. |
| Medium | `PaymentController::ipn` so khớp secret header và amount; nhưng callback không ghi bảng giao dịch, và không thấy chống replay bằng transaction ID duy nhất từ gateway trong schema. | Secret/amount là kiểm tra hữu ích, nhưng auditability/idempotency theo từng giao dịch và xử lý hoàn tiền chưa đầy đủ; xem xét chữ ký, mã giao dịch, idempotency theo hợp đồng SePay. |
| Medium | `AboutController.php:18,21` dùng path `app/Views` không khớp `app/views`. | Có thể làm route `/about` lỗi trên Linux; ảnh hưởng khả dụng khi triển khai. |
| Low | `Product::all()` không giới hạn kết quả/phân trang. | Có thể gây tải lớn/DoS chi phí thấp khi catalog tăng; chưa thấy rate limiting. |
| Low | Giá trị trạng thái chuỗi và CHECK phụ thuộc DB engine/version. | Nếu DB bỏ qua CHECK, các giá trị không hợp lệ có thể lọt khi dữ liệu được ghi ngoài ứng dụng. |

### Biện pháp hiện diện

- Prepared statements được dùng rộng rãi; SQL ORDER BY catalog được whitelist.
- Password được lưu bằng `password_hash` và xác minh bằng `password_verify` trong `Account`.
- CSRF token `hash_equals` có trên đa số form POST; auth/session regeneration sau login; cookie session thiết lập HttpOnly, SameSite=Lax và Secure theo HTTPS tại `public/index.php`.
- Admin kiểm tra role/status trong `AdminController::authorize`; order management kiểm tra role NhanVien/QuanTriVien cùng account status. Các thao tác khách lọc theo owner.
- Nhiều view escape dữ liệu bằng `htmlspecialchars`; không thấy upload endpoint (ảnh nhập dưới dạng đường dẫn).

Không phát hiện bằng chứng rõ về SQL injection trực tiếp trong các đường truy vấn đã rà; đây không phải cam kết không có lỗi vì không chạy phân tích động. Chưa thấy CSP/rate limiting/lockout kiểm soát brute force trong phạm vi đã đọc. Rủi ro XSS cần tiếp tục kiểm tra từng context; không kết luận toàn bộ view an toàn chỉ từ một số helper escape.

## PHẦN 7: CHỨC NĂNG NGOÀI TÀI LIỆU

Không xác định chắc chắn chức năng nào là “ngoài tài liệu” so với toàn bộ nội dung BanMoi, vì tài liệu có mô tả actor Google và giao dịch online. Các phần source vượt mức mô tả chi tiết hoặc được cụ thể hóa gồm:

- Đăng nhập/liên kết Google bằng ID token (`AuthController`, `GoogleID`) — UC02 có Google Identity nên hợp lý; tài liệu nên mô tả link account và quy tắc email/identity rõ hơn.
- SePay cụ thể với IPN và trang return (`PaymentController`, `SePayPayment`) — phù hợp UC06 ở mức cổng thanh toán, nhưng tên nhà cung cấp/định dạng callback không nằm trong đặc tả tổng quát; cập nhật tài liệu triển khai.
- Sổ nhiều địa chỉ giao hàng (`DiaChiNhanHang`, `DeliveryAddress`) — nằm trong phạm vi quản lý địa chỉ, nên làm rõ trong UC08 và ERD.
- Màn hình dashboard, top sản phẩm/tồn thấp và quản lý nhân viên cụ thể hóa quản trị/thống kê; nhìn chung hợp lý với UC13/15.
- Trang giới thiệu `/about` — chức năng nội dung phụ không được liệt kê thành UC; ghi nhận là ngoài danh sách UC, cập nhật catalog chức năng nếu cần nghiệm thu.

## PHẦN 8: DANH SÁCH VẤN ĐỀ CẦN KHẮC PHỤC

| STT | Vấn đề | Mức độ | File/vị trí | Hướng khắc phục |
|---:|---|---|---|---|
| 1 | UC16–UC18 giảm giá/voucher chưa có trong code/schema. | P1 | routes, `app/Models/Order.php`, `NOTE/database.sql` | Bổ sung theo đặc tả: campaign/voucher, validate server-side, giới hạn đồng thời, lưu snapshot giá/discount; hoặc sửa đặc tả nếu ngoài phạm vi nghiệm thu. |
| 2 | Nhân viên bị chặn khỏi chức năng UC10 kiểm tra tồn. | P1 | `app/Controllers/InventoryController.php::index/update`, `AdminController::authorize` | Đồng bộ ma trận actor/role với tài liệu; tách quyền xem tồn của nhân viên khỏi quyền điều chỉnh kho nếu đó là quy tắc mong muốn. |
| 3 | Thanh toán IPN không lưu lịch sử vào `GiaoDichThanhToan`, chưa thể hiện idempotency theo transaction gateway. | P1 | `app/Controllers/PaymentController.php::ipn`, `app/Models/Order.php::markOnlineOrderPaid`, schema | Lưu mã giao dịch duy nhất và trạng thái/amount/time trong transaction; xử lý callback lặp, thanh toán đến sau hủy, đối soát/refund. |
| 4 | SQL bootstrap phá hủy bảng hiện hữu. | P1 | `NOTE/database.sql:16–27` | Không chạy trên DB có dữ liệu; tách schema init/migration và thêm quy trình backup/target verification. |
| 5 | Route about sai chữ hoa/thường và bypass View MVC. | P1 | `app/Controllers/AboutController.php:18,21` | Dùng `Controller::view()`/`App\Core\View` và đường dẫn chuẩn `app/views`; xác minh trên Linux. |
| 6 | Lọc giá/brand/loại và phân trang theo UC03 còn thiếu. | P1 | `app/Models/Product.php::all`, `app/views/products/index.php` | Bổ sung bộ lọc từ whitelist/validated input và phân trang; đối chiếu chính xác trường thuộc tính yêu cầu. |
| 7 | Sơ đồ Class/ERD không đồng nhất với triển khai hiện tại; địa chỉ và nhiều cột mới thiếu. | P2 | `NOTE/LaTex_Document/Nhap/codeumlClass.txt`, `codeumldatabase.txt`, PNG | Cập nhật sơ đồ từ schema/code hiện hành và ghi rõ cardinality, bảng địa chỉ, GoogleID, trạng thái, cờ hoàn kho, module mới. |
| 8 | SQL thống kê nằm trong Controller. | P2 | `app/Controllers/StatisticsController.php` | Chuyển truy vấn/tổng hợp sang Model/Repository để giữ trách nhiệm MVC và dễ kiểm thử. |
| 9 | UC13 quản trị người dùng chỉ rõ cho nhân viên, chưa bao quát khách hàng. | P2 | `UserManagementController`, `Account`, UC13 BanMoi | Chốt phạm vi quản trị user; nếu yêu cầu bao gồm khách, bổ sung tìm/khóa/mở khóa an toàn, giữ lịch sử giao dịch. |
| 10 | Sơ đồ/đặc tả thiếu nhánh Google linking, SePay IPN, sổ địa chỉ và trang About. | P2 | `BanMoi.tex`, diagram files | Cập nhật tài liệu thiết kế và traceability với chức năng source bổ sung. |
| 11 | Catalog tải không phân trang và chưa có giới hạn truy vấn. | P2 | `Product::all` | Thêm phân trang và giới hạn kết quả, chỉ mục phù hợp sau khi khảo sát dữ liệu thực. |
| 12 | Trạng thái dạng chuỗi và CHECK có phụ thuộc phiên bản DB. | P2 | `NOTE/database.sql` trạng thái DonHang | Chốt phiên bản MySQL/MariaDB, xác minh CHECK thực thi, dùng migration có kiểm soát/enum lookup phù hợp. |
| 13 | Sinh ID kiểu max+1 có nguy cơ trùng khi concurrent writes. | P2 | `app/Models/Product.php` helper ID và các call site | Dùng auto-increment/UUID hoặc xử lý retry trên unique conflict; kiểm tra mọi module cấp mã. |

## PHẦN 9: KẾT LUẬN

Source có cấu trúc MVC nhẹ tương đối rõ, dùng PDO parameterization, hash mật khẩu, CSRF ở các POST nhạy cảm, session regeneration và transaction cho nhiều nghiệp vụ đơn hàng/kho. Phần catalog, cart, account, checkout, order lifecycle và admin cơ bản đã có các luồng thực thi thật, không chỉ dừng ở file giao diện.

Ước lượng tương thích theo UC là **69,4% (12,5/18 điểm)** theo phương pháp ở Phần 1. Mức này bị kéo xuống bởi UC16–18 chưa triển khai; UC03, UC05, UC06, UC10, UC13 còn thiếu một phần hoặc lệch actor/DB. Cần ưu tiên UC16–18 nếu chúng thuộc tiêu chí nghiệm thu; đồng thời xử lý quyền kiểm tra tồn của nhân viên và lịch sử giao dịch thanh toán. Cập nhật UML/traceability để phản ánh source sau đó.

Các chức năng hoàn thành theo đặc tả: UC01, UC02, UC04, UC07, UC08, UC09, UC11, UC12, UC14, UC15. Hoàn thành một phần: UC03, UC05, UC06, UC10, UC13. Chưa triển khai: UC16, UC17, UC18. Tỷ lệ không thay thế kiểm thử nghiệp vụ; các luồng Google, SePay, quyền DB, callback IPN, cạnh tranh tồn kho, và trạng thái migration chưa thể xác minh nếu không có môi trường chạy.

**Thứ tự đề xuất:** (1) làm rõ UC16–18 và hoàn thiện logic giảm giá/voucher; (2) sửa quyền actor tồn kho, bổ sung audit/idempotency payment; (3) bổ sung bộ lọc catalog và làm rõ phạm vi quản trị user; (4) xử lý lỗi case-sensitive About và truy vấn thống kê trong Controller; (5) cập nhật schema/UML và quy trình migration an toàn; (6) xác minh trên DB/runtime tương thích trước nghiệm thu.
