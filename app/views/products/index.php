<?php
// Giao diện bộ lọc và danh sách sản phẩm.

$sort = $sort ?? 'default';
?>

<form class="product-filter" method="get" action="<?php echo htmlspecialchars(
        $baseUrl.'/products',
        ENT_QUOTES,
        'UTF-8'
    ); ?>">
	<!-- Tìm kiếm theo tên sản phẩm -->
	<input type="search" name="q" placeholder="Nhập tên sản phẩm..." value="<?php echo htmlspecialchars(
            $keyword,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

	<!-- Lọc theo danh mục -->
	<select name="category">
		<option value="">Tất cả danh mục</option>

		<?php foreach ($categories as $category) { ?>
		<option value="<?php echo htmlspecialchars(
                    (string) $category['MaDM'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>" <?php echo (string) $categoryId === (string) $category['MaDM']
                    ? 'selected'
                    : ''; ?>>
			<?php echo htmlspecialchars(
                    $category['TenDM'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
		</option>
		<?php } ?>
	</select>

	<label>Thương hiệu
		<select name="brand"><option value="">Tất cả thương hiệu</option>
		<?php foreach ($brands as $brandOption) { ?><option value="<?php echo htmlspecialchars((string) $brandOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $brand === $brandOption ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) $brandOption, ENT_QUOTES, 'UTF-8'); ?></option><?php } ?>
		</select>
	</label>
	<label>Loại sản phẩm
		<select name="type"><option value="">Tất cả loại</option>
		<?php foreach ($types as $typeOption) { ?><option value="<?php echo htmlspecialchars((string) $typeOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $type === $typeOption ? 'selected' : ''; ?>><?php echo htmlspecialchars((string) $typeOption, ENT_QUOTES, 'UTF-8'); ?></option><?php } ?>
		</select>
	</label>
	<label>Giá từ <input type="number" name="min_price" min="0" step="1" value="<?php echo htmlspecialchars((string) $minPrice, ENT_QUOTES, 'UTF-8'); ?>"></label>
	<label>Đến <input type="number" name="max_price" min="0" step="1" value="<?php echo htmlspecialchars((string) $maxPrice, ENT_QUOTES, 'UTF-8'); ?>"></label>

	<!-- Sắp xếp sản phẩm -->
	<select name="sort">
		<option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>
			Mặc định
		</option>

		<option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>
			Tên A → Z
		</option>

		<option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>
			Tên Z → A
		</option>

		<option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>
			Giá thấp → cao
		</option>

		<option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>
			Giá cao → thấp
		</option>
	</select>

	<button class="primary-button" type="submit">
		Áp dụng
	</button>

	<!-- Xóa toàn bộ bộ lọc -->
	<a class="reset-link" href="<?php echo htmlspecialchars(
            $baseUrl.'/products',
            ENT_QUOTES,
            'UTF-8'
        ); ?>">
		Xóa bộ lọc
	</a>
</form>

<!-- Số lượng sản phẩm tìm được -->
<p class="result-count">
	Tìm thấy <?php echo (int) ($totalProducts ?? count($products)); ?> sản phẩm
</p>

<?php if ($products === []) { ?>

<div class="empty-state">
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
