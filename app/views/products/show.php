
<?php
$image = trim((string) ($product['HinhAnh'] ?? ''));

if ($image !== '') {
    if (preg_match('~^https?://~i', $image)) {
        // Giữ nguyên URL.
    } elseif (str_starts_with($image, '/DoAn1CongThanhDucThinh/public/')) {
        // Đường dẫn đã có tiền tố dự án.
    } elseif (str_starts_with($image, '/images/')) {
        $image = $baseUrl . $image;
    } elseif (str_starts_with($image, 'public/')) {
        $image = $baseUrl . '/' . substr($image, 7);
    } else {
        $image = $baseUrl . '/' . ltrim($image, '/');
    }
}
?>

<section class="product-detail">
    <div class="detail-image">
        <?php if ($image !== ''): ?>
            <img
                src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($product['TenSP'], ENT_QUOTES, 'UTF-8') ?>"
            >
        <?php else: ?>
            <div class="image-placeholder">Chưa có hình ảnh</div>
        <?php endif; ?>
    </div>

    <div class="detail-info">
        <p class="eyebrow">
            <?= htmlspecialchars($product['TenDM'], ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h1><?= htmlspecialchars($product['TenSP'], ENT_QUOTES, 'UTF-8') ?></h1>

        <p class="detail-price">
            <?= number_format((float) $product['GiaBan'], 0, ',', '.') ?> ₫
        </p>

        <p>
            <strong>Mã sản phẩm:</strong>
            <?= htmlspecialchars($product['MaSP'], ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Thương hiệu:</strong>
            <?= htmlspecialchars($product['ThuongHieu'] ?: 'Chưa cập nhật', ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Loại sản phẩm:</strong>
            <?= htmlspecialchars($product['LoaiSP'] ?: 'Chưa cập nhật', ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            <strong>Tồn kho:</strong>
            <?= (int) $product['SoLuongTon'] > 0
                ? 'Còn ' . (int) $product['SoLuongTon'] . ' sản phẩm'
                : 'Tạm hết hàng' ?>
        </p>

        <div class="detail-description">
            <h2>Mô tả sản phẩm</h2>
            <p><?= nl2br(htmlspecialchars(
                $product['MoTa'] ?: 'Chưa có mô tả sản phẩm.',
                ENT_QUOTES,
                'UTF-8'
            )) ?></p>
        </div>

        <div class="detail-description">
            <h2>Thông số kỹ thuật</h2>
            <p><?= nl2br(htmlspecialchars(
                $product['ThongSoKT'] ?: 'Chưa cập nhật thông số kỹ thuật.',
                ENT_QUOTES,
                'UTF-8'
            )) ?></p>
        </div>

        <a class="primary-link"
           href="<?= htmlspecialchars($baseUrl . '/products', ENT_QUOTES, 'UTF-8') ?>">
            Quay lại sản phẩm
        </a>
    </div>
</section>