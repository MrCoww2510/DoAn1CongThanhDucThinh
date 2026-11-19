<?php
// Layout chung cho các trang phía khách hàng.

$appConfig = require BASE_PATH.'/config/app.php';
$baseUrl = rtrim($appConfig['base_url'] ?? '', '/');
$currentRole = (string) ($_SESSION['user']['VaiTro'] ?? '');
$roleLayoutClass = match ($currentRole) {
    'QuanTriVien' => 'app-admin',
    'NhanVien' => 'app-staff',
    default => 'app-customer',
};
?>
<!doctype html>
<html lang="vi">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars(($pageTitle ?? 'T&T Computer').' | T&T Computer', ENT_QUOTES, 'UTF-8'); ?>
	</title>
	<?php
    $stylesheets = [
        'base.css',
        'header.css',
        'components.css',
    ];
foreach (($pageCss ?? []) as $stylesheet) {
    $stylesheets[] = $stylesheet;
}
foreach (array_unique($stylesheets) as $stylesheet) {
    ?>
	<link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl.'/css/'.$stylesheet, ENT_QUOTES, 'UTF-8'); ?>">
	<?php } ?>
	<script src="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/js/nav.js">
	</script>
</head>

<body class="<?php echo htmlspecialchars($roleLayoutClass, ENT_QUOTES, 'UTF-8'); ?>">


	<header class="site-header">
		<div class="container header-inner">

			<!-- Logo -->
			<a class="brand" href="<?php echo htmlspecialchars(
			    $baseUrl.match ($currentRole) {
			        'QuanTriVien' => '/admin',
			        'NhanVien' => '/admin/orders',
			        default => '/',
			    },
			    ENT_QUOTES,
			    'UTF-8'
			); ?>">
				T&amp;T <span>COMPUTER</span>
			</a>



			<!-- Mô tả cửa hàng -->
			<p class="header-note">
				Máy tính &amp; linh kiện máy tính
			</p>

			<!-- Thanh tìm kiếm sản phẩm -->
			<form class="header-search" method="get"
				action="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>">
				<input type="search" name="q" placeholder="Tìm sản phẩm..." aria-label="Tìm kiếm sản phẩm" value="<?php
			        echo htmlspecialchars(
			            (string) ($_GET['q'] ?? ''),
			            ENT_QUOTES,
			            'UTF-8'
			        );
?>">

				<button type="submit" aria-label="Tìm kiếm">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
						stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
						aria-hidden="true">
						<circle cx="11" cy="11" r="8"></circle>
						<path d="m21 21-4.35-4.35"></path>
					</svg>
				</button>
			</form>
			<div class="header-actions">
				<button type="button" class="mobile-menu-toggle" aria-label="Mở menu" aria-controls="mainMenu"
					aria-expanded="false">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
						<path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round"></path>
					</svg>
				</button>
				<!-- Tài khoản -->
				<div class="header-account">
					<?php if (isset($_SESSION['user'])) { ?>
					<?php
                $user = $_SESSION['user'];
					    $canOpenAccount = in_array($user['VaiTro'] ?? '', ['KhachHang', 'NhanVien', 'QuanTriVien'], true);
					    $googlePicture = filter_var(
					        (string) ($user['GooglePicture'] ?? ''),
					        FILTER_VALIDATE_URL
					    ) ?: '';
					    $accountIcon = $googlePicture !== ''
					        ? '<img class="header-account-avatar" src="'.htmlspecialchars($googlePicture, ENT_QUOTES, 'UTF-8').'" alt="" aria-hidden="true">'
					        : '<svg class="header-account-user-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"></circle><path d="M5 21a7 7 0 0 1 14 0"></path></svg>';
					    ?>
					<?php if ($canOpenAccount) { ?>
					<a class="header-account-profile-link header-account-icon-link" aria-label="Tài khoản"
						title="Tài khoản"
						href="<?php echo htmlspecialchars($baseUrl.'/account', ENT_QUOTES, 'UTF-8'); ?>"
						<?php echo $requestPath === '/account' ? ' aria-current="page"' : ''; ?>>
						<?php echo $accountIcon; ?>
					</a>
					<?php } else { ?>
					<span class="header-account-icon" aria-label="Tài khoản" title="Tài khoản">
						<?php echo $accountIcon; ?>
					</span>
					<?php } ?>
					<?php if (($_SESSION['user']['VaiTro'] ?? '') !== 'KhachHang') { ?>
					<form method="post"
						action="<?php echo htmlspecialchars($baseUrl.'/logout', ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="_csrf"
							value="<?php echo htmlspecialchars($_SESSION['_csrf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
					</form>
					<?php } ?>
					<?php } else { ?>
					<a href="<?php echo htmlspecialchars($baseUrl.'/login', ENT_QUOTES, 'UTF-8'); ?>"
						<?php echo $requestPath === '/login' ? ' aria-current="page"' : ''; ?>>
						Đăng nhập
					</a>
					<a href="<?php echo htmlspecialchars($baseUrl.'/register', ENT_QUOTES, 'UTF-8'); ?>"
						<?php echo $requestPath === '/register' ? ' aria-current="page"' : ''; ?>>
						Đăng ký
					</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</header>



	<nav class="main-nav" aria-label="Điều hướng chính">
		<button class="mobile-menu-backdrop" type="button" aria-label="Đóng menu"></button>
		<div class="container nav-container">
			<div class="mobile-menu-heading">
				<span>Danh mục</span>
				<button type="button" class="mobile-menu-close" aria-label="Đóng menu">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
						<path d="m6 6 12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round"></path>
					</svg>
				</button>
			</div>

			<!-- Danh sách menu -->
			<div class="nav-inner" id="mainMenu">

				<?php if (!in_array($_SESSION['user']['VaiTro'] ?? '', ['NhanVien', 'QuanTriVien'], false)) { ?>
				<a href="<?php echo htmlspecialchars($baseUrl.'/', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/' ? ' aria-current="page"' : ''; ?>>Trang chủ</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/about', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/about' ? ' aria-current="page"' : ''; ?>>Giới thiệu</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/products' ? ' aria-current="page"' : ''; ?>>Sản phẩm</a>
				<?php } ?>

				<?php if (!in_array($_SESSION['user']['VaiTro'] ?? '', ['NhanVien', 'QuanTriVien'], true)) { ?>
				<a href="<?php echo htmlspecialchars($baseUrl.'/cart', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/cart' ? ' aria-current="page"' : ''; ?>>Giỏ hàng</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/orders', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo str_starts_with($requestPath, '/orders') ? ' aria-current="page"' : ''; ?>>Theo dõi đơn
					hàng</a>
				<?php } ?>

				<?php if (($_SESSION['user']['VaiTro'] ?? '') === 'NhanVien') { ?>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/orders', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo str_starts_with($requestPath, '/admin/orders') ? ' aria-current="page"' : ''; ?>>Quản lý
					đơn
					hàng</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/inventory', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/inventory' ? ' aria-current="page"' : ''; ?>>Kiểm tra tồn
					kho</a>
				<?php } ?>

				<?php if (($_SESSION['user']['VaiTro'] ?? '') === 'QuanTriVien') { ?>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin' ? ' aria-current="page"' : ''; ?>>Tổng quan quản trị</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/products', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/products' ? ' aria-current="page"' : ''; ?>>Quản lý sản
					phẩm</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/categories', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/categories' ? ' aria-current="page"' : ''; ?>>Quản lý danh
					mục</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/orders', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo str_starts_with($requestPath, '/admin/orders') ? ' aria-current="page"' : ''; ?>>Quản lý
					đơn hàng</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/inventory', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/inventory' ? ' aria-current="page"' : ''; ?>>Quản lý kho</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/statistics', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/statistics' ? ' aria-current="page"' : ''; ?>>Thống kê</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/promotions', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/promotions' ? ' aria-current="page"' : ''; ?>>Chương trình giảm
					giá</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/vouchers', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/vouchers' ? ' aria-current="page"' : ''; ?>>Quản lý voucher</a>
				<a href="<?php echo htmlspecialchars($baseUrl.'/admin/users', ENT_QUOTES, 'UTF-8'); ?>"
					<?php echo $requestPath === '/admin/users' ? ' aria-current="page"' : ''; ?>>Quản lý người dùng</a>
				<?php } ?>
			</div>
		</div>
	</nav>

	<main class="container main-content">
		<?php echo $content ?? ''; ?>
	</main>

	<?php if (!in_array($_SESSION['user']['VaiTro'] ?? '', ['NhanVien', 'QuanTriVien'], false)) { ?>
	<footer class="site-footer">
		<div class="container footer-top">
			<!-- Cột 1: Thông tin thương hiệu -->
			<div class="footer-col">
				<h3 class="footer-brand">T&amp;T <span>Computer</span></h3>
				<p class="muted">Chuyên cung cấp các linh kiện máy tính, laptop và thiết bị văn phòng chính hãng với giá
					cả cạnh tranh nhất. Cam kết chất lượng và dịch vụ tận tâm.</p>
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
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/', ENT_QUOTES, 'UTF-8'); ?>"
							<?php echo $requestPath === '/' ? ' aria-current="page"' : ''; ?>>Trang chủ</a></li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/about', ENT_QUOTES, 'UTF-8'); ?>"
							<?php echo $requestPath === '/about' ? ' aria-current="page"' : ''; ?>>Giới thiệu</a>
					</li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/products', ENT_QUOTES, 'UTF-8'); ?>"
							<?php echo $requestPath === '/products' ? ' aria-current="page"' : ''; ?>>Sản phẩm</a></li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/promotions', ENT_QUOTES, 'UTF-8'); ?>">Khuyến
							mãi</a></li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/tech-news', ENT_QUOTES, 'UTF-8'); ?>">Tin tức
							công nghệ</a></li>
				</ul>
			</div>

			<!-- Cột 3: Chính sách hỗ trợ -->
			<div class="footer-col">
				<h4>Hỗ trợ khách hàng</h4>
				<ul class="footer-list">
					<li><a
							href="<?php echo htmlspecialchars($baseUrl.'/support/shopping-guide', ENT_QUOTES, 'UTF-8'); ?>">Hướng
							dẫn mua hàng</a></li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/support/warranty', ENT_QUOTES, 'UTF-8'); ?>">Chính
							sách bảo hành</a></li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/support/returns', ENT_QUOTES, 'UTF-8'); ?>">Chính
							sách đổi trả</a></li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/support/shipping', ENT_QUOTES, 'UTF-8'); ?>">Chính
							sách vận chuyển</a></li>
					<li><a href="<?php echo htmlspecialchars($baseUrl.'/support/faq', ENT_QUOTES, 'UTF-8'); ?>">Câu hỏi
							thường gặp (FAQ)</a></li>
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
				<p>&copy; <?php echo date('Y'); ?> T&amp;T Computer. Đồ án thương mại điện tử - Nội dung và chức năng
					đang được triển khai.</p>
			</div>
		</div>
	</footer>
	<?php } ?>
	<button onclick="scrollToTop()" id="backToTop">↑</button>
	<script>
	let backToTop = document.getElementById("backToTop");

	// Chỉ hiện nút quay lên đầu trang sau khi người dùng cuộn đủ xa.
	window.onscroll = function() {
		if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
			backToTop.style.display = "block";
		} else {
			backToTop.style.display = "none";
		}
	};

	// Đưa trang về đầu khi người dùng kích hoạt nút lên đầu trang.
	function scrollToTop() {
		window.scrollTo({
			top: 0,
			behavior: "smooth"
		});
	}
	</script>
</body>

</html>
