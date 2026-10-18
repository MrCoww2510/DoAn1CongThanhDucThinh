<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Cart
{
    private \PDO $db;

    // Lưu kết nối cơ sở dữ liệu để dùng trong các thao tác giỏ hàng.
    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * Tìm giỏ hàng của người dùng; tạo mới nếu chưa có.
     */
    public function getOrCreateCart(string $maND): string
    {
        $stmt = $this->db->prepare(
            'SELECT MaGH FROM GioHang WHERE MaND = :maND LIMIT 1'
        );
        $stmt->execute(['maND' => $maND]);

        $maGH = $stmt->fetchColumn();

        if ($maGH !== false) {
            return (string) $maGH;
        }

        $maGH = 'GH'.strtoupper(bin2hex(random_bytes(8)));

        $stmt = $this->db->prepare(
            'INSERT INTO GioHang (MaGH, MaND, TongTien)
             VALUES (:maGH, :maND, 0)'
        );

        try {
            $stmt->execute([
                'maGH' => $maGH,
                'maND' => $maND,
            ]);
        } catch (\PDOException $e) {
            // Có thể người dùng có giỏ hàng được tạo đồng thời.
            $stmt = $this->db->prepare(
                'SELECT MaGH FROM GioHang WHERE MaND = :maND LIMIT 1'
            );
            $stmt->execute(['maND' => $maND]);
            $existing = $stmt->fetchColumn();

            if ($existing !== false) {
                return (string) $existing;
            }

            throw $e;
        }

        return $maGH;
    }

    /**
     * Lấy giỏ hàng cùng chi tiết và thông tin sản phẩm.
     */
    public function getItems(string $maND): array
    {
        $maGH = $this->getOrCreateCart($maND);

        $stmt = $this->db->prepare(
            'SELECT
                ct.MaCTGH,
                ct.MaSP,
                ct.SoLuong,
                sp.TenSP,
                sp.GiaBan,
                sp.SoLuongTon,
                cts.HinhAnh,
                cts.TrangThai,
                (ct.SoLuong * sp.GiaBan) AS ThanhTien
             FROM ChiTietGioHang ct
             INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
             LEFT JOIN ChiTietSanPham cts ON cts.MaSP = sp.MaSP
             WHERE ct.MaGH = :maGH
             ORDER BY ct.MaCTGH DESC'
        );

        $stmt->execute(['maGH' => $maGH]);
        $items = $stmt->fetchAll();

        $total = 0;

        foreach ($items as $item) {
            $total += (float) $item['ThanhTien'];
        }

        $stmt = $this->db->prepare(
            'UPDATE GioHang SET TongTien = :total WHERE MaGH = :maGH'
        );
        $stmt->execute([
            'total' => $total,
            'maGH' => $maGH,
        ]);

        return [
            'maGH' => $maGH,
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Thêm sản phẩm hoặc cộng số lượng nếu sản phẩm đã có.
     */
    public function addItem(
        string $maND,
        string $maSP,
        int $quantity
    ): void {
        if ($quantity < 1) {
            throw new \RuntimeException('Số lượng phải lớn hơn 0.');
        }

        $this->db->beginTransaction();

        try {
            $maGH = $this->getOrCreateCart($maND);

            $stmt = $this->db->prepare(
                'SELECT MaGH FROM GioHang WHERE MaGH = :maGH FOR UPDATE'
            );
            $stmt->execute(['maGH' => $maGH]);

            $stmt = $this->db->prepare(
                'SELECT
                    sp.MaSP,
                    sp.SoLuongTon,
                    cts.TrangThai
                 FROM SanPham sp
                 LEFT JOIN ChiTietSanPham cts ON cts.MaSP = sp.MaSP
                 WHERE sp.MaSP = :maSP
                 FOR UPDATE'
            );
            $stmt->execute(['maSP' => $maSP]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new \RuntimeException('Sản phẩm không tồn tại.');
            }

            // Nếu trạng thái được nhập và không phải trạng thái bán,
            // từ chối thêm vào giỏ hàng.
            $status = trim((string) ($product['TrangThai'] ?? ''));

            if (
                $status !== ''
                && !in_array(
                    mb_strtolower($status),
                    ['dangban', 'đang bán', 'hoatdong', 'hoạt động'],
                    true
                )
            ) {
                throw new \RuntimeException('Sản phẩm hiện không được phép bán.');
            }

            $stmt = $this->db->prepare(
                'SELECT SoLuong
                 FROM ChiTietGioHang
                 WHERE MaGH = :maGH AND MaSP = :maSP
                 FOR UPDATE'
            );
            $stmt->execute([
                'maGH' => $maGH,
                'maSP' => $maSP,
            ]);

            $current = $stmt->fetchColumn();
            $newQuantity = (int) ($current === false ? 0 : $current)
                + $quantity;

            if ($newQuantity > (int) $product['SoLuongTon']) {
                throw new \RuntimeException('Số lượng yêu cầu vượt quá số lượng tồn kho.');
            }

            if ($current !== false) {
                $stmt = $this->db->prepare(
                    'UPDATE ChiTietGioHang
                     SET SoLuong = :quantity
                     WHERE MaGH = :maGH AND MaSP = :maSP'
                );
                $stmt->execute([
                    'quantity' => $newQuantity,
                    'maGH' => $maGH,
                    'maSP' => $maSP,
                ]);
            } else {
                $maCTGH = 'CTGH'.strtoupper(bin2hex(random_bytes(6)));

                $stmt = $this->db->prepare(
                    'INSERT INTO ChiTietGioHang
                        (MaCTGH, MaGH, MaSP, SoLuong)
                     VALUES (:maCTGH, :maGH, :maSP, :quantity)'
                );
                $stmt->execute([
                    'maCTGH' => $maCTGH,
                    'maGH' => $maGH,
                    'maSP' => $maSP,
                    'quantity' => $quantity,
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Lấy các mặt hàng hiện tại mà không tạo giỏ mới.
     */
    public function findItems(string $maND): array
    {
        $stmt = $this->db->prepare(
            'SELECT MaGH FROM GioHang WHERE MaND = :maND LIMIT 1'
        );
        $stmt->execute(['maND' => $maND]);
        $maGH = $stmt->fetchColumn();

        if ($maGH === false) {
            return [
                'maGH' => null,
                'items' => [],
                'total' => 0,
            ];
        }

        $stmt = $this->db->prepare(
            'SELECT
                ct.MaCTGH,
                ct.MaSP,
                ct.SoLuong,
                sp.TenSP,
                sp.GiaBan,
                sp.SoLuongTon,
                cts.HinhAnh,
                cts.TrangThai,
                (ct.SoLuong * sp.GiaBan) AS ThanhTien
             FROM ChiTietGioHang ct
             INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
             LEFT JOIN ChiTietSanPham cts ON cts.MaSP = sp.MaSP
             WHERE ct.MaGH = :maGH
             ORDER BY ct.MaCTGH DESC'
        );
        $stmt->execute(['maGH' => $maGH]);
        $items = $stmt->fetchAll();
        $total = 0;

        foreach ($items as $item) {
            $total += (float) $item['ThanhTien'];
        }

        return [
            'maGH' => (string) $maGH,
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Cập nhật số lượng của một sản phẩm trong giỏ.
     */
    public function updateItem(
        string $maND,
        string $maSP,
        int $quantity
    ): void {
        if ($quantity < 1) {
            throw new \RuntimeException('Số lượng phải lớn hơn 0.');
        }

        $stmt = $this->db->prepare(
            'SELECT ct.MaCTGH, sp.SoLuongTon
             FROM ChiTietGioHang ct
             INNER JOIN GioHang gh ON gh.MaGH = ct.MaGH
             INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
             WHERE gh.MaND = :maND AND ct.MaSP = :maSP'
        );
        $stmt->execute([
            'maND' => $maND,
            'maSP' => $maSP,
        ]);
        $item = $stmt->fetch();

        if (!$item) {
            throw new \RuntimeException('Không tìm thấy sản phẩm trong giỏ hàng.');
        }

        if ($quantity > (int) $item['SoLuongTon']) {
            throw new \RuntimeException('Số lượng vượt quá số lượng tồn kho.');
        }

        $stmt = $this->db->prepare(
            'UPDATE ChiTietGioHang ct
             INNER JOIN GioHang gh ON gh.MaGH = ct.MaGH
             SET ct.SoLuong = :quantity
             WHERE gh.MaND = :maND AND ct.MaSP = :maSP'
        );
        $stmt->execute([
            'quantity' => $quantity,
            'maND' => $maND,
            'maSP' => $maSP,
        ]);
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng của chính người dùng.
     */
    public function removeItem(string $maND, string $maSP): void
    {
        $stmt = $this->db->prepare(
            'DELETE ct FROM ChiTietGioHang ct
             INNER JOIN GioHang gh ON gh.MaGH = ct.MaGH
             WHERE gh.MaND = :maND AND ct.MaSP = :maSP'
        );
        $stmt->execute([
            'maND' => $maND,
            'maSP' => $maSP,
        ]);
    }

    /**
     * Xóa toàn bộ sản phẩm trong giỏ, giữ lại giỏ hàng.
     */
    public function clear(string $maND): void
    {
        $stmt = $this->db->prepare(
            'DELETE ct FROM ChiTietGioHang ct
             INNER JOIN GioHang gh ON gh.MaGH = ct.MaGH
             WHERE gh.MaND = :maND'
        );
        $stmt->execute(['maND' => $maND]);

        $stmt = $this->db->prepare(
            'UPDATE GioHang SET TongTien = 0 WHERE MaND = :maND'
        );
        $stmt->execute(['maND' => $maND]);
    }
}
