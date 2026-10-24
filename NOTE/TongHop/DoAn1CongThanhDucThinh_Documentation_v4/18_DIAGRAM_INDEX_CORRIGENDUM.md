# Đính chính vị trí sơ đồ

`17_DOCUMENTATION_INDEX.md` nói sơ đồ Mermaid tổng quan có ở “các phiên bản trước” và gợi ý xem v3. Kiểm tra thư mục cho thấy sơ đồ ở bản documentation gốc (`C:\xampp\htdocs\DoAn1CongThanhDucThinh_Documentation\diagrams`), còn v3 không có thư mục `.mmd`. Bản v4 hiện có bộ sơ đồ mới, tự chứa:

- [system_architecture.mmd](diagrams/system_architecture.mmd)
- [request_lifecycle.mmd](diagrams/request_lifecycle.mmd)
- [module_dependencies.mmd](diagrams/module_dependencies.mmd)
- [database_relationships.mmd](diagrams/database_relationships.mmd)

Sơ đồ chỉ biểu diễn đường đi/quan hệ có bằng chứng từ `.htaccess`, entry point, router, controller, model, view và khóa ngoại schema. Chưa xác minh cấu hình/runtime Apache hay kết nối database. Quan hệ ER là cardinality theo FK/UNIQUE/NULL trong `database.sql`; logic khác không có constraint không được suy diễn thành FK.
