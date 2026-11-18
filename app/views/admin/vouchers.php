<?php
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$app = require BASE_PATH.'/config/app.php';
$baseUrl = rtrim((string) ($app['base_url'] ?? ''), '/');
$dateInput = static fn ($value): string => $value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '';
?>
<link rel="stylesheet" href="<?php echo $escape($baseUrl); ?>/css/admin.css">
<section class="admin-page">
    <h1>Quản lý voucher</h1>
    <?php if ($error !== '') { ?><p class="form-error" role="alert"><?php echo $escape($error); ?></p><?php } ?>
    <?php if ($success !== '') { ?><p class="form-success" role="status"><?php echo $escape($success); ?></p><?php } ?>
    <section class="admin-card"><h2><?php echo $editing ? 'Chỉnh sửa voucher' : 'Tạo voucher'; ?></h2>
        <form method="post" action="<?php echo $escape($baseUrl.'/admin/vouchers/save'); ?>" class="admin-form">
            <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
            <?php if ($editing) { ?><input type="hidden" name="voucher_id" value="<?php echo $escape($editing['MaVoucher']); ?>"><?php } ?>
            <label>Mã voucher <input name="code" maxlength="50" pattern="[A-Za-z0-9_-]{3,50}" required value="<?php echo $escape($editing['MaCode'] ?? ''); ?>"></label>
            <label>Loại giảm <select name="type"><option value="PhanTram" <?php echo ($editing['LoaiGiam'] ?? '') === 'PhanTram' ? 'selected' : ''; ?>>Phần trăm</option><option value="SoTien" <?php echo ($editing['LoaiGiam'] ?? '') === 'SoTien' ? 'selected' : ''; ?>>Số tiền cố định</option></select></label>
            <label>Mức giảm <input name="value" type="number" min="0.01" step="0.01" required value="<?php echo $escape($editing['GiaTri'] ?? ''); ?>"></label>
            <label>Đơn hàng tối thiểu <input name="minimum" type="number" min="0" step="0.01" value="<?php echo $escape($editing['DonToiThieu'] ?? '0'); ?>"></label>
            <label>Giảm tối đa (chỉ cho %) <input name="max_discount" type="number" min="0" step="0.01" value="<?php echo $escape($editing['GiamToiDa'] ?? ''); ?>"></label>
            <label>Tổng lượt tối đa (trống = không giới hạn) <input name="usage_limit" type="number" min="1" step="1" value="<?php echo $escape($editing['TongLuotToiDa'] ?? ''); ?>"></label>
            <label>Lượt tối đa mỗi khách (trống = không giới hạn) <input name="per_customer" type="number" min="1" step="1" value="<?php echo $escape($editing['LuotMoiKhachToiDa'] ?? ''); ?>"></label>
            <label>Bắt đầu <input name="start" type="datetime-local" required value="<?php echo $escape($dateInput($editing['NgayBatDau'] ?? '')); ?>"></label>
            <label>Kết thúc <input name="end" type="datetime-local" required value="<?php echo $escape($dateInput($editing['NgayKetThuc'] ?? '')); ?>"></label>
            <label>Trạng thái <select name="status"><option value="HoatDong" <?php echo ($editing['TrangThai'] ?? 'HoatDong') === 'HoatDong' ? 'selected' : ''; ?>>Đang bật</option><option value="VoHieuHoa" <?php echo ($editing['TrangThai'] ?? '') === 'VoHieuHoa' ? 'selected' : ''; ?>>Vô hiệu hóa</option></select></label>
            <button type="submit">Lưu voucher</button><?php if ($editing) { ?><a href="<?php echo $escape($baseUrl.'/admin/vouchers'); ?>">Hủy chỉnh sửa</a><?php } ?>
        </form>
    </section>
    <div class="admin-table-wrap"><table><thead><tr><th>Mã</th><th>Giảm</th><th>Điều kiện</th><th>Lượt</th><th>Hạn</th><th>Trạng thái</th><th></th></tr></thead><tbody>
    <?php foreach ($vouchers as $voucher) { ?><tr><td><?php echo $escape($voucher['MaCode']); ?></td><td><?php echo $escape($voucher['LoaiGiam'] === 'PhanTram' ? $voucher['GiaTri'].'%' : number_format((float) $voucher['GiaTri'], 0, ',', '.').' ₫'); ?></td><td>Tối thiểu <?php echo number_format((float) $voucher['DonToiThieu'], 0, ',', '.'); ?> ₫</td><td><?php echo (int) $voucher['SoLuotDaDung']; ?> / <?php echo $voucher['TongLuotToiDa'] === null ? '∞' : (int) $voucher['TongLuotToiDa']; ?></td><td><?php echo $escape($voucher['NgayBatDau'].' – '.$voucher['NgayKetThuc']); ?></td><td><?php echo $escape($voucher['TrangThai']); ?></td><td><a href="<?php echo $escape($baseUrl.'/admin/vouchers?edit='.rawurlencode((string) $voucher['MaVoucher'])); ?>">Sửa</a></td></tr><?php } ?>
    <?php if ($vouchers === []) { ?><tr><td colspan="7">Chưa có voucher.</td></tr><?php } ?>
    </tbody></table></div>
</section>
