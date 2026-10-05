<?php

$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "TTComputer";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$sql = "
    SELECT
        sp.MaSP,
        sp.TenSP,
        sp.GiaBan,
        sp.SoLuongTon,
        ctsp.HinhAnh
    FROM SanPham sp
    INNER JOIN ChiTietSanPham ctsp
        ON sp.MaSP = ctsp.MaSP
    ORDER BY sp.MaSP ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Lỗi SQL: " . $conn->error);
}


/*
|--------------------------------------------------------------------------
| CẤU HÌNH ĐƯỜNG DẪN ẢNH
|--------------------------------------------------------------------------
*/

// Tên project trong htdocs
$projectName = "DoAn1CongThanhDucThinh";

// URL gốc của project
$baseUrl = "/" . $projectName . "/public";

// Đường dẫn vật lý tới thư mục public
$basePath = $_SERVER["DOCUMENT_ROOT"]
          . DIRECTORY_SEPARATOR
          . $projectName
          . DIRECTORY_SEPARATOR
          . "public";


$totalProducts = 0;
$successImages = 0;
$missingImages = 0;

$products = [];

while ($row = $result->fetch_assoc()) {

    $totalProducts++;

    /*
     * Database:
     * \images\products\SP001.jpg
     *
     * Chuyển "\" thành "/"
     */
    $dbImagePath = str_replace("\\", "/", $row["HinhAnh"]);

    /*
     * Bỏ dấu "/" ở đầu để tránh ghép thành //images
     */
    $dbImagePath = ltrim($dbImagePath, "/");

    /*
     * Đường dẫn vật lý:
     *
     * C:\xampp\htdocs\
     * DoAn1CongThanhDucThinh\
     * public\
     * images\products\SP001.jpg
     */
    $physicalPath = $basePath . DIRECTORY_SEPARATOR
                  . str_replace("/", DIRECTORY_SEPARATOR, $dbImagePath);

    /*
     * URL:
     *
     * /DoAn1CongThanhDucThinh/public/images/products/SP001.jpg
     */
    $imageUrl = $baseUrl . "/" . $dbImagePath;

    $imageExists = file_exists($physicalPath);

    if ($imageExists) {
        $successImages++;
    } else {
        $missingImages++;
    }

    $products[] = [
        "MaSP"       => $row["MaSP"],
        "TenSP"      => $row["TenSP"],
        "GiaBan"     => $row["GiaBan"],
        "SoLuongTon" => $row["SoLuongTon"],
        "HinhAnh"    => $row["HinhAnh"],
        "imageUrl"   => $imageUrl,
        "imageExists" => $imageExists
    ];
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Test 120 sản phẩm - T&T Computer</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        h1 {
            margin-bottom: 10px;
        }

        .summary {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }

        .summary-box {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .products {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
        }

        .product {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .product-image {
            width: 100%;
            height: 180px;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .no-image {
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eee;
            color: #888;
            border-radius: 8px;
        }

        .product-code {
            font-weight: bold;
            color: #555;
        }

        .product-name {
            min-height: 45px;
            margin: 8px 0;
        }

        .price {
            color: #e65100;
            font-weight: bold;
        }

        .status {
            margin-top: 10px;
            font-size: 13px;
        }

        .ok {
            color: green;
        }

        .error {
            color: red;
        }

        .path {
            font-size: 11px;
            color: #777;
            word-break: break-all;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<h1>Test 120 sản phẩm - T&T Computer</h1>

<div class="summary">

    <div class="summary-box">
        <strong>Tổng sản phẩm:</strong>
        <?= $totalProducts ?>
    </div>

    <div class="summary-box">
        <strong>Ảnh tồn tại:</strong>
        <?= $successImages ?>
    </div>

    <div class="summary-box">
        <strong>Ảnh bị thiếu:</strong>
        <?= $missingImages ?>
    </div>

</div>


<div class="products">

<?php foreach ($products as $product): ?>

    <div class="product">

        <?php if ($product["imageExists"]): ?>

            <img
                class="product-image"
                src="<?= htmlspecialchars($product["imageUrl"]) ?>"
                alt="<?= htmlspecialchars($product["TenSP"]) ?>"
            >

        <?php else: ?>

            <div class="no-image">
                Không tìm thấy ảnh
            </div>

        <?php endif; ?>


        <div class="product-code">
            <?= htmlspecialchars($product["MaSP"]) ?>
        </div>

        <div class="product-name">
            <?= htmlspecialchars($product["TenSP"]) ?>
        </div>

        <div class="price">
            <?= number_format($product["GiaBan"], 0, ",", ".") ?> đ
        </div>

        <div>
            Số lượng: <?= $product["SoLuongTon"] ?>
        </div>

        <div class="status <?= $product["imageExists"] ? "ok" : "error" ?>">

            <?php if ($product["imageExists"]): ?>

                ✓ Ảnh tồn tại

            <?php else: ?>

                ✗ Không tìm thấy ảnh

            <?php endif; ?>

        </div>

        <div class="path">
            DB: <?= htmlspecialchars($product["HinhAnh"]) ?>
        </div>

    </div>

<?php endforeach; ?>

</div>

</body>
</html>