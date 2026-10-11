
<?php
$image = trim((string) ($product['HinhAnh'] ?? ''));

if ($image !== '') {
    if (preg_match('~^https?://~i', $image)) {
        // Giữ nguyên URL ảnh bên ngoài.
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

<article class="product-card">
    <a class="product-image" href="<?= htmlspecialchars(
        $baseUrl . '/product?id=' . rawurlencode($product['MaSP']),
        ENT_QUOTES,
        'UTF-8'
    ) ?>">
        <?php if ($image !== ''): ?>
            <img
                src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($product['TenSP'], ENT_QUOTES, 'UTF-8') ?>"
                loading="lazy"
            >
        <?php else: ?>
            <span class="image-placeholder">Chưa có hình ảnh</span>
        <?php endif; ?>
    </a>

    <div class="product-card-body">
        <p class="product-category">
            <?= htmlspecialchars($product['TenDM'], ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h3>
            <a href="<?= htmlspecialchars(
                $baseUrl . '/product?id=' . rawurlencode($product['MaSP']),
                ENT_QUOTES,
                'UTF-8'
            ) ?>">
                <?= htmlspecialchars($product['TenSP'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h3>

        <p class="product-price">
            <?= number_format((float) $product['GiaBan'], 0, ',', '.') ?> ₫
        </p>

        <p class="product-stock">
            <?php if ((int) $product['SoLuongTon'] > 0): ?>
                Còn hàng: <?= (int) $product['SoLuongTon'] ?>
            <?php else: ?>
                Tạm hết hàng
            <?php endif; ?>
        </p>

        <a class="product-detail-link"
           href="<?= htmlspecialchars(
               $baseUrl . '/product?id=' . rawurlencode($product['MaSP']),
               ENT_QUOTES,
               'UTF-8'
           ) ?>">
            Xem chi tiết
        </a>
    </div>
</article>