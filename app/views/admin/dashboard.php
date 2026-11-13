<?php
// Giao diện tổng quan khu vực quản trị.
 $escape = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>
<section class="admin-page">
    <p class="admin-eyebrow">KHU VỰC QUẢN TRỊ</p><h1>Tổng quan hệ thống</h1>
    <p>Xin chào, <?php echo $escape($_SESSION['user']['HoTen'] ?? $_SESSION['user']['TenDangNhap'] ?? 'Quản trị viên'); ?>. Chọn một chức năng để bắt đầu.</p>
    <div class="admin-stats">
        <article><strong><?php echo $escape($productCount); ?></strong><span>Sản phẩm</span><a href="<?php echo $escape($baseUrl.'/admin/products'); ?>">Quản lý sản phẩm →</a></article>
        <article><strong><?php echo $escape($categoryCount); ?></strong><span>Danh mục</span><a href="<?php echo $escape($baseUrl.'/admin/categories'); ?>">Quản lý danh mục →</a></article>
        <article><strong><?php echo $escape($userCount); ?></strong><span>Tài khoản</span><a href="<?php echo $escape($baseUrl.'/admin/users'); ?>">Quản lý người dùng →</a></article>
    </div>
    <h2>Danh mục hiện có</h2>
    <div class="admin-table-wrap"><table><thead><tr><th>Mã</th><th>Tên danh mục</th><th>Sản phẩm</th><th>Trạng thái</th></tr></thead><tbody>
    <?php foreach ($categories as $category) { ?><tr><td><?php echo $escape($category['MaDM']); ?></td><td><?php echo $escape($category['TenDM']); ?></td><td><?php echo $escape($category['SoSanPham']); ?></td><td><?php echo $escape($category['TrangThai'] === 'NgungKinhDoanh' ? 'Ngừng kinh doanh' : 'Đang kinh doanh'); ?></td></tr><?php } ?>
    </tbody></table></div>
</section>
