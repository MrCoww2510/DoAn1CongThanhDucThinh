<?php
declare(strict_types=1);

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$appConfig = require BASE_PATH . '/config/app.php';
$baseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
?>

<link rel="stylesheet" href="<?= $escape($baseUrl) ?>/css/account.css">

<section class="account-page">
    <div class="account-heading">
        <p class="account-eyebrow">TÀI KHOẢN CỦA BẠN</p>
        <h1>Quản lý tài khoản</h1>
        <p>Cập nhật thông tin cá nhân, địa chỉ nhận hàng và mật khẩu.</p>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="account-notice account-notice-success" role="status">
            <?= $escape($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="account-notice account-notice-error" role="alert">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="account-form" method="post" action="<?= $escape($baseUrl) ?>/account">
        <input type="hidden" name="_csrf" value="<?= $escape($csrfToken) ?>">

        <section class="account-card">
            <h2>Thông tin cá nhân</h2>
            <p class="account-section-note">
                Tên đăng nhập: <strong><?= $escape($profile['TenDangNhap']) ?></strong>
                <span> (không thể thay đổi)</span>
            </p>

            <div class="account-fields">
                <div class="account-field">
                    <label for="account-name">Họ và tên *</label>
                    <input
                        id="account-name"
                        name="name"
                        type="text"
                        maxlength="100"
                        autocomplete="name"
                        required
                        value="<?= $escape($form['name'] ?? '') ?>"
                    >
                </div>

                <div class="account-field">
                    <label for="account-email">Email *</label>
                    <input
                        id="account-email"
                        name="email"
                        type="email"
                        maxlength="100"
                        autocomplete="email"
                        required
                        value="<?= $escape($form['email'] ?? '') ?>"
                    >
                </div>

                <div class="account-field">
                    <label for="account-phone">Số điện thoại</label>
                    <input
                        id="account-phone"
                        name="phone"
                        type="tel"
                        maxlength="20"
                        autocomplete="tel"
                        value="<?= $escape($form['phone'] ?? '') ?>"
                    >
                </div>

                <div class="account-field account-field-wide">
                    <label for="account-address">Địa chỉ nhận hàng</label>
                    <textarea
                        id="account-address"
                        name="address"
                        rows="3"
                        maxlength="255"
                        autocomplete="street-address"
                    ><?= $escape($form['address'] ?? '') ?></textarea>
                </div>
            </div>
        </section>

        <section class="account-card">
            <h2>Đổi mật khẩu</h2>
            <p class="account-section-note">
                Để trống các trường bên dưới nếu bạn không muốn đổi mật khẩu.
            </p>

            <div class="account-fields">
                <div class="account-field">
                    <label for="current-password">Mật khẩu hiện tại</label>
                    <input
                        id="current-password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                    >
                </div>

                <div class="account-field">
                    <label for="new-password">Mật khẩu mới</label>
                    <input
                        id="new-password"
                        name="new_password"
                        type="password"
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>

                <div class="account-field">
                    <label for="confirm-password">Xác nhận mật khẩu mới</label>
                    <input
                        id="confirm-password"
                        name="confirm_password"
                        type="password"
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>
            </div>
        </section>

        <div class="account-actions">
            <button class="account-save-button" type="submit">Lưu thay đổi</button>
        </div>
    </form>

    <div class="account-logout">
        <form method="post" action="<?= $escape($baseUrl) ?>/logout">
            <input type="hidden" name="_csrf" value="<?= $escape($csrfToken) ?>">
            <button class="account-logout-button" type="submit">Đăng xuất</button>
        </form>
    </div>
</section>
