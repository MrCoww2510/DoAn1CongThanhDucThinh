# 11 — Hạn chế và khuyến nghị

Ngày 19-10-2026. Chỉ đề xuất, không sửa source.

1. Ưu tiên cao: lập route-method-auth-CSRF matrix cho mọi action ghi dữ liệu.
2. Ưu tiên cao: xác định trạng thái schema; schema hiện có DiaChiNhanHang trong khi migration thêm cột này.
3. Ưu tiên vừa: kiểm thử đơn hàng/tồn kho cạnh tranh, hủy lặp và uniqueness race.
4. Ưu tiên vừa: rà output encoding theo HTML context và session cookie trên môi trường HTTPS.
5. Ưu tiên vừa: cân nhắc inject dependencies để testability tốt hơn.
6. Ưu tiên thấp: dùng EXPLAIN trước khi thêm index.
7. Ưu tiên thấp: xác minh UTF-8 end-to-end; console hiện hiển thị mojibake nên chưa kết luận file source lỗi encoding.

Giới hạn: không chạy lint/tests/app/SQL; không kết nối database; không đọc toàn bộ binary/PDF/zip; không xác minh XAMPP config/log/session.
