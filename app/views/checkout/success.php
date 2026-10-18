<?php
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn ($amount): string => number_format((float) $amount, 0, ',', '.').' ₫';
$appConfig = require BASE_PATH.'/config/app.php';
$checkoutBaseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
$createdAt = strtotime((string) ($order['NgayTao'] ?? ''));
?>

<link
    rel="stylesheet"
    href="<?php echo $escape($checkoutBaseUrl); ?>/css/checkout.css"
>

<section class="checkout-page">
    <div class="checkout-success-card">
        <p class="checkout-success-mark" aria-hidden="true">✓</p>
        <p class="checkout-eyebrow">ĐẶT HÀNG THÀNH CÔNG</p>
        <h1>Cảm ơn bạn đã đặt hàng!</h1>
        <p>
            Đơn hàng đang chờ xác nhận. Bạn thanh toán khi nhận hàng;
            trạng thái thanh toán hiện là
            <strong><?php echo $escape($order['TrangThaiTT']); ?></strong>.
        </p>

        <dl class="checkout-receipt">
            <div>
                <dt>Mã đơn hàng</dt>
                <dd><?php echo $escape($order['MaDH']); ?></dd>
            </div>
            <div>
                <dt>Người nhận</dt>
                <dd><?php echo $escape($order['TenNguoiNhan']); ?></dd>
            </div>
            <div>
                <dt>Địa chỉ giao hàng</dt>
                <dd><?php echo $escape($order['DiaChiGiao']); ?></dd>
            </div>
            <div>
                <dt>Phương thức thanh toán</dt>
                <dd>Thanh toán khi nhận hàng (COD)</dd>
            </div>
            <div>
                <dt>Trạng thái đơn hàng</dt>
                <dd><?php echo $escape($order['TrangThaiDH']); ?></dd>
            </div>
            <div>
                <dt>Ngày đặt</dt>
                <dd><?php echo $createdAt === false ? '—' : date('d/m/Y H:i', $createdAt); ?></dd>
            </div>
        </dl>

        <h2>Sản phẩm</h2>
        <ul class="checkout-items checkout-receipt-items">
            <?php foreach ($items as $item) { ?>
                <li>
                    <span>
                        <?php echo $escape($item['TenSP']); ?>
                        × <?php echo (int) $item['SoLuong']; ?>
                    </span>
                    <strong><?php echo $formatMoney($item['ThanhTien']); ?></strong>
                </li>
            <?php } ?>
        </ul>
        <table class="checkout-order-items checkout-receipt-items">
            <thead>
                <tr>
                    <th scope="col">Sản phẩm</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td class="checkout-item-name"><?php echo $escape($item['TenSP']); ?></td>
                    <td class="checkout-item-quantity"><?php echo (int) $item['SoLuong']; ?></td>
                    <td class="checkout-item-price"><?php echo $formatMoney($item['ThanhTien']); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <div class="checkout-total">
            <span>Tổng tiền sản phẩm</span>
            <strong><?php echo $formatMoney($order['TongTien']); ?></strong>
        </div>
        <p class="checkout-shipping-note">
            Phí giao hàng chưa được tính trong hệ thống.
        </p>

        <a class="checkout-submit" href="<?php echo $escape($checkoutBaseUrl); ?>/products">
            Tiếp tục mua sắm
        </a>
        <a
            class="checkout-submit"
            href="<?php echo $escape($checkoutBaseUrl); ?>/orders/<?php echo rawurlencode((string) $order['MaDH']); ?>"
        >
            Theo dõi đơn hàng
        </a>
    </div>
</section>
