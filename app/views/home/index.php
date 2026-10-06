<?php

require_once __DIR__ . '/../layouts/header.php';

$baseUrl = '/DoAn1CongThanhDucThinh/public';

?>


<main class="home-page">


    <!-- =================================================
         HERO
    ================================================== -->

    <section class="hero-section">

        <div class="container hero-container">


            <div class="hero-content">


                <span class="hero-label">

                    T&T COMPUTER

                </span>


                <h1>

                    CÔNG NGHỆ

                    <span>
                        CHO ĐAM MÊ
                    </span>

                </h1>


                <p>

                    PC Gaming, Laptop và linh kiện máy tính
                    chính hãng. Hiệu năng mạnh mẽ,
                    giá tốt cho mọi nhu cầu.

                </p>


                <div class="hero-actions">


                    <a
                        href="<?= $baseUrl ?>/products"
                        class="hero-button primary"
                    >

                        KHÁM PHÁ SẢN PHẨM

                    </a>


                    <a
                        href="#categories"
                        class="hero-button secondary"
                    >

                        XEM DANH MỤC

                    </a>


                </div>


            </div>


            <div class="hero-decoration">

                <div class="hero-circle">

                    <span>
                        T&T
                    </span>

                    <small>
                        COMPUTER
                    </small>

                </div>

            </div>


        </div>

    </section>


    <!-- =================================================
         FEATURES
    ================================================== -->

    <section class="features-section">

        <div class="container features-grid">


            <div class="feature-card">

                <div class="feature-icon">
                    🚚
                </div>

                <div>

                    <strong>
                        Giao hàng nhanh
                    </strong>

                    <span>
                        Toàn quốc
                    </span>

                </div>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🛡️
                </div>

                <div>

                    <strong>
                        Chính hãng
                    </strong>

                    <span>
                        Bảo hành đầy đủ
                    </span>

                </div>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    💰
                </div>

                <div>

                    <strong>
                        Giá cạnh tranh
                    </strong>

                    <span>
                        Nhiều ưu đãi
                    </span>

                </div>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🎧
                </div>

                <div>

                    <strong>
                        Hỗ trợ tận tâm
                    </strong>

                    <span>
                        Tư vấn miễn phí
                    </span>

                </div>

            </div>


        </div>

    </section>


    <!-- =================================================
         CATEGORIES
    ================================================== -->

    <section
        class="categories-section"
        id="categories"
    >

        <div class="container">


            <div class="section-heading">

                <div>

                    <span>
                        DANH MỤC
                    </span>

                    <h2>
                        Khám phá sản phẩm
                    </h2>

                </div>


                <a
                    href="<?= $baseUrl ?>/products"
                    class="view-all"
                >

                    Xem tất cả →

                </a>

            </div>


            <div class="category-grid">


                <!-- PC GAMING -->

                <a
                    href="#"
                    class="category-card"
                >

                    <div class="category-icon">
                        🖥️
                    </div>

                    <div>

                        <h3>
                            PC Gaming
                        </h3>

                        <p>
                            Chiến game cực đỉnh
                        </p>

                    </div>

                </a>


                <!-- LAPTOP -->

                <a
                    href="#"
                    class="category-card"
                >

                    <div class="category-icon">
                        💻
                    </div>

                    <div>

                        <h3>
                            Laptop
                        </h3>

                        <p>
                            Học tập, làm việc, gaming
                        </p>

                    </div>

                </a>


                <!-- CPU -->

                <a
                    href="#"
                    class="category-card"
                >

                    <div class="category-icon">
                        ⚙️
                    </div>

                    <div>

                        <h3>
                            CPU
                        </h3>

                        <p>
                            Hiệu năng xử lý
                        </p>

                    </div>

                </a>


                <!-- VGA -->

                <a
                    href="#"
                    class="category-card"
                >

                    <div class="category-icon">
                        🎮
                    </div>

                    <div>

                        <h3>
                            Card đồ họa
                        </h3>

                        <p>
                            Gaming & sáng tạo
                        </p>

                    </div>

                </a>


                <!-- MONITOR -->

                <a
                    href="#"
                    class="category-card"
                >

                    <div class="category-icon">
                        🖥️
                    </div>

                    <div>

                        <h3>
                            Màn hình
                        </h3>

                        <p>
                            Gaming & văn phòng
                        </p>

                    </div>

                </a>


                <!-- ACCESSORIES -->

                <a
                    href="#"
                    class="category-card"
                >

                    <div class="category-icon">
                        🎧
                    </div>

                    <div>

                        <h3>
                            Phụ kiện
                        </h3>

                        <p>
                            Gaming Gear
                        </p>

                    </div>

                </a>


            </div>

        </div>

    </section>


    <!-- =================================================
         CTA
    ================================================== -->

    <section class="cta-section">

        <div class="container">

            <div class="cta-box">


                <div>

                    <span>
                        T&T COMPUTER
                    </span>

                    <h2>
                        Sẵn sàng nâng cấp setup?
                    </h2>

                    <p>
                        Khám phá hàng trăm sản phẩm
                        máy tính và linh kiện.
                    </p>

                </div>


                <a
                    href="<?= $baseUrl ?>/products"
                    class="hero-button primary"
                >

                    MUA SẮM NGAY

                </a>


            </div>

        </div>

    </section>


</main>


<?php

require_once __DIR__ . '/../layouts/footer.php';

?>