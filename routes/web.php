<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| T&T COMPUTER
| Web Routes
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| BASE URL
|--------------------------------------------------------------------------
*/

$baseUrl = '/DoAn1CongThanhDucThinh/public';


/*
|--------------------------------------------------------------------------
| LẤY URL HIỆN TẠI
|--------------------------------------------------------------------------
*/

$requestUri = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);


/*
|--------------------------------------------------------------------------
| LOẠI BỎ BASE URL
|--------------------------------------------------------------------------
*/

if (str_starts_with($requestUri, $baseUrl)) {

    $requestUri = substr(
        $requestUri,
        strlen($baseUrl)
    );

}


/*
|--------------------------------------------------------------------------
| CHUẨN HÓA URL
|--------------------------------------------------------------------------
*/

$requestUri = '/' . trim(
    $requestUri,
    '/'
);


if ($requestUri === '//') {

    $requestUri = '/';

}


/*
|--------------------------------------------------------------------------
| BIẾN DÙNG CHUNG
|--------------------------------------------------------------------------
*/

$cartCount = 0;

$isLoggedIn = false;

$userName = '';


/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/

switch ($requestUri) {


    /*
    |--------------------------------------------------------------------------
    | TRANG CHỦ
    |--------------------------------------------------------------------------
    */

    case '/':

        $pageTitle = 'T&T COMPUTER - Máy tính & Linh kiện';

        require BASE_PATH . '/app/views/home/index.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | SẢN PHẨM
    |--------------------------------------------------------------------------
    */

    case '/products':

        $pageTitle = 'Sản phẩm - T&T COMPUTER';

        require BASE_PATH . '/app/views/home/index.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | GIỎ HÀNG
    |--------------------------------------------------------------------------
    */

    case '/cart':

        $pageTitle = 'Giỏ hàng - T&T COMPUTER';

        require BASE_PATH . '/app/views/home/index.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | ĐĂNG NHẬP
    |--------------------------------------------------------------------------
    */

    case '/login':

        $pageTitle = 'Đăng nhập - T&T COMPUTER';

        require BASE_PATH . '/app/views/home/index.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | 404
    |--------------------------------------------------------------------------
    */

    default:

        http_response_code(404);

        $pageTitle = '404 - Không tìm thấy trang';

        require BASE_PATH . '/app/views/layouts/header.php';

        ?>

        <main class="error-page">

            <div class="container">

                <div class="error-content">

                    <div class="error-number">
                        404
                    </div>

                    <h1>
                        Không tìm thấy trang
                    </h1>

                    <p>
                        Xin lỗi, trang bạn đang tìm kiếm
                        không tồn tại hoặc đã được di chuyển.
                    </p>

                    <a
                        href="<?= $baseUrl ?>/"
                        class="error-button"
                    >
                        Về trang chủ
                    </a>

                </div>

            </div>

        </main>

        <?php

        require BASE_PATH . '/app/views/layouts/footer.php';

        break;

}