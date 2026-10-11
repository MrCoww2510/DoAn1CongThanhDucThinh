<?php
$image = trim((string) ($product['HinhAnh'] ?? ''));

if ($image !== '') {
    if (preg_match('~^https?://~i', $image)) {
        // Giữ nguyên URL ảnh bên ngoài.
    } elseif (str_starts_with($image, '/DoAn1CongThanhDucThinh/public/')) {
        // Đường dẫn đã có tiền tố dự án.
    } elseif (str_starts_with($image, '/images/')) {
        $image = $baseUrl.$image;
    } elseif (str_starts_with($image, 'public/')) {
        $image = $baseUrl.'/'.substr($image, 7);
    } else {
        $image = $baseUrl.'/'.ltrim($image, '/');
    }
}

$stock = (int) ($product['SoLuongTon'] ?? 0);
$csrfToken = $_SESSION['_csrf'] ?? '';

$productUrl = $baseUrl.'/product?id='.rawurlencode(
    (string) $product['MaSP']
);

$cartUrl = $baseUrl.'/cart/add';
$buyNowUrl = $baseUrl.'/cart/buy-now';

$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<article class="product-card">
    <a class="product-image" href="<?php echo $e($productUrl); ?>">
        <?php if ($image !== '') { ?>
            <img
                src="<?php echo $e($image); ?>"
                alt="<?php echo $e($product['TenSP']); ?>"
                loading="lazy"
            >
        <?php } else { ?>
            <span class="image-placeholder">Chưa có hình ảnh</span>
        <?php } ?>
    </a>

    <div class="product-card-body">
        <p class="product-category">
            <?php echo $e($product['TenDM'] ?? ''); ?>
        </p>

        <h3>
            <a href="<?php echo $e($productUrl); ?>">
                <?php echo $e($product['TenSP'] ?? ''); ?>
            </a>
        </h3>

        <p class="product-price">
            <?php echo number_format((float) $product['GiaBan'], 0, ',', '.'); ?> ₫
        </p>

        <p class="product-stock">
            <?php if ($stock > 0) { ?>
                Còn hàng: <?php echo $stock; ?>
            <?php } else { ?>
                Tạm hết hàng
            <?php } ?>
        </p>

        <a class="product-detail-link" href="<?php echo $e($productUrl); ?>">
            Xem chi tiết
        </a>

        <div class="product-btn">
            <?php if ($stock > 0) { ?>

                <!-- Thêm vào giỏ hàng -->
                <form
                    action="<?php echo $e($cartUrl); ?>"
                    method="POST"
                    class="form-add-cart"
                >
                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?php echo $e($csrfToken); ?>"
                    >

                    <input
                        type="hidden"
                        name="maSP"
                        value="<?php echo $e($product['MaSP']); ?>"
                    >

                    <input
                        type="hidden"
                        name="quantity"
                        value="1"
                    >

                    <button
                        type="submit"
                        class="btn-add-cart"
                        title="Thêm vào giỏ hàng"
                    >
                        🛒 Thêm
                    </button>
                </form>

                <!-- Mua ngay: cần triển khai route riêng -->
                <form
                    action="<?php echo $e($buyNowUrl); ?>"
                    method="POST"
                    class="detail-cart-form"
                >
                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?php echo $e($csrfToken); ?>"
                    >

                    <input
                        type="hidden"
                        name="maSP"
                        value="<?php echo $e($product['MaSP']); ?>"
                    >

                    <input
                        type="hidden"
                        name="quantity"
                        value="1"
                    >

                    <button type="submit" class="detail-add-cart">
                        Mua ngay
                    </button>
                </form>

            <?php } else { ?>

                <button
                    type="button"
                    class="btn-add-cart"
                    disabled
                >
                    Hết hàng
                </button>

            <?php } ?>
        </div>
    </div>
</article>