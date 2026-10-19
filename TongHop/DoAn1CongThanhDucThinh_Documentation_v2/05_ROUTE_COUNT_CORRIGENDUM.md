# Đính chính: số lượng route theo method

Ngày 19-10-2026. Đếm lại trực tiếp các khai báo trong `routes/web.php` cho kết quả **27 khai báo: 13 GET và 14 POST**. Ghi `15 GET / 12 POST` trong `04_CONTINUATION_STATUS.md` là sai và bị thay thế bởi số liệu này. Ma trận tại `01_ROUTE_AUTH_MATRIX.md` liệt kê tổng 27 khai báo đúng.

Cách xác minh: đếm `$router->get(` và `$router->post(` trong file route; không tính số URL duy nhất. Có bốn URL được khai báo cả GET và POST: `/register`, `/login`, `/account`, `/checkout`.
