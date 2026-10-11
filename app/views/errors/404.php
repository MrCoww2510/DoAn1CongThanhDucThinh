<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - T&amp;T Computer</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<main class="container error-page">
    <p class="eyebrow">LỖI 404</p>
    <h1><?= htmlspecialchars($title ?? 'Không tìm thấy trang', ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars($message ?? 'Đường dẫn không tồn tại.', ENT_QUOTES, 'UTF-8') ?></p>
    <a class="primary-link" href="/">Về trang chủ</a>
</main>
</body>
</html>
