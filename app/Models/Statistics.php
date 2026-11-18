<?php

// Truy vấn các số liệu quản trị theo khoảng ngày.

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Statistics
{
    public function forPeriod(string $start, string $end): array
    {
        $pdo = Database::connection();
        $where = ' WHERE dh.NgayTao >= :start AND dh.NgayTao < :end';
        $params = ['start' => $start, 'end' => $end];
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

        return compact('summary', 'statuses', 'topProducts', 'lowStock');
    }
}
