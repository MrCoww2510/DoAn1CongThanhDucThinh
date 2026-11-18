<?php

// Controller tổng hợp và hiển thị thống kê kinh doanh.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Statistics;

final class StatisticsController extends Controller
{
    // Chuẩn bị dữ liệu và hiển thị trang tương ứng.
    public function index(): void
    {
        AdminController::authorize();
        $from = trim((string) ($_GET['from'] ?? date('Y-m-01')));
        $to = trim((string) ($_GET['to'] ?? date('Y-m-d')));
        $error = '';
        $start = $this->validDate($from);
        $end = $this->validDate($to);
        if ($start === null || $end === null || $start > $end) {
            $error = 'Khoảng ngày không hợp lệ.';
            $from = date('Y-m-01');
            $to = date('Y-m-d');
            $start = $this->validDate($from);
            $end = $this->validDate($to);
        }
        $statistics = (new Statistics())->forPeriod(
            $start->format('Y-m-d 00:00:00'),
            $end->modify('+1 day')->format('Y-m-d 00:00:00')
        );
        $this->view('admin/statistics', [
            'pageTitle' => 'Thống kê', 'from' => $from, 'to' => $to, 'error' => $error,
            'summary' => $statistics['summary'], 'statuses' => $statistics['statuses'],
            'topProducts' => $statistics['topProducts'], 'lowStock' => $statistics['lowStock'],
        ]);
    }

    // Kiểm tra ngày đầu vào có đúng định dạng và là ngày hợp lệ.
    private function validDate(string $date): ?\DateTimeImmutable
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($parsed === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $parsed->format('Y-m-d') !== $date) {
            return null;
        }

        return $parsed;
    }
}
