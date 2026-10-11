<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

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

            $userId = 'ND' . bin2hex(random_bytes(9));
            $accountId = 'TK' . bin2hex(random_bytes(9));

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
