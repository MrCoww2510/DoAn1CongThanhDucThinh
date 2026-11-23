# Danh sách ảnh cần chụp cho Chương 3 – T&T Computer

**Trạng thái tất cả ảnh:** Chưa chụp. Danh sách này là hướng dẫn thao tác, không phải ảnh đã có.

## Cách truy cập và chuẩn bị

- URL ứng dụng trong cấu hình hiện tại: `http://localhost/DoAn1CongThanhDucThinh/public`. Nếu Apache đang dùng VirtualHost hoặc cổng khác, thay phần gốc URL theo môi trường đang chạy; các đường dẫn dưới đây giữ nguyên.
- Các trang quản trị dùng tiền tố `/admin`. Nhân viên và quản trị viên phải đăng nhập bằng tài khoản có đúng vai trò. Không đưa mật khẩu vào ảnh hoặc tài liệu.
- Script `database/dulieuao.sql` có tài khoản demo, danh mục, sản phẩm và địa chỉ; không nạp lại script lên cơ sở dữ liệu đang sử dụng. Script không tạo đơn hàng hay voucher. Tạo đơn kiểm thử qua giao diện trong cơ sở dữ liệu thử nghiệm riêng.
- Dữ liệu sản phẩm ví dụ đã có trong seed: `SP001` (Logitech G102 Lightsync, DM001 Chuột, Logitech, loại Chuột Gaming). Giá và tồn kho trên môi trường đang chạy có thể đã thay đổi; chụp dữ liệu đang hiển thị, không sửa số liệu để khớp ví dụ.
- Dùng trình duyệt ở độ rộng đủ để thấy nội dung chính, ẩn thông báo trình duyệt có thông tin cá nhân. Chụp PNG toàn vùng trang cần chứng minh. Không chụp mật khẩu, token, thông tin cá nhân thật hay khóa SePay.
- Tên ảnh đề xuất trong LaTeX dùng `TaiLieu/AnhChuong3/`. Chỉ tạo thư mục và chép ảnh vào đó sau khi tự chụp; hiện chưa tạo thư mục ảnh, chưa chèn đường dẫn ảnh vào báo cáo.

## Nhóm A – Giao diện khách hàng

### IMG01
- **Tên file:** `IMG01_trang_chu.png`
- **Chức năng / đối tượng:** Trang chủ; khách vãng lai hoặc khách hàng.
- **Đường dẫn:** `/`
- **Thao tác:** Mở URL gốc, chưa cần đăng nhập; cuộn tới vị trí phần đầu trang có thể thấy bố cục chính và sản phẩm.
- **Cần thấy:** Header/logo, ô tìm kiếm, điều hướng/danh mục, banner hoặc khối giới thiệu nếu đang được render, khu vực sản phẩm.
- **Dữ liệu mẫu:** Sản phẩm lấy từ cơ sở dữ liệu hiện hành; không yêu cầu đơn hàng.
- **Vị trí báo cáo:** `Chapter_3/Chuong3_main.tex`, §3.1 “Sản phẩm phần mềm…” sau đoạn giới thiệu sản phẩm.
- **Caption:** Giao diện trang chủ T&T Computer.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG01_trang_chu.png}` `\caption{Giao diện trang chủ T\&T Computer}` `\label{fig:img01-trang-chu}` `\end{figure}`

### IMG02
- **Tên file:** `IMG02_danh_sach_san_pham.png`
- **Chức năng / đối tượng:** Duyệt danh sách sản phẩm; khách vãng lai/khách hàng.
- **Đường dẫn:** `/products`
- **Thao tác:** Mở trang danh sách, giữ bộ lọc mặc định, chụp phần danh sách và điều khiển phân trang nếu cùng khung hình.
- **Cần thấy:** Tiêu đề danh sách, các thẻ sản phẩm (tên/giá/ảnh), điều khiển danh mục/tìm kiếm/sắp xếp đang tồn tại.
- **Dữ liệu mẫu:** Các sản phẩm hiện có trong DB.
- **Vị trí báo cáo:** §3.2, mục chức năng khách hàng, đoạn duyệt sản phẩm.
- **Caption:** Danh sách sản phẩm trên T&T Computer.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG02_danh_sach_san_pham.png}` `\caption{Danh sách sản phẩm}` `\label{fig:img02-danh-sach-san-pham}` `\end{figure}`

### IMG03
- **Tên file:** `IMG03_tim_kiem_loc_san_pham.png`
- **Chức năng / đối tượng:** Tìm kiếm, lọc theo tiêu chí hiện có và sắp xếp; khách vãng lai/khách hàng.
- **Đường dẫn:** `/products` (query string do form sinh ra)
- **Thao tác:** Nhập Logitech hoặc chọn DM001/Chuột, Logitech, loại Chuột Gaming, khoảng giá phù hợp; bấm tìm/lọc. Chỉ chọn tiêu chí thực sự có trong form giao diện.
- **Cần thấy:** Tiêu chí đã chọn, kết quả và trạng thái lọc. Hệ thống lọc từ khóa, danh mục, thương hiệu, loại, giá và sắp xếp; không có lọc thông số kỹ thuật.
- **Dữ liệu mẫu:** SP001 và các sản phẩm khớp điều kiện trên DB hiện tại.
- **Vị trí báo cáo:** §3.3 “Minh họa quy trình nghiệp vụ” → “Tra cứu sản phẩm”.
- **Caption:** Kết quả tìm kiếm và lọc sản phẩm theo tiêu chí hỗ trợ.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG03_tim_kiem_loc_san_pham.png}` `\caption{Tìm kiếm và lọc sản phẩm}` `\label{fig:img03-tim-kiem-loc}` `\end{figure}`

### IMG04
- **Tên file:** `IMG04_chi_tiet_san_pham.png`
- **Chức năng / đối tượng:** Xem chi tiết sản phẩm; khách vãng lai/khách hàng.
- **Đường dẫn:** `/product?id=SP001`
- **Thao tác:** Mở URL và chờ ảnh/thông tin tải hoàn tất.
- **Cần thấy:** Tên, ảnh, giá, mô tả/thông số hiển thị, tồn kho hoặc lựa chọn số lượng và nút thao tác mua nếu có.
- **Dữ liệu mẫu:** SP001; nếu mã không còn tồn tại, chọn mã thực tế từ `/products` và thay URL.
- **Vị trí báo cáo:** §3.2, chức năng khách hàng, đoạn xem chi tiết.
- **Caption:** Trang chi tiết sản phẩm.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG04_chi_tiet_san_pham.png}` `\caption{Chi tiết sản phẩm}` `\label{fig:img04-chi-tiet}` `\end{figure}`

### IMG05
- **Tên file:** `IMG05_dang_ky_tai_khoan.png`
- **Chức năng / đối tượng:** Đăng ký; khách vãng lai.
- **Đường dẫn:** `/register`
- **Thao tác:** Mở form, để trống hoặc nhập dữ liệu giả chưa gửi; không tạo tài khoản nếu không cần.
- **Cần thấy:** Các trường đăng ký, nút gửi và hướng dẫn/kiểm tra dữ liệu hiện trên giao diện.
- **Dữ liệu mẫu:** Dùng dữ liệu giả, không dùng email/số điện thoại thật.
- **Vị trí báo cáo:** §3.2, chức năng khách hàng, đoạn tài khoản.
- **Caption:** Giao diện đăng ký tài khoản.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=0.85\textwidth]{TaiLieu/AnhChuong3/IMG05_dang_ky_tai_khoan.png}` `\caption{Giao diện đăng ký tài khoản}` `\label{fig:img05-dang-ky}` `\end{figure}`

### IMG06
- **Tên file:** `IMG06_dang_nhap.png`
- **Chức năng / đối tượng:** Đăng nhập; khách vãng lai.
- **Đường dẫn:** `/login`
- **Thao tác:** Mở form đăng nhập, chưa điền thông tin.
- **Cần thấy:** Trường đăng nhập, nút gửi và tùy chọn Google nếu được hiển thị/cấu hình trong môi trường.
- **Dữ liệu mẫu:** Không hiển thị hoặc lưu mật khẩu trong ảnh.
- **Vị trí báo cáo:** §3.2, chức năng khách hàng, đoạn tài khoản.
- **Caption:** Giao diện đăng nhập.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=0.85\textwidth]{TaiLieu/AnhChuong3/IMG06_dang_nhap.png}` `\caption{Giao diện đăng nhập}` `\label{fig:img06-dang-nhap}` `\end{figure}`

### IMG07
- **Tên file:** `IMG07_gio_hang.png`
- **Chức năng / đối tượng:** Xem/cập nhật giỏ hàng; khách hàng.
- **Đường dẫn:** `/cart`
- **Thao tác:** Đăng nhập bằng khách demo nếu được yêu cầu, mở chi tiết một sản phẩm, thêm vào giỏ rồi mở giỏ hàng.
- **Cần thấy:** Sản phẩm, số lượng, đơn giá/tạm tính và nút cập nhật/xóa/tiếp tục mua nếu hiện diện.
- **Dữ liệu mẫu:** Sản phẩm còn hàng như SP001; không dùng dữ liệu cá nhân thật.
- **Vị trí báo cáo:** §3.2, chức năng khách hàng, đoạn giỏ hàng.
- **Caption:** Giỏ hàng của khách hàng.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG07_gio_hang.png}` `\caption{Giỏ hàng}` `\label{fig:img07-gio-hang}` `\end{figure}`

### IMG08
- **Tên file:** `IMG08_thong_tin_tai_khoan.png`
- **Chức năng / đối tượng:** Xem thông tin tài khoản/địa chỉ; khách hàng.
- **Đường dẫn:** `/account`
- **Thao tác:** Đăng nhập khách demo, vào trang tài khoản; dùng địa chỉ demo hoặc che thông tin định danh trước khi chụp.
- **Cần thấy:** Khu vực hồ sơ và địa chỉ mà trang thực sự hiển thị; tránh chụp tên/số điện thoại nhạy cảm.
- **Dữ liệu mẫu:** Tài khoản seed có địa chỉ demo; thay/ẩn dữ liệu nếu là dữ liệu cá nhân thật.
- **Vị trí báo cáo:** §3.2, chức năng khách hàng, đoạn quản lý tài khoản/địa chỉ.
- **Caption:** Trang thông tin tài khoản khách hàng.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG08_thong_tin_tai_khoan.png}` `\caption{Thông tin tài khoản khách hàng}` `\label{fig:img08-tai-khoan}` `\end{figure}`

### IMG09
- **Tên file:** `IMG09_checkout_thong_tin_giao_hang.png`
- **Chức năng / đối tượng:** Xác nhận giỏ và thông tin đặt hàng; khách hàng.
- **Đường dẫn:** `/checkout`
- **Thao tác:** Với giỏ có sản phẩm, mở checkout và điền thông tin giao thử nghiệm. Chưa gửi đơn ở ảnh này.
- **Cần thấy:** Tóm tắt sản phẩm/đơn hàng, trường giao hàng và phần chọn phương thức thanh toán.
- **Dữ liệu mẫu:** SP001, địa chỉ thử nghiệm; không dùng thông tin cá nhân thật.
- **Vị trí báo cáo:** §3.3 → “Đặt hàng và chọn phương thức thanh toán”.
- **Caption:** Màn hình xác nhận thông tin đặt hàng.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG09_checkout_thong_tin_giao_hang.png}` `\caption{Màn hình xác nhận đặt hàng}` `\label{fig:img09-checkout}` `\end{figure}`

### IMG10
- **Tên file:** `IMG10_checkout_chon_cod.png`
- **Chức năng / đối tượng:** Chọn COD; khách hàng.
- **Đường dẫn:** `/checkout`
- **Thao tác:** Trên checkout, chọn COD và chụp trước khi xác nhận tạo đơn.
- **Cần thấy:** COD đang chọn, tóm tắt đơn và nút xác nhận. Không chú thích COD là SePay hay thanh toán đã thu tiền.
- **Dữ liệu mẫu:** Giỏ thử nghiệm có sản phẩm còn hàng.
- **Vị trí báo cáo:** §3.3 → “Đặt hàng và chọn phương thức thanh toán”.
- **Caption:** Lựa chọn thanh toán khi nhận hàng (COD).
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG10_checkout_chon_cod.png}` `\caption{Lựa chọn phương thức COD}` `\label{fig:img10-cod}` `\end{figure}`

### IMG11
- **Tên file:** `IMG11_checkout_chon_sepay_sandbox.png`
- **Chức năng / đối tượng:** Chọn thanh toán trực tuyến SePay Sandbox; khách hàng.
- **Đường dẫn:** `/checkout`
- **Thao tác:** Chọn lựa chọn thanh toán trực tuyến trong checkout, chụp trước khi điều hướng sang cổng.
- **Cần thấy:** Phương thức trực tuyến được chọn và đơn hàng/tổng tiền. Ghi rõ môi trường Sandbox; không lộ cấu hình bí mật.
- **Dữ liệu mẫu:** Giỏ hàng thử nghiệm. Cần cấu hình sandbox hoạt động để đi tiếp; nếu không cấu hình, chụp lựa chọn đang hiện và ghi trạng thái lỗi môi trường.
- **Vị trí báo cáo:** §3.2 “Tích hợp ngoài” và §3.3 quy trình đặt hàng.
- **Caption:** Lựa chọn thanh toán trực tuyến SePay Sandbox.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG11_checkout_chon_sepay_sandbox.png}` `\caption{Lựa chọn SePay Sandbox}` `\label{fig:img11-sepay-chon}` `\end{figure}`

### IMG12
- **Tên file:** `IMG12_dat_hang_thanh_cong_cod.png`
- **Chức năng / đối tượng:** Xác nhận kết quả đặt COD; khách hàng.
- **Đường dẫn:** `/checkout/success?id=<MaDH>` (đi theo luồng checkout)
- **Thao tác:** Trong DB thử nghiệm, tạo đơn bằng COD qua giao diện; chụp trang thành công sau khi hệ thống chuyển hướng.
- **Cần thấy:** Mã đơn và thông báo kết quả thực tế. Nếu trang không hiện thông báo thành công, không dùng ảnh này như bằng chứng thành công.
- **Dữ liệu mẫu:** Đơn thử nghiệm vừa tạo; ghi lại mã đơn để đối chiếu.
- **Vị trí báo cáo:** §3.3, quy trình đặt hàng.
- **Caption:** Kết quả tạo đơn hàng COD.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG12_dat_hang_thanh_cong_cod.png}` `\caption{Kết quả đặt đơn COD}` `\label{fig:img12-dat-hang-cod}` `\end{figure}`

### IMG13
- **Tên file:** `IMG13_lich_su_don_hang.png`
- **Chức năng / đối tượng:** Xem lịch sử đơn hàng; khách hàng.
- **Đường dẫn:** `/orders`
- **Thao tác:** Đăng nhập khách thử nghiệm sau khi tạo ít nhất một đơn.
- **Cần thấy:** Danh sách và trạng thái đơn, mã đơn có thể đọc được; che dữ liệu giao nhận cá nhân.
- **Dữ liệu mẫu:** Đơn thử nghiệm COD vừa tạo.
- **Vị trí báo cáo:** §3.2 khách hàng hoặc §3.3 theo dõi đơn.
- **Caption:** Danh sách đơn hàng của khách hàng.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG13_lich_su_don_hang.png}` `\caption{Lịch sử đơn hàng}` `\label{fig:img13-lich-su-don}` `\end{figure}`

### IMG14
- **Tên file:** `IMG14_chi_tiet_theo_doi_don_hang.png`
- **Chức năng / đối tượng:** Xem chi tiết/trạng thái đơn; khách hàng.
- **Đường dẫn:** `/orders/{orderId}`
- **Thao tác:** Từ lịch sử đơn mở đơn thử nghiệm.
- **Cần thấy:** Trạng thái đơn, thông tin sản phẩm và các hành động hợp lệ với trạng thái hiện tại.
- **Dữ liệu mẫu:** Đơn thử nghiệm. Tránh hiển thị thông tin người nhận thật.
- **Vị trí báo cáo:** §3.3 → “Theo dõi và hủy đơn”.
- **Caption:** Chi tiết và trạng thái đơn hàng.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG14_chi_tiet_theo_doi_don_hang.png}` `\caption{Chi tiết đơn hàng}` `\label{fig:img14-chi-tiet-don}` `\end{figure}`

### IMG15
- **Tên file:** `IMG15_huy_don_chua_thanh_toan.png`
- **Chức năng / đối tượng:** Khách yêu cầu hủy đơn chưa thanh toán, đang chờ xử lý.
- **Đường dẫn:** `/orders/{orderId}` rồi dùng nút/hành động hủy trên giao diện (POST).
- **Thao tác:** Với đơn thử nghiệm còn ở trạng thái cho phép hủy, chụp hộp xác nhận hoặc kết quả sau khi hủy. Không gọi endpoint POST bằng URL thủ công.
- **Cần thấy:** Đơn ở đúng điều kiện trước thao tác hoặc trạng thái sau hủy. Hệ thống có hoàn lại tồn kho trong luồng hủy khách hàng theo transaction; nếu chụp kết quả, dùng đối chiếu tồn kho trước/sau trong DB thử nghiệm.
- **Dữ liệu mẫu:** Đơn chưa thanh toán, đang chờ xử lý.
- **Vị trí báo cáo:** §3.3 → “Theo dõi và hủy đơn”.
- **Caption:** Hủy đơn hàng chưa thanh toán từ tài khoản khách hàng.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG15_huy_don_chua_thanh_toan.png}` `\caption{Hủy đơn hàng chưa thanh toán}` `\label{fig:img15-huy-don}` `\end{figure}`

## Nhóm B – Giao diện nhân viên

### IMG16
- **Tên file:** `IMG16_nhan_vien_danh_sach_don.png`
- **Chức năng / đối tượng:** Nhân viên xem và xử lý danh sách đơn hàng được phân quyền.
- **Đường dẫn:** `/admin/orders`
- **Thao tác:** Đăng nhập tài khoản nhân viên, mở đường dẫn; không dùng tài khoản admin để thay thế minh chứng vai trò.
- **Cần thấy:** Danh sách đơn/trạng thái và thao tác mà nhân viên thực sự được phép dùng.
- **Dữ liệu mẫu:** Đơn thử nghiệm từ IMG12.
- **Vị trí báo cáo:** §3.2 → chức năng dành cho nhân viên.
- **Caption:** Danh sách đơn hàng trong giao diện nhân viên.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG16_nhan_vien_danh_sach_don.png}` `\caption{Nhân viên quản lý đơn hàng}` `\label{fig:img16-nv-don}` `\end{figure}`

### IMG17
- **Tên file:** `IMG17_nhan_vien_cap_nhat_trang_thai_don.png`
- **Chức năng / đối tượng:** Nhân viên cập nhật trạng thái đơn trong giới hạn quyền.
- **Đường dẫn:** `/admin/orders` → mở đơn thử nghiệm.
- **Thao tác:** Đăng nhập nhân viên, chọn đơn hợp lệ, mở chi tiết; chụp giao diện trước/sau hành động cập nhật được phép.
- **Cần thấy:** Trạng thái hiện tại và điều khiển cập nhật; nếu thao tác làm thay đổi dữ liệu, chỉ thực hiện trên đơn thử nghiệm.
- **Dữ liệu mẫu:** Đơn thử nghiệm. Không tự suy diễn trạng thái nào nhân viên được phép chuyển ngoài các lựa chọn UI đang cung cấp.
- **Vị trí báo cáo:** §3.2 nhân viên; có thể liên kết §3.4 kiểm thử.
- **Caption:** Cập nhật trạng thái đơn hàng theo quyền nhân viên.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG17_nhan_vien_cap_nhat_trang_thai_don.png}` `\caption{Nhân viên cập nhật trạng thái đơn}` `\label{fig:img17-nv-cap-nhat-don}` `\end{figure}`

### IMG18
- **Tên file:** `IMG18_nhan_vien_xem_ton_kho.png`
- **Chức năng / đối tượng:** Nhân viên tra cứu tồn kho (nếu trang được cấp quyền ở cấu hình hiện hành).
- **Đường dẫn:** `/admin/inventory`
- **Thao tác:** Đăng nhập nhân viên và mở trang tồn kho; nếu bị từ chối quyền thì ghi nhận không chụp như chức năng nhân viên.
- **Cần thấy:** Danh sách mặt hàng và số lượng tồn; không mô tả nhân viên có quyền điều chỉnh nếu giao diện không cung cấp.
- **Dữ liệu mẫu:** Sản phẩm SP001 hoặc sản phẩm đang tồn tại.
- **Vị trí báo cáo:** §3.2 nhân viên, nghiệp vụ tra cứu tồn kho nếu quyền thực tế cho phép.
- **Caption:** Tra cứu tồn kho trong phạm vi quyền nhân viên.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG18_nhan_vien_xem_ton_kho.png}` `\caption{Tra cứu tồn kho}` `\label{fig:img18-nv-ton-kho}` `\end{figure}`

## Nhóm C – Giao diện quản trị viên

### IMG19
- **Tên file:** `IMG19_admin_dashboard.png`
- **Chức năng / đối tượng:** Dashboard quản trị; quản trị viên.
- **Đường dẫn:** `/admin`
- **Thao tác:** Đăng nhập bằng tài khoản admin, mở trang dashboard.
- **Cần thấy:** Các thẻ/tổng hợp và biểu đồ thực có trên trang. Không gọi doanh thu là lợi nhuận.
- **Dữ liệu mẫu:** DB hiện hành; số liệu phụ thuộc dữ liệu kiểm thử.
- **Vị trí báo cáo:** §3.2 quản trị viên.
- **Caption:** Bảng điều khiển quản trị.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG19_admin_dashboard.png}` `\caption{Bảng điều khiển quản trị}` `\label{fig:img19-admin-dashboard}` `\end{figure}`

### IMG20
- **Tên file:** `IMG20_admin_quan_ly_san_pham.png`
- **Chức năng / đối tượng:** Danh sách/quản lý sản phẩm; quản trị viên.
- **Đường dẫn:** `/admin/products`
- **Thao tác:** Đăng nhập admin, mở danh sách. Không cần sửa/xóa sản phẩm thật.
- **Cần thấy:** Bảng sản phẩm, chức năng tìm/ lọc và nút thao tác đang có.
- **Dữ liệu mẫu:** SP001.
- **Vị trí báo cáo:** §3.2 quản trị viên, quản lý sản phẩm.
- **Caption:** Giao diện quản lý sản phẩm.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG20_admin_quan_ly_san_pham.png}` `\caption{Quản lý sản phẩm}` `\label{fig:img20-admin-san-pham}` `\end{figure}`

### IMG21
- **Tên file:** `IMG21_admin_them_sua_san_pham.png`
- **Chức năng / đối tượng:** Form thêm/sửa sản phẩm; quản trị viên.
- **Đường dẫn:** `/admin/products` → mở form thêm hoặc sửa.
- **Thao tác:** Mở form và chụp trước khi lưu; không làm thay đổi sản phẩm thật.
- **Cần thấy:** Các trường sản phẩm/danh mục/giá/tồn kho và nút lưu hiện hữu.
- **Dữ liệu mẫu:** Form rỗng hoặc sản phẩm thử nghiệm; không gửi dữ liệu nếu không có DB thử nghiệm.
- **Vị trí báo cáo:** §3.2 quản trị viên, quản lý sản phẩm.
- **Caption:** Biểu mẫu cập nhật sản phẩm.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG21_admin_them_sua_san_pham.png}` `\caption{Biểu mẫu sản phẩm}` `\label{fig:img21-admin-form-san-pham}` `\end{figure}`

### IMG22
- **Tên file:** `IMG22_admin_quan_ly_danh_muc.png`
- **Chức năng / đối tượng:** Quản lý danh mục; quản trị viên.
- **Đường dẫn:** `/admin/categories`
- **Thao tác:** Mở danh sách danh mục; không xóa danh mục đang dùng.
- **Cần thấy:** Tên/mã danh mục và thao tác quản lý đang có.
- **Dữ liệu mẫu:** DM001 Chuột.
- **Vị trí báo cáo:** §3.2 quản trị viên, quản lý danh mục.
- **Caption:** Giao diện quản lý danh mục sản phẩm.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG22_admin_quan_ly_danh_muc.png}` `\caption{Quản lý danh mục}` `\label{fig:img22-admin-danh-muc}` `\end{figure}`

### IMG23
- **Tên file:** `IMG23_admin_quan_ly_nguoi_dung.png`
- **Chức năng / đối tượng:** Quản lý tài khoản/người dùng; quản trị viên.
- **Đường dẫn:** `/admin/users`
- **Thao tác:** Mở danh sách tài khoản; không sửa dữ liệu tài khoản demo nếu không cần.
- **Cần thấy:** Các trường danh sách và vai trò; che thông tin cá nhân nếu dữ liệu không phải giả lập.
- **Dữ liệu mẫu:** Tài khoản demo. Không để lộ mật khẩu/hash/token.
- **Vị trí báo cáo:** §3.2 quản trị viên, quản lý người dùng.
- **Caption:** Giao diện quản lý tài khoản người dùng.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG23_admin_quan_ly_nguoi_dung.png}` `\caption{Quản lý tài khoản người dùng}` `\label{fig:img23-admin-nguoi-dung}` `\end{figure}`

### IMG24
- **Tên file:** `IMG24_admin_quan_ly_don_hang.png`
- **Chức năng / đối tượng:** Quản trị đơn hàng; quản trị viên.
- **Đường dẫn:** `/admin/orders`
- **Thao tác:** Đăng nhập admin, mở danh sách có đơn thử nghiệm.
- **Cần thấy:** Mã đơn, thời gian/tổng tiền/phương thức/trạng thái thực tế (theo các cột UI hiện có).
- **Dữ liệu mẫu:** Đơn COD thử nghiệm IMG12.
- **Vị trí báo cáo:** §3.2 quản trị viên.
- **Caption:** Danh sách đơn hàng trong trang quản trị.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG24_admin_quan_ly_don_hang.png}` `\caption{Quản lý đơn hàng}` `\label{fig:img24-admin-don}` `\end{figure}`

### IMG25
- **Tên file:** `IMG25_admin_chi_tiet_cap_nhat_don.png`
- **Chức năng / đối tượng:** Xem/cập nhật đơn hoặc xác nhận tình huống hoàn hàng nếu UI cho phép; quản trị viên.
- **Đường dẫn:** `/admin/orders` → mở đơn thử nghiệm.
- **Thao tác:** Chụp chi tiết và các lựa chọn chuyển trạng thái thực sự hiển thị. Chỉ thực hiện chuyển trạng thái trên đơn thử nghiệm.
- **Cần thấy:** Thông tin đơn và điều khiển hành động theo quyền admin; không trình bày tự động hủy quá hạn vì không có tác vụ nền xác nhận.
- **Dữ liệu mẫu:** Đơn thử nghiệm.
- **Vị trí báo cáo:** §3.2 quản trị viên; §3.4 kiểm thử nếu có ghi nhận thao tác thực tế.
- **Caption:** Chi tiết và thao tác xử lý đơn hàng của quản trị viên.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG25_admin_chi_tiet_cap_nhat_don.png}` `\caption{Chi tiết xử lý đơn hàng}` `\label{fig:img25-admin-chi-tiet-don}` `\end{figure}`

### IMG26
- **Tên file:** `IMG26_admin_quan_ly_ton_kho.png`
- **Chức năng / đối tượng:** Xem tồn kho và lịch sử/điều chỉnh tồn theo chức năng thực có; quản trị viên.
- **Đường dẫn:** `/admin/inventory`
- **Thao tác:** Đăng nhập admin; mở trang tồn kho, chụp trạng thái ban đầu. Không điều chỉnh nếu chưa ở DB thử nghiệm.
- **Cần thấy:** Số lượng tồn, lịch sử hoặc form điều chỉnh nếu trang hiển thị; phân biệt quyền admin với quyền nhân viên.
- **Dữ liệu mẫu:** SP001 hoặc sản phẩm thử nghiệm; đối chiếu DB nếu minh họa thay đổi tồn.
- **Vị trí báo cáo:** §3.2 quản trị viên, quản lý kho.
- **Caption:** Quản lý tồn kho và lịch sử biến động.
- **Ưu tiên:** Bắt buộc. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG26_admin_quan_ly_ton_kho.png}` `\caption{Quản lý tồn kho}` `\label{fig:img26-admin-ton-kho}` `\end{figure}`

### IMG27
- **Tên file:** `IMG27_admin_thong_ke.png`
- **Chức năng / đối tượng:** Xem thống kê quản trị; quản trị viên.
- **Đường dẫn:** `/admin/statistics`
- **Thao tác:** Mở trang thống kê sau khi DB có đơn thử nghiệm nếu trang cần dữ liệu.
- **Cần thấy:** Thống kê đang có và khoảng thời gian/bộ lọc nếu hiện diện. Doanh thu không đồng nghĩa lợi nhuận.
- **Dữ liệu mẫu:** Đơn thử nghiệm; số liệu phải là số hệ thống trả ra, không tự ghi thêm.
- **Vị trí báo cáo:** §3.2 quản trị viên.
- **Caption:** Trang thống kê hoạt động bán hàng.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG27_admin_thong_ke.png}` `\caption{Thống kê quản trị}` `\label{fig:img27-admin-thong-ke}` `\end{figure}`

### IMG28
- **Tên file:** `IMG28_admin_quan_ly_voucher.png`
- **Chức năng / đối tượng:** Quản lý voucher/khuyến mãi; quản trị viên.
- **Đường dẫn:** `/admin/vouchers` và/hoặc `/admin/promotions` (chụp riêng màn hình có UI thực tế cần minh họa).
- **Thao tác:** Mở từng trang; ưu tiên chụp danh sách. Không tạo mã có hiệu lực trên hệ thống đang dùng.
- **Cần thấy:** Danh sách/trạng thái/điều kiện được giao diện hiển thị. Seed không có voucher/promotions, nên cần tạo dữ liệu trong DB thử nghiệm nếu muốn minh họa bản ghi.
- **Dữ liệu mẫu:** Bản ghi thử nghiệm riêng; nếu không có bản ghi, ảnh có thể chứng minh trạng thái danh sách rỗng, không khẳng định quy trình áp dụng voucher đã kiểm thử.
- **Vị trí báo cáo:** §3.2 quản trị viên, nghiệp vụ khuyến mãi.
- **Caption:** Giao diện quản lý voucher và khuyến mãi.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG28_admin_quan_ly_voucher.png}` `\caption{Quản lý voucher/khuyến mãi}` `\label{fig:img28-admin-voucher}` `\end{figure}`

## Nhóm D – Minh chứng nghiệp vụ và kiểm thử

### IMG29
- **Tên file:** `IMG29_sepay_sandbox_ket_qua_thanh_toan.png`
- **Chức năng / đối tượng:** Minh chứng kết quả luồng SePay Sandbox; khách hàng và đối chiếu quản trị.
- **Đường dẫn:** Đi theo `/checkout` → `/payments/sepay/start?id=<MaDH>`; kết quả quay về trang success/error/cancel do ứng dụng điều hướng.
- **Thao tác:** Tạo đơn thử nghiệm chọn thanh toán online, hoàn tất giao dịch sandbox theo cấu hình tích hợp; chỉ chụp trạng thái sau khi hệ thống nhận IPN hợp lệ. Không mở endpoint IPN bằng GET.
- **Cần thấy:** Trạng thái kết quả do website hiển thị và mã đơn; nếu IPN/cấu hình không hoạt động, không ghi nhận thành công. Không chụp khóa, QR/transaction secrets hoặc tài khoản thật.
- **Dữ liệu mẫu:** Đơn SePay thử nghiệm và log giao dịch trong DB. Đối chiếu bảng giao dịch nếu cần; không khẳng định thanh toán production.
- **Vị trí báo cáo:** §3.2 “Tích hợp ngoài”; §3.3 quy trình thanh toán; §3.4 chỉ khi có biên bản kiểm tra thật.
- **Caption:** Kết quả kiểm tra thanh toán SePay Sandbox.
- **Ưu tiên:** Bắt buộc nếu báo cáo nêu tích hợp thanh toán; nếu môi trường sandbox không khả dụng, ghi rõ chưa có minh chứng chạy được. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG29_sepay_sandbox_ket_qua_thanh_toan.png}` `\caption{Kết quả luồng SePay Sandbox}` `\label{fig:img29-sepay-ket-qua}` `\end{figure}`

### IMG30
- **Tên file:** `IMG30_minh_chung_doi_chieu_don_va_giao_dich.png`
- **Chức năng / đối tượng:** Đối chiếu kết quả đơn và giao dịch; người thực hiện kiểm thử/quản trị.
- **Đường dẫn:** Giao diện `/admin/orders` và công cụ DB chỉ dành cho môi trường cục bộ (ví dụ phpMyAdmin); không có trang người dùng riêng cho bảng giao dịch.
- **Thao tác:** Sau luồng COD/SePay thử nghiệm, đối chiếu mã đơn, phương thức, trạng thái thanh toán và bản ghi giao dịch tương ứng trong DB. Chụp riêng màn hình DB đã lọc theo mã test; không lộ dữ liệu khác.
- **Cần thấy:** Các cột định danh cần thiết để chứng minh liên kết; che thông tin cá nhân, thông tin xác thực và dữ liệu giao dịch không liên quan. Đây là minh chứng kiểm tra DB, không phải giao diện sản phẩm.
- **Dữ liệu mẫu:** Mã đơn/giao dịch vừa tạo trong DB thử nghiệm; nếu không có bản ghi thì ghi nhận đúng là không có.
- **Vị trí báo cáo:** §3.4 “Kết quả kiểm thử hệ thống”, kèm mô tả phương pháp đối chiếu; không thay thế biên bản/kết quả kiểm thử.
- **Caption:** Đối chiếu đơn hàng và giao dịch trong cơ sở dữ liệu thử nghiệm.
- **Ưu tiên:** Bổ sung. **Trạng thái:** Chưa chụp.
- **LaTeX dự kiến:** `\begin{figure}[H]` `\centering` `\includegraphics[width=\textwidth]{TaiLieu/AnhChuong3/IMG30_minh_chung_doi_chieu_don_va_giao_dich.png}` `\caption{Đối chiếu dữ liệu đơn hàng và giao dịch}` `\label{fig:img30-doi-chieu-giao-dich}` `\end{figure}`

## Bảng tổng hợp và ánh xạ vị trí LaTeX

| Mã | Tên file | Ưu tiên | Vị trí dự kiến trong LaTeX |
|---|---|---|---|
| IMG01 | `IMG01_trang_chu.png` | Bắt buộc | `Chapter_3/Chuong3_main.tex`, §3.1 |
| IMG02 | `IMG02_danh_sach_san_pham.png` | Bắt buộc | §3.2, chức năng khách hàng |
| IMG03 | `IMG03_tim_kiem_loc_san_pham.png` | Bắt buộc | §3.3, tra cứu sản phẩm |
| IMG04 | `IMG04_chi_tiet_san_pham.png` | Bắt buộc | §3.2, chức năng khách hàng |
| IMG05 | `IMG05_dang_ky_tai_khoan.png` | Bổ sung | §3.2, tài khoản |
| IMG06 | `IMG06_dang_nhap.png` | Bổ sung | §3.2, tài khoản |
| IMG07 | `IMG07_gio_hang.png` | Bắt buộc | §3.2, giỏ hàng |
| IMG08 | `IMG08_thong_tin_tai_khoan.png` | Bổ sung | §3.2, tài khoản/địa chỉ |
| IMG09 | `IMG09_checkout_thong_tin_giao_hang.png` | Bắt buộc | §3.3, đặt hàng |
| IMG10 | `IMG10_checkout_chon_cod.png` | Bắt buộc | §3.3, phương thức thanh toán |
| IMG11 | `IMG11_checkout_chon_sepay_sandbox.png` | Bắt buộc* | §3.2 tích hợp; §3.3 thanh toán |
| IMG12 | `IMG12_dat_hang_thanh_cong_cod.png` | Bắt buộc | §3.3, đặt hàng |
| IMG13 | `IMG13_lich_su_don_hang.png` | Bắt buộc | §3.2/§3.3, đơn hàng khách |
| IMG14 | `IMG14_chi_tiet_theo_doi_don_hang.png` | Bắt buộc | §3.3, theo dõi đơn |
| IMG15 | `IMG15_huy_don_chua_thanh_toan.png` | Bổ sung | §3.3, hủy đơn |
| IMG16 | `IMG16_nhan_vien_danh_sach_don.png` | Bắt buộc | §3.2, nhân viên |
| IMG17 | `IMG17_nhan_vien_cap_nhat_trang_thai_don.png` | Bắt buộc | §3.2, nhân viên |
| IMG18 | `IMG18_nhan_vien_xem_ton_kho.png` | Bổ sung | §3.2, nhân viên (chỉ nếu quyền cho phép) |
| IMG19 | `IMG19_admin_dashboard.png` | Bắt buộc | §3.2, quản trị viên |
| IMG20 | `IMG20_admin_quan_ly_san_pham.png` | Bắt buộc | §3.2, quản trị viên |
| IMG21 | `IMG21_admin_them_sua_san_pham.png` | Bổ sung | §3.2, quản trị viên |
| IMG22 | `IMG22_admin_quan_ly_danh_muc.png` | Bắt buộc | §3.2, quản trị viên |
| IMG23 | `IMG23_admin_quan_ly_nguoi_dung.png` | Bắt buộc | §3.2, quản trị viên |
| IMG24 | `IMG24_admin_quan_ly_don_hang.png` | Bắt buộc | §3.2, quản trị viên |
| IMG25 | `IMG25_admin_chi_tiet_cap_nhat_don.png` | Bổ sung | §3.2/§3.4 |
| IMG26 | `IMG26_admin_quan_ly_ton_kho.png` | Bắt buộc | §3.2, quản trị viên |
| IMG27 | `IMG27_admin_thong_ke.png` | Bổ sung | §3.2, quản trị viên |
| IMG28 | `IMG28_admin_quan_ly_voucher.png` | Bổ sung | §3.2, quản trị viên |
| IMG29 | `IMG29_sepay_sandbox_ket_qua_thanh_toan.png` | Bắt buộc* | §3.2/§3.3/§3.4 nếu kiểm thử được |
| IMG30 | `IMG30_minh_chung_doi_chieu_don_va_giao_dich.png` | Bổ sung | §3.4, kiểm thử/đối chiếu DB |

**Tổng số:** 30 ảnh: **20 bắt buộc** và **10 bổ sung** (IMG29 chỉ bắt buộc nếu muốn báo cáo kết quả luồng SePay; nếu Sandbox không hoạt động, ghi rõ không có minh chứng giao dịch thành công). Mã IMG11/IMG29 không chứng minh thanh toán production.

## Chức năng không đề xuất chụp hoặc chỉ chụp khi có căn cứ

- Dashboard nhân viên riêng: không thấy route/dashboard riêng cho vai trò nhân viên; nhân viên vào các màn hình nghiệp vụ được phân quyền.
- Lọc theo thông số kỹ thuật trên trang danh sách: source hiện hỗ trợ từ khóa, danh mục, thương hiệu, loại, khoảng giá và sắp xếp; không chụp/ghi lọc theo cấu hình CPU, RAM, GPU như chức năng đã có.
- Tự động hủy đơn nền khi thanh toán thất bại/quá hạn: không thấy scheduler/cron/worker thực hiện; không tạo ảnh minh chứng tự động hủy. Hủy đơn khách hàng là thao tác riêng theo điều kiện hiện tại.
- Hoàn tất thu COD hoặc xác nhận đã thu tiền bởi khách: không đề xuất ảnh vì luồng COD tạo đơn với trạng thái chưa thanh toán; không đồng nghĩa hệ thống đã thu tiền.
- Thanh toán SePay production: chỉ có căn cứ cho luồng tích hợp Sandbox; không chụp hoặc mô tả như giao dịch production.
- Tính lợi nhuận, FIFO, quản lý lô, KNN/AI nhập hàng hoặc nghiệp vụ của đề tài khác: không có căn cứ trong hệ thống T&T Computer.
- Ảnh riêng cho endpoint IPN: endpoint nhận POST từ cổng, không phải trang giao diện để mở/chụp trực tiếp.
- Bảng Use Case, sequence diagram, ERD và ảnh sản phẩm trong thư mục `Chapter_3`: đây là sơ đồ/ảnh sản phẩm, không phải ảnh chụp giao diện phần mềm. Không tính thay cho ảnh màn hình trong danh sách này.
- Các tình huống lỗi đăng ký/đăng nhập/đặt hàng: chỉ chụp bổ sung nếu thực hiện thật trên môi trường thử nghiệm và thông báo lỗi xuất hiện; không dựng thông báo giả hoặc ghi thành kết quả kiểm thử nếu chưa chạy.

## Chuẩn bị dữ liệu và lưu ý trước ngày chụp

1. Khởi động Apache/PHP và MySQL, xác nhận URL gốc đúng cấu hình tại `config/app.php`. Kiểm tra môi trường đang dùng là DB thử nghiệm, đặc biệt trước khi đặt/hủy đơn hoặc thay đổi tồn kho.
2. Đăng nhập theo vai trò bằng các tài khoản demo có sẵn tại `database/dulieuao.sql` (khách `nguyenvanan`, nhân viên `tranquocviet`, quản trị `buihoanglong` nếu các tài khoản còn tồn tại). Mật khẩu không ghi trong tài liệu và không xuất hiện trên ảnh; nếu đăng nhập không được thì dùng tài khoản thử nghiệm hợp lệ do chủ dự án quản lý.
3. Chuẩn bị một giỏ hàng thử nghiệm với sản phẩm còn hàng; tạo tối thiểu một đơn COD để minh họa lịch sử, chi tiết và màn hình quản trị. Không tự tạo đơn trực tiếp bằng SQL nếu mục đích là chứng minh luồng giao diện.
4. Đơn SePay cần cấu hình Sandbox và IPN hợp lệ; nếu môi trường không nhận callback, chỉ ghi nhận màn hình đã thực sự quan sát, không khẳng định giao dịch thành công.
5. Voucher/khuyến mãi không có dữ liệu seed. Chỉ chuẩn bị bản ghi trên DB thử nghiệm nếu cần minh họa danh sách có dữ liệu; không nhập voucher vào môi trường thật.
6. Đối chiếu mọi con số hiển thị trên dashboard/tồn kho với DB hiện hành. Chụp trước/sau thao tác trong các ảnh riêng nếu cần chứng minh thay đổi, không ghép dựng ảnh.

## Trạng thái chèn ảnh vào báo cáo

Các nội dung Chương 3 hiện nằm trong `NOTE/Bản Đã Sửa/DATN__Template_LaTexStudio/Chapter_3/Chuong3_main.tex`; `main.tex` nạp chương này. Các vị trí ở trên là điểm chèn đề xuất theo mục/đoạn, không phải khẳng định ảnh đã được đưa vào PDF. Mỗi mục cung cấp sẵn đoạn `figure`/`includegraphics`/`caption`/`label`; chỉ chèn sau khi file PNG tương ứng đã tồn tại và được kiểm tra. Giữ nhãn duy nhất, chạy biên dịch đủ lượt để cập nhật danh mục hình và tham chiếu.
