<?php
$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn ($amount): string =>
    number_format((float) $amount, 0, ',', '.') . ' ₫';
$appConfig = require BASE_PATH . '/config/app.php';
$checkoutBaseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
?>

<link
    rel="stylesheet"
    href="<?= $escape($checkoutBaseUrl) ?>/css/checkout.css"
>

<section class="checkout-page">
    <div class="checkout-heading">
        <div>
            <p class="checkout-eyebrow">HOÀN TẤT ĐƠN HÀNG</p>
            <h1>Thanh toán</h1>
            <p>Kiểm tra thông tin giao hàng và chọn phương thức thanh toán.</p>
        </div>
        <a href="<?= $escape($checkoutBaseUrl) ?>/cart">← Quay lại giỏ hàng</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="checkout-errors" role="alert">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($items === []): ?>
        <div class="checkout-empty">
            <h2>Giỏ hàng đang trống</h2>
            <p>Hãy chọn sản phẩm trước khi tiến hành thanh toán.</p>
            <a class="checkout-submit" href="<?= $escape($checkoutBaseUrl) ?>/products">
                Khám phá sản phẩm
            </a>
        </div>
    <?php else: ?>
        <form
            class="checkout-layout"
            method="post"
            action="<?= $escape($checkoutBaseUrl) ?>/checkout"
        >
            <input
                type="hidden"
                name="_csrf"
                value="<?= $escape($csrfToken) ?>"
            >

            <div class="checkout-form-card">
                <section class="checkout-section">
                    <h2>Thông tin người nhận</h2>

                    <label for="recipient-name">Họ và tên *</label>
                    <input
                        id="recipient-name"
                        name="recipientName"
                        type="text"
                        maxlength="100"
                        autocomplete="name"
                        value="<?= $escape($form['recipientName'] ?? '') ?>"
                        required
                    >

                    <label for="recipient-phone">Số điện thoại *</label>
                    <input
                        id="recipient-phone"
                        name="recipientPhone"
                        type="tel"
                        maxlength="20"
                        autocomplete="tel"
                        value="<?= $escape($form['recipientPhone'] ?? '') ?>"
                        required
                    >

                    <label for="delivery-address">Địa chỉ giao hàng *</label>
                    <textarea
                        id="delivery-address"
                        name="deliveryAddress"
                        rows="4"
                        maxlength="255"
                        autocomplete="street-address"
                        required
                    ><?= $escape($form['deliveryAddress'] ?? '') ?></textarea>
                </section>

                <section class="checkout-section">
                    <h2>Phương thức thanh toán</h2>

                    <label class="payment-option">
                        <input
                            type="radio"
                            name="paymentMethod"
                            value="COD"
                            <?= ($form['paymentMethod'] ?? '') === 'COD' ? 'checked' : '' ?>
                            required
                        >
                        <span>
                            <strong>Thanh toán khi nhận hàng (COD)</strong>
                            <small>Thanh toán cho đơn vị giao hàng khi nhận sản phẩm.</small>
                        </span>
                    </label>

                    <div class="payment-option payment-option-disabled">
                        <input type="radio" disabled>
                        <span>
                            <strong>Thanh toán trực tuyến</strong>
                            <small>Chưa khả dụng vì dự án chưa tích hợp cổng thanh toán.</small>
                        </span>
                    </div>
                </section>
            </div>

            <aside class="checkout-summary">
                <h2>Đơn hàng của bạn</h2>

                <table class="checkout-order-items">
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="checkout-item-name">
                                <?= $escape($item['TenSP'] ?? 'Sản phẩm') ?>
                            </td>
                            <td class="checkout-item-quantity">
                                <?= (int) ($item['SoLuong'] ?? 0) ?>
                            </td>
                            <td class="checkout-item-price"><?= $formatMoney($item['ThanhTien'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="checkout-total">
                    <span>Tổng tiền sản phẩm</span>
                    <strong><?= $formatMoney($total) ?></strong>
                </div>

                <p class="checkout-shipping-note">
                    Phí giao hàng chưa được tính trong hệ thống và sẽ được xác nhận riêng.
                </p>

                <button class="checkout-submit" type="submit">
                    Đặt hàng — thanh toán khi nhận
                </button>
            </aside>
        </form>
    <?php endif; ?>
</section>
