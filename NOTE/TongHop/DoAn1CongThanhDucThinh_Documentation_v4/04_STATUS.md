# Tiến độ v4

Ngày 19-10-2026. Trong v4 đã đọc đầy đủ 8 PHP source: 4 core, ProductController, Product model, HomeController, AboutController; cộng 5 PHP view ở catalog/home. Tổng 13 file trong cụm này đọc toàn văn. Đặc tả 16 method/class operations (Router 5 gồm constructor, Controller 2, View 1, Database 1, ProductController 3, Product 5, Home 1, About 1 = 19; sửa lại: tổng **19** operations). Chưa rà CSS declarations trong cụm này.

### Phát hiện mới

- Category catalog đầy đủ; product listing không pagination.
- `featured` sắp MaSP DESC, không dùng flag nổi bật.
- Guest product add form có thể render CSRF rỗng vì controller product/home không init token; mutation endpoint sẽ reject.
- Router unknown method/path trả 404; một số action tự POST-only trả 405.
- AboutController include thư mục `Views` viết hoa trong khi trên đĩa `views` chữ thường; Windows thường không phân biệt, Linux có thể. Chưa xác minh deploy OS.

### Còn lại

Order/Checkout/UserManagement và views; các controller/account/cart views (đã làm ở v3); các views còn lại; 9 CSS; 3 SQL đầy đủ, nhất là fixture; 15 TEX/tài liệu phụ trợ; toàn bộ biến local nhánh chưa thuộc các module đã đọc. Runtime config/session/Apache/DB chưa kiểm tra. Không chạy PHP/tests/SQL/scripts.
