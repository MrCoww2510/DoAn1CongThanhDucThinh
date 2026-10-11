<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Product
{
    public function featured(int $limit = 8): array
    {
        $limit = max(1, min($limit, 24));

        $sql = $this->baseQuery()
            . ' ORDER BY sp.MaSP DESC LIMIT ' . $limit;

        return Database::connection()
            ->query($sql)
            ->fetchAll();
    }

    public function all(string $keyword = '', string $categoryId = ''): array
    {
        $sql = $this->baseQuery();
        $conditions = [];
        $params = [];

        if ($keyword !== '') {
            $conditions[] = 'sp.TenSP LIKE :keyword';
            $params['keyword'] = '%' . $keyword . '%';
        }

        if ($categoryId !== '') {
            $conditions[] = 'sp.MaDM = :category';
            $params['category'] = $categoryId;
        }

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY sp.TenSP ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function categories(): array
    {
        $sql = 'SELECT MaDM, TenDM
                FROM DanhMuc
                ORDER BY TenDM ASC';

        return Database::connection()
            ->query($sql)
            ->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $sql = $this->baseQuery()
            . ' WHERE sp.MaSP = :id LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);

        $product = $stmt->fetch();

        return $product === false ? null : $product;
    }

    private function baseQuery(): string
    {
        return '
            SELECT
                sp.MaSP,
                sp.MaDM,
                sp.TenSP,
                sp.GiaBan,
                sp.SoLuongTon,
                dm.TenDM,
                ct.LoaiSP,
                ct.ThongSoKT,
                ct.ThuongHieu,
                ct.HinhAnh,
                ct.MoTa,
                ct.TrangThai
            FROM SanPham sp
            INNER JOIN DanhMuc dm
                ON dm.MaDM = sp.MaDM
            LEFT JOIN ChiTietSanPham ct
                ON ct.MaSP = sp.MaSP
        ';
    }
}