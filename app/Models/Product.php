<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Product
{
    /** @return array<int, array<string, mixed>> */
    public function allForManagement(string $keyword = ''): array
    {
        $sql = $this->baseQuery();
        $params = [];
        if ($keyword !== '') {
            $sql .= ' WHERE sp.TenSP LIKE :name OR sp.MaSP LIKE :id';
            $params = ['name' => '%'.$keyword.'%', 'id' => '%'.$keyword.'%'];
        }
        $sql .= ' ORDER BY sp.MaSP DESC LIMIT 300';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function saveManaged(array $data, ?string $id = null): string
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            if ($id === null) {
                $id = $this->nextId($pdo, 'SanPham', 'MaSP', 'SP');
                $detailId = $this->nextId($pdo, 'ChiTietSanPham', 'MaCTSP', 'CTSP');
                $stmt = $pdo->prepare('INSERT INTO SanPham (MaSP, MaDM, TenSP, GiaBan, SoLuongTon) VALUES (:id, :category, :name, :price, 0)');
                $stmt->execute(['id' => $id, 'category' => $data['category'], 'name' => $data['name'], 'price' => $data['price']]);
                $stmt = $pdo->prepare('INSERT INTO ChiTietSanPham (MaCTSP, MaSP, LoaiSP, ThongSoKT, ThuongHieu, HinhAnh, MoTa, TrangThai) VALUES (:detail, :id, :type, :specs, :brand, :image, :description, :status)');
                $stmt->execute(['detail' => $detailId, 'id' => $id, 'type' => $data['type'], 'specs' => $data['specs'], 'brand' => $data['brand'], 'image' => $data['image'], 'description' => $data['description'], 'status' => $data['status']]);
            } else {
                $stmt = $pdo->prepare('UPDATE SanPham SET MaDM = :category, TenSP = :name, GiaBan = :price WHERE MaSP = :id');
                $stmt->execute(['category' => $data['category'], 'name' => $data['name'], 'price' => $data['price'], 'id' => $id]);
                $stmt = $pdo->prepare('UPDATE ChiTietSanPham SET LoaiSP = :type, ThongSoKT = :specs, ThuongHieu = :brand, HinhAnh = :image, MoTa = :description, TrangThai = :status WHERE MaSP = :id');
                $stmt->execute(['type' => $data['type'], 'specs' => $data['specs'], 'brand' => $data['brand'], 'image' => $data['image'], 'description' => $data['description'], 'status' => $data['status'], 'id' => $id]);
                if ($stmt->rowCount() === 0) {
                    $exists = $pdo->prepare('SELECT 1 FROM ChiTietSanPham WHERE MaSP = :id');
                    $exists->execute(['id' => $id]);
                    if (!$exists->fetchColumn()) {
                        throw new \RuntimeException('Product not found.');
                    }
                }
            }
            $pdo->commit();
            return $id;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $exception;
        }
    }

    public function findManaged(string $id): ?array
    {
        $stmt = Database::connection()->prepare($this->baseQuery().' WHERE sp.MaSP = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        return $product === false ? null : $product;
    }

    public function changeProductStatus(string $id, string $status): bool
    {
        $stmt = Database::connection()->prepare('UPDATE ChiTietSanPham SET TrangThai = :status WHERE MaSP = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /** @return array<int, array<string, mixed>> */
    public function categoriesForManagement(): array
    {
        return Database::connection()->query('SELECT dm.MaDM, dm.TenDM, dm.TrangThai, COUNT(sp.MaSP) AS SoSanPham FROM DanhMuc dm LEFT JOIN SanPham sp ON sp.MaDM = dm.MaDM GROUP BY dm.MaDM, dm.TenDM, dm.TrangThai ORDER BY dm.TenDM')->fetchAll();
    }

    public function categoryIsActive(string $id): bool
    {
        $stmt = Database::connection()->prepare("SELECT 1 FROM DanhMuc WHERE MaDM = :id AND TrangThai = 'DangKinhDoanh'");
        $stmt->execute(['id' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    public function saveCategory(string $name, ?string $id = null): void
    {
        $pdo = Database::connection();
        if ($id === null) {
            $id = $this->nextId($pdo, 'DanhMuc', 'MaDM', 'DM');
            $stmt = $pdo->prepare('INSERT INTO DanhMuc (MaDM, TenDM, TrangThai) VALUES (:id, :name, \'DangKinhDoanh\')');
            $stmt->execute(['id' => $id, 'name' => $name]);
            return;
        }
        $stmt = $pdo->prepare('UPDATE DanhMuc SET TenDM = :name WHERE MaDM = :id');
        $stmt->execute(['name' => $name, 'id' => $id]);
        if ($stmt->rowCount() === 0 && !$this->categoryExists($pdo, $id)) {
            throw new \RuntimeException('Category not found.');
        }
    }

    public function changeCategoryStatus(string $id, string $status): bool
    {
        $stmt = Database::connection()->prepare('UPDATE DanhMuc SET TrangThai = :status WHERE MaDM = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteCategory(string $id): bool
    {
        $pdo = Database::connection();
        $check = $pdo->prepare('SELECT COUNT(*) FROM SanPham WHERE MaDM = :id');
        $check->execute(['id' => $id]);
        if ((int) $check->fetchColumn() > 0) return false;
        $stmt = $pdo->prepare('DELETE FROM DanhMuc WHERE MaDM = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private function categoryExists(\PDO $pdo, string $id): bool
    {
        $stmt = $pdo->prepare('SELECT 1 FROM DanhMuc WHERE MaDM = :id');
        $stmt->execute(['id' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    private function nextId(\PDO $pdo, string $table, string $column, string $prefix): string
    {
        $stmt = $pdo->query('SELECT '.$column.' FROM '.$table.' WHERE '.$column.' REGEXP \'^'.$prefix.'[0-9]+$\' ORDER BY CAST(SUBSTRING('.$column.', '.(strlen($prefix) + 1).') AS UNSIGNED) DESC LIMIT 1');
        $current = (string) ($stmt->fetchColumn() ?: '');
        $number = $current === '' ? 1 : ((int) substr($current, strlen($prefix)) + 1);
        return $prefix.str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Lấy danh sách sản phẩm nổi bật.
     */
    public function featured(int $limit = 8): array
    {
        $limit = max(1, min($limit, 24));

        $sql = $this->baseQuery()
            ." WHERE ct.TrangThai IN ('DangKinhDoanh', 'Đang bán') AND dm.TrangThai = 'DangKinhDoanh' ORDER BY sp.MaSP DESC LIMIT ".$limit;

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

        $conditions = ["ct.TrangThai IN ('DangKinhDoanh', 'Đang bán')", "dm.TrangThai = 'DangKinhDoanh'"];
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
            WHERE TrangThai = \'DangKinhDoanh\'
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
            ." WHERE sp.MaSP = :id AND ct.TrangThai IN ('DangKinhDoanh', 'Đang bán') AND dm.TrangThai = 'DangKinhDoanh' LIMIT 1";

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
