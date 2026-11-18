<?php
// Giao diện bộ lọc và danh sách sản phẩm.

$sort = $sort ?? 'default';
?>

<section class="catalog-page" aria-labelledby="catalog-title">
	<header class="catalog-heading">
		<p class="catalog-eyebrow">T&amp;T COMPUTER · CỬA HÀNG</p>
		<h1 id="catalog-title">Sản phẩm máy tính</h1>
		<p>Tìm thiết bị và linh kiện phù hợp với nhu cầu của bạn.</p>
	</header>

	<form class="product-filter" method="get" action="<?php echo htmlspecialchars(
		$baseUrl.'/products',
		ENT_QUOTES,
		'UTF-8'
	); ?>">
		<div class="filter-heading">
			<h2>Lọc và sắp xếp</h2>
			<p>Thu hẹp lựa chọn để tìm sản phẩm phù hợp.</p>
		</div>

		<label class="filter-field filter-search">
			<span>Tìm sản phẩm</span>
			<input type="search" name="q" placeholder="Nhập tên sản phẩm..." value="<?php echo htmlspecialchars(
				$keyword,
				ENT_QUOTES,
				'UTF-8'
			); ?>">
		</label>

		<label class="filter-field">
			<span>Danh mục</span>
			<select name="category">
				<option value="">Tất cả danh mục</option>
				<?php foreach ($categories as $category) { ?>
				<option value="<?php echo htmlspecialchars((string) $category['MaDM'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (string) $categoryId === (string) $category['MaDM'] ? 'selected' : ''; ?>>
					<?php echo htmlspecialchars($category['TenDM'], ENT_QUOTES, 'UTF-8'); ?>
				</option>
				<?php } ?>
			</select>
		</label>

		<label class="filter-field">
			<span>Thương hiệu</span>
			<select name="brand">
				<option value="">Tất cả thương hiệu</option>
				<?php foreach ($brands as $brandOption) { ?>
				<option value="<?php echo htmlspecialchars((string) $brandOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $brand === $brandOption ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) $brandOption, ENT_QUOTES, 'UTF-8'); ?></option>
				<?php } ?>
			</select>
		</label>

		<label class="filter-field">
			<span>Loại sản phẩm</span>
			<select name="type">
				<option value="">Tất cả loại</option>
				<?php foreach ($types as $typeOption) { ?>
				<option value="<?php echo htmlspecialchars((string) $typeOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $type === $typeOption ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) $typeOption, ENT_QUOTES, 'UTF-8'); ?></option>
				<?php } ?>
			</select>
		</label>

		<fieldset class="filter-price">
			<legend>Khoảng giá</legend>
			<label class="filter-field">
				<span>Từ (₫)</span>
				<input type="number" name="min_price" min="0" step="1" value="<?php echo htmlspecialchars((string) $minPrice, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Không giới hạn">
			</label>
			<label class="filter-field">
				<span>Đến (₫)</span>
				<input type="number" name="max_price" min="0" step="1" value="<?php echo htmlspecialchars((string) $maxPrice, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Không giới hạn">
			</label>
		</fieldset>

		<label class="filter-field filter-sort">
			<span>Sắp xếp theo</span>
			<select name="sort">
				<option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>Mặc định</option>
				<option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Tên A → Z</option>
				<option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Tên Z → A</option>
				<option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Giá thấp → cao</option>
				<option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Giá cao → thấp</option>
			</select>
		</label>

		<div class="filter-actions">
			<button class="primary-button" type="submit">Áp dụng bộ lọc</button>
			<a class="reset-link" href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>">Xóa bộ lọc</a>
		</div>
	</form>

	<div class="catalog-results" aria-live="polite">
		<p class="result-count">Tìm thấy <strong><?php echo (int) ($totalProducts ?? count($products)); ?></strong> sản phẩm</p>
	</div>

	<?php if ($products === []) { ?>
	<div class="empty-state">
		<svg class="empty-state-icon" viewBox="0 0 64 64" role="img" aria-label="Kính lúp tìm kiếm">
			<circle cx="27" cy="27" r="17" fill="none" stroke="currentColor" stroke-width="3"></circle>
			<path d="m40 40 13 13M20 27h14M27 20v14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
		</svg>
		<h2>Chưa tìm thấy sản phẩm</h2>
		<p>Hãy thử từ khóa khác hoặc chọn danh mục khác.</p>
	</div>
	<?php } else { ?>
	<div class="product-grid">
		<?php foreach ($products as $product) { ?>
		<?php require __DIR__.'/_card.php'; ?>
		<?php } ?>
	</div>
	<?php } ?>

	<?php if (($totalPages ?? 1) > 1) { ?>
	<nav class="catalog-pagination" aria-label="Phân trang sản phẩm">
		<?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++) { $pageQuery = $_GET; $pageQuery['page'] = $pageNumber; ?>
		<a href="<?php echo htmlspecialchars($baseUrl.'/products?'.http_build_query($pageQuery), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $pageNumber === $currentPage ? 'aria-current="page"' : ''; ?>><?php echo $pageNumber; ?></a>
		<?php } ?>
	</nav>
	<?php } ?>
</section>
