<?php
$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn ($amount): string =>
    number_format((float) $amount, 0, ',', '.') . ' ₫';
$appConfig = require BASE_PATH . '/config/app.php';
$checkoutBaseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
$createdAt = strtotime((string) ($order['NgayTao'] ?? ''));
?>

<link
    rel="stylesheet"
    href="<?= $escape($checkoutBaseUrl) ?>/css/checkout.css"
>

<section class="checkout-page">
    <div class="checkout-success-card">
        <p class="checkout-success-mark" aria-hidden="true">✓</p>
        <p class="checkout-eyebrow">ĐẶT HÀNG THÀNH CÔNG</p>
        <h1>Cảm ơn bạn đã đặt hàng!</h1>
        <p>
            Đơn hàng đang chờ xác nhận. Bạn thanh toán khi nhận hàng;
            trạng thái thanh toán hiện là
            <strong><?= $escape($order['TrangThaiTT']) ?></strong>.
        </p>

        <dl class="checkout-receipt">
            <div>
                <dt>Mã đơn hàng</dt>
                <dd><?= $escape($order['MaDH']) ?></dd>
            </div>
            <div>
                <dt>Người nhận</dt>
                <dd><?= $escape($order['TenNguoiNhan']) ?></dd>
            </div>
            <div>
                <dt>Địa chỉ giao hàng</dt>
                <dd><?= $escape($order['DiaChiGiao']) ?></dd>
            </div>
            <div>
                <dt>Phương thức thanh toán</dt>
                <dd>Thanh toán khi nhận hàng (COD)</dd>
            </div>
            <div>
                <dt>Trạng thái đơn hàng</dt>
                <dd><?= $escape($order['TrangThaiDH']) ?></dd>
            </div>
            <div>
                <dt>Ngày đặt</dt>
                <dd><?= $createdAt === false ? '—' : date('d/m/Y H:i', $createdAt) ?></dd>
            </div>
        </dl>

        <h2>Sản phẩm</h2>
        <table class="checkout-order-items checkout-receipt-items">
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
                    <td class="checkout-item-name"><?= $escape($item['TenSP']) ?></td>
                    <td class="checkout-item-quantity"><?= (int) $item['SoLuong'] ?></td>
                    <td class="checkout-item-price"><?= $formatMoney($item['ThanhTien']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="checkout-total">
            <span>Tổng tiền sản phẩm</span>
            <strong><?= $formatMoney($order['TongTien']) ?></strong>
        </div>
        <p class="checkout-shipping-note">
            Phí giao hàng chưa được tính trong hệ thống.
        </p>

        <a class="checkout-submit" href="<?= $escape($checkoutBaseUrl) ?>/products">
            Tiếp tục mua sắm
        </a>
        <a
            class="checkout-submit"
            href="<?= $escape($checkoutBaseUrl) ?>/orders/<?= rawurlencode((string) $order['MaDH']) ?>"
        >
            Theo dõi đơn hàng
        </a>
    </div>
</section>
