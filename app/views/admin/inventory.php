<?php $escape = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>
<section class="admin-page">
    <p class="admin-eyebrow"><a href="<?php echo $escape($baseUrl.'/admin'); ?>">Quản trị</a> / UC14</p><h1>Quản lý kho</h1>
    <?php if ($error !== '') { ?><p class="admin-message error" role="alert"><?php echo $escape($error); ?></p><?php } ?>
    <?php if ($success !== '') { ?><p class="admin-message success" role="status"><?php echo $escape($success); ?></p><?php } ?>
    <section class="admin-form"><h2>Nhập kho hoặc điều chỉnh tồn</h2><p>Nhập kho sẽ cộng số lượng. Điều chỉnh sẽ đặt tồn kho bằng số lượng thực tế đã kiểm kê. Mỗi thao tác được lưu vào lịch sử.</p>
        <form method="post" action="<?php echo $escape($baseUrl.'/admin/inventory/update'); ?>">
            <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
            <label>Sản phẩm <select name="product_id" required><option value="">Chọn sản phẩm</option><?php foreach ($products as $product) { ?><option value="<?php echo $escape($product['MaSP']); ?>"><?php echo $escape($product['MaSP'].' — '.$product['TenSP'].' (tồn: '.$product['SoLuongTon'].')'); ?></option><?php } ?></select></label>
            <label>Loại thao tác <select name="operation"><option value="NhapKho">Nhập kho</option><option value="DieuChinh">Điều chỉnh theo tồn thực tế</option></select></label>
            <label>Số lượng <input type="number" name="quantity" min="0" step="1" required></label>
            <div><button type="submit">Cập nhật tồn kho</button></div>
        </form>
    </section>
    <h2>Tồn kho hiện tại</h2><div class="admin-table-wrap"><table><thead><tr><th>Mã</th><th>Sản phẩm</th><th>Danh mục</th><th>Tồn</th><th>Cảnh báo</th></tr></thead><tbody>
    <?php foreach ($products as $product) { ?><tr><td><?php echo $escape($product['MaSP']); ?></td><td><?php echo $escape($product['TenSP']); ?></td><td><?php echo $escape($product['TenDM']); ?></td><td><?php echo $escape($product['SoLuongTon']); ?></td><td><?php echo (int) $product['SoLuongTon'] === 0 ? 'Hết hàng' : ((int) $product['SoLuongTon'] <= 5 ? 'Sắp hết' : ''); ?></td></tr><?php } ?>
    <?php if ($products === []) { ?><tr><td colspan="5">Chưa có sản phẩm.</td></tr><?php } ?>
    </tbody></table></div>
    <h2>Lịch sử kho gần đây</h2><div class="admin-table-wrap"><table><thead><tr><th>Thời gian</th><th>Sản phẩm</th><th>Thao tác</th><th>Thay đổi</th><th>Người thực hiện</th></tr></thead><tbody>
    <?php foreach ($history as $item) { $operationLabel = ['NhapKho' => 'Nhập kho', 'DieuChinh' => 'Điều chỉnh', 'XuatKho' => 'Xuất kho'][$item['LoaiThaoTac']] ?? $item['LoaiThaoTac']; ?><tr><td><?php echo $escape($item['ThoiGian']); ?></td><td><?php echo $escape($item['MaSP'].' — '.$item['TenSP']); ?></td><td><?php echo $escape($operationLabel); ?></td><td><?php echo (int) $item['SoLuongThayDoi'] > 0 ? '+' : ''; ?><?php echo $escape($item['SoLuongThayDoi']); ?></td><td><?php echo $escape($item['HoTen']); ?></td></tr><?php } ?>
    <?php if ($history === []) { ?><tr><td colspan="5">Chưa có giao dịch kho.</td></tr><?php } ?>
    </tbody></table></div>
</section>
