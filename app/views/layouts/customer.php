<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(($pageTitle ?? 'T&T Computer') . ' | T&T Computer', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="/">T&amp;T <span>COMPUTER</span></a>
            <p class="header-note">Máy tính &amp; linh kiện máy tính</p>
        </div>
    </header>

    <nav class="main-nav" aria-label="Điều hướng chính">
        <div class="container nav-inner">
            <a href="/">Trang chủ</a>
            <a href="/products">Sản phẩm</a>
            <a href="/about">Giới thiệu</a>
            <a href="/warranty">Bảo hành</a>
            <a href="/news">Tin công nghệ</a>
        </div>
    </nav>

    <main class="container main-content">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p><strong>T&amp;T Computer</strong> — Website đồ án thương mại điện tử.</p>
            <p class="muted">Nội dung và chức năng sẽ được triển khai theo tài liệu nghiệp vụ đã thống nhất.</p>
        </div>
    </footer>
</body>
</html>
