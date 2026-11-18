<?php
$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$app = require BASE_PATH.'/config/app.php';
$baseUrl = rtrim((string) ($app['base_url'] ?? ''), '/');
$selectedProducts = $editing && !empty($editing['ProductIds']) ? explode(',', (string) $editing['ProductIds']) : [];
$dateInput = static fn ($value): string => $value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '';
?>
<link rel="stylesheet" href="<?php echo $escape($baseUrl); ?>/css/admin.css">
<section class="admin-page">
    <h1>Quản lý chương trình giảm giá</h1>
    <?php if ($error !== '') { ?><p class="form-error" role="alert"><?php echo $escape($error); ?></p><?php } ?>
    <?php if ($success !== '') { ?><p class="form-success" role="status"><?php echo $escape($success); ?></p><?php } ?>
    <section class="admin-card">
        <h2><?php echo $editing ? 'Chỉnh sửa chương trình' : 'Tạo chương trình'; ?></h2>
        <form method="post" action="<?php echo $escape($baseUrl.'/admin/promotions/save'); ?>" class="admin-form">
            <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
            <?php if ($editing) { ?><input type="hidden" name="campaign_id" value="<?php echo $escape($editing['MaCTGG']); ?>"><?php } ?>
            <label>Tên chương trình <input name="name" maxlength="100" required value="<?php echo $escape($editing['TenCTGG'] ?? ''); ?>"></label>
            <label>Phạm vi <select name="scope"><option value="ToanTrang" <?php echo ($editing['PhamVi'] ?? '') === 'ToanTrang' ? 'selected' : ''; ?>>Toàn trang</option><option value="SanPham" <?php echo ($editing['PhamVi'] ?? '') === 'SanPham' ? 'selected' : ''; ?>>Sản phẩm chọn</option></select></label>
            <label>Sản phẩm áp dụng (Ctrl để chọn nhiều)
                <select name="products[]" multiple size="7">
                    <?php foreach ($products as $product) { ?><option value="<?php echo $escape($product['MaSP']); ?>" <?php echo in_array((string) $product['MaSP'], $selectedProducts, true) ? 'selected' : ''; ?>><?php echo $escape($product['MaSP'].' · '.$product['TenSP']); ?></option><?php } ?>
                </select>
            </label>
            <label>Loại giảm <select name="type"><option value="PhanTram" <?php echo ($editing['LoaiGiam'] ?? '') === 'PhanTram' ? 'selected' : ''; ?>>Phần trăm</option><option value="SoTien" <?php echo ($editing['LoaiGiam'] ?? '') === 'SoTien' ? 'selected' : ''; ?>>Số tiền cố định</option></select></label>
            <label>Mức giảm <input name="value" type="number" min="0.01" step="0.01" required value="<?php echo $escape($editing['GiaTri'] ?? ''); ?>"></label>
            <label>Bắt đầu <input name="start" type="datetime-local" required value="<?php echo $escape($dateInput($editing['NgayBatDau'] ?? '')); ?>"></label>
            <label>Kết thúc <input name="end" type="datetime-local" required value="<?php echo $escape($dateInput($editing['NgayKetThuc'] ?? '')); ?>"></label>
            <label>Trạng thái <select name="status"><option value="HoatDong" <?php echo ($editing['TrangThai'] ?? 'HoatDong') === 'HoatDong' ? 'selected' : ''; ?>>Đang bật</option><option value="TamDung" <?php echo ($editing['TrangThai'] ?? '') === 'TamDung' ? 'selected' : ''; ?>>Tạm dừng</option><option value="KetThuc" <?php echo ($editing['TrangThai'] ?? '') === 'KetThuc' ? 'selected' : ''; ?>>Kết thúc</option></select></label>
            <button type="submit">Lưu chương trình</button>
            <?php if ($editing) { ?><a href="<?php echo $escape($baseUrl.'/admin/promotions'); ?>">Hủy chỉnh sửa</a><?php } ?>
        </form>
        <p>Thời gian áp dụng được kiểm tra theo giờ hệ thống. Nếu nhiều chương trình cùng áp dụng, sản phẩm nhận mức giảm lớn nhất.</p>
    </section>
    <div class="admin-table-wrap"><table><thead><tr><th>Tên</th><th>Phạm vi</th><th>Mức giảm</th><th>Thời gian</th><th>Trạng thái</th><th></th></tr></thead><tbody>
    <?php foreach ($campaigns as $campaign) { ?><tr><td><?php echo $escape($campaign['TenCTGG']); ?></td><td><?php echo $campaign['PhamVi'] === 'ToanTrang' ? 'Toàn trang' : 'Sản phẩm chọn'; ?></td><td><?php echo $escape($campaign['LoaiGiam'] === 'PhanTram' ? $campaign['GiaTri'].'%' : number_format((float) $campaign['GiaTri'], 0, ',', '.').' ₫'); ?></td><td><?php echo $escape($campaign['NgayBatDau'].' – '.$campaign['NgayKetThuc']); ?></td><td><?php echo $escape($campaign['TrangThai']); ?></td><td><a href="<?php echo $escape($baseUrl.'/admin/promotions?edit='.rawurlencode((string) $campaign['MaCTGG'])); ?>">Sửa</a></td></tr><?php } ?>
    <?php if ($campaigns === []) { ?><tr><td colspan="6">Chưa có chương trình giảm giá.</td></tr><?php } ?>
    </tbody></table></div>
</section>
