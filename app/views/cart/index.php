<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Dữ liệu giỏ hàng
|--------------------------------------------------------------------------
*/
$items = $cart['items'] ?? [];
$total = (float) ($cart['total'] ?? 0);

$csrf = htmlspecialchars(
    $csrfToken ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$formatMoney = static fn ($amount): string => number_format((float) $amount, 0, ',', '.').' ₫';

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

/*
|--------------------------------------------------------------------------
| Cấu hình ứng dụng
|--------------------------------------------------------------------------
*/
$appConfig = require BASE_PATH.'/config/app.php';

$baseUrl = rtrim(
    (string) ($appConfig['base_url'] ?? ''),
    '/'
);

$baseUrlEscaped = $escape($baseUrl);
?>

<link
    rel="stylesheet"
    href="<?php echo $baseUrlEscaped; ?>/css/cart.css"
>

<section class="cart-page">

    <!-- Tiêu đề -->
    <div class="cart-heading">
        <div>
            <h1>Giỏ hàng của bạn</h1>
            <p>Kiểm tra sản phẩm trước khi tiếp tục mua sắm.</p>
        </div>

        <a
            class="cart-continue"
            href="<?php echo $baseUrlEscaped; ?>/products"
        >
            ← Tiếp tục mua sắm
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (!empty($flash)) { ?>
        <div class="cart-alert <?php echo ($flash['type'] ?? '') === 'error'
            ? 'cart-alert-error'
            : 'cart-alert-success'; ?>">
            <?php echo $escape($flash['message'] ?? ''); ?>
        </div>
    <?php } ?>

    <?php if (empty($items)) { ?>

        <!-- Giỏ hàng trống -->
        <div class="cart-empty">
            <div class="cart-empty-icon">🛒</div>

            <h2>Giỏ hàng đang trống</h2>

            <p>Hãy chọn những sản phẩm bạn muốn mua.</p>

            <a
                class="cart-primary-btn"
                href="<?php echo $baseUrlEscaped; ?>/products"
            >
                Khám phá sản phẩm
            </a>
        </div>

    <?php } else { ?>

        <div class="cart-layout">

            <!-- Danh sách sản phẩm -->
            <div class="cart-products">

                <div class="cart-products-header">
                    <h2>Sản phẩm đã chọn</h2>

                    <form
                        method="POST"
                        action="<?php echo $baseUrlEscaped; ?>/cart/clear"
                        onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?');"
                    >
                        <input
                            type="hidden"
                            name="_csrf"
                            value="<?php echo $csrf; ?>"
                        >

                        <button
                            class="cart-text-btn"
                            type="submit"
                        >
                            Xóa tất cả
                        </button>
                    </form>
                </div>

                <?php foreach ($items as $item) { ?>
                    <?php
                    /*
                    |--------------------------------------------------------------------------
                    | Xử lý đường dẫn ảnh
                    |--------------------------------------------------------------------------
                    */
                    $image = trim(
                        (string) ($item['HinhAnh'] ?? '')
                    );

                    $imageSrc = '';

                    if ($image !== '') {
                        if (preg_match('~^https?://~i', $image)) {
                            // Ảnh từ URL bên ngoài
                            $imageSrc = $image;
                        } elseif (
                            str_starts_with(
                                $image,
                                '/DoAn1CongThanhDucThinh/public/'
                            )
                        ) {
                            // Đường dẫn đã có tiền tố dự án
                            $imageSrc = $image;
                        } elseif (str_starts_with($image, '/images/')) {
                            // Ví dụ: /images/products/SP001.jpg
                            $imageSrc = $baseUrl.$image;
                        } elseif (str_starts_with($image, 'public/')) {
                            // Ví dụ: public/images/products/SP001.jpg
                            $imageSrc = $baseUrl.'/'.substr($image, 7);
                        } else {
                            // Ví dụ: images/products/SP001.jpg
                            $imageSrc = $baseUrl.'/'
                                .ltrim($image, '/');
                        }
                    }

                    $maSP = (string) ($item['MaSP'] ?? '');
                    $tenSP = (string) ($item['TenSP'] ?? 'Sản phẩm');
                    $giaBan = (float) ($item['GiaBan'] ?? 0);
                    $soLuong = (int) ($item['SoLuong'] ?? 1);
                    $soLuongTon = (int) ($item['SoLuongTon'] ?? 0);
                    $thanhTien = (float) (
                        $item['ThanhTien'] ?? ($giaBan * $soLuong)
                    );

                    // Tránh giá trị max nhỏ hơn min của input
                    $maxQuantity = max(1, $soLuongTon);

                    $quantityId = 'quantity-'.preg_replace(
                        '/[^a-zA-Z0-9_-]/',
                        '_',
                        $maSP
                    );
                    ?>

                    <article class="cart-product">

                        <!-- Ảnh sản phẩm -->
                        <div class="cart-product-image">
                            <?php if ($imageSrc !== '') { ?>
                                <img
                                    src="<?php echo $escape($imageSrc); ?>"
                                    alt="<?php echo $escape($tenSP); ?>"
                                    loading="lazy"
                                >
                            <?php } else { ?>
                                <span>PC</span>
                            <?php } ?>
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div class="cart-product-info">

                            <h3>
                                <?php echo $escape($tenSP); ?>
                            </h3>

                            <p class="cart-product-code">
                                Mã sản phẩm:
                                <?php echo $escape($maSP); ?>
                            </p>

                            <p class="cart-unit-price">
                                <?php echo $formatMoney($giaBan); ?>
                            </p>

                            <!-- Cập nhật số lượng -->
                            <form
                                method="POST"
                                action="<?php echo $baseUrlEscaped; ?>/cart/update"
                                class="cart-quantity-form"
                            >
                                <input
                                    type="hidden"
                                    name="_csrf"
                                    value="<?php echo $csrf; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="maSP"
                                    value="<?php echo $escape($maSP); ?>"
                                >

                                <label for="<?php echo $escape($quantityId); ?>">
                                    Số lượng
                                </label>

                                <input
                                    id="<?php echo $escape($quantityId); ?>"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="<?php echo $maxQuantity; ?>"
                                    value="<?php echo $soLuong; ?>"
                                    required
                                >

                                <button
                                    type="submit"
                                    class="cart-update-btn"
                                >
                                    Cập nhật
                                </button>
                            </form>

                            <p class="cart-stock">
                                Còn <?php echo $soLuongTon; ?> sản phẩm
                            </p>

                        </div>

                        <!-- Thành tiền và nút xóa -->
                        <div class="cart-product-end">

                            <strong>
                                <?php echo $formatMoney($thanhTien); ?>
                            </strong>

                            <form
                                method="POST"
                                action="<?php echo $baseUrlEscaped; ?>/cart/remove"
                                onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ hàng?');"
                            >
                                <input
                                    type="hidden"
                                    name="_csrf"
                                    value="<?php echo $csrf; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="maSP"
                                    value="<?php echo $escape($maSP); ?>"
                                >

                                <button
                                    type="submit"
                                    class="cart-remove-btn"
                                >
                                    Xóa sản phẩm
                                </button>
                            </form>

                        </div>

                    </article>

                <?php } ?>

            </div>

            <!-- Tóm tắt đơn hàng -->
            <aside class="cart-summary">

                <h2>Tóm tắt đơn hàng</h2>

                <div class="cart-summary-row">
                    <span>Số loại sản phẩm</span>
                    <strong><?php echo count($items); ?></strong>
                </div>

                <div class="cart-summary-row">
                    <span>Tạm tính</span>
                    <strong><?php echo $formatMoney($total); ?></strong>
                </div>

                <div class="cart-summary-row">
                    <span>Phí vận chuyển</span>
                    <strong>Tính khi đặt hàng</strong>
                </div>

                <div class="cart-summary-total">
                    <span>Tổng tiền sản phẩm</span>
                    <strong><?php echo $formatMoney($total); ?></strong>
                </div>

                <a
                    class="cart-checkout-btn"
                    href="<?= $baseUrlEscaped ?>/checkout"
                >
                    Tiến hành đặt hàng
                </a>

                <p class="cart-summary-note">
                    Tồn kho và giá sẽ được kiểm tra lại khi bạn đặt hàng.
                </p>

            </aside>

        </div>

    <?php } ?>

</section>