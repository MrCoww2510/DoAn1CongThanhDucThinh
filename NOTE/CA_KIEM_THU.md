# Kế hoạch và kết quả kiểm thử T&T Computer

> **Trạng thái:** Đây là biểu mẫu kiểm thử. Các cột “Kết quả thực tế”, “Trạng thái” và “Bằng chứng” cần được điền sau khi chạy trên môi trường thử nghiệm. Không đánh dấu Đạt chỉ dựa trên việc đã đọc mã nguồn hoặc xem ảnh giao diện.

## 1. Thông tin môi trường

| Thông tin | Giá trị ghi nhận |
|---|---|
| Ngày kiểm thử | |
| Người kiểm thử | |
| Hệ điều hành | |
| Phiên bản PHP | |
| Phiên bản MySQL | |
| Trình duyệt | |
| Địa chỉ môi trường thử nghiệm | |
| Cấu hình SePay | Sandbox / Không cấu hình |

Chỉ dùng tài khoản và dữ liệu thử. Không đưa mật khẩu, khóa bí mật, token hoặc thông tin thanh toán thật vào báo cáo hay ảnh chụp.

## 2. Quy ước kết quả

- **Đạt:** Kết quả thực tế khớp kết quả mong đợi.
- **Không đạt:** Kết quả thực tế khác kết quả mong đợi; ghi rõ sai khác và mã bằng chứng.
- **Chưa thực hiện:** Chưa chạy được ca kiểm thử, chẳng hạn thiếu cấu hình dịch vụ ngoài.

Sau mỗi thao tác có thay đổi dữ liệu, kiểm tra trạng thái trên giao diện và dữ liệu liên quan trong cơ sở dữ liệu thử nghiệm. Không sửa cột “Kết quả mong đợi” để khớp với kết quả thực tế.

## 3. Danh sách ca kiểm thử

| Mã ca | Use Case / yêu cầu | Điều kiện ban đầu và thao tác | Kết quả mong đợi | Kết quả thực tế | Trạng thái | Bằng chứng |
|---|---|---|---|---|---|---|
| TC-AUTH-01 | Phân quyền quản trị | Đăng nhập bằng tài khoản Nhân viên; truy cập trực tiếp một route chỉ dành cho Quản trị viên. | Hệ thống từ chối truy cập; không thay đổi dữ liệu. | | Chưa thực hiện | |
| TC-ORD-01 | UC05 – Đặt hàng COD | Khách hàng đăng nhập; giỏ có sản phẩm đang bán và tồn kho đủ. Đặt đơn COD. | Đơn được tạo ở trạng thái **Chờ xác nhận / Chưa thanh toán**; tồn kho giảm đúng số lượng đặt. | | Chưa thực hiện | |
| TC-ORD-02 | UC05 – Không đủ tồn kho | Giỏ có số lượng lớn hơn tồn kho. Thử xác nhận đặt hàng. | Hệ thống báo không đủ hàng; không tạo đơn và tồn kho không đổi. | | Chưa thực hiện | |
| TC-CAN-01 | UC07 – Hủy đơn hợp lệ | Có đơn chưa thanh toán ở trạng thái **Chờ xác nhận**. Khách hàng thực hiện hủy. | Đơn chuyển thành **Đã hủy**; số lượng được hoàn lại kho và có lịch sử kho tương ứng. | | Chưa thực hiện | |
| TC-CAN-02 | UC07 – Hủy lặp | Sau TC-CAN-01, thử gửi yêu cầu hủy lại cùng đơn. | Đơn không bị xử lý hủy lần nữa; tồn kho không tăng thêm lần thứ hai. | | Chưa thực hiện | |
| TC-VOU-01 | UC18 – Voucher hợp lệ | Dùng voucher còn hiệu lực, đủ điều kiện và còn lượt. Áp dụng tại checkout rồi tạo đơn. | Hệ thống tính lại số tiền giảm phía máy chủ; lượt sử dụng chỉ được ghi khi tạo đơn thành công. | | Chưa thực hiện | |
| TC-VOU-02 | UC18 – Voucher không hợp lệ | Dùng mã không tồn tại, hết hạn hoặc không đạt giá trị đơn tối thiểu. | Voucher không được áp dụng; tổng tiền không bị giảm sai. | | Chưa thực hiện | |
| TC-PAY-01 | UC06 – Không có IPN hợp lệ | Tạo đơn Online ở Sandbox; quay về trang kết quả nhưng không có IPN hợp lệ được xác minh. | Đơn vẫn **Chưa thanh toán**; trang kết quả không tự xác nhận đã thu tiền. | | Chưa thực hiện | |
| TC-PAY-02 | UC06 – IPN hợp lệ và gửi lặp | Với cấu hình Sandbox hoạt động, gửi IPN hợp lệ rồi gửi lại cùng mã giao dịch. | Lần đầu cập nhật thanh toán theo kết quả hợp lệ; lần gửi lặp không tạo giao dịch trùng hoặc cập nhật sai trạng thái. | | Chưa thực hiện | |

## 4. Ghi nhận lỗi

Với mỗi ca **Không đạt**, ghi lỗi theo mẫu:

| Mã lỗi | Mã ca | Các bước tái hiện | Kết quả mong đợi | Kết quả thực tế | Mức độ | Ảnh/log tham chiếu |
|---|---|---|---|---|---|---|
| | | | | | | |

## 5. Tổng hợp

| Tổng số ca | Đạt | Không đạt | Chưa thực hiện |
|---:|---:|---:|---:|
| 9 | | | |

Tỷ lệ đạt = số ca Đạt / (số ca Đạt + số ca Không đạt) × 100%. Không tính ca Chưa thực hiện vào mẫu số; phải báo cáo riêng số ca này và lý do chưa thực hiện.

> Nếu chưa cấu hình được SePay Sandbox, ghi TC-PAY-01 và TC-PAY-02 là **Chưa thực hiện**; không ghi kết quả giả định. Báo cáo hiện tại mô tả luồng COD là tạo đơn ở trạng thái chưa thanh toán, chưa có bước xác nhận đã thu COD.
