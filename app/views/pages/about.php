<?php
declare(strict_types=1);

$appConfig = require BASE_PATH.'/config/app.php';
$baseUrl = rtrim((string) ($appConfig['base_url'] ?? ''), '/');

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<link
    rel="stylesheet"
    href="<?php echo $escape($baseUrl); ?>/css/about.css"
>

<main class="about-page">

    <!-- Hero -->
    <section class="about-hero">
        <div class="about-hero-content">
            <span class="about-eyebrow">WELCOME TO T&T COMPUTER</span>

            <h1>
                Đồng hành cùng bạn<br>
                <span>trong thế giới công nghệ</span>
            </h1>

            <p>
                T&T Computer hướng đến việc mang đến trải nghiệm mua sắm
                thiết bị công nghệ thuận tiện, thông tin rõ ràng và phù hợp
                với nhu cầu của từng khách hàng.
            </p>

            <div class="about-hero-actions">
                <a
                    class="about-btn about-btn-primary"
                    href="<?php echo $escape($baseUrl); ?>/products"
                >
                    Khám phá sản phẩm
                    <span aria-hidden="true">→</span>
                </a>

                <a
                    class="about-btn about-btn-outline"
                    href="<?php echo $escape($baseUrl); ?>/"
                >
                    Về trang chủ
                </a>
            </div>
        </div>

        <div class="about-hero-visual" aria-hidden="true">
            <div class="about-tech-orbit orbit-one"></div>
            <div class="about-tech-orbit orbit-two"></div>

            <div class="about-tech-card">
                <div class="about-tech-icon">T&T</div>
                <span class="about-tech-label">COMPUTER</span>
                <h2>Technology<br>for everyone.</h2>
                <div class="about-tech-line"></div>
                <p>Build your digital world.</p>
            </div>

            <div class="about-floating-chip chip-one">CPU</div>
            <div class="about-floating-chip chip-two">GPU</div>
            <div class="about-floating-chip chip-three">SSD</div>
        </div>
    </section>

    <!-- Giới thiệu -->
    <section class="about-intro">
        <div class="about-section-label">
            <span>01</span>
            <span>VỀ CHÚNG TÔI</span>
        </div>

        <div class="about-intro-grid">
            <div class="about-intro-heading">
                <h2>
                    Công nghệ tốt hơn<br>
                    bắt đầu từ lựa chọn<br>
                    <span>phù hợp.</span>
                </h2>
            </div>

            <div class="about-intro-text">
                <p>
                    T&T Computer là website bán hàng hướng đến các sản phẩm
                    máy tính và thiết bị công nghệ. Website được xây dựng
                    nhằm giúp khách hàng tìm kiếm, tham khảo thông tin và
                    lựa chọn sản phẩm thuận tiện hơn.
                </p>

                <p>
                    Chúng tôi hướng đến trải nghiệm mua sắm dễ sử dụng,
                    thông tin sản phẩm rõ ràng và quy trình thao tác
                    trực quan, từ tìm kiếm sản phẩm đến quản lý giỏ hàng.
                </p>

                <p>
                    Mục tiêu của T&T Computer là không ngừng hoàn thiện
                    trải nghiệm trên website để đáp ứng tốt hơn nhu cầu
                    tìm hiểu và mua sắm thiết bị công nghệ.
                </p>
            </div>
        </div>
    </section>

    <!-- Giá trị hướng đến -->
    <section class="about-values">
        <div class="about-section-label">
            <span>02</span>
            <span>GIÁ TRỊ HƯỚNG ĐẾN</span>
        </div>

        <div class="about-section-heading">
            <h2>Trải nghiệm mua sắm<br>đơn giản và thuận tiện</h2>
            <p>
                Tập trung vào những yếu tố giúp khách hàng dễ dàng
                tiếp cận thông tin và lựa chọn sản phẩm.
            </p>
        </div>

        <div class="about-value-grid">

            <article class="about-value-card">
                <div class="about-value-number">01</div>
                <div class="about-value-icon" aria-hidden="true">⌕</div>
                <h3>Dễ dàng tìm kiếm</h3>
                <p>
                    Hỗ trợ khách hàng tiếp cận sản phẩm thông qua
                    danh mục và các chức năng tìm kiếm trên website.
                </p>
            </article>

            <article class="about-value-card">
                <div class="about-value-number">02</div>
                <div class="about-value-icon" aria-hidden="true">▤</div>
                <h3>Thông tin rõ ràng</h3>
                <p>
                    Trình bày thông tin sản phẩm để khách hàng thuận tiện
                    tham khảo trước khi đưa ra lựa chọn.
                </p>
            </article>

            <article class="about-value-card">
                <div class="about-value-number">03</div>
                <div class="about-value-icon" aria-hidden="true">◇</div>
                <h3>Thao tác thuận tiện</h3>
                <p>
                    Hướng đến giao diện trực quan, giúp khách hàng
                    quản lý sản phẩm đã chọn trong giỏ hàng.
                </p>
            </article>

        </div>
    </section>

    <!-- Danh mục sản phẩm -->
    <section class="about-categories">
        <div class="about-section-label">
            <span>03</span>
            <span>KHÁM PHÁ SẢN PHẨM</span>
        </div>

        <div class="about-categories-content">
            <div>
                <h2>
                    Tìm thiết bị<br>
                    phù hợp với bạn.
                </h2>

                <p>
                    Khám phá các danh mục sản phẩm công nghệ trên
                    T&T Computer và tìm lựa chọn phù hợp với nhu cầu
                    học tập, làm việc hoặc giải trí.
                </p>
            </div>

            <a
                class="about-btn about-btn-primary"
                href="<?php echo $escape($baseUrl); ?>/products"
            >
                Xem tất cả sản phẩm
                <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="about-category-list">
            <a
                class="about-category-item"
                href="<?php echo $escape($baseUrl); ?>/products"
            >
                <span class="about-category-index">01</span>
                <span class="about-category-name">Linh kiện máy tính</span>
                <span class="about-category-arrow">↗</span>
            </a>

            <a
                class="about-category-item"
                href="<?php echo $escape($baseUrl); ?>/products"
            >
                <span class="about-category-index">02</span>
                <span class="about-category-name">Chuột và bàn phím</span>
                <span class="about-category-arrow">↗</span>
            </a>

            <a
                class="about-category-item"
                href="<?php echo $escape($baseUrl); ?>/products"
            >
                <span class="about-category-index">03</span>
                <span class="about-category-name">Tai nghe và phụ kiện</span>
                <span class="about-category-arrow">↗</span>
            </a>

            <a
                class="about-category-item"
                href="<?php echo $escape($baseUrl); ?>/products"
            >
                <span class="about-category-index">04</span>
                <span class="about-category-name">Thiết bị máy tính</span>
                <span class="about-category-arrow">↗</span>
            </a>
        </div>
    </section>

    <!-- CTA -->
    <section class="about-cta">
        <div class="about-cta-content">
            <span class="about-eyebrow">YOUR NEXT TECH JOURNEY</span>

            <h2>
                Sẵn sàng khám phá<br>
                thế giới công nghệ?
            </h2>

            <p>
                Bắt đầu tìm kiếm sản phẩm phù hợp với nhu cầu của bạn
                tại T&T Computer.
            </p>

            <a
                class="about-btn about-btn-light"
                href="<?php echo $escape($baseUrl); ?>/products"
            >
                Bắt đầu mua sắm
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>

</main>
