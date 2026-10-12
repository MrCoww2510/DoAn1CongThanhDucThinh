<?php
declare(strict_types=1);

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn ($amount): string =>
    number_format((float) $amount, 0, ',', '.') . ' ₫';
$appConfig = require BASE_PATH . '/config/app.php';
$baseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
$createdAt = strtotime((string) ($order['NgayTao'] ?? ''));
$canCancel = ($order['TrangThaiDH'] ?? '') === 'Chờ xác nhận';
$statusClass = ($order['TrangThaiDH'] ?? '') === 'Đã hủy'
    ? 'order-status-cancelled'
    : 'order-status-active';
?>

<link rel="stylesheet" href="<?= $escape($baseUrl) ?>/css/orders.css">

<section class="orders-page">
    <a class="orders-back-link orders-list-link" href="<?= $escape($baseUrl) ?>/orders">
        ← Danh sách đơn hàng
    </a>

    <div class="orders-heading">
        <div>
            <p class="orders-eyebrow">CHI TIẾT ĐƠN HÀNG</p>
            <h1><?= $escape($order['MaDH']) ?></h1>
        </div>
        <span class="order-status <?= $statusClass ?>">
            <?= $escape($order['TrangThaiDH']) ?>
        </span>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="orders-alert <?= ($flash['type'] ?? '') === 'error'
            ? 'orders-alert-error'
            : 'orders-alert-success' ?>" role="status">
            <?= $escape($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>

    <div class="order-detail-layout">
        <div class="order-detail-card">
            <h2>Thông tin giao hàng</h2>
            <dl class="order-detail-list">
                <div>
                    <dt>Người nhận</dt>
                    <dd><?= $escape($order['TenNguoiNhan']) ?></dd>
                </div>
                <div>
                    <dt>Số điện thoại</dt>
                    <dd><?= $escape($order['SDTNhan']) ?></dd>
                </div>
                <div>
                    <dt>Địa chỉ giao hàng</dt>
                    <dd><?= $escape($order['DiaChiGiao']) ?></dd>
                </div>
                <div>
                    <dt>Ngày đặt</dt>
                    <dd><?= $createdAt === false
                        ? '—'
                        : $escape(date('d/m/Y H:i', $createdAt)) ?></dd>
                </div>
                <div>
                    <dt>Phương thức thanh toán</dt>
                    <dd><?= $escape($order['PhuongThucTT']) ?></dd>
                </div>
                <div>
                    <dt>Trạng thái thanh toán</dt>
                    <dd><?= $escape($order['TrangThaiTT']) ?></dd>
                </div>
            </dl>
        </div>

        <aside class="order-detail-card">
            <h2>Sản phẩm đã đặt</h2>
            <?php if ($items === []): ?>
                <p class="orders-muted">Không có thông tin sản phẩm trong đơn hàng.</p>
            <?php else: ?>
                <table class="order-items-table">
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
                            <td class="order-item-name">
                                <?= $escape($item['TenSP']) ?>
                            </td>
                            <td class="order-item-quantity">
                                <?= (int) $item['SoLuong'] ?>
                            </td>
                            <td class="order-item-price"><?= $formatMoney($item['ThanhTien']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <div class="order-grand-total">
                <span>Tổng tiền sản phẩm</span>
                <strong><?= $formatMoney($order['TongTien']) ?></strong>
            </div>
            <p class="orders-muted">Phí giao hàng chưa được tính trong hệ thống.</p>

            <?php if ($canCancel): ?>
                <form
                    class="order-cancel-form"
                    method="post"
                    action="<?= $escape($baseUrl) ?>/orders/<?= rawurlencode((string) $order['MaDH']) ?>/cancel"
                    data-order-cancel-form
                >
                    <input type="hidden" name="_csrf" value="<?= $escape($csrfToken) ?>">
                    <button
                        class="orders-button orders-button-danger"
                        type="button"
                        data-order-cancel-open
                    >
                        Hủy đơn hàng
                    </button>
                </form>
                <p class="orders-muted">
                    Bạn chỉ có thể hủy đơn khi đơn hàng còn chờ xác nhận.
                </p>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php if ($canCancel): ?>
    <dialog class="order-cancel-dialog" data-order-cancel-dialog aria-labelledby="order-cancel-title">
        <div class="order-cancel-dialog-content">
            <span class="order-cancel-dialog-icon" aria-hidden="true">!</span>
            <h2 id="order-cancel-title">Xác nhận hủy đơn hàng</h2>
            <p>
                Bạn có chắc muốn hủy đơn hàng này không? Sản phẩm sẽ được hoàn lại kho
                và thao tác này không thể hoàn tác.
            </p>
            <div class="order-cancel-dialog-actions">
                <button
                    class="order-cancel-dialog-button order-cancel-dialog-keep"
                    type="button"
                    data-order-cancel-close
                >
                    Giữ lại đơn
                </button>
                <button
                    class="order-cancel-dialog-button order-cancel-dialog-confirm"
                    type="button"
                    data-order-cancel-confirm
                >
                    Xác nhận hủy
                </button>
            </div>
        </div>
    </dialog>
    <script src="<?= $escape($baseUrl) ?>/js/order-cancel-dialog.js" defer></script>
<?php endif; ?>
