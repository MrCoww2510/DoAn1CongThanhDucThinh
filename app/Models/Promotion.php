<?php

// Truy vấn chương trình giảm giá và kiểm tra voucher dùng chung toàn hệ thống.

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use RuntimeException;

final class Promotion
{
    /** Gắn giá hiệu lực vào danh sách sản phẩm; campaign giảm cao nhất được áp dụng. */
    public function applyEffectivePrices(array $items): array
    {
        if ($items === []) {
            return $items;
        }

        $campaigns = Database::connection()->query(
            "SELECT c.MaCTGG, c.PhamVi, c.LoaiGiam, c.GiaTri, cp.MaSP
             FROM ChuongTrinhGiamGia c
             LEFT JOIN ChuongTrinhGiamGiaSanPham cp ON cp.MaCTGG = c.MaCTGG
             WHERE c.TrangThai = 'HoatDong'
               AND c.NgayBatDau <= NOW() AND c.NgayKetThuc > NOW()"
        )->fetchAll();
        $byProduct = [];
        $global = [];
        foreach ($campaigns as $campaign) {
            if ($campaign['PhamVi'] === 'ToanTrang') {
                $global[] = $campaign;
            } elseif (!empty($campaign['MaSP'])) {
                $byProduct[(string) $campaign['MaSP']][] = $campaign;
            }
        }

        foreach ($items as &$item) {
            $base = round((float) ($item['GiaBan'] ?? 0), 2);
            $productId = (string) ($item['MaSP'] ?? '');
            $best = 0.0;
            foreach (array_merge($global, $byProduct[$productId] ?? []) as $campaign) {
                $best = max($best, $this->discountAmount($base, $campaign['LoaiGiam'], (float) $campaign['GiaTri']));
            }
            $item['GiaGoc'] = $base;
            $item['GiamGiaChuongTrinh'] = $best;
            $item['GiaBanHieuLuc'] = round(max(0, $base - $best), 2);
            if (array_key_exists('SoLuong', $item)) {
                $item['ThanhTien'] = round($item['GiaBanHieuLuc'] * (int) $item['SoLuong'], 2);
            }
        }
        unset($item);

        return $items;
    }

    public function previewVoucher(string $code, string $customerId, float $subtotal): array
    {
        $voucher = $this->findEligibleVoucher(Database::connection(), $code, $customerId, $subtotal, false);
        $discount = $this->voucherDiscount($voucher, $subtotal);

        return ['code' => (string) $voucher['MaCode'], 'discount' => $discount, 'total' => round(max(0, $subtotal - $discount), 2)];
    }

    /** Revalidates under a voucher row lock; caller must have an active transaction. */
    public function lockAndValidateVoucher(PDO $pdo, string $code, string $customerId, float $subtotal): array
    {
        $voucher = $this->findEligibleVoucher($pdo, $code, $customerId, $subtotal, true);
        $discount = $this->voucherDiscount($voucher, $subtotal);

        return ['id' => (string) $voucher['MaVoucher'], 'code' => (string) $voucher['MaCode'], 'discount' => $discount];
    }

    public function listCampaigns(): array
    {
        return Database::connection()->query(
            "SELECT c.*, GROUP_CONCAT(cp.MaSP ORDER BY cp.MaSP SEPARATOR ',') AS ProductIds
             FROM ChuongTrinhGiamGia c
             LEFT JOIN ChuongTrinhGiamGiaSanPham cp ON cp.MaCTGG = c.MaCTGG
             GROUP BY c.MaCTGG ORDER BY c.NgayBatDau DESC"
        )->fetchAll();
    }

    public function listVouchers(): array
    {
        return Database::connection()->query(
            'SELECT v.*,
                    (SELECT COUNT(*) FROM VoucherSuDung u WHERE u.MaVoucher = v.MaVoucher) AS SoLuotDaDung
             FROM Voucher v ORDER BY v.NgayBatDau DESC'
        )->fetchAll();
    }

    public function findCampaign(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT c.*, GROUP_CONCAT(cp.MaSP ORDER BY cp.MaSP SEPARATOR ',') AS ProductIds
             FROM ChuongTrinhGiamGia c LEFT JOIN ChuongTrinhGiamGiaSanPham cp ON cp.MaCTGG = c.MaCTGG
             WHERE c.MaCTGG = :id GROUP BY c.MaCTGG LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function findVoucher(string $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM Voucher WHERE MaVoucher = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function saveCampaign(array $data, ?string $id = null): void
    {
        $pdo = Database::connection();
        $productIds = array_values(array_unique(array_filter(array_map('strval', $data['products'] ?? []))));
        if ($data['scope'] === 'SanPham' && $productIds === []) {
            throw new RuntimeException('Chương trình áp dụng theo sản phẩm cần chọn ít nhất một sản phẩm.');
        }
        if ($data['scope'] === 'ToanTrang') {
            $productIds = [];
        }
        if ($data['type'] === 'PhanTram' && (float) $data['value'] > 100) {
            throw new RuntimeException('Mức giảm phần trăm không được vượt quá 100%.');
        }
        $this->validatePeriod($data['start'], $data['end']);
        $this->validateProducts($pdo, $productIds);
        $id ??= 'KM'.strtoupper(bin2hex(random_bytes(8)));
        $pdo->beginTransaction();
        try {
            if ($id === '') {
                throw new RuntimeException('Mã chương trình không hợp lệ.');
            }
            $stmt = $pdo->prepare(
                'INSERT INTO ChuongTrinhGiamGia (MaCTGG, TenCTGG, PhamVi, LoaiGiam, GiaTri, NgayBatDau, NgayKetThuc, TrangThai)
                 VALUES (:id, :name, :scope, :type, :value, :start, :end, :status)
                 ON DUPLICATE KEY UPDATE TenCTGG = VALUES(TenCTGG), PhamVi = VALUES(PhamVi),
                    LoaiGiam = VALUES(LoaiGiam), GiaTri = VALUES(GiaTri), NgayBatDau = VALUES(NgayBatDau),
                    NgayKetThuc = VALUES(NgayKetThuc), TrangThai = VALUES(TrangThai)'
            );
            $stmt->execute(['id' => $id, 'name' => $data['name'], 'scope' => $data['scope'], 'type' => $data['type'],
                'value' => $data['value'], 'start' => $data['start'], 'end' => $data['end'], 'status' => $data['status']]);
            $delete = $pdo->prepare('DELETE FROM ChuongTrinhGiamGiaSanPham WHERE MaCTGG = :id');
            $delete->execute(['id' => $id]);
            $insert = $pdo->prepare('INSERT INTO ChuongTrinhGiamGiaSanPham (MaCTGG, MaSP) VALUES (:campaign, :product)');
            foreach ($productIds as $productId) {
                $insert->execute(['campaign' => $id, 'product' => $productId]);
            }
            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function saveVoucher(array $data, ?string $id = null): void
    {
        $this->validatePeriod($data['start'], $data['end']);
        if ($data['type'] === 'PhanTram' && (float) $data['value'] > 100) {
            throw new RuntimeException('Mức giảm phần trăm không được vượt quá 100%.');
        }
        if ($data['type'] === 'SoTien' && $data['max_discount'] !== null) {
            throw new RuntimeException('Mức giảm tối đa chỉ áp dụng cho voucher phần trăm.');
        }
        $pdo = Database::connection();
        $duplicate = $pdo->prepare('SELECT 1 FROM Voucher WHERE MaCode = :code AND MaVoucher <> :id LIMIT 1');
        $duplicate->execute(['code' => $data['code'], 'id' => $id ?? '']);
        if ($duplicate->fetchColumn()) {
            throw new RuntimeException('Mã voucher đã được sử dụng.');
        }
        $id ??= 'VC'.strtoupper(bin2hex(random_bytes(8)));
        $values = ['id' => $id, 'code' => $data['code'], 'type' => $data['type'], 'value' => $data['value'],
            'start' => $data['start'], 'end' => $data['end'], 'minimum' => $data['minimum'],
            'maximum' => $data['max_discount'], 'usage_limit' => $data['usage_limit'],
            'per_customer' => $data['per_customer'], 'status' => $data['status']];
        if ($data['id'] ?? null) {
            $stmt = $pdo->prepare(
                'UPDATE Voucher SET MaCode = :code, LoaiGiam = :type, GiaTri = :value,
                    NgayBatDau = :start, NgayKetThuc = :end, DonToiThieu = :minimum,
                    GiamToiDa = :maximum, TongLuotToiDa = :usage_limit,
                    LuotMoiKhachToiDa = :per_customer, TrangThai = :status WHERE MaVoucher = :id'
            );
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO Voucher (MaVoucher, MaCode, LoaiGiam, GiaTri, NgayBatDau, NgayKetThuc,
                    DonToiThieu, GiamToiDa, TongLuotToiDa, LuotMoiKhachToiDa, TrangThai)
                 VALUES (:id, :code, :type, :value, :start, :end, :minimum, :maximum, :usage_limit, :per_customer, :status)'
            );
        }
        $stmt->execute($values);
    }

    private function findEligibleVoucher(PDO $pdo, string $code, string $customerId, float $subtotal, bool $lock): array
    {
        $normalized = strtoupper(trim($code));
        if ($normalized === '' || $customerId === '') {
            throw new RuntimeException('Vui lòng nhập mã voucher hợp lệ.');
        }
        $stmt = $pdo->prepare('SELECT * FROM Voucher WHERE MaCode = :code LIMIT 1'.($lock ? ' FOR UPDATE' : ''));
        $stmt->execute(['code' => $normalized]);
        $voucher = $stmt->fetch();
        if (!$voucher || $voucher['TrangThai'] !== 'HoatDong') {
            throw new RuntimeException('Voucher không tồn tại hoặc đã bị vô hiệu hóa.');
        }
        $now = time();
        if (strtotime((string) $voucher['NgayBatDau']) > $now || strtotime((string) $voucher['NgayKetThuc']) <= $now) {
            throw new RuntimeException('Voucher chưa bắt đầu hoặc đã hết hạn.');
        }
        if ($subtotal < (float) $voucher['DonToiThieu']) {
            throw new RuntimeException('Giá trị đơn hàng chưa đạt mức tối thiểu của voucher.');
        }
        if ($voucher['TongLuotToiDa'] !== null) {
            $count = $pdo->prepare('SELECT MaSuDung FROM VoucherSuDung WHERE MaVoucher = :voucher LIMIT :usageLimit FOR UPDATE');
            $count->bindValue(':voucher', $voucher['MaVoucher']);
            $count->bindValue(':usageLimit', (int) $voucher['TongLuotToiDa'], PDO::PARAM_INT);
            $count->execute();
            if (count($count->fetchAll(PDO::FETCH_COLUMN)) >= (int) $voucher['TongLuotToiDa']) {
                throw new RuntimeException('Voucher đã hết lượt sử dụng.');
            }
        }
        if ($voucher['LuotMoiKhachToiDa'] !== null) {
            $countCustomer = $pdo->prepare('SELECT MaSuDung FROM VoucherSuDung WHERE MaVoucher = :voucher AND MaND = :customer LIMIT :usageLimit FOR UPDATE');
            $countCustomer->bindValue(':voucher', $voucher['MaVoucher']);
            $countCustomer->bindValue(':customer', $customerId);
            $countCustomer->bindValue(':usageLimit', (int) $voucher['LuotMoiKhachToiDa'], PDO::PARAM_INT);
            $countCustomer->execute();
            if (count($countCustomer->fetchAll(PDO::FETCH_COLUMN)) >= (int) $voucher['LuotMoiKhachToiDa']) {
                throw new RuntimeException('Bạn đã sử dụng hết lượt cho phép của voucher này.');
            }
        }

        return $voucher;
    }

    private function voucherDiscount(array $voucher, float $subtotal): float
    {
        $discount = $this->discountAmount($subtotal, $voucher['LoaiGiam'], (float) $voucher['GiaTri']);
        if ($voucher['LoaiGiam'] === 'PhanTram' && $voucher['GiamToiDa'] !== null) {
            $discount = min($discount, (float) $voucher['GiamToiDa']);
        }

        return round(min($subtotal, max(0, $discount)), 2);
    }

    private function discountAmount(float $base, string $type, float $value): float
    {
        return round($type === 'PhanTram' ? $base * $value / 100 : $value, 2);
    }

    private function validatePeriod(string $start, string $end): void
    {
        $startTime = strtotime($start);
        $endTime = strtotime($end);
        if ($startTime === false || $endTime === false || $endTime <= $startTime) {
            throw new RuntimeException('Thời điểm kết thúc phải sau thời điểm bắt đầu.');
        }
    }

    private function validateProducts(PDO $pdo, array $productIds): void
    {
        if ($productIds === []) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM SanPham WHERE MaSP IN ($placeholders)");
        $stmt->execute($productIds);
        if ((int) $stmt->fetchColumn() !== count($productIds)) {
            throw new RuntimeException('Có sản phẩm được chọn không còn tồn tại.');
        }
    }
}
