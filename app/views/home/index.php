
<?php
$featuredProducts = $featuredProducts ?? [];
$categories = $categories ?? [];
?>

<!-- HERO BANNER -->
<section class="home-hero">
    <div class="home-hero-content">
        <span class="home-eyebrow">
            T&amp;T COMPUTER
        </span>

        <h1>
            Công nghệ tốt.<br>
            <span>Trải nghiệm vượt trội.</span>
        </h1>

        <p>
            Khám phá máy tính, linh kiện và thiết bị công nghệ
            phù hợp với nhu cầu học tập, làm việc và giải trí của bạn.
        </p>

        <div class="home-hero-actions">
            <a
                class="home-btn home-btn-primary"
                href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>"
            >
                Khám phá sản phẩm
                <span aria-hidden="true">→</span>
            </a>

            <a
                class="home-btn home-btn-secondary"
                href="<?php echo htmlspecialchars($baseUrl.'/about', ENT_QUOTES, 'UTF-8'); ?>"
            >
                Về T&amp;T Computer
            </a>
        </div>

        <div class="home-trust">
            <div class="home-trust-item">
                <span class="home-trust-icon">✓</span>
                <span>Sản phẩm đa dạng</span>
            </div>

            <div class="home-trust-item">
                <span class="home-trust-icon">✓</span>
                <span>Thông tin rõ ràng</span>
            </div>

            <div class="home-trust-item">
                <span class="home-trust-icon">✓</span>
                <span>Mua sắm tiện lợi</span>
            </div>
        </div>
    </div>

    <div class="home-hero-visual" aria-hidden="true">
        <div class="hero-orbit hero-orbit-one"></div>
        <div class="hero-orbit hero-orbit-two"></div>

        <div class="hero-tech-card">
            <div class="hero-tech-top">
                <span class="hero-tech-dot"></span>
                <span class="hero-tech-dot"></span>
                <span class="hero-tech-dot"></span>
                <span class="hero-tech-label">TECH / PERFORMANCE</span>
            </div>

            <div class="hero-chip">
                <svg viewBox="0 0 120 120" fill="none">
                    <rect x="22" y="22" width="76" height="76"
                          rx="10" stroke="currentColor" stroke-width="3"/>
                    <rect x="36" y="36" width="48" height="48"
                          rx="5" stroke="currentColor" stroke-width="3"/>
                    <path d="M45 10V22M60 10V22M75 10V22
                             M45 98V110M60 98V110M75 98V110
                             M10 45H22M10 60H22M10 75H22
                             M98 45H110M98 60H110M98 75H110"
                          stroke="currentColor" stroke-width="3"
                          stroke-linecap="round"/>
                    <path d="M48 49H72V71H48Z"
                          fill="currentColor" opacity=".15"/>
                </svg>
            </div>

            <div class="hero-tech-caption">
                <span>BUILD YOUR SETUP</span>
                <strong>Find your next upgrade.</strong>
            </div>

            <div class="hero-tech-bars">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div class="hero-float-tag">
            <span class="hero-float-icon">T&amp;T</span>
            <span>
                <strong>Technology</strong>
                <small>Made for your needs</small>
            </span>
        </div>
    </div>
</section>

<!-- DANH MỤC -->
<section class="home-section home-category-section">
    <div class="home-section-heading">
        <div>
            <span class="home-section-eyebrow">KHÁM PHÁ</span>
            <h2>Danh mục sản phẩm</h2>
            <p>Tìm nhanh thiết bị phù hợp với nhu cầu của bạn.</p>
        </div>

        <a
            class="home-text-link"
            href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>"
        >
            Tất cả sản phẩm <span aria-hidden="true">→</span>
        </a>
    </div>

    <?php if ($categories !== []) { ?>
        <div class="home-category-grid">
            <?php foreach ($categories as $category) { ?>
                <a
                    class="home-category-card"
                    href="<?php
                        echo htmlspecialchars(
                            $baseUrl.'/products?category='.rawurlencode((string) $category['MaDM']),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                ?>"
                >
                    <span class="home-category-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="13"
                                  rx="2" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8 21H16M12 17V21"
                                  stroke="currentColor" stroke-width="1.7"
                                  stroke-linecap="round"/>
                        </svg>
                    </span>

                    <span class="home-category-name">
                        <?php
                    echo htmlspecialchars(
                        (string) $category['TenDM'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>
                    </span>

                    <span class="home-category-arrow" aria-hidden="true">↗</span>
                </a>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="home-empty-message">
            Danh mục sản phẩm hiện chưa có dữ liệu.
        </div>
    <?php } ?>
</section>

<!-- SẢN PHẨM NỔI BẬT -->
<section class="home-section home-featured-section">
    <div class="home-section-heading">
        <div>
            <span class="home-section-eyebrow">LỰA CHỌN DÀNH CHO BẠN</span>
            <h2>Sản phẩm nổi bật</h2>
            <p>Khám phá các sản phẩm đang có trong hệ thống.</p>
        </div>

        <a
            class="home-text-link"
            href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>"
        >
            Xem tất cả <span aria-hidden="true">→</span>
        </a>
    </div>

    <?php if ($featuredProducts !== []) { ?>
        <div class="product-grid home-featured-grid">
            <?php foreach ($featuredProducts as $product) { ?>
                <?php require dirname(__DIR__).'/products/_card.php'; ?>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="home-empty-message">
            Chưa có sản phẩm để hiển thị. Vui lòng quay lại sau.
        </div>
    <?php } ?>
</section>

<!-- BANNER CUỐI TRANG -->
<section class="home-bottom-banner">
    <div>
        <span class="home-section-eyebrow">T&amp;T COMPUTER</span>
        <h2>Sẵn sàng nâng cấp góc công nghệ của bạn?</h2>
        <p>Tìm kiếm sản phẩm phù hợp ngay hôm nay.</p>
    </div>

    <a
        class="home-btn home-btn-light"
        href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>"
    >
        Mua sắm ngay <span aria-hidden="true">→</span>
    </a>
</section>