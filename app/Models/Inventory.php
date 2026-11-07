<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Inventory
{
    public function update(string $productId, string $operation, int $quantity, string $userId): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT SoLuongTon FROM SanPham WHERE MaSP = :id FOR UPDATE');
            $stmt->execute(['id' => $productId]);
            $current = $stmt->fetchColumn();
            if ($current === false) throw new \RuntimeException('Không tìm thấy sản phẩm.');
            $current = (int) $current;
            if ($operation === 'NhapKho') {
                if ($quantity < 1) throw new \InvalidArgumentException('Số lượng nhập phải lớn hơn 0.');
                $delta = $quantity;
                $target = $current + $delta;
            } else {
                if ($quantity < 0) throw new \InvalidArgumentException('Tồn kho mới không thể âm.');
                $target = $quantity;
                $delta = $target - $current;
            }
            if ($delta === 0) throw new \InvalidArgumentException('Số lượng tồn kho không thay đổi.');
            $stmt = $pdo->prepare('UPDATE SanPham SET SoLuongTon = :quantity WHERE MaSP = :id');
            $stmt->execute(['quantity' => $target, 'id' => $productId]);
            $stmt = $pdo->prepare('INSERT INTO LichSuKho (MaLS, MaSP, SoLuongThayDoi, LoaiThaoTac, MaND) VALUES (:id, :product, :delta, :operation, :user)');
            $stmt->execute([
                'id' => 'LS'.strtoupper(bin2hex(random_bytes(9))), 'product' => $productId,
                'delta' => $delta, 'operation' => $operation, 'user' => $userId,
            ]);
            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $exception;
        }
    }

    public function products(): array
    {
        return Database::connection()->query('SELECT sp.MaSP, sp.TenSP, sp.SoLuongTon, dm.TenDM FROM SanPham sp JOIN DanhMuc dm ON dm.MaDM = sp.MaDM ORDER BY sp.SoLuongTon ASC, sp.TenSP ASC')->fetchAll();
    }

    public function history(): array
    {
        return Database::connection()->query('SELECT ls.MaLS, ls.SoLuongThayDoi, ls.LoaiThaoTac, ls.ThoiGian, sp.MaSP, sp.TenSP, nd.HoTen FROM LichSuKho ls JOIN SanPham sp ON sp.MaSP = ls.MaSP JOIN NguoiDung nd ON nd.MaND = ls.MaND ORDER BY ls.ThoiGian DESC LIMIT 100')->fetchAll();
    }
}
