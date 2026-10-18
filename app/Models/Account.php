<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Account
{
    /**
     * Lấy tối đa 200 tài khoản, có thể lọc theo tên, email hoặc tên đăng nhập.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchForManagement(string $query = ''): array
    {
        $sql = '
            SELECT tk.MaTK, tk.MaND, tk.TenDangNhap, tk.VaiTro, tk.TrangThai,
                   nd.HoTen, nd.Email, nd.SDT
            FROM TaiKhoan tk
            INNER JOIN NguoiDung nd ON nd.MaND = tk.MaND
        ';
        $params = [];

        if ($query !== '') {
            $sql .= ' WHERE tk.TenDangNhap LIKE :username
                       OR nd.HoTen LIKE :name
                       OR nd.Email LIKE :email';
            $needle = '%'.$query.'%';
            $params = ['username' => $needle, 'name' => $needle, 'email' => $needle];
        }

        $sql .= ' ORDER BY tk.VaiTro, nd.HoTen LIMIT 200';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Tìm thông tin một tài khoản dùng cho màn hình quản lý.
    public function findForManagement(string $accountId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT tk.MaTK, tk.MaND, tk.TenDangNhap, tk.VaiTro, tk.TrangThai,
                    nd.HoTen, nd.Email, nd.SDT
             FROM TaiKhoan tk
             INNER JOIN NguoiDung nd ON nd.MaND = tk.MaND
             WHERE tk.MaTK = :id LIMIT 1'
        );
        $stmt->execute(['id' => $accountId]);
        $account = $stmt->fetch();

        return $account ?: null;
    }

    // Tạo hồ sơ người dùng và tài khoản nhân viên trong cùng giao dịch.
    public function createStaff(
        string $name,
        string $username,
        string $email,
        string $phone,
        string $password
    ): void {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $userId = 'ND'.bin2hex(random_bytes(9));
            $accountId = 'TK'.bin2hex(random_bytes(9));
            $stmt = $pdo->prepare(
                'INSERT INTO NguoiDung (MaND, HoTen, SDT, Email)
                 VALUES (:id, :name, :phone, :email)'
            );
            $stmt->execute([
                'id' => $userId, 'name' => $name, 'phone' => $phone ?: null, 'email' => $email,
            ]);
            $stmt = $pdo->prepare(
                'INSERT INTO TaiKhoan
                    (MaTK, MaND, TenDangNhap, MatKhau, VaiTro, TrangThai)
                 VALUES (:id, :user_id, :username, :password, "NhanVien", "HoatDong")'
            );
            $stmt->execute([
                'id' => $accountId,
                'user_id' => $userId,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    // Cập nhật thông tin hồ sơ và tên đăng nhập của nhân viên.
    public function updateStaff(
        string $accountId,
        string $name,
        string $username,
        string $email,
        string $phone
    ): bool {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT MaND FROM TaiKhoan
                 WHERE MaTK = :id AND VaiTro = "NhanVien" FOR UPDATE'
            );
            $stmt->execute(['id' => $accountId]);
            $userId = $stmt->fetchColumn();
            if (!$userId) {
                $pdo->rollBack();

                return false;
            }

            $stmt = $pdo->prepare(
                'UPDATE NguoiDung SET HoTen = :name, Email = :email, SDT = :phone
                 WHERE MaND = :user_id'
            );
            $stmt->execute([
                'name' => $name, 'email' => $email, 'phone' => $phone ?: null, 'user_id' => $userId,
            ]);
            $stmt = $pdo->prepare(
                'UPDATE TaiKhoan SET TenDangNhap = :username WHERE MaTK = :id'
            );
            $stmt->execute(['username' => $username, 'id' => $accountId]);
            $pdo->commit();

            return true;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    // Kiểm tra tên đăng nhập đã thuộc tài khoản khác hay chưa.
    public function usernameUsedByOther(string $username, string $accountId = ''): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM TaiKhoan WHERE TenDangNhap = :username AND MaTK <> :id LIMIT 1'
        );
        $stmt->execute(['username' => $username, 'id' => $accountId]);

        return (bool) $stmt->fetchColumn();
    }

    // Kiểm tra email đã thuộc tài khoản khác hay chưa.
    public function emailUsedByOther(string $email, string $accountId = ''): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM NguoiDung nd
             INNER JOIN TaiKhoan tk ON tk.MaND = nd.MaND
             WHERE nd.Email = :email AND tk.MaTK <> :id LIMIT 1'
        );
        $stmt->execute(['email' => $email, 'id' => $accountId]);

        return (bool) $stmt->fetchColumn();
    }

    // Lấy hồ sơ khách hàng dùng cho trang tài khoản và biểu mẫu đặt hàng.
    public function findCustomerProfile(string $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT nd.MaND, nd.HoTen, nd.SDT, nd.Email, nd.DiaChiNhanHang
             FROM NguoiDung nd
             INNER JOIN TaiKhoan tk ON tk.MaND = nd.MaND
             WHERE nd.MaND = :userId AND tk.VaiTro = :role
             LIMIT 1'
        );
        $stmt->execute(['userId' => $userId, 'role' => 'KhachHang']);
        $profile = $stmt->fetch();

        return $profile ?: null;
    }

    // Cập nhật hồ sơ khách hàng; chỉ đổi mật khẩu khi người dùng cung cấp mật khẩu hiện tại.
    public function updateCustomerProfile(
        string $userId,
        string $name,
        string $email,
        string $phone,
        string $address,
        ?string $currentPassword = null,
        ?string $newPassword = null
    ): void {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT tk.MaTK, tk.MatKhau
                 FROM TaiKhoan tk
                 WHERE tk.MaND = :userId AND tk.VaiTro = :role
                 FOR UPDATE'
            );
            $stmt->execute(['userId' => $userId, 'role' => 'KhachHang']);
            $account = $stmt->fetch();

            if (!$account) {
                throw new \RuntimeException('Không tìm thấy tài khoản khách hàng.');
            }

            $stmt = $pdo->prepare(
                'SELECT 1 FROM NguoiDung
                 WHERE Email = :email AND MaND <> :userId LIMIT 1'
            );
            $stmt->execute(['email' => $email, 'userId' => $userId]);
            if ($stmt->fetchColumn()) {
                throw new \RuntimeException('Email này đã được sử dụng bởi tài khoản khác.');
            }

            if ($currentPassword !== null || $newPassword !== null) {
                if (
                    $currentPassword === null
                    || $newPassword === null
                    || !password_verify($currentPassword, (string) $account['MatKhau'])
                ) {
                    throw new \RuntimeException('Mật khẩu hiện tại không chính xác.');
                }

                $stmt = $pdo->prepare('UPDATE TaiKhoan SET MatKhau = :password WHERE MaTK = :accountId');
                $stmt->execute([
                    'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                    'accountId' => $account['MaTK'],
                ]);
            }

            $stmt = $pdo->prepare(
                'UPDATE NguoiDung
                 SET HoTen = :name, Email = :email, SDT = :phone, DiaChiNhanHang = :address
                 WHERE MaND = :userId'
            );
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone !== '' ? $phone : null,
                'address' => $address !== '' ? $address : null,
                'userId' => $userId,
            ]);

            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    // Đổi trạng thái tài khoản, đồng thời bảo vệ quản trị viên cuối cùng.
    public function changeStatus(string $accountId, string $status): string
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $admins = $pdo->query(
                'SELECT MaTK, TrangThai FROM TaiKhoan
                 WHERE VaiTro = "QuanTriVien" FOR UPDATE'
            )->fetchAll();
            $stmt = $pdo->prepare(
                'SELECT VaiTro, TrangThai FROM TaiKhoan WHERE MaTK = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $accountId]);
            $target = $stmt->fetch();

            if (!$target) {
                $pdo->rollBack();

                return 'missing';
            }
            if ($target['VaiTro'] === 'QuanTriVien' && $target['TrangThai'] === 'HoatDong'
                && $status !== 'HoatDong') {
                $activeAdmins = array_filter(
                    $admins,
                    static fn (array $admin): bool => $admin['TrangThai'] === 'HoatDong'
                );
                if (count($activeAdmins) <= 1) {
                    $pdo->rollBack();

                    return 'last_admin';
                }
            }

            $stmt = $pdo->prepare('UPDATE TaiKhoan SET TrangThai = :status WHERE MaTK = :id');
            $stmt->execute(['status' => $status, 'id' => $accountId]);
            $pdo->commit();

            return 'updated';
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    // Tìm tài khoản theo tên đăng nhập hoặc email.
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

    // Kiểm tra tên đăng nhập đã tồn tại chưa.
    public function usernameExists(string $username): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM TaiKhoan WHERE TenDangNhap = :username LIMIT 1'
        );
        $stmt->execute(['username' => $username]);

        return (bool) $stmt->fetchColumn();
    }

    // Kiểm tra email đã tồn tại chưa.
    public function emailExists(string $email): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM NguoiDung WHERE Email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    // Tạo tài khoản khách hàng và lưu mật khẩu dạng hash.
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
