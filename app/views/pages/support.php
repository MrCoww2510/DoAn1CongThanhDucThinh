<?php

declare(strict_types=1);

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<main class="support-page">
	<div class="support-shell">
		<nav class="support-breadcrumb" aria-label="Đường dẫn">
			<a href="<?php echo $escape($baseUrl); ?>/">Trang chủ</a>
			<span aria-hidden="true">/</span>
			<span aria-current="page"><?php echo $escape($supportPage['title']); ?></span>
		</nav>

		<header class="support-hero">
			<span class="support-eyebrow">T&amp;T COMPUTER · HỖ TRỢ KHÁCH HÀNG</span>
			<h1><?php echo $escape($supportPage['title']); ?></h1>
			<p><?php echo $escape($supportPage['intro']); ?></p>
		</header>

		<div class="support-content">
			<?php foreach ($supportPage['sections'] as $section) { ?>
			<section class="support-section">
				<h2><?php echo $escape($section['title']); ?></h2>
				<p><?php echo $escape($section['body']); ?></p>
			</section>
			<?php } ?>

			<aside class="support-contact">
				<div>
					<h2>Cần hỗ trợ thêm?</h2>
					<p>Gửi câu hỏi cho đội ngũ T&amp;T Computer qua email.</p>
				</div>
				<a href="mailto:support@ttcomputer.com">support@ttcomputer.com</a>
			</aside>
		</div>
	</div>
</main>
