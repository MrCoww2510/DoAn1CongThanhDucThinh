
<section class="page-heading">
    <p class="eyebrow">T&T COMPUTER</p>
    <h1>Sản phẩm</h1>
    <p>Tìm kiếm và khám phá các sản phẩm máy tính, linh kiện.</p>
</section>

<form class="product-filter" method="get"
      action="<?= htmlspecialchars($baseUrl . '/products', ENT_QUOTES, 'UTF-8') ?>">

    <input
        type="search"
        name="q"
        placeholder="Nhập tên sản phẩm..."
        value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
    >

    <select name="category">
        <option value="">Tất cả danh mục</option>

        <?php foreach ($categories as $category): ?>
            <option
                value="<?= htmlspecialchars($category['MaDM'], ENT_QUOTES, 'UTF-8') ?>"
                <?= $categoryId === $category['MaDM'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($category['TenDM'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button class="primary-button" type="submit">Tìm kiếm</button>

    <a class="reset-link"
       href="<?= htmlspecialchars($baseUrl . '/products', ENT_QUOTES, 'UTF-8') ?>">
        Xóa bộ lọc
    </a>
</form>

<p class="result-count">
    Tìm thấy <?= count($products) ?> sản phẩm
</p>

<?php if ($products === []): ?>
    <div class="empty-state">
        <h2>Chưa tìm thấy sản phẩm</h2>
        <p>Hãy thử từ khóa khác hoặc chọn danh mục khác.</p>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <?php require __DIR__ . '/_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>