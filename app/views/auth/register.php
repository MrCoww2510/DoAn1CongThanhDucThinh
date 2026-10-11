
<div class="auth-page">
    <section class="auth-card">
        <p class="eyebrow">T&amp;T COMPUTER</p>
        <h1>Tạo tài khoản</h1>
        <p class="auth-subtitle">
            Đăng ký tài khoản khách hàng để trải nghiệm mua sắm thuận tiện hơn.
        </p>

        <?php if ($error !== ''): ?>
            <div class="auth-error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post"
              action="<?= htmlspecialchars($baseUrl . '/register', ENT_QUOTES, 'UTF-8') ?>"
              class="auth-form">

            <input type="hidden" name="_csrf"
                   value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <label for="name">Họ và tên *</label>
            <input id="name" name="name" type="text" maxlength="100"
                   autocomplete="name" required
                   value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Nguyễn Văn A">

            <label for="username">Tên đăng nhập *</label>
            <input id="username" name="username" type="text"
                   minlength="4" maxlength="50" required
                   autocomplete="username"
                   value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Ví dụ: nguyenvana">

            <label for="email">Email *</label>
            <input id="email" name="email" type="email" maxlength="100"
                   autocomplete="email" required
                   value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="email@example.com">

            <label for="phone">Số điện thoại</label>
            <input id="phone" name="phone" type="tel" maxlength="15"
                   autocomplete="tel"
                   value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Nhập số điện thoại">

            <label for="password">Mật khẩu *</label>
            <input id="password" name="password" type="password"
                   minlength="8" required autocomplete="new-password"
                   placeholder="Ít nhất 8 ký tự">

            <label for="confirm_password">Xác nhận mật khẩu *</label>
            <input id="confirm_password" name="confirm_password"
                   type="password" minlength="8" required
                   autocomplete="new-password"
                   placeholder="Nhập lại mật khẩu">

            <button type="submit" class="auth-submit">Tạo tài khoản</button>
        </form>

        <p class="auth-footer">
            Đã có tài khoản?
            <a href="<?= htmlspecialchars($baseUrl . '/login', ENT_QUOTES, 'UTF-8') ?>">
                Đăng nhập
            </a>
        </p>
    </section>
</div>