<?php
// Giao diện hồ sơ tài khoản và thông tin nhận hàng của khách.

declare(strict_types=1);

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$appConfig = require BASE_PATH.'/config/app.php';
$baseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');
?>

<link rel="stylesheet" href="<?php echo $escape($baseUrl); ?>/css/account.css">

<section class="account-page">
	<div class="account-heading">
		<p class="account-eyebrow">TÀI KHOẢN CỦA BẠN</p>
		<h1>Quản lý tài khoản</h1>
		<p>Cập nhật thông tin cá nhân, mật khẩu và sổ địa chỉ giao hàng.</p>
	</div>

	<?php if (!empty($flash)) { ?>
	<div class="account-notice account-notice-success" role="status">
		<?php echo $escape($flash['message'] ?? ''); ?>
	</div>
	<?php } ?>

	<?php if (!empty($errors)) { ?>
	<div class="account-notice account-notice-error" role="alert">
		<ul>
			<?php foreach ($errors as $error) { ?>
			<li><?php echo $escape($error); ?></li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>

	<form class="account-form" method="post" action="<?php echo $escape($baseUrl); ?>/account">
		<input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken ?? ''); ?>">

		<section class="account-card">
			<h2>Thông tin cá nhân</h2>
			<p class="account-section-note">
				Tên đăng nhập: <strong><?php echo $escape($profile['TenDangNhap'] ?? ''); ?></strong>
				<span> (không thể thay đổi)</span>
			</p>

			<div class="account-fields">
				<div class="account-field">
					<label for="account-name">Họ và tên *</label>
					<input id="account-name" name="name" type="text" maxlength="100" autocomplete="name" required
						value="<?php echo $escape($form['name'] ?? ''); ?>">
				</div>

				<div class="account-field">
					<label for="account-email">Email *</label>
					<input id="account-email" name="email" type="email" maxlength="100" autocomplete="email" required
						value="<?php echo $escape($form['email'] ?? ''); ?>">
				</div>

				<div class="account-field">
					<label for="account-phone">Số điện thoại</label>
					<input id="account-phone" name="phone" type="tel" maxlength="20" autocomplete="tel"
						value="<?php echo $escape($form['phone'] ?? ''); ?>">
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
					<input id="current-password" name="current_password" type="password"
						autocomplete="current-password">
				</div>

				<div class="account-field">
					<label for="new-password">Mật khẩu mới</label>
					<input id="new-password" name="new_password" type="password" minlength="8"
						autocomplete="new-password">
				</div>

				<div class="account-field">
					<label for="confirm-password">Xác nhận mật khẩu mới</label>
					<input id="confirm-password" name="confirm_password" type="password" minlength="8"
						autocomplete="new-password">
				</div>
			</div>
		</section>

		<div class="account-actions">
			<button class="account-save-button" type="submit">Lưu thay đổi</button>
		</div>
	</form>

	<?php if (!empty($addressBookEnabled)) { ?>
	<section class="account-card account-address-book">
		<h2>Sổ địa chỉ giao hàng</h2>
		<p class="account-section-note">
			Lưu nhiều địa chỉ để chọn khi đặt hàng. Thay đổi ở đây không làm thay đổi địa chỉ đã lưu trên đơn hàng trước
			đó.
		</p>

		<form class="account-address-add" method="post" action="<?php echo $escape($baseUrl); ?>/account/addresses/add">
			<input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken ?? ''); ?>">
			<div class="account-field">
				<label for="new-delivery-address">Thêm địa chỉ mới</label>
				<textarea id="new-delivery-address" name="address" rows="3" maxlength="255"
					autocomplete="street-address"
					required><?php echo $escape(($addressDraft['id'] ?? '') === '' ? ($addressDraft['address'] ?? '') : ''); ?></textarea>
			</div>
			<button class="account-save-button" type="submit">Thêm địa chỉ</button>
		</form>

		<?php if (empty($addresses)) { ?>
		<p class="account-address-empty">Bạn chưa lưu địa chỉ giao hàng nào.</p>
		<?php } else { ?>
		<ul class="account-address-list">
			<?php foreach ($addresses as $address) { ?>
			<?php
                        $addressId = (string) $address['MaDiaChi'];
                        $addressValue = ($addressDraft['id'] ?? null) === $addressId
                            ? (string) ($addressDraft['address'] ?? '')
                            : (string) $address['DiaChi'];
                        ?>
			<li class="account-address-item">
				<form method="post" action="<?php echo $escape($baseUrl); ?>/account/addresses/update">
					<input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken ?? ''); ?>">
					<input type="hidden" name="address_id" value="<?php echo $escape($addressId); ?>">
					<input type="hidden" name="current_address" value="<?php echo $escape($address['DiaChi']); ?>">
					<div class="account-field">
						<label for="delivery-address-<?php echo $escape($addressId); ?>">
							Địa chỉ giao hàng<?php echo $addressId === 'profile' ? ' (địa chỉ cũ)' : ''; ?>
						</label>
						<textarea id="delivery-address-<?php echo $escape($addressId); ?>" name="address" rows="3"
							maxlength="255" autocomplete="street-address"
							required><?php echo $escape($addressValue); ?></textarea>
					</div>
					<button class="account-save-button" type="submit">Lưu địa chỉ</button>
				</form>
				<form method="post" action="<?php echo $escape($baseUrl); ?>/account/addresses/delete">
					<input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken ?? ''); ?>">
					<input type="hidden" name="address_id" value="<?php echo $escape($addressId); ?>">
					<input type="hidden" name="current_address" value="<?php echo $escape($address['DiaChi']); ?>">
					<button class="account-address-delete" type="submit">Xóa địa chỉ</button>
				</form>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</section>
	<?php } ?>

	<div class="account-logout">
		<form method="post" action="<?php echo $escape($baseUrl); ?>/logout">
			<input type="hidden" name="_csrf" value="<?php echo $escape($csrfToken ?? ''); ?>">
			<button class="account-logout-button" type="submit">Đăng xuất</button>
		</form>
	</div>
</section>