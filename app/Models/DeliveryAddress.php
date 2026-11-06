<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class DeliveryAddress
{
    /**
     * @return array<int, array{MaDiaChi: int, DiaChi: string}>
     */
    public function findForCustomer(string $customerId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT MaDiaChi, DiaChi
             FROM DiaChiNhanHang
             WHERE MaND = :customerId
             ORDER BY MaDiaChi'
        );
        $stmt->execute(['customerId' => $customerId]);

        return $stmt->fetchAll();
    }

    public function findForCustomerById(int $addressId, string $customerId): ?string
    {
        $stmt = Database::connection()->prepare(
            'SELECT DiaChi
             FROM DiaChiNhanHang
             WHERE MaDiaChi = :addressId AND MaND = :customerId
             LIMIT 1'
        );
        $stmt->execute([
            'addressId' => $addressId,
            'customerId' => $customerId,
        ]);
        $address = $stmt->fetchColumn();

        return $address === false ? null : (string) $address;
    }

    public function addForCustomer(string $customerId, string $address): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT MaND, DiaChiNhanHang
                 FROM NguoiDung
                 WHERE MaND = :customerId
                 FOR UPDATE'
            );
            $stmt->execute(['customerId' => $customerId]);
            $customer = $stmt->fetch();

            if (!$customer) {
                throw new \RuntimeException('Không tìm thấy tài khoản.');
            }

            $stmt = $pdo->prepare(
                'SELECT MaDiaChi
                 FROM DiaChiNhanHang
                 WHERE MaND = :customerId AND DiaChi = :address
                 LIMIT 1'
            );
            $stmt->execute([
                'customerId' => $customerId,
                'address' => $address,
            ]);
            $existingId = $stmt->fetchColumn();

            if ($existingId !== false) {
                $addressId = (int) $existingId;
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO DiaChiNhanHang (MaND, DiaChi)
                     VALUES (:customerId, :address)'
                );
                $stmt->execute([
                    'customerId' => $customerId,
                    'address' => $address,
                ]);
                $addressId = (int) $pdo->lastInsertId();
            }

            if (trim((string) ($customer['DiaChiNhanHang'] ?? '')) === '') {
                $stmt = $pdo->prepare(
                    'UPDATE NguoiDung
                     SET DiaChiNhanHang = :address
                     WHERE MaND = :customerId
                       AND (DiaChiNhanHang IS NULL OR TRIM(DiaChiNhanHang) = :emptyAddress)'
                );
                $stmt->execute([
                    'address' => $address,
                    'customerId' => $customerId,
                    'emptyAddress' => '',
                ]);
            }

            $pdo->commit();

            return $addressId;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }
}
