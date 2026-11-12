<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Order
{
    private \PDO $db;

    // Lưu kết nối cơ sở dữ liệu để dùng trong các thao tác đơn hàng.
    public function __construct()
    {
        $this->db = Database::connection();
    }

    // Tạo đơn COD, lưu chi tiết và trừ tồn kho trong cùng giao dịch.
    public function placeCodOrder(
        string $customerId,
        string $recipientName,
        string $deliveryAddress,
        string $recipientPhone
    ): array {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'SELECT MaGH
                 FROM GioHang
                 WHERE MaND = :customerId
                 LIMIT 1
                 FOR UPDATE'
            );
            $stmt->execute(['customerId' => $customerId]);
            $cartId = $stmt->fetchColumn();

            if ($cartId === false) {
                throw new \RuntimeException('Giỏ hàng đang trống.');
            }

            $stmt = $this->db->prepare(
                'SELECT
                    ct.MaSP,
                    ct.SoLuong,
                    sp.TenSP,
                    sp.GiaBan,
                    sp.SoLuongTon,
                    cts.TrangThai
                 FROM ChiTietGioHang ct
                 INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
                 LEFT JOIN ChiTietSanPham cts ON cts.MaSP = sp.MaSP
                 WHERE ct.MaGH = :cartId
                 ORDER BY ct.MaSP
                 FOR UPDATE'
            );
            $stmt->execute(['cartId' => $cartId]);
            $items = $stmt->fetchAll();

            if ($items === []) {
                throw new \RuntimeException('Giỏ hàng đang trống.');
            }

            foreach ($items as $item) {
                if ((int) $item['SoLuong'] < 1) {
                    throw new \RuntimeException('Số lượng sản phẩm trong giỏ hàng không hợp lệ.');
                }

                $status = trim((string) ($item['TrangThai'] ?? ''));

                if (
                    $status !== ''
                    && !in_array(
                        mb_strtolower($status),
                        ['dangban', 'đang bán', 'hoatdong', 'hoạt động'],
                        true
                    )
                ) {
                    throw new \RuntimeException('Sản phẩm "'.$item['TenSP'].'" hiện không được phép bán.');
                }

                if ((int) $item['SoLuong'] > (int) $item['SoLuongTon']) {
                    throw new \RuntimeException('Sản phẩm "'.$item['TenSP'].'" không đủ tồn kho.');
                }
            }

            $stmt = $this->db->prepare(
                'SELECT COALESCE(SUM(sp.GiaBan * ct.SoLuong), 0)
                 FROM ChiTietGioHang ct
                 INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
                 WHERE ct.MaGH = :cartId'
            );
            $stmt->execute(['cartId' => $cartId]);
            $total = (string) $stmt->fetchColumn();

            $orderId = 'DH'.strtoupper(bin2hex(random_bytes(9)));
            $stmt = $this->db->prepare(
                'INSERT INTO DonHang
                    (MaDH, MaND, MaNV, TenNguoiNhan, DiaChiGiao, SDTNhan,
                     TongTien, PhuongThucTT, TrangThaiDH, TrangThaiTT)
                 VALUES
                    (:orderId, :customerId, NULL, :recipientName, :deliveryAddress,
                     :recipientPhone, :total, :paymentMethod, :orderStatus, :paymentStatus)'
            );
            $stmt->execute([
                'orderId' => $orderId,
                'customerId' => $customerId,
                'recipientName' => $recipientName,
                'deliveryAddress' => $deliveryAddress,
                'recipientPhone' => $recipientPhone,
                'total' => $total,
                'paymentMethod' => 'COD',
                'orderStatus' => 'Chờ xác nhận',
                'paymentStatus' => 'Chưa thanh toán',
            ]);

            foreach ($items as $item) {
                $quantity = (int) $item['SoLuong'];
                $productId = (string) $item['MaSP'];

                $stmt = $this->db->prepare(
                    'UPDATE SanPham
                     SET SoLuongTon = SoLuongTon - :deduct
                     WHERE MaSP = :productId
                       AND SoLuongTon >= :minimum'
                );
                $stmt->execute([
                    'deduct' => $quantity,
                    'productId' => $productId,
                    'minimum' => $quantity,
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new \RuntimeException('Tồn kho sản phẩm "'.$item['TenSP'].'" vừa thay đổi. Vui lòng kiểm tra giỏ hàng và thử lại.');
                }

                $detailId = 'CTDH'.strtoupper(bin2hex(random_bytes(8)));
                $stmt = $this->db->prepare(
                    'INSERT INTO ChiTietDonHang
                        (MaCTDH, MaDH, MaSP, SoLuong, DonGia)
                     VALUES
                        (:detailId, :orderId, :productId, :quantity, :unitPrice)'
                );
                $stmt->execute([
                    'detailId' => $detailId,
                    'orderId' => $orderId,
                    'productId' => $productId,
                    'quantity' => $quantity,
                    'unitPrice' => $item['GiaBan'],
                ]);

                $historyId = 'LS'.strtoupper(bin2hex(random_bytes(9)));
                $stmt = $this->db->prepare(
                    'INSERT INTO LichSuKho
                        (MaLS, MaSP, SoLuongThayDoi, LoaiThaoTac, MaND)
                     VALUES
                        (:historyId, :productId, :quantityChange, :operation, :customerId)'
                );
                $stmt->execute([
                    'historyId' => $historyId,
                    'productId' => $productId,
                    'quantityChange' => -$quantity,
                    'operation' => 'XuatKho',
                    'customerId' => $customerId,
                ]);
            }

            $stmt = $this->db->prepare(
                'DELETE FROM ChiTietGioHang WHERE MaGH = :cartId'
            );
            $stmt->execute(['cartId' => $cartId]);

            $stmt = $this->db->prepare(
                'UPDATE GioHang SET TongTien = 0 WHERE MaGH = :cartId'
            );
            $stmt->execute(['cartId' => $cartId]);

            $this->db->commit();

            return [
                'MaDH' => $orderId,
                'TongTien' => $total,
            ];
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    // Tìm đơn hàng thuộc về khách hàng đã đăng nhập.
    public function findForCustomer(string $orderId, string $customerId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                MaDH,
                TenNguoiNhan,
                DiaChiGiao,
                SDTNhan,
                TongTien,
                PhuongThucTT,
                TrangThaiDH,
                TrangThaiTT,
                NgayTao
             FROM DonHang
             WHERE MaDH = :orderId AND MaND = :customerId
             LIMIT 1'
        );
        $stmt->execute([
            'orderId' => $orderId,
            'customerId' => $customerId,
        ]);
        $order = $stmt->fetch();

        if ($order === false) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT
                ct.MaSP,
                ct.SoLuong,
                ct.DonGia,
                sp.TenSP,
                (ct.SoLuong * ct.DonGia) AS ThanhTien
             FROM ChiTietDonHang ct
             INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
             WHERE ct.MaDH = :orderId
             ORDER BY ct.MaCTDH'
        );
        $stmt->execute(['orderId' => $orderId]);

        return [
            'order' => $order,
            'items' => $stmt->fetchAll(),
        ];
    }

    public function findAllForManagement(string $status = '', string $query = ''): array
    {
        $conditions = [];
        $params = [];

        if ($status !== '') {
            $conditions[] = 'dh.TrangThaiDH = :status';
            $params['status'] = $status;
        }

        if ($query !== '') {
            $conditions[] = '(
                dh.MaDH LIKE :orderId
                OR nd.HoTen LIKE :customerName
                OR dh.TenNguoiNhan LIKE :recipientName
            )';
            $needle = '%'.$query.'%';
            $params['orderId'] = $needle;
            $params['customerName'] = $needle;
            $params['recipientName'] = $needle;
        }

        $where = $conditions === [] ? '' : ' WHERE '.implode(' AND ', $conditions);
        $stmt = $this->db->prepare(
            'SELECT
                dh.MaDH,
                dh.TenNguoiNhan,
                dh.TongTien,
                dh.TrangThaiDH,
                dh.TrangThaiTT,
                dh.NgayTao,
                nd.HoTen AS TenKhachHang,
                (
                    SELECT COUNT(*)
                    FROM ChiTietDonHang ct
                    WHERE ct.MaDH = dh.MaDH
                ) AS SoMatHang
             FROM DonHang dh
             INNER JOIN NguoiDung nd ON nd.MaND = dh.MaND'
             .$where.
            ' ORDER BY dh.NgayTao DESC, dh.MaDH DESC'
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findForManagement(string $orderId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                dh.MaDH,
                dh.MaND,
                dh.MaNV,
                dh.TenNguoiNhan,
                dh.DiaChiGiao,
                dh.SDTNhan,
                dh.TongTien,
                dh.PhuongThucTT,
                dh.TrangThaiDH,
                dh.TrangThaiTT,
                dh.NgayTao,
                dh.DaHoanKho,
                nd.HoTen AS TenKhachHang,
                nv.HoTen AS TenNhanVien
             FROM DonHang dh
             INNER JOIN NguoiDung nd ON nd.MaND = dh.MaND
             LEFT JOIN NguoiDung nv ON nv.MaND = dh.MaNV
             WHERE dh.MaDH = :orderId
             LIMIT 1'
        );
        $stmt->execute(['orderId' => $orderId]);
        $order = $stmt->fetch();

        if ($order === false) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT
                ct.MaSP,
                ct.SoLuong,
                ct.DonGia,
                sp.TenSP,
                sp.SoLuongTon,
                (ct.SoLuong * ct.DonGia) AS ThanhTien
             FROM ChiTietDonHang ct
             INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
             WHERE ct.MaDH = :orderId
             ORDER BY ct.MaCTDH'
        );
        $stmt->execute(['orderId' => $orderId]);

        return [
            'order' => $order,
            'items' => $stmt->fetchAll(),
        ];
    }

    public function findAllForCustomer(string $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                dh.MaDH,
                dh.TongTien,
                dh.PhuongThucTT,
                dh.TrangThaiDH,
                dh.TrangThaiTT,
                dh.NgayTao,
                (
                    SELECT COUNT(*)
                    FROM ChiTietDonHang ct
                    WHERE ct.MaDH = dh.MaDH
                ) AS SoMatHang
             FROM DonHang dh
             WHERE dh.MaND = :customerId
             ORDER BY dh.NgayTao DESC, dh.MaDH DESC'
        );
        $stmt->execute(['customerId' => $customerId]);

        return $stmt->fetchAll();
    }

    public function updateStatusForManagement(
        string $orderId,
        string $staffUserId,
        string $targetStatus
    ): void {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'SELECT TrangThaiDH
                 FROM DonHang
                 WHERE MaDH = :orderId
                 LIMIT 1
                 FOR UPDATE'
            );
            $stmt->execute(['orderId' => $orderId]);
            $currentStatus = $stmt->fetchColumn();

            if ($currentStatus === false) {
                throw new RuntimeException('Không tìm thấy đơn hàng.');
            }

            $transitions = [
                'Chờ xác nhận' => ['Đang chuẩn bị hàng'],
                'Đang chuẩn bị hàng' => ['Đang giao hàng'],
                'Đang giao hàng' => ['Hoàn thành', 'Giao hàng thất bại'],
            ];
            if (!in_array($targetStatus, $transitions[$currentStatus] ?? [], true)) {
                throw new RuntimeException(
                    'Trạng thái hiện tại không cho phép thực hiện thao tác này.'
                );
            }

            if ($currentStatus === 'Chờ xác nhận') {
                $stmt = $this->db->prepare(
                    'SELECT ct.MaSP, ct.SoLuong, sp.SoLuongTon, sp.TenSP
                     FROM ChiTietDonHang ct
                     INNER JOIN SanPham sp ON sp.MaSP = ct.MaSP
                     WHERE ct.MaDH = :orderId
                     ORDER BY ct.MaSP
                     FOR UPDATE'
                );
                $stmt->execute(['orderId' => $orderId]);
                $items = $stmt->fetchAll();

                if ($items === []) {
                    throw new RuntimeException(
                        'Đơn hàng không có sản phẩm để xác nhận.'
                    );
                }

                foreach ($items as $item) {
                    if ((int) $item['SoLuong'] < 1 || (int) $item['SoLuongTon'] < 0) {
                        throw new RuntimeException(
                            'Không thể xác nhận vì tồn kho sản phẩm "'.$item['TenSP'].'" không hợp lệ.'
                        );
                    }
                }
            }

            $stmt = $this->db->prepare(
                'UPDATE DonHang
                 SET TrangThaiDH = :targetStatus, MaNV = :staffUserId
                 WHERE MaDH = :orderId AND TrangThaiDH = :currentStatus'
            );
            $stmt->execute([
                'targetStatus' => $targetStatus,
                'staffUserId' => $staffUserId,
                'orderId' => $orderId,
                'currentStatus' => $currentStatus,
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Trạng thái đơn hàng đã thay đổi. Vui lòng tải lại trang.'
                );
            }

            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    public function confirmReturnForManagement(string $orderId, string $staffUserId): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'SELECT TrangThaiDH, DaHoanKho
                 FROM DonHang
                 WHERE MaDH = :orderId
                 LIMIT 1
                 FOR UPDATE'
            );
            $stmt->execute(['orderId' => $orderId]);
            $order = $stmt->fetch();

            if ($order === false) {
                throw new RuntimeException('Không tìm thấy đơn hàng.');
            }
            if ($order['TrangThaiDH'] !== 'Giao hàng thất bại') {
                throw new RuntimeException(
                    'Chỉ có thể xác nhận hàng hoàn về kho cho đơn giao thất bại.'
                );
            }
            if ((int) $order['DaHoanKho'] === 1) {
                throw new RuntimeException('Hàng của đơn này đã được hoàn về kho.');
            }

            $stmt = $this->db->prepare(
                'SELECT MaSP, SoLuong
                 FROM ChiTietDonHang
                 WHERE MaDH = :orderId
                 ORDER BY MaSP
                 FOR UPDATE'
            );
            $stmt->execute(['orderId' => $orderId]);
            $items = $stmt->fetchAll();

            if ($items === []) {
                throw new RuntimeException(
                    'Đơn hàng không có sản phẩm để hoàn trả tồn kho.'
                );
            }

            $this->restoreOrderStock($items, $staffUserId);

            $stmt = $this->db->prepare(
                'UPDATE DonHang
                 SET DaHoanKho = 1, MaNV = :staffUserId
                 WHERE MaDH = :orderId
                   AND TrangThaiDH = :failedStatus
                   AND DaHoanKho = 0'
            );
            $stmt->execute([
                'staffUserId' => $staffUserId,
                'orderId' => $orderId,
                'failedStatus' => 'Giao hàng thất bại',
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Trạng thái hoàn kho đã thay đổi. Vui lòng tải lại trang.'
                );
            }

            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    public function cancelForCustomer(string $orderId, string $customerId): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'SELECT TrangThaiDH
                 FROM DonHang
                 WHERE MaDH = :orderId AND MaND = :customerId
                 LIMIT 1
                 FOR UPDATE'
            );
            $stmt->execute([
                'orderId' => $orderId,
                'customerId' => $customerId,
            ]);
            $status = $stmt->fetchColumn();

            if ($status === false) {
                throw new RuntimeException(
                    'Đơn hàng không tồn tại hoặc không thuộc tài khoản của bạn.'
                );
            }

            if ($status === 'Đã hủy') {
                throw new RuntimeException('Đơn hàng này đã được hủy trước đó.');
            }

            if ($status !== 'Chờ xác nhận') {
                throw new RuntimeException(
                    'Chỉ có thể hủy đơn hàng đang ở trạng thái Chờ xác nhận.'
                );
            }

            $stmt = $this->db->prepare(
                'SELECT MaSP, SoLuong
                 FROM ChiTietDonHang
                 WHERE MaDH = :orderId
                 ORDER BY MaSP
                 FOR UPDATE'
            );
            $stmt->execute(['orderId' => $orderId]);
            $items = $stmt->fetchAll();

            $this->restoreOrderStock($items, $customerId);

            $stmt = $this->db->prepare(
                'UPDATE DonHang
                 SET TrangThaiDH = :cancelledStatus
                 WHERE MaDH = :orderId
                   AND MaND = :customerId
                   AND TrangThaiDH = :pendingStatus'
            );
            $stmt->execute([
                'cancelledStatus' => 'Đã hủy',
                'orderId' => $orderId,
                'customerId' => $customerId,
                'pendingStatus' => 'Chờ xác nhận',
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Trạng thái đơn hàng đã thay đổi. Vui lòng tải lại trang.'
                );
            }

            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @param array<int, array{MaSP:mixed, SoLuong:mixed}> $items
     */
    private function restoreOrderStock(array $items, string $userId): void
    {
        foreach ($items as $item) {
            $productId = (string) $item['MaSP'];
            $quantity = (int) $item['SoLuong'];

            $stmt = $this->db->prepare(
                'UPDATE SanPham
                 SET SoLuongTon = SoLuongTon + :quantity
                 WHERE MaSP = :productId'
            );
            $stmt->execute([
                'quantity' => $quantity,
                'productId' => $productId,
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Không thể hoàn trả tồn kho cho sản phẩm trong đơn hàng.'
                );
            }

            $historyId = 'LS'.strtoupper(bin2hex(random_bytes(9)));
            $stmt = $this->db->prepare(
                'INSERT INTO LichSuKho
                    (MaLS, MaSP, SoLuongThayDoi, LoaiThaoTac, MaND)
                 VALUES
                    (:historyId, :productId, :quantityChange, :operation, :userId)'
            );
            $stmt->execute([
                'historyId' => $historyId,
                'productId' => $productId,
                'quantityChange' => $quantity,
                'operation' => 'NhapKho',
                'userId' => $userId,
            ]);
        }
    }
}
