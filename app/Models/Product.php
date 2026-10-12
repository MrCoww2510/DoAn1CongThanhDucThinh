<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Product
{
    /**
     * Lấy danh sách sản phẩm nổi bật.
     */
    public function featured(int $limit = 8): array
    {
        $limit = max(1, min($limit, 24));

        $sql = $this->baseQuery()
            .' ORDER BY sp.MaSP DESC LIMIT '.$limit;

        return Database::connection()
            ->query($sql)
            ->fetchAll();
    }

    /**
     * Tìm kiếm, lọc danh mục và sắp xếp sản phẩm.
     */
    public function all(
        string $keyword = '',
        string $categoryId = '',
        string $sort = 'default'
    ): array {
        $sql = $this->baseQuery();

        $conditions = [];
        $params = [];

        // Tìm kiếm theo tên sản phẩm
        if ($keyword !== '') {
            $conditions[] = 'sp.TenSP LIKE :keyword';
            $params['keyword'] = '%'.$keyword.'%';
        }

        // Lọc theo danh mục
        if ($categoryId !== '') {
            $conditions[] = 'sp.MaDM = :category';
            $params['category'] = $categoryId;
        }

        // Ghép điều kiện WHERE
        if ($conditions !== []) {
            $sql .= ' WHERE '.implode(' AND ', $conditions);
        }

        // Các kiểu sắp xếp hợp lệ
        $sortOptions = [
            'default' => 'sp.MaSP DESC',
            'name_asc' => 'sp.TenSP ASC',
            'name_desc' => 'sp.TenSP DESC',
            'price_asc' => 'sp.GiaBan ASC',
            'price_desc' => 'sp.GiaBan DESC',
        ];

        // Chỉ sử dụng câu ORDER BY nằm trong danh sách cho phép
        $orderBy = $sortOptions[$sort] ?? $sortOptions['default'];

        $sql .= ' ORDER BY '.$orderBy;

        // Thực thi truy vấn
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách danh mục.
     */
    public function categories(): array
    {
        $sql = '
            SELECT MaDM, TenDM
            FROM DanhMuc
            ORDER BY TenDM ASC
        ';

        return Database::connection()
            ->query($sql)
            ->fetchAll();
    }

    /**
     * Tìm sản phẩm theo mã.
     */
    public function findById(string $id): ?array
    {
        $sql = $this->baseQuery()
            .' WHERE sp.MaSP = :id LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);

        $product = $stmt->fetch();

        return $product === false ? null : $product;
    }

    /**
     * Truy vấn thông tin sản phẩm dùng chung.
     */
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
