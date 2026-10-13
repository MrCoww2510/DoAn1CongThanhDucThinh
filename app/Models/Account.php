<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use RuntimeException;
use Throwable;

final class Account
{
    public function findForLogin(string $identity): ?array
    {
        $sql = '
            SELECT
                tk.MaTK,
                tk.MaND,
                tk.TenDangNhap,
                tk.MatKhau,
                tk.VaiTro,
                tk.TrangThai,
                nd.HoTen,
                nd.Email,
                nd.SDT
            FROM TaiKhoan tk
            INNER JOIN NguoiDung nd ON nd.MaND = tk.MaND
            WHERE tk.TenDangNhap = :username
               OR nd.Email = :email
            LIMIT 1
        ';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            'username' => $identity,
            'email' => $identity,
        ]);

        $account = $stmt->fetch();

        return $account ?: null;
    }

    public function usernameExists(string $username): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM TaiKhoan WHERE TenDangNhap = :username LIMIT 1'
        );
        $stmt->execute(['username' => $username]);

        return (bool) $stmt->fetchColumn();
    }

    public function emailExists(string $email): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM NguoiDung WHERE Email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    public function findCustomerProfile(string $customerId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT
                nd.MaND,
                nd.HoTen,
                nd.SDT,
                nd.Email,
                nd.DiaChiNhanHang,
                tk.TenDangNhap
             FROM NguoiDung nd
             INNER JOIN TaiKhoan tk ON tk.MaND = nd.MaND
             WHERE nd.MaND = :customerId
               AND tk.VaiTro = :role
             LIMIT 1'
        );
        $stmt->execute([
            'customerId' => $customerId,
            'role' => 'KhachHang',
        ]);
        $profile = $stmt->fetch();

        return $profile ?: null;
    }

    public function updateCustomerProfile(
        string $customerId,
        string $name,
        string $email,
        string $phone,
        string $address,
        ?string $currentPassword,
        ?string $newPassword
    ): void {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT tk.MatKhau
                 FROM TaiKhoan tk
                 WHERE tk.MaND = :customerId
                   AND tk.VaiTro = :role
                 LIMIT 1
                 FOR UPDATE'
            );
            $stmt->execute([
                'customerId' => $customerId,
                'role' => 'KhachHang',
            ]);
            $passwordHash = $stmt->fetchColumn();

            if ($passwordHash === false) {
                throw new RuntimeException('Không tìm thấy tài khoản khách hàng.');
            }

            $stmt = $pdo->prepare(
                'SELECT 1
                 FROM NguoiDung
                 WHERE Email = :email AND MaND <> :customerId
                 LIMIT 1'
            );
            $stmt->execute([
                'email' => $email,
                'customerId' => $customerId,
            ]);

            if ($stmt->fetchColumn() !== false) {
                throw new RuntimeException('Email đã được sử dụng bởi tài khoản khác.');
            }

            if (
                $newPassword !== null
                && (
                    $currentPassword === null
                    || !password_verify($currentPassword, (string) $passwordHash)
                )
            ) {
                throw new RuntimeException('Mật khẩu hiện tại không chính xác.');
            }

            $stmt = $pdo->prepare(
                'UPDATE NguoiDung
                 SET HoTen = :name,
                     Email = :email,
                     SDT = :phone,
                     DiaChiNhanHang = :address
                 WHERE MaND = :customerId'
            );
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone !== '' ? $phone : null,
                'address' => $address !== '' ? $address : null,
                'customerId' => $customerId,
            ]);

            if ($newPassword !== null) {
                $stmt = $pdo->prepare(
                    'UPDATE TaiKhoan
                     SET MatKhau = :password
                     WHERE MaND = :customerId
                       AND VaiTro = :role'
                );
                $stmt->execute([
                    'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                    'customerId' => $customerId,
                    'role' => 'KhachHang',
                ]);
            }

            $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function register(
        string $name,
        string $username,
        string $email,
        string $phone,
        string $password
    ): void {
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $userId = 'ND'.bin2hex(random_bytes(9));
            $accountId = 'TK'.bin2hex(random_bytes(9));

            $stmt = $pdo->prepare(
                'INSERT INTO NguoiDung (MaND, HoTen, SDT, Email)
                 VALUES (:id, :name, :phone, :email)'
            );

            $stmt->execute([
                'id' => $userId,
                'name' => $name,
                'phone' => $phone !== '' ? $phone : null,
                'email' => $email,
            ]);

            $stmt = $pdo->prepare(
                'INSERT INTO TaiKhoan
                    (MaTK, MaND, TenDangNhap, MatKhau, VaiTro, TrangThai)
                 VALUES
                    (:id, :user_id, :username, :password, :role, :status)'
            );

            $stmt->execute([
                'id' => $accountId,
                'user_id' => $userId,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'KhachHang',
                'status' => 'HoatDong',
            ]);

            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }
}
