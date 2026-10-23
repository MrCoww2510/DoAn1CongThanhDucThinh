# Module giao diện và tài nguyên tĩnh

Ngày 19-10-2026. Source: `app/views/`, 12 CSS trong `public/css/`, `public/js/nav.js`, `public/js/order-cancel-dialog.js`.

Template PHP nhận biến từ View/controller và dựng HTML. Layout `customer.php` cung cấp khung dùng chung, navigation, assets và nội dung page. CSS chia theo nhóm trang. `nav.js` gắn click toggle class cho menu nếu DOM có button/menu. `order-cancel-dialog.js` kiểm đúng kiểu HTMLDialogElement/Form/Button; mở/đóng dialog, focus lại nút và requestSubmit khi xác nhận.

JS không gửi fetch/AJAX và không thay thế kiểm tra server: cancel vẫn POST qua controller. Native dialog và focus restore hỗ trợ UX; cần audit keyboard, focus trap, labels và fallback trình duyệt. Nhiều template chưa được đọc toàn văn line-by-line trong phiên này, vì vậy không kết luận coverage escape toàn bộ. Escape phải phù hợp text/attribute/URL context.
