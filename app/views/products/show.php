
<?php
$image = trim((string) ($product['HinhAnh'] ?? ''));

if ($image !== '') {
    if (preg_match('~^https?://~i', $image)) {
        // Giữ nguyên URL.
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
?>

<section class="product-detail">
    <div class="detail-image">
        <?php if ($image !== '') { ?>
            <img
                src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>"
                alt="<?php echo htmlspecialchars($product['TenSP'], ENT_QUOTES, 'UTF-8'); ?>"
            >
        <?php } else { ?>
            <div class="image-placeholder">Chưa có hình ảnh</div>
        <?php } ?>
    </div>

    <div class="detail-info">
        <p class="eyebrow">
            <?php echo htmlspecialchars($product['TenDM'], ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <h1><?php echo htmlspecialchars($product['TenSP'], ENT_QUOTES, 'UTF-8'); ?></h1>

        <p class="detail-price">
            <?php echo number_format((float) $product['GiaBan'], 0, ',', '.'); ?> ₫
        </p>

        <p>
            <strong>Mã sản phẩm:</strong>
            <?php echo htmlspecialchars($product['MaSP'], ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <p>
            <strong>Thương hiệu:</strong>
            <?php echo htmlspecialchars($product['ThuongHieu'] ?: 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <p>
            <strong>Loại sản phẩm:</strong>
            <?php echo htmlspecialchars($product['LoaiSP'] ?: 'Chưa cập nhật', ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <p>
            <strong>Tồn kho:</strong>
            <?php echo (int) $product['SoLuongTon'] > 0
                ? 'Còn '.(int) $product['SoLuongTon'].' sản phẩm'
                : 'Tạm hết hàng'; ?>
        </p>

        <?php
        $stock = (int) ($product['SoLuongTon'] ?? 0);
$csrfToken = $_SESSION['_csrf'] ?? '';
?>

        <div class="detail-cart">
            <?php if ($stock > 0) { ?>
                <form
                    method="POST"
                    action="<?php echo htmlspecialchars($baseUrl.'/cart/add', ENT_QUOTES, 'UTF-8'); ?>"
                    class="detail-cart-form"
                >
                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"
                    >

                    <input
                        type="hidden"
                        name="maSP"
                        value="<?php echo htmlspecialchars($product['MaSP'], ENT_QUOTES, 'UTF-8'); ?>"
                    >

                    <div class="detail-quantity">
                        <label for="cart-quantity">Số lượng</label>
                        <input
                            id="cart-quantity"
                            type="number"
                            name="quantity"
                            min="1"
                            max="<?php echo $stock; ?>"
                            value="1"
                            required
                        >
                    </div>

                    <button type="submit" class="detail-add-cart">
                        Thêm vào giỏ hàng
                    </button>
                </form>
            <?php } else { ?>
                <button type="button" class="detail-add-cart" disabled>
                    Sản phẩm tạm hết hàng
                </button>
            <?php } ?>
        </div>

        <div class="detail-description">
            <h2>Mô tả sản phẩm</h2>
            <p><?php echo nl2br(htmlspecialchars(
                $product['MoTa'] ?: 'Chưa có mô tả sản phẩm.',
                ENT_QUOTES,
                'UTF-8'
            )); ?></p>
        </div>

        <div class="detail-description">
            <h2>Thông số kỹ thuật</h2>
            <p><?php echo nl2br(htmlspecialchars(
                $product['ThongSoKT'] ?: 'Chưa cập nhật thông số kỹ thuật.',
                ENT_QUOTES,
                'UTF-8'
            )); ?></p>
        </div>

        <a class="primary-link"
           href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>">
            Quay lại sản phẩm
        </a>
    </div>
</section>