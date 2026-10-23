# 10 — Hiệu năng và bảo trì

Ngày 19-10-2026; suy luận từ code, chưa benchmark.

- Catalog dùng JOIN/filter/sort/pagination; offset lớn có thể chậm. Đo EXPLAIN/index bằng DB staging trước khi tối ưu.
- Controller tạo model trực tiếp và Database singleton làm unit test/mock khó; có thể dùng constructor injection nếu quy mô/test cần.
- Cart tổng tiền được lưu song song với dòng; writer ngoài model có thể làm lệch.
- Order place/cancel transaction + row lock hỗ trợ nhất quán tồn; lock time/index phụ thuộc dữ liệu và workload.
- Không thấy cache/API async; không thể kết luận tải thực tế.
- CSS chia theo trang; JS nhỏ. Native dialog/visually-hidden là dấu hiệu accessibility nhưng chưa audit keyboard/screen reader.
- Nhiều schema/migration/fixture và LaTeX artifacts; cần phân biệt rõ file triển khai với fixture.

Gợi ý: test use-case và concurrency trên DB test, quy trình migration rõ, đo truy vấn đại diện, ghi trạng thái đơn/route contract.
