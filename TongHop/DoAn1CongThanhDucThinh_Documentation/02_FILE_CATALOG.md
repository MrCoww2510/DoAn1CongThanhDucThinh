# 02 — Danh mục file

Ngày 19-10-2026. Ngoài `.git` có 232 file; phân loại theo extension. Binary chỉ kiểm metadata.

## PHP (38)

`public/index.php`, `routes/web.php`; Core: `app/Core/{Controller,Database,Router,View}.php`; Controllers: About, Account, Auth, Cart, Checkout, Home, Order, Product, UserManagement trong `app/Controllers/`; Models: Account, Cart, Order, Product trong `app/Models/`; Views: `account/index`, `admin/users`, `auth/login`, `auth/register`, `cart/index`, `checkout/index`, `checkout/success`, `errors/404`, `home/index`, `layouts/customer`, `orders/index`, `orders/show`, `pages/about`, `products/index`, `products/not-found`, `products/show`, `products/_card` trong `app/views/`.

## Khác

12 CSS, 2 JS trong `public/`; 3 SQL; `config/*`, `.htaccess`, `public/.htaccess`; văn bản `AGENTS.md`, `KienThuc.txt`, `rule.txt`; LaTeX sources/artifacts trong `LaTex_Document/`; 125 JPG, 5 PNG, 1 SVG; có PDF, ZIP, GZ và build artifacts. Không phân tích từng ảnh.

Trạng thái theo loại trong [coverage](14_ANALYSIS_COVERAGE.md). Không thấy Composer/npm manifests trong danh sách root.
