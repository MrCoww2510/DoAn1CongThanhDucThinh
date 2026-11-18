<?php

// Controller hiển thị trang giới thiệu cửa hàng.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class AboutController extends Controller
{
    // Hiển thị trang giới thiệu cửa hàng.
    public function index(): void
    {
        $this->view('pages/about', ['pageTitle' => 'Giới thiệu T&T Computer']);
    }
}
