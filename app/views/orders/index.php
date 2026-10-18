<?php
declare(strict_types=1);

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn ($amount): string =>
    number_format((float) $amount, 0, ',', '.') . ' ₫';
$appConfig = require BASE_PATH . '/config/app.php';
$baseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
?>

<link rel="stylesheet" href="<?= $escape($baseUrl) ?>/css/orders.css">

<section class="orders-page">
    <div class="orders-heading">
        <div>
            <p class="orders-eyebrow">TÀI KHOẢN CỦA BẠN</p>
            <h1>Theo dõi đơn hàng</h1>
            <p>Xem trạng thái xử lý và thông tin thanh toán của các đơn hàng.</p>
        </div>
        <a class="orders-back-link" href="<?= $escape($baseUrl) ?>/products">
            Tiếp tục mua sắm
        </a>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="orders-alert <?= ($flash['type'] ?? '') === 'error'
            ? 'orders-alert-error'
            : 'orders-alert-success' ?>" role="status">
            <?= $escape($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>

    <?php if ($orders === []): ?>
        <div class="orders-empty">
            <h2>Bạn chưa có đơn hàng nào</h2>
            <p>Đơn hàng bạn đặt sẽ xuất hiện tại đây để tiện theo dõi.</p>
            <a class="orders-button" href="<?= $escape($baseUrl) ?>/products">
                Khám phá sản phẩm
            </a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <?php
                $createdAt = strtotime((string) ($order['NgayTao'] ?? ''));
                $statusClass = ($order['TrangThaiDH'] ?? '') === 'Đã hủy'
                    ? 'order-status-cancelled'
                    : 'order-status-active';
                ?>
                <article class="order-card">
                    <div class="order-card-main">
                        <div class="order-card-heading">
                            <div>
                                <span class="order-label">Mã đơn hàng</span>
                                <h2><?= $escape($order['MaDH']) ?></h2>
                            </div>
                            <span class="order-status <?= $statusClass ?>">
                                <?= $escape($order['TrangThaiDH']) ?>
                            </span>
                        </div>
                        <dl class="order-summary">
                            <div>
                                <dt>Ngày đặt</dt>
                                <dd><?= $createdAt === false
                                    ? '—'
                                    : $escape(date('d/m/Y H:i', $createdAt)) ?></dd>
                            </div>
                            <div>
                                <dt>Sản phẩm</dt>
                                <dd><?= (int) $order['SoMatHang'] ?> mặt hàng</dd>
                            </div>
                            <div>
                                <dt>Thanh toán</dt>
                                <dd><?= $escape($order['TrangThaiTT']) ?></dd>
                            </div>
                            <div>
                                <dt>Tổng tiền</dt>
                                <dd class="order-total"><?= $formatMoney($order['TongTien']) ?></dd>
                            </div>
                        </dl>
                    </div>
                    <a
                        class="orders-button orders-button-secondary"
                        href="<?= $escape($baseUrl) ?>/orders/<?= rawurlencode((string) $order['MaDH']) ?>"
                    >
                        Xem chi tiết
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
