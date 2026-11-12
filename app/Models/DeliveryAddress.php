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

    public function updateForCustomer(int $addressId, string $customerId, string $address): bool
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT DiaChi
                 FROM DiaChiNhanHang
                 WHERE MaDiaChi = :addressId AND MaND = :customerId
                 FOR UPDATE'
            );
            $stmt->execute(['addressId' => $addressId, 'customerId' => $customerId]);
            $currentAddress = $stmt->fetchColumn();

            if ($currentAddress === false) {
                $pdo->rollBack();

                return false;
            }

            $stmt = $pdo->prepare(
                'UPDATE DiaChiNhanHang
                 SET DiaChi = :address
                 WHERE MaDiaChi = :addressId AND MaND = :customerId'
            );
            $stmt->execute([
                'address' => $address,
                'addressId' => $addressId,
                'customerId' => $customerId,
            ]);
            $stmt = $pdo->prepare(
                'UPDATE NguoiDung
                 SET DiaChiNhanHang = :address
                 WHERE MaND = :customerId AND DiaChiNhanHang = :currentAddress'
            );
            $stmt->execute([
                'address' => $address,
                'customerId' => $customerId,
                'currentAddress' => $currentAddress,
            ]);
            $pdo->commit();

            return true;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function deleteForCustomer(int $addressId, string $customerId): bool
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT DiaChi
                 FROM DiaChiNhanHang
                 WHERE MaDiaChi = :addressId AND MaND = :customerId
                 FOR UPDATE'
            );
            $stmt->execute(['addressId' => $addressId, 'customerId' => $customerId]);
            $currentAddress = $stmt->fetchColumn();

            if ($currentAddress === false) {
                $pdo->rollBack();

                return false;
            }

            $stmt = $pdo->prepare(
                'DELETE FROM DiaChiNhanHang
                 WHERE MaDiaChi = :addressId AND MaND = :customerId'
            );
            $stmt->execute(['addressId' => $addressId, 'customerId' => $customerId]);
            $stmt = $pdo->prepare(
                'UPDATE NguoiDung
                 SET DiaChiNhanHang = NULL
                 WHERE MaND = :customerId AND DiaChiNhanHang = :currentAddress'
            );
            $stmt->execute([
                'customerId' => $customerId,
                'currentAddress' => $currentAddress,
            ]);
            $pdo->commit();

            return true;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function updateProfileAddressForCustomer(
        string $customerId,
        string $currentAddress,
        string $address
    ): bool {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT DiaChiNhanHang FROM NguoiDung WHERE MaND = :customerId LIMIT 1'
        );
        $stmt->execute(['customerId' => $customerId]);
        $storedAddress = $stmt->fetchColumn();
        if ($storedAddress === false || $storedAddress === null
            || (string) $storedAddress !== $currentAddress) {
            return false;
        }
        if ($currentAddress === $address) {
            return true;
        }

        $stmt = Database::connection()->prepare(
            'UPDATE NguoiDung
             SET DiaChiNhanHang = :address
             WHERE MaND = :customerId AND DiaChiNhanHang = :currentAddress'
        );
        $stmt->execute([
            'address' => $address,
            'customerId' => $customerId,
            'currentAddress' => $currentAddress,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function deleteProfileAddressForCustomer(
        string $customerId,
        string $currentAddress
    ): bool {
        $stmt = Database::connection()->prepare(
            'UPDATE NguoiDung
             SET DiaChiNhanHang = NULL
             WHERE MaND = :customerId AND DiaChiNhanHang = :currentAddress'
        );
        $stmt->execute([
            'customerId' => $customerId,
            'currentAddress' => $currentAddress,
        ]);

        return $stmt->rowCount() > 0;
    }
}
