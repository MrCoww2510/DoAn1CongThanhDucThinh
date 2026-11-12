<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatMoney = static fn (mixed $amount): string => number_format((float) $amount, 0, ',', '.').' ₫';
$createdAt = strtotime((string) ($order['NgayTao'] ?? ''));
$status = (string) ($order['TrangThaiDH'] ?? '');
$canPrepare = $status === 'Chờ xác nhận';
$canShip = $status === 'Đang chuẩn bị hàng';
$canCompleteOrFail = $status === 'Đang giao hàng';
$canConfirmReturn = $status === 'Giao hàng thất bại' && (int) ($order['DaHoanKho'] ?? 0) !== 1;
?>
<section class="admin-page">
    <p class="admin-eyebrow"><a href="<?php echo $escape($baseUrl.'/admin/orders'); ?>">UC09 / Danh sách đơn hàng</a></p>
    <h1>Chi tiết đơn hàng <?php echo $escape($order['MaDH']); ?></h1>

    <?php if ($error !== '') { ?><p class="admin-message error" role="alert"><?php echo $escape($error); ?></p><?php } ?>
    <?php if ($success !== '') { ?><p class="admin-message success" role="status"><?php echo $escape($success); ?></p><?php } ?>

    <div class="admin-order-detail">
        <section class="admin-form">
            <h2>Thông tin đơn hàng</h2>
            <dl>
                <dt>Trạng thái</dt><dd><?php echo $escape($status); ?></dd>
                <dt>Ngày đặt</dt><dd><?php echo $createdAt === false ? '—' : $escape(date('d/m/Y H:i', $createdAt)); ?></dd>
                <dt>Khách hàng</dt><dd><?php echo $escape($order['TenKhachHang']); ?></dd>
                <dt>Người nhận</dt><dd><?php echo $escape($order['TenNguoiNhan']); ?></dd>
                <dt>Số điện thoại</dt><dd><?php echo $escape($order['SDTNhan']); ?></dd>
                <dt>Địa chỉ giao hàng</dt><dd><?php echo $escape($order['DiaChiGiao']); ?></dd>
                <dt>Phương thức thanh toán</dt><dd><?php echo $escape($order['PhuongThucTT']); ?></dd>
                <dt>Trạng thái thanh toán</dt><dd><?php echo $escape($order['TrangThaiTT']); ?></dd>
                <dt>Nhân viên xử lý gần nhất</dt><dd><?php echo $escape($order['TenNhanVien'] ?? 'Chưa có'); ?></dd>
                <dt>Tổng tiền</dt><dd><strong><?php echo $formatMoney($order['TongTien']); ?></strong></dd>
            </dl>
        </section>

        <section class="admin-form">
            <h2>Sản phẩm và tồn kho</h2>
            <p>Tồn kho đã được giữ chỗ khi tạo đơn; xác nhận đơn không trừ tồn kho lần nữa.</p>
            <div class="admin-table-wrap">
                <table>
                    <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th><th>Tồn hiện tại</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $item) { ?>
                            <tr>
                                <td><?php echo $escape($item['MaSP'].' — '.$item['TenSP']); ?></td>
                                <td><?php echo (int) $item['SoLuong']; ?></td>
                                <td><?php echo $formatMoney($item['DonGia']); ?></td>
                                <td><?php echo $formatMoney($item['ThanhTien']); ?></td>
                                <td><?php echo (int) $item['SoLuongTon']; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($items === []) { ?><tr><td colspan="5">Đơn hàng không có sản phẩm.</td></tr><?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="admin-form admin-order-actions">
        <h2>Xử lý đơn hàng</h2>
        <?php if ($canPrepare || $canShip) { ?>
            <?php $targetStatus = $canPrepare ? 'Đang chuẩn bị hàng' : 'Đang giao hàng'; ?>
            <p>Trạng thái tiếp theo: <strong><?php echo $escape($targetStatus); ?></strong></p>
            <form method="post" action="<?php echo $escape($baseUrl.'/admin/orders/'.rawurlencode((string) $order['MaDH']).'/status'); ?>">
                <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
                <input type="hidden" name="status" value="<?php echo $escape($targetStatus); ?>">
                <button type="submit">Xác nhận chuyển trạng thái</button>
            </form>
        <?php } elseif ($canCompleteOrFail) { ?>
            <p>Chọn kết quả sau khi giao hàng.</p>
            <form class="admin-order-action" method="post" action="<?php echo $escape($baseUrl.'/admin/orders/'.rawurlencode((string) $order['MaDH']).'/status'); ?>">
                <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
                <input type="hidden" name="status" value="Hoàn thành">
                <button type="submit">Xác nhận giao thành công</button>
            </form>
            <form class="admin-order-action" method="post" action="<?php echo $escape($baseUrl.'/admin/orders/'.rawurlencode((string) $order['MaDH']).'/status'); ?>">
                <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
                <input type="hidden" name="status" value="Giao hàng thất bại">
                <button class="danger-button" type="submit">Ghi nhận giao hàng thất bại</button>
            </form>
        <?php } elseif ($canConfirmReturn) { ?>
            <p>Đơn giao thất bại. Chỉ cập nhật tồn kho sau khi hàng thực tế đã được nhận về.</p>
            <form method="post" action="<?php echo $escape($baseUrl.'/admin/orders/'.rawurlencode((string) $order['MaDH']).'/return'); ?>">
                <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
                <button type="submit">Xác nhận đã nhận hàng hoàn về kho</button>
            </form>
        <?php } elseif ($status === 'Giao hàng thất bại' && (int) ($order['DaHoanKho'] ?? 0) === 1) { ?>
            <p class="admin-message success" role="status">Hàng đã được xác nhận hoàn về kho.</p>
        <?php } else { ?>
            <p>Đơn hàng đã ở trạng thái kết thúc hoặc không có bước xử lý tiếp theo.</p>
        <?php } ?>
        <p><a href="<?php echo $escape($baseUrl.'/admin/orders'); ?>">Quay lại danh sách đơn hàng</a></p>
    </section>
</section>
