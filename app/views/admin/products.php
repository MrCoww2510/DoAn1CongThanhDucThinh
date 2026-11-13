<?php
// Giao diện tạo, cập nhật và quản lý sản phẩm.

$escape = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$editing = $editing ?? null;
?>
<section class="admin-page">
    <p class="admin-eyebrow"><a href="<?php echo $escape($baseUrl.'/admin'); ?>">Quản trị</a> / UC11</p><h1>Quản lý sản phẩm</h1>
    <?php if ($error !== '') { ?><p class="admin-message error" role="alert"><?php echo $escape($error); ?></p><?php } ?>
    <?php if ($success !== '') { ?><p class="admin-message success" role="status"><?php echo $escape($success); ?></p><?php } ?>
    <details class="admin-form" <?php echo $editing ? 'open' : ''; ?>><summary><?php echo $editing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới'; ?></summary>
        <form method="post" action="<?php echo $escape($baseUrl.'/admin/products/save'); ?>">
            <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
            <?php if ($editing) { ?><input type="hidden" name="product_id" value="<?php echo $escape($editing['MaSP']); ?>"><?php } ?>
            <label>Tên sản phẩm <input name="name" maxlength="200" required value="<?php echo $escape($editing['TenSP'] ?? ''); ?>"></label>
            <label>Danh mục <select name="category" required>
                <option value="">Chọn danh mục</option>
                <?php foreach ($categories as $category) { ?>
                <option value="<?php echo $escape($category['MaDM']); ?>" <?php echo ($editing['MaDM'] ?? '') === $category['MaDM'] ? 'selected' : ''; ?>>
                <?php echo $escape($category['TenDM']); ?>
                </option>
                <?php } ?>
                </select>
                </label>
            <label>Giá bán (₫) <input type="number" name="price" min="0" step="0.01" required value="<?php echo $escape($editing['GiaBan'] ?? ''); ?>"></label>
            <label>Loại sản phẩm <input name="type" maxlength="100" value="<?php echo $escape($editing['LoaiSP'] ?? ''); ?>"></label>
            <label>Thương hiệu <input name="brand" maxlength="100" value="<?php echo $escape($editing['ThuongHieu'] ?? ''); ?>"></label>
            <label>Đường dẫn ảnh <input name="image" maxlength="500" placeholder="/images/products/ten-anh.jpg" value="<?php echo $escape($editing['HinhAnh'] ?? ''); ?>"></label>
            <label class="wide">Thông số kỹ thuật <textarea name="specs" rows="3"><?php echo $escape($editing['ThongSoKT'] ?? ''); ?></textarea></label>
            <label class="wide">Mô tả <textarea name="description" rows="4"><?php echo $escape($editing['MoTa'] ?? ''); ?></textarea></label>
            <label>Trạng thái <select name="status">
                <option value="DangKinhDoanh" <?php echo ($editing['TrangThai'] ?? '') !== 'NgungKinhDoanh' ? 'selected' : ''; ?>>Đang kinh doanh</option>
                <option value="NgungKinhDoanh" <?php echo ($editing['TrangThai'] ?? '') === 'NgungKinhDoanh' ? 'selected' : ''; ?>>Ngừng kinh doanh</option>
                </select>
                </label>
            <div class="wide">
                <button type="submit">
                <?php echo $editing ? 'Lưu thay đổi' : 'Tạo sản phẩm'; ?>
                </button> <?php if ($editing) { ?>
                <a class="admin-secondary" href="<?php echo $escape($baseUrl.'/admin/products'); ?>">Hủy</a>
                <?php } ?>
                </div>
        </form>
    </details>
    <form class="admin-search" method="get" action="<?php echo $escape($baseUrl.'/admin/products'); ?>">
        <input type="search" name="q" maxlength="100" placeholder="Tìm tên hoặc mã sản phẩm" value="<?php echo $escape($query); ?>">
        <button type="submit">Tìm kiếm</button>
        </form>
    <div class="admin-table-wrap">
        <table>
        <thead>
        <tr>
        <th>Mã</th>
        <th>Sản phẩm</th>
        <th>Danh mục</th>
        <th>Giá</th>
        <th>Tồn kho</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
    <?php foreach ($products as $product) { ?>
        <tr>
        <td>
        <?php echo $escape($product['MaSP']); ?>
        </td>
        <td>
        <strong>
        <?php echo $escape($product['TenSP']); ?>
        </strong>
        <small>
        <?php echo $escape($product['ThuongHieu'] ?? ''); ?>
        </small>
        </td>
        <td>
        <?php echo $escape($product['TenDM']); ?>
        </td>
        <td>
        <?php echo number_format((float) $product['GiaBan'], 0, ',', '.'); ?> ₫</td>
        <td>
        <?php echo $escape($product['SoLuongTon']); ?>
        </td>
        <td>
        <?php echo $escape($product['TrangThai'] === 'NgungKinhDoanh' ? 'Ngừng kinh doanh' : 'Đang kinh doanh'); ?>
        </td>
        <td>
        <a href="<?php echo $escape($baseUrl.'/admin/products?edit='.rawurlencode($product['MaSP'])); ?>">Sửa</a>
        <form class="inline-form" method="post" action="<?php echo $escape($baseUrl.'/admin/products/status'); ?>">
        <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
        <input type="hidden" name="product_id" value="<?php echo $escape($product['MaSP']); ?>">
        <input type="hidden" name="status" value="<?php echo $product['TrangThai'] === 'NgungKinhDoanh' ? 'DangKinhDoanh' : 'NgungKinhDoanh'; ?>">
        <button class="link-button" type="submit">
        <?php echo $product['TrangThai'] === 'NgungKinhDoanh' ? 'Mở bán' : 'Ngừng bán'; ?>
        </button>
        </form>
        </td>
        </tr>
        <?php } ?>
    <?php if ($products === []) { ?><tr><td colspan="7">Chưa có sản phẩm phù hợp.</td></tr><?php } ?>
    </tbody></table></div>
</section>
