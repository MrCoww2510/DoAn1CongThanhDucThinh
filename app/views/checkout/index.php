<?php
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn ($amount): string => number_format((float) $amount, 0, ',', '.').' ₫';
$appConfig = require BASE_PATH.'/config/app.php';
$checkoutBaseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
?>

<link
    rel="stylesheet"
    href="<?php echo $escape($checkoutBaseUrl); ?>/css/checkout.css?v=20261106-address-layout"
>
<script
    src="<?php echo $escape($checkoutBaseUrl); ?>/js/checkout-address.js"
    defer
></script>

<section class="checkout-page">
    <div class="checkout-heading">
        <div>
            <p class="checkout-eyebrow">HOÀN TẤT ĐƠN HÀNG</p>
            <h1>Thanh toán</h1>
            <p>Kiểm tra thông tin giao hàng và chọn phương thức thanh toán.</p>
        </div>
        <a href="<?php echo $escape($checkoutBaseUrl); ?>/cart">← Quay lại giỏ hàng</a>
    </div>

    <?php if ($errors !== []) { ?>
        <div class="checkout-errors" role="alert">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo $escape($error); ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if ($items === []) { ?>
        <div class="checkout-empty">
            <h2>Giỏ hàng đang trống</h2>
            <p>Hãy chọn sản phẩm trước khi tiến hành thanh toán.</p>
            <a class="checkout-submit" href="<?php echo $escape($checkoutBaseUrl); ?>/products">
                Khám phá sản phẩm
            </a>
        </div>
    <?php } else { ?>
        <form
            class="checkout-layout"
            method="post"
            action="<?php echo $escape($checkoutBaseUrl); ?>/checkout"
        >
            <input
                type="hidden"
                name="_csrf"
                value="<?php echo $escape($csrfToken); ?>"
            >

            <div class="checkout-form-card">
                <section class="checkout-section">
                    <h2>Địa chỉ nhận hàng</h2>

                    <?php
                    $selectedAddress = null;
                    foreach ($addresses as $address) {
                        if (($form['deliveryAddressId'] ?? '') === $address['id']) {
                            $selectedAddress = $address;
                            break;
                        }
                    }
                    if ($selectedAddress === null && $addresses !== []) {
                        $selectedAddress = $addresses[0];
                    }
                    ?>
                    <button
                        class="checkout-selected-address"
                        type="button"
                        data-open-address-dialog
                        aria-haspopup="dialog"
                    >
                        <span class="checkout-selected-address-content">
                            <span class="checkout-selected-address-contact">
                                <strong data-selected-recipient>
                                    <?php echo $escape($form['recipientName'] ?? 'Người nhận'); ?>
                                </strong>
                                <span data-selected-phone>
                                    <?php echo $escape($form['recipientPhone'] ?? ''); ?>
                                </span>
                            </span>
                            <span class="checkout-selected-address-text" data-selected-address>
                                <?php echo $escape($selectedAddress['address'] ?? 'Chưa có địa chỉ nhận hàng'); ?>
                            </span>
                        </span>
                    </button>

                    <dialog
                        class="checkout-address-dialog"
                        data-address-dialog
                        <?php echo ($form['newAddress'] ?? '') !== '' ? 'data-reopen-new-address' : ''; ?>
                    >
                        <div class="checkout-address-dialog-header">
                            <button
                                class="checkout-dialog-back"
                                type="button"
                                data-close-address-dialog
                                aria-label="Đóng"
                            >←</button>
                            <h3 data-dialog-title>Chọn địa chỉ nhận hàng</h3>
                        </div>

                        <div class="checkout-address-dialog-content" data-address-list>
                            <p class="checkout-address-dialog-label">Địa chỉ</p>
                            <?php if ($addresses !== []) { ?>
                                <div class="checkout-address-options">
                                    <?php foreach ($addresses as $address) { ?>
                                        <label class="checkout-address-option">
                                            <input
                                                type="radio"
                                                name="deliveryAddressId"
                                                value="<?php echo $escape($address['id']); ?>"
                                                data-address-value="<?php echo $escape($address['address']); ?>"
                                                data-address-default="<?php echo $address['isDefault'] ? 'true' : 'false'; ?>"
                                                <?php echo ($form['deliveryAddressId'] ?? '') === $address['id'] ? 'checked' : ''; ?>
                                                required
                                            >
                                            <span class="checkout-address-option-content">
                                                <span class="checkout-address-option-contact">
                                                    <strong data-dialog-recipient><?php echo $escape($form['recipientName'] ?? 'Người nhận'); ?></strong>
                                                    <small data-dialog-phone><?php echo $escape($form['recipientPhone'] ?? ''); ?></small>
                                                </span>
                                                <span class="checkout-address-option-text">
                                                    <?php echo $escape($address['address']); ?>
                                                </span>
                                                <?php if ($address['isDefault']) { ?>
                                                    <span class="checkout-address-default-badge">Mặc định</span>
                                                <?php } ?>
                                            </span>
                                        </label>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <p class="checkout-address-empty">
                                    Bạn chưa có địa chỉ nhận hàng.
                                </p>
                            <?php } ?>
                        </div>

                        <div class="checkout-new-address-fields" data-new-address-fields hidden>
                            <label for="new-delivery-address">Địa chỉ mới</label>
                            <textarea
                                id="new-delivery-address"
                                name="newAddress"
                                rows="3"
                                maxlength="255"
                                autocomplete="street-address"
                            ><?php echo $escape($form['newAddress'] ?? ''); ?></textarea>
                        </div>

                        <div class="checkout-address-dialog-actions" data-address-list-actions>
                            <button
                                class="checkout-address-add-button"
                                type="button"
                                data-show-new-address
                            >＋ Thêm địa chỉ mới</button>
                            <button
                                class="checkout-submit"
                                type="button"
                                data-confirm-address
                            >Xác nhận địa chỉ</button>
                        </div>

                        <div class="checkout-address-dialog-actions" data-new-address-actions hidden>
                            <button
                                class="checkout-address-cancel-button"
                                type="button"
                                data-cancel-new-address
                            >Quay lại</button>
                            <button
                                class="checkout-submit"
                                type="submit"
                                formaction="<?php echo $escape($checkoutBaseUrl); ?>/checkout/address"
                                formmethod="post"
                                formnovalidate
                            >Lưu địa chỉ mới</button>
                        </div>
                    </dialog>
                </section>

                <section class="checkout-section">
                    <h2>Thông tin người nhận</h2>

                    <label for="recipient-name">Họ và tên *</label>
                    <input
                        id="recipient-name"
                        name="recipientName"
                        type="text"
                        maxlength="100"
                        autocomplete="name"
                        value="<?php echo $escape($form['recipientName'] ?? ''); ?>"
                        required
                    >

                    <label for="recipient-phone">Số điện thoại *</label>
                    <input
                        id="recipient-phone"
                        name="recipientPhone"
                        type="tel"
                        maxlength="20"
                        autocomplete="tel"
                        value="<?php echo $escape($form['recipientPhone'] ?? ''); ?>"
                        required
                    >

                </section>

                <section class="checkout-section">
                    <h2>Phương thức thanh toán</h2>

                    <label class="payment-option">
                        <input
                            type="radio"
                            name="paymentMethod"
                            value="COD"
                            <?php echo ($form['paymentMethod'] ?? '') === 'COD' ? 'checked' : ''; ?>
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
                    <strong><?php echo $formatMoney($total); ?></strong>
                </div>

                <p class="checkout-shipping-note">
                    Phí giao hàng chưa được tính trong hệ thống và sẽ được xác nhận riêng.
                </p>

                <button class="checkout-submit" type="submit">
                    Đặt hàng — thanh toán khi nhận
                </button>
            </aside>
        </form>
    <?php } ?>
</section>
