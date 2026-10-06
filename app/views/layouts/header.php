<?php

/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/


$pageTitle = $pageTitle ?? 'T&T COMPUTER';

$cartCount = $cartCount ?? 0;

$isLoggedIn = $isLoggedIn ?? false;

$userName = $userName ?? '';

$baseUrl = '/DoAn1CongThanhDucThinh/public';

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="T&T COMPUTER - Máy tính, PC Gaming, Laptop và linh kiện máy tính"
    >

    <meta
        name="theme-color"
        content="#0b0f14"
    >

    <title>
        <?= htmlspecialchars($pageTitle) ?>
    </title>


    <!-- ==================================================
         GOOGLE FONT
    =================================================== -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- ==================================================
         CSS
    =================================================== -->

    <link
        rel="stylesheet"
        href="<?= $baseUrl ?>/css/global.css"
    >

    <link
        rel="stylesheet"
        href="<?= $baseUrl ?>/css/header.css"
    >

    <link
        rel="stylesheet"
        href="<?= $baseUrl ?>/css/footer.css"
    >

    <link
        rel="stylesheet"
        href="<?= $baseUrl ?>/css/home.css"
    >

</head>


<body>


<!-- =====================================================
     HEADER
====================================================== -->

<header class="site-header">


    <!-- =================================================
         TOP BAR
    ================================================== -->

    <div class="top-bar">

        <div class="container top-bar-inner">

            <div class="top-bar-left">

                <span>
                    🚚
                </span>

                <span>
                    Miễn phí vận chuyển đơn từ 500.000đ
                </span>

            </div>


            <div class="top-bar-right">

                <a href="#">
                    Hỗ trợ
                </a>

                <span class="separator">
                    |
                </span>

                <a href="#">
                    Chính sách bảo hành
                </a>

                <span class="separator">
                    |
                </span>

                <span>
                    📞 09xx xxx xxx
                </span>

            </div>

        </div>

    </div>


    <!-- =================================================
         MAIN HEADER
    ================================================== -->

    <div class="main-header">

        <div class="container main-header-inner">


            <!-- MOBILE BUTTON -->

            <button
                type="button"
                class="mobile-menu-button"
                id="mobileMenuButton"
                aria-label="Mở menu"
            >

                ☰

            </button>


            <!-- LOGO -->

            <a
                href="<?= $baseUrl ?>/"
                class="logo"
            >

                <span class="logo-main">
                    T&amp;T
                </span>

                <span class="logo-sub">
                    COMPUTER
                </span>

            </a>


            <!-- SEARCH -->

            <form
                action="<?= $baseUrl ?>/products"
                method="GET"
                class="search-form"
            >

                <input
                    type="search"
                    name="q"
                    placeholder="Tìm kiếm sản phẩm..."
                    autocomplete="off"
                >

                <button
                    type="submit"
                    aria-label="Tìm kiếm"
                >

                    🔍

                </button>

            </form>


            <!-- ACTIONS -->

            <div class="header-actions">


                <!-- ACCOUNT -->

                <a
                    href="<?= $baseUrl ?>/login"
                    class="header-action"
                >

                    <span class="header-action-icon">
                        👤
                    </span>

                    <span class="header-action-content">

                        <small>
                            Tài khoản
                        </small>

                        <strong>

                            <?php if ($isLoggedIn): ?>

                                <?= htmlspecialchars($userName) ?>

                            <?php else: ?>

                                Đăng nhập

                            <?php endif; ?>

                        </strong>

                    </span>

                </a>


                <!-- CART -->

                <a
                    href="<?= $baseUrl ?>/cart"
                    class="header-action"
                >

                    <span class="header-action-icon cart-icon">

                        🛒

                        <?php if ($cartCount > 0): ?>

                            <span class="cart-badge">
                                <?= (int) $cartCount ?>
                            </span>

                        <?php endif; ?>

                    </span>

                    <span class="header-action-content">

                        <small>
                            Giỏ hàng
                        </small>

                        <strong>
                            <?= (int) $cartCount ?> sản phẩm
                        </strong>

                    </span>

                </a>

            </div>

        </div>

    </div>


    <!-- =================================================
         NAVIGATION
    ================================================== -->

    <nav class="navigation">

        <div class="container">

            <ul class="nav-list">


                <!-- HOME -->

                <li>

                    <a
                        href="<?= $baseUrl ?>/"
                        class="nav-link"
                    >
                        TRANG CHỦ
                    </a>

                </li>


                <!-- PRODUCTS -->

                <li class="nav-dropdown">

                    <button
                        type="button"
                        class="nav-dropdown-button"
                        id="productMenuButton"
                    >

                        SẢN PHẨM

                        <span class="dropdown-arrow">
                            ▼
                        </span>

                    </button>


                    <div
                        class="dropdown-menu"
                        id="productDropdown"
                    >


                        <!-- COMPUTER -->

                        <div class="dropdown-column">

                            <h4>
                                MÁY TÍNH
                            </h4>

                            <a href="#">
                                PC Gaming
                            </a>

                            <a href="#">
                                PC Văn phòng
                            </a>

                            <a href="#">
                                Workstation
                            </a>

                            <a href="#">
                                Laptop
                            </a>

                        </div>


                        <!-- COMPONENTS -->

                        <div class="dropdown-column">

                            <h4>
                                LINH KIỆN
                            </h4>

                            <a href="#">
                                CPU
                            </a>

                            <a href="#">
                                Mainboard
                            </a>

                            <a href="#">
                                RAM
                            </a>

                            <a href="#">
                                VGA
                            </a>

                        </div>


                        <!-- STORAGE -->

                        <div class="dropdown-column">

                            <h4>
                                LƯU TRỮ & NGUỒN
                            </h4>

                            <a href="#">
                                SSD
                            </a>

                            <a href="#">
                                HDD
                            </a>

                            <a href="#">
                                PSU
                            </a>

                            <a href="#">
                                Case
                            </a>

                        </div>


                        <!-- ACCESSORIES -->

                        <div class="dropdown-column">

                            <h4>
                                PHỤ KIỆN
                            </h4>

                            <a href="#">
                                Màn hình
                            </a>

                            <a href="#">
                                Bàn phím
                            </a>

                            <a href="#">
                                Chuột
                            </a>

                            <a href="#">
                                Tai nghe
                            </a>

                        </div>

                    </div>

                </li>


                <!-- PC -->

                <li>

                    <a
                        href="#"
                        class="nav-link"
                    >
                        PC GAMING
                    </a>

                </li>


                <!-- LAPTOP -->

                <li>

                    <a
                        href="#"
                        class="nav-link"
                    >
                        LAPTOP
                    </a>

                </li>


                <!-- COMPONENTS -->

                <li>

                    <a
                        href="#"
                        class="nav-link"
                    >
                        LINH KIỆN
                    </a>

                </li>


                <!-- MONITOR -->

                <li>

                    <a
                        href="#"
                        class="nav-link"
                    >
                        MÀN HÌNH
                    </a>

                </li>


                <!-- ACCESSORIES -->

                <li>

                    <a
                        href="#"
                        class="nav-link"
                    >
                        PHỤ KIỆN
                    </a>

                </li>


                <!-- SALE -->

                <li>

                    <a
                        href="#"
                        class="nav-link sale-link"
                    >

                        🔥

                        KHUYẾN MÃI

                    </a>

                </li>

            </ul>

        </div>

    </nav>


    <!-- =================================================
         MOBILE NAVIGATION
    ================================================== -->

    <div
        class="mobile-navigation"
        id="mobileNavigation"
    >

        <div class="mobile-navigation-header">

            <strong>
                MENU
            </strong>

            <button
                type="button"
                id="mobileMenuClose"
                aria-label="Đóng menu"
            >
                ✕
            </button>

        </div>


        <div class="mobile-navigation-content">

            <a href="<?= $baseUrl ?>/">
                Trang chủ
            </a>

            <a href="<?= $baseUrl ?>/products">
                Sản phẩm
            </a>

            <a href="#">
                PC Gaming
            </a>

            <a href="#">
                Laptop
            </a>

            <a href="#">
                Linh kiện
            </a>

            <a href="#">
                Màn hình
            </a>

            <a href="#">
                Phụ kiện
            </a>

            <a href="#">
                🔥 Khuyến mãi
            </a>

        </div>

    </div>


</header>