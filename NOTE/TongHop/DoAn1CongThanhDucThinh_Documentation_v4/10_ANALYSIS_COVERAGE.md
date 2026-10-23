# Phạm vi và độ bao phủ phân tích

- Ngày phân tích: 2026-10-19.
- Thư mục gốc: `C:\xampp\htdocs\DoAn1CongThanhDucThinh`.
- Phân tích tĩnh. Không chạy ứng dụng, PHP, SQL, migration, tests, Composer hay script; không khảo sát runtime Apache/PHP/MySQL/session.
- Tổng file trong snapshot đã đếm trước đó: 232 (bao gồm source, hình ảnh và artifact); con số cần được xem là snapshot, không phải số lượng file được phân tích nội dung.

## Thống kê theo nhóm cần thiết

| Nhóm | Phát hiện | Đọc đầy đủ | Đọc một phần | Bỏ qua có chủ đích | Ghi chú |
|---|---:|---:|---:|---:|---|
| PHP ứng dụng/config/routes | 38 | 38 | 0 | 0 | Bao gồm 17 view; theo trạng thái phân tích qua các lượt và tài liệu modules. |
| CSS | 12 | 12 | 0 | 0 | Toàn bộ file; không render bằng browser. |
| JavaScript | 2 | 2 | 0 | 0 | Không chạy JS. |
| SQL | 3 | 2 | 1 | 0 | Schema và migration đọc nội dung; fixture đọc cấu trúc lệnh/bảng, không hiển thị hay phân tích giá trị từng row. Không thực thi. |
| LaTeX `.tex` | 15 | 0 | 15 | 0 | Đã kiểm kê và lấy heading/outline; chưa rà từng đoạn thân, citation, hình và assertion nghiệp vụ. |
| TXT tài liệu | 4 | 3 | 1 | 0 | `KienThuc.txt` và 2 UML đọc đủ; `rule.txt` đọc heading và các phần trích phục vụ đối chiếu, chưa đọc từng dòng. `AGENTS.md` được cung cấp trong hội thoại, không tính là đọc file trong workspace. |
| Tài nguyên/binary và artifact | Phần còn lại của snapshot | 0 | 0 | Không đếm chính xác theo từng loại | Ảnh, PDF, ZIP và artifact sinh tự động chỉ kiểm tra danh sách/metadata; không mở hoặc giải nén. Storage/log/upload không đọc nội dung nhạy cảm. |

Ghi chú: con số 38 PHP, 12 CSS, 2 JS, 3 SQL, 15 TEX được xác nhận bằng danh sách file. Trạng thái “đọc đầy đủ” là đánh giá theo các lượt đọc source đã thực hiện; không có manifest lưu nội dung từng file trong dự án. Các file không được liệt kê trong phạm vi source hữu ích không mặc nhiên được coi là đã phân tích.

## Độ bao phủ đặc tả chức năng

- 41 method/constructor của cụm Account, Cart, CartController, AuthController, AccountController được đặc tả trong tài liệu trước.
- Core, product, home/about, checkout/order/admin đã phân tích trong tài liệu v4.
- Tổng số method/function/class trên toàn dự án chưa được thống kê nhất quán ở một bảng; không suy ra tổng từ con số 41.
- SQL call-site đã được ghi theo query purpose/file-line ranges ở các module trước; danh sách literal statement ID toàn cục chưa được lập.
- Ma trận escaping đã rà theo nhóm view đã đọc, nhưng không thay thế kiểm thử XSS/runtime. Các view động phải được truy vết về callsite trong tài liệu module.
- Các sơ đồ dự kiến được tạo trong các bộ tài liệu trước; cần kiểm liên kết sơ đồ và inventory toàn bộ file output trước khi phát hành bản chốt.

## Các phần còn lại để đạt mục tiêu “toàn diện”

1. Đọc đầy đủ 15 file `.tex`, `rule.txt`, đặc biệt đối chiếu các use case/khẳng định với source.
2. Sinh bảng định danh từng truy vấn SQL literal/callsite và đối chiếu query/schema/foreign key; tiếp tục giữ fixture values ở chế độ bí mật.
3. Chuẩn hóa coverage toàn bộ 17 view thành từng biến dynamic → context → encoder; rà kiểm tra ảnh/srcset/URL và inline JS nếu có.
4. Tổng hợp chính xác số class, method/function và SQL statement trên tất cả source.
5. Xác minh liên kết chéo, sơ đồ, dòng dẫn chứng và tính nhất quán số liệu giữa toàn bộ file tài liệu v4.
6. Runtime Apache/PHP/MySQL/session và browser behavior chưa xác minh có chủ đích; cần môi trường kiểm thử riêng và cho phép rõ ràng nếu muốn thực hiện sau.

Đây là trạng thái chưa hoàn tất toàn bộ mục tiêu. Các số dòng là snapshot và có thể thay đổi khi source được cập nhật.
