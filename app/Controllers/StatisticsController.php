<?php

// Controller tổng hợp và hiển thị thống kê kinh doanh.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

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
        $pdo = Database::connection();
        $where = ' WHERE dh.NgayTao >= :start AND dh.NgayTao < :end';
        $params = ['start' => $start->format('Y-m-d 00:00:00'), 'end' => $end->modify('+1 day')->format('Y-m-d 00:00:00')];
        $stmt = $pdo->prepare('SELECT COUNT(*) AS total_orders, COALESCE(SUM(CASE WHEN TrangThaiDH = \'Hoàn thành\' THEN 1 ELSE 0 END), 0) AS completed_orders, COALESCE(SUM(CASE WHEN TrangThaiDH = \'Đã hủy\' THEN 1 ELSE 0 END), 0) AS cancelled_orders, COALESCE(SUM(CASE WHEN TrangThaiDH = \'Hoàn thành\' THEN TongTien ELSE 0 END), 0) AS revenue FROM DonHang dh'.$where);
        $stmt->execute($params);
        $summary = $stmt->fetch();
        $stmt = $pdo->prepare('SELECT TrangThaiDH, COUNT(*) AS SoDon FROM DonHang dh'.$where.' GROUP BY TrangThaiDH ORDER BY SoDon DESC');
        $stmt->execute($params);
        $statuses = $stmt->fetchAll();
        $stmt = $pdo->prepare('SELECT sp.MaSP, sp.TenSP, SUM(ct.SoLuong) AS SoLuongBan, SUM(ct.SoLuong * ct.DonGia) AS DoanhThu FROM DonHang dh JOIN ChiTietDonHang ct ON ct.MaDH = dh.MaDH JOIN SanPham sp ON sp.MaSP = ct.MaSP'.$where.' AND dh.TrangThaiDH = \'Hoàn thành\' GROUP BY sp.MaSP, sp.TenSP ORDER BY SoLuongBan DESC LIMIT 10');
        $stmt->execute($params);
        $topProducts = $stmt->fetchAll();
        $lowStock = $pdo->query('SELECT sp.MaSP, sp.TenSP, sp.SoLuongTon, dm.TenDM FROM SanPham sp JOIN DanhMuc dm ON dm.MaDM = sp.MaDM WHERE sp.SoLuongTon <= 5 ORDER BY sp.SoLuongTon ASC, sp.TenSP LIMIT 20')->fetchAll();
        $this->view('admin/statistics', [
            'pageTitle' => 'Thống kê', 'from' => $from, 'to' => $to, 'error' => $error,
            'summary' => $summary, 'statuses' => $statuses, 'topProducts' => $topProducts, 'lowStock' => $lowStock,
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