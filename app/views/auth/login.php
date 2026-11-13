<!-- Giao diện biểu mẫu đăng nhập. -->

<div class="auth-page">
	<section class="auth-card">
		<p class="eyebrow">T&amp;T COMPUTER</p>
		<h1>Đăng nhập</h1>
		<p class="auth-subtitle">
			Đăng nhập để tiếp tục mua sắm và quản lý đơn hàng.
		</p>

		<?php if (!empty($_SESSION['_register_success'])) { ?>
		<div class="auth-success">
			Đăng ký thành công. Hãy đăng nhập bằng tài khoản vừa tạo.
		</div>
		<?php unset($_SESSION['_register_success']); ?>
		<?php } ?>

		<?php if ($error !== '') { ?>
		<div class="auth-error">
			<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
		</div>
		<?php } ?>

		<form method="post" action="<?php echo htmlspecialchars($baseUrl.'/login', ENT_QUOTES, 'UTF-8'); ?>"
			class="auth-form">

			<input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

			<label for="identity">Tên đăng nhập hoặc email</label>
			<input id="identity" name="identity" type="text" autocomplete="username" required
				value="<?php echo htmlspecialchars($identity, ENT_QUOTES, 'UTF-8'); ?>"
				placeholder="Nhập tên đăng nhập hoặc email">

			<label for="password">Mật khẩu</label>
			<input id="password" name="password" type="password" autocomplete="current-password" required
				placeholder="Nhập mật khẩu">

			<button type="submit" class="auth-submit">Đăng nhập</button>
		</form>

		<div class="google-auth" data-google-auth
			data-client-id="<?php echo htmlspecialchars($googleClientId ?? '', ENT_QUOTES, 'UTF-8'); ?>"
			data-endpoint="<?php echo htmlspecialchars($baseUrl.'/auth/google/login', ENT_QUOTES, 'UTF-8'); ?>"
			data-csrf="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
			<div class="google-divider"><span>hoặc</span></div>
			<div class="google-button" data-google-button></div>
			<p class="google-message" data-google-message role="status" aria-live="polite"></p>
		</div>
		<script src="https://accounts.google.com/gsi/client" async defer></script>
		<script src="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/js/google-auth.js" defer></script>

		<p class="auth-footer">
			Chưa có tài khoản?
			<a href="<?php echo htmlspecialchars($baseUrl.'/register', ENT_QUOTES, 'UTF-8'); ?>">
				Đăng ký ngay
			</a>
		</p>
	</section>
</div>
