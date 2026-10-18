<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$roles = ['KhachHang' => 'Khách hàng', 'NhanVien' => 'Nhân viên', 'QuanTriVien' => 'Quản trị viên'];
$statuses = ['HoatDong' => 'Hoạt động', 'VoHieuHoa' => 'Bị vô hiệu hóa', 'KhongConHoatDong' => 'Không còn hoạt động'];
?>
<section class="user-management">
    <h1>Quản lý người dùng</h1>
    <p>Xem và tìm tài khoản; tạo, cập nhật nhân viên; quản lý trạng thái tài khoản.</p>

    <?php if ($error !== '') { ?><p role="alert"><?php echo $escape($error); ?></p><?php } ?>
    <?php if ($success !== '') { ?><p role="status"><?php echo $escape($success); ?></p><?php } ?>

    <h2><?php echo $editing ? 'Cập nhật nhân viên' : 'Thêm nhân viên'; ?></h2>
    <form method="post" action="<?php echo $escape($baseUrl.'/admin/users/'.($editing ? 'update-staff' : 'create-staff')); ?>">
        <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
        <?php if ($editing) { ?><input type="hidden" name="account_id" value="<?php echo $escape($editing['MaTK']); ?>"><?php } ?>
        <label>Họ tên <input name="name" maxlength="100" required value="<?php echo $escape($editing['HoTen'] ?? ''); ?>"></label>
        <label>Tên đăng nhập <input name="username" maxlength="50" required value="<?php echo $escape($editing['TenDangNhap'] ?? ''); ?>"></label>
        <label>Email <input type="email" name="email" maxlength="100" required value="<?php echo $escape($editing['Email'] ?? ''); ?>"></label>
        <label>Số điện thoại <input name="phone" maxlength="15" value="<?php echo $escape($editing['SDT'] ?? ''); ?>"></label>
        <?php if (!$editing) { ?><label>Mật khẩu ban đầu <input type="password" name="password" minlength="8" maxlength="255" required autocomplete="new-password"></label><?php } ?>
        <button type="submit"><?php echo $editing ? 'Lưu thông tin' : 'Tạo nhân viên'; ?></button>
        <?php if ($editing) { ?><a href="<?php echo $escape($baseUrl.'/admin/users'); ?>">Hủy</a><?php } ?>
    </form>

    <h2>Danh sách tài khoản</h2>
    <form method="get" action="<?php echo $escape($baseUrl.'/admin/users'); ?>">
        <label>Tìm theo tên, email hoặc tên đăng nhập
            <input type="search" name="q" value="<?php echo $escape($query); ?>" maxlength="100">
        </label>
        <button type="submit">Tìm</button>
    </form>

    <div style="overflow-x:auto">
        <table>
            <thead><tr><th>Tài khoản</th><th>Họ tên</th><th>Email</th><th>Điện thoại</th><th>Vai trò</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
            <tbody>
            <?php foreach ($accounts as $account) { ?>
                <tr>
                    <td><?php echo $escape($account['TenDangNhap']); ?></td>
                    <td><?php echo $escape($account['HoTen']); ?></td>
                    <td><?php echo $escape($account['Email']); ?></td>
                    <td><?php echo $escape($account['SDT']); ?></td>
                    <td><?php echo $escape($roles[$account['VaiTro']] ?? $account['VaiTro']); ?></td>
                    <td><?php echo $escape($statuses[$account['TrangThai']] ?? $account['TrangThai']); ?></td>
                    <td>
                        <?php if ($account['VaiTro'] === 'NhanVien') { ?>
                            <a href="<?php echo $escape($baseUrl.'/admin/users?edit='.rawurlencode($account['MaTK'])); ?>">Sửa</a>
                        <?php } ?>
                        <?php if ($account['MaTK'] !== ($_SESSION['user']['MaTK'] ?? '')) { ?>
                            <form method="post" action="<?php echo $escape($baseUrl.'/admin/users/status'); ?>">
                                <input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken); ?>">
                                <input type="hidden" name="account_id" value="<?php echo $escape($account['MaTK']); ?>">
                                <label class="visually-hidden" for="status-<?php echo $escape($account['MaTK']); ?>">Trạng thái <?php echo $escape($account['TenDangNhap']); ?></label>
                                <select id="status-<?php echo $escape($account['MaTK']); ?>" name="status">
                                    <?php foreach ($statuses as $value => $label) { ?>
                                        <option value="<?php echo $escape($value); ?>" <?php echo $account['TrangThai'] === $value ? 'selected' : ''; ?>><?php echo $escape($label); ?></option>
                                    <?php } ?>
                                </select>
                                <button type="submit">Cập nhật</button>
                            </form>
                        <?php } else { ?>
                            Tài khoản đang đăng nhập
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($accounts === []) { ?><tr><td colspan="7">Không tìm thấy tài khoản phù hợp.</td></tr><?php } ?>
            </tbody>
        </table>
    </div>
</section>
