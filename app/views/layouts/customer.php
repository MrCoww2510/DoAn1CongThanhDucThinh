<?php
$appConfig = require BASE_PATH.'/config/app.php';
$baseUrl = rtrim($appConfig['base_url'] ?? '', '/');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars(($pageTitle ?? 'T&T Computer').' | T&T Computer', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/css/base.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/css/auth.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/css/header.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/css/products.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/css/about.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/css/users.css">
    <script src="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/js/nav.js"></script>
</head>
<body>


<header class="site-header">
    <div class="container header-inner">

        <!-- Logo -->
        <a
            class="brand"
            href="<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/"
        >
            T&amp;T <span>COMPUTER</span>
        </a>

        <!-- Mô tả cửa hàng -->
        <p class="header-note">
            Máy tính &amp; linh kiện máy tính
        </p>

        <!-- Thanh tìm kiếm sản phẩm -->
        <form
            class="header-search"
            method="get"
            action="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>"
        >
            <input
                type="search"
                name="q"
                placeholder="Tìm kiếm sản phẩm..."
                aria-label="Tìm kiếm sản phẩm"
                value="<?php
                    echo htmlspecialchars(
                        (string) ($_GET['q'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    );
?>"
            >

            <button type="submit" aria-label="Tìm kiếm">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </button>
        </form>
        <!-- Tài khoản -->
        <div class="header-account">
            <?php if (isset($_SESSION['user'])) { ?>
                <?php
                $greetingName = htmlspecialchars(
                    (string) (
                        $_SESSION['user']['HoTen']
                        ?? $_SESSION['user']['TenDangNhap']
                        ?? 'Khách hàng'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
                <?php if (($_SESSION['user']['VaiTro'] ?? '') === 'KhachHang') { ?>
                    <a
                        class="header-account-profile-link"
                        href="<?php echo htmlspecialchars($baseUrl.'/account', ENT_QUOTES, 'UTF-8'); ?>"
                    >
                        Xin chào, <?php echo $greetingName; ?>
                    </a>
                <?php } else { ?>
                    <span class="header-greeting">Xin chào, <?php echo $greetingName; ?></span>
                <?php } ?>
                <form
                    method="post"
                    action="<?php echo htmlspecialchars($baseUrl.'/logout', ENT_QUOTES, 'UTF-8'); ?>"
                >
                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?php echo htmlspecialchars($_SESSION['_csrf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >
                    <button class="header-account-button" type="submit">Đăng xuất</button>
                </form>
            <?php } else { ?>
                <a href="<?php echo htmlspecialchars($baseUrl.'/login', ENT_QUOTES, 'UTF-8'); ?>">
                    Đăng nhập
                </a>
                <a href="<?php echo htmlspecialchars($baseUrl.'/register', ENT_QUOTES, 'UTF-8'); ?>">
                    Đăng ký
                </a>
            <?php } ?>
        </div>
    </div>
</header>



    <nav class="main-nav" aria-label="Điều hướng chính">
        <div class="container nav-container">
            <!-- Nút Hamburger (Chỉ hiện trên điện thoại) -->
            <button type="button" class="mobile-menu-toggle" aria-label="Mở menu">
                &#9776; <!-- Mã HTML của biểu tượng ☰ -->
            </button>

            <!-- Danh sách menu -->
            <div class="nav-inner" id="mainMenu">
                <a href="<?php echo htmlspecialchars($baseUrl.'/', ENT_QUOTES, 'UTF-8'); ?>">Trang chủ</a>
                <a href="<?php echo htmlspecialchars($baseUrl.'/about', ENT_QUOTES, 'UTF-8'); ?>">Giới thiệu</a>
                <a href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>">Sản phẩm</a>
                <?php if (($_SESSION['user']['VaiTro'] ?? '') !== 'QuanTriVien') { ?>
                    <a href="<?php echo htmlspecialchars($baseUrl.'/cart', ENT_QUOTES, 'UTF-8'); ?>">Giỏ hàng</a>
                    <a href="<?php echo htmlspecialchars($baseUrl.'/orders', ENT_QUOTES, 'UTF-8'); ?>">Theo dõi đơn hàng</a>
                <?php } ?>
                <?php if (($_SESSION['user']['VaiTro'] ?? '') === 'QuanTriVien') { ?>
                    <a href="<?php echo htmlspecialchars($baseUrl.'/admin/users', ENT_QUOTES, 'UTF-8'); ?>">Quản lý người dùng</a>
                <?php } ?>
            </div>
        </div>
    </nav>

    <main class="container main-content">
        <?php echo $content; ?>
    </main>

    <footer class="site-footer">
        <div class="container footer-top">
            <!-- Cột 1: Thông tin thương hiệu -->
            <div class="footer-col">
                <h3 class="footer-brand">T&amp;T <span>Computer</span></h3>
                <p class="muted">Chuyên cung cấp các linh kiện máy tính, laptop và thiết bị văn phòng chính hãng với giá cả cạnh tranh nhất. Cam kết chất lượng và dịch vụ tận tâm.</p>
                <ul class="footer-list">
                    <li>📍 123 Đường 3/2, Ninh Kiều, Cần Thơ</li>
                    <li>📞 0123 456 789</li>
                    <li>✉️ support@ttcomputer.com</li>
                </ul>
            </div>

            <!-- Cột 2: Đường dẫn nhanh -->
            <div class="footer-col">
                <h4>Về chúng tôi</h4>
                <ul class="footer-list">
                    <li><a href="<?php echo htmlspecialchars($baseUrl.'/', ENT_QUOTES, 'UTF-8'); ?>">Trang chủ</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl.'/about', ENT_QUOTES, 'UTF-8'); ?>">Giới thiệu</a></li>
                    <li><a href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>">Sản phẩm</a></li>
                    <li><a href="#">Khuyến mãi</a></li>
                    <li><a href="#">Tin tức công nghệ</a></li>
                </ul>
            </div>

            <!-- Cột 3: Chính sách hỗ trợ -->
            <div class="footer-col">
                <h4>Hỗ trợ khách hàng</h4>
                <ul class="footer-list">
                    <li><a href="#">Hướng dẫn mua hàng</a></li>
                    <li><a href="#">Chính sách bảo hành</a></li>
                    <li><a href="#">Chính sách đổi trả</a></li>
                    <li><a href="#">Chính sách vận chuyển</a></li>
                    <li><a href="#">Câu hỏi thường gặp (FAQ)</a></li>
                </ul>
            </div>

            <!-- Cột 4: Kết nối -->
            <div class="footer-col">
                <h4>Kết nối</h4>
                <p class="muted">Theo dõi chúng tôi trên các nền tảng mạng xã hội để cập nhật những ưu đãi mới nhất.</p>
                <ul class="footer-list">
                    <li><a href="https://www.facebook.com/mrcoww05">🌐 Facebook</a></li>
                    <li><a href="https://www.youtube.com/@mrcoww05">📺 YouTube</a></li>
                    <li><a href="https://www.tiktok.com/@mrcoww05">🎵 TikTok</a></li>
                </ul>
            </div>
        </div>

        <!-- Thanh bản quyền dưới cùng -->
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> T&amp;T Computer. Đồ án thương mại điện tử - Nội dung và chức năng đang được triển khai.</p>
            </div>
        </div>
    </footer>
    <button onclick="scrollToTop()" id="backToTop">↑</button>
    <script>
    let backToTop = document.getElementById("backToTop");

    window.onscroll = function() {
        if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
            backToTop.style.display = "block";
        } else {
            backToTop.style.display = "none";
        }
    };

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }
    </script>
</body>
</html>
