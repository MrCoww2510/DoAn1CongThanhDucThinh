<?php
$appConfig = require BASE_PATH . '/config/app.php';
$baseUrl = rtrim($appConfig['base_url'] ?? '', '/');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - T&amp;T Computer</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/css/errors404.css">
</head>
<body>
<main class="container error-page">
    <p class="eyebrow">LỖI 404</p>
    <h1><?= htmlspecialchars($title ?? 'Không tìm thấy trang', ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars($message ?? 'Đường dẫn không tồn tại.', ENT_QUOTES, 'UTF-8') ?></p>
    <a class="primary-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>">Về trang chủ</a>
</main>
</body>
</html>
