
<section class="hero">
    <p class="eyebrow">T&amp;T COMPUTER</p>
    <h1>Nâng cấp góc máy, nâng tầm trải nghiệm</h1>
    <p class="hero-description">
        Khám phá các sản phẩm máy tính và linh kiện phù hợp với nhu cầu của bạn.
    </p>

    <a class="primary-link"
       href="<?= htmlspecialchars($baseUrl . '/products', ENT_QUOTES, 'UTF-8') ?>">
        Khám phá sản phẩm
    </a>
</section>

<section class="section-block">
    <div class="section-heading">
        <div>
            <p class="eyebrow">GỢI Ý CHO BẠN</p>
            <h2>Sản phẩm nổi bật</h2>
        </div>

        <a href="<?= htmlspecialchars($baseUrl . '/products', ENT_QUOTES, 'UTF-8') ?>">
            Xem tất cả
        </a>
    </div>

    <?php if ($products === []): ?>
        <div class="empty-state">
            <p>Chưa có sản phẩm để hiển thị. Hãy kiểm tra dữ liệu trong database.</p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <?php require BASE_PATH . '/app/Views/products/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>