<?php
// Giao diện danh sách và quản lý đơn hàng.
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?>
<section class="admin-page">
    <p class="admin-eyebrow">KHU VỰC QUẢN LÝ / UC09</p>
    <h1>Quản lý đơn hàng</h1>
    <p>Xem thông tin đơn và xử lý đơn theo đúng trình tự trạng thái.</p>

    <?php if ($error !== '') { ?><p class="admin-message error" role="alert"><?php echo $escape($error); ?></p><?php } ?>
    <?php if ($success !== '') { ?><p class="admin-message success" role="status"><?php echo $escape($success); ?></p><?php } ?>

    <form class="admin-search" method="get" action="<?php echo $escape($baseUrl.'/admin/orders'); ?>">
        <label>Tìm mã đơn, khách hàng hoặc người nhận
            <input type="search" name="q" maxlength="100" value="<?php echo $escape($query); ?>">
        </label>
        <label>Trạng thái
            <select name="status">
                <option value="">Tất cả</option>
                <?php foreach ($statuses as $status) { ?>
                    <option value="<?php echo $escape($status); ?>" <?php echo $statusFilter === $status ? 'selected' : ''; ?>>
                        <?php echo $escape($status); ?>
                    </option>
                <?php } ?>
            </select>
        </label>
        <button type="submit">Lọc đơn hàng</button>
    </form>

    <div class="admin-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Người nhận</th>
                    <th>Ngày đặt</th>
                    <th>Sản phẩm</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) { ?>
                    <?php $createdAt = strtotime((string) ($order['NgayTao'] ?? '')); ?>
                    <tr>
                        <td><?php echo $escape($order['MaDH']); ?></td>
                        <td><?php echo $escape($order['TenKhachHang']); ?></td>
                        <td><?php echo $escape($order['TenNguoiNhan']); ?></td>
                        <td><?php echo $createdAt === false ? '—' : $escape(date('d/m/Y H:i', $createdAt)); ?></td>
                        <td><?php echo (int) $order['SoMatHang']; ?></td>
                        <td><?php echo number_format((float) $order['TongTien'], 0, ',', '.'); ?> ₫</td>
                        <td><?php echo $escape($order['TrangThaiTT']); ?></td>
                        <td><?php echo $escape($order['TrangThaiDH']); ?></td>
                        <td>
                            <a href="<?php echo $escape($baseUrl.'/admin/orders/'.rawurlencode((string) $order['MaDH'])); ?>">
                                Xem và xử lý
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($orders === []) { ?><tr><td colspan="9">Không tìm thấy đơn hàng phù hợp.</td></tr><?php } ?>
            </tbody>
        </table>
    </div>
</section>
