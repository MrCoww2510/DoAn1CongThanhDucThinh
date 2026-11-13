<?php

// Model truy vấn và cập nhật dữ liệu tài khoản người dùng.

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
    public function findCustomerProfile(string $userId, string $role = 'KhachHang'): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT nd.MaND, nd.HoTen, nd.SDT, nd.Email, nd.DiaChiNhanHang,
                    tk.TenDangNhap
             FROM NguoiDung nd
             INNER JOIN TaiKhoan tk ON tk.MaND = nd.MaND
             WHERE nd.MaND = :userId AND tk.VaiTro = :role
             LIMIT 1'
        );
        $stmt->execute(['userId' => $userId, 'role' => $role]);
        $profile = $stmt->fetch();

        return $profile ?: null;
    }

    // Cập nhật hồ sơ khách hàng; chỉ đổi mật khẩu khi người dùng cung cấp mật khẩu hiện tại.
    public function updateCustomerProfile(
        string $userId,
        string $name,
        string $email,
        string $phone,
        ?string $currentPassword = null,
        ?string $newPassword = null,
        string $role = 'KhachHang'
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
            $stmt->execute(['userId' => $userId, 'role' => $role]);
            $account = $stmt->fetch();

            if (!$account) {
                throw new \RuntimeException('Không tìm thấy tài khoản.');
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
                 SET HoTen = :name, Email = :email, SDT = :phone
                 WHERE MaND = :userId'
            );
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone !== '' ? $phone : null,
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

    /** Tìm tài khoản bằng mã định danh ổn định do Google cấp. */
    public function findForGoogleLogin(string $googleId): ?array
    {
        // Google sub là định danh ổn định; không dùng email làm khóa đăng nhập Google.
        $stmt = Database::connection()->prepare(
            'SELECT tk.MaTK, tk.MaND, tk.TenDangNhap, tk.VaiTro, tk.TrangThai,
                    nd.HoTen, nd.Email, nd.SDT
             FROM TaiKhoan tk
             INNER JOIN NguoiDung nd ON nd.MaND = tk.MaND
             WHERE tk.GoogleID = :googleId LIMIT 1'
        );
        $stmt->execute(['googleId' => $googleId]);
        $account = $stmt->fetch();

        return $account ?: null;
    }

    /** Tìm email đã đăng ký, để không liên kết tự động tài khoản hiện hữu. */
    public function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT tk.MaTK, tk.MaND, tk.TenDangNhap, tk.VaiTro, tk.TrangThai,
                    nd.HoTen, nd.Email, nd.SDT
             FROM TaiKhoan tk
             INNER JOIN NguoiDung nd ON nd.MaND = tk.MaND
             WHERE nd.Email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $account = $stmt->fetch();

        return $account ?: null;
    }

    /** Tạo hồ sơ/tài khoản khách Google trong transaction; password hash ngẫu nhiên không dùng để đăng nhập Google. */
    public function registerGoogle(string $googleId, string $name, string $email): array
    {
        $pdo = Database::connection();
        // Khóa theo email chuẩn hóa để hai yêu cầu Google đồng thời không tạo trùng hồ sơ.
        $lockName = 'ttg_'.substr(hash('sha256', mb_strtolower($email)), 0, 60);
        $lock = $pdo->prepare('SELECT GET_LOCK(:name, 10)');
        $lock->execute(['name' => $lockName]);
        if ((int) $lock->fetchColumn() !== 1) {
            throw new \RuntimeException('Không thể hoàn tất đăng ký bằng Google. Vui lòng thử lại.');
        }

        try {
            $pdo->beginTransaction();
            $existing = $this->findByEmail($email);
            if ($existing !== null) {
                $pdo->commit();

                return ['status' => 'existing', 'account' => $existing];
            }

            // Tạo username duy nhất theo quy ước hiện tại; mật khẩu chỉ là hash của chuỗi ngẫu nhiên.
            $localPart = strstr($email, '@', true) ?: 'google-user';
            $base = substr((string) preg_replace('/[^a-zA-Z0-9_.-]/', '', $localPart), 0, 35);
            $base = strlen($base) >= 4 ? $base : 'googleuser';
            do {
                $username = $base.'_'.bin2hex(random_bytes(5));
            } while ($this->usernameExists($username));

            $userId = 'ND'.bin2hex(random_bytes(9));
            $accountId = 'TK'.bin2hex(random_bytes(9));
            $stmt = $pdo->prepare(
                'INSERT INTO NguoiDung (MaND, HoTen, SDT, Email)
                 VALUES (:id, :name, NULL, :email)'
            );
            $stmt->execute(['id' => $userId, 'name' => mb_substr($name, 0, 100), 'email' => $email]);
            $stmt = $pdo->prepare(
                'INSERT INTO TaiKhoan
                    (MaTK, MaND, TenDangNhap, MatKhau, VaiTro, TrangThai, GoogleID)
                 VALUES (:id, :user_id, :username, :password, "KhachHang", "HoatDong", :googleId)'
            );
            $stmt->execute([
                'id' => $accountId,
                'user_id' => $userId,
                'username' => $username,
                'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
                'googleId' => $googleId,
            ]);
            $pdo->commit();

            return ['status' => 'created', 'account' => $this->findForGoogleLogin($googleId)];
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        } finally {
            $release = $pdo->prepare('SELECT RELEASE_LOCK(:name)');
            $release->execute(['name' => $lockName]);
        }
    }

    /** Liên kết chỉ đúng tài khoản đang có session; email phải khớp và GoogleID chưa được gán nơi khác. */
    public function linkGoogle(string $accountId, string $googleId, string $email): string
    {
        // Không cho phép một Google sub đã liên kết bị chuyển sang tài khoản khác.
        $alreadyLinked = $this->findForGoogleLogin($googleId);
        if ($alreadyLinked !== null && $alreadyLinked['MaTK'] !== $accountId) {
            return 'already_linked';
        }
        // Chỉ liên kết nếu email Google khớp email hồ sơ và tài khoản hiện vẫn hoạt động.
        $stmt = Database::connection()->prepare(
            'SELECT tk.MaTK, tk.VaiTro, tk.TrangThai, nd.Email
             FROM TaiKhoan tk INNER JOIN NguoiDung nd ON nd.MaND = tk.MaND
             WHERE tk.MaTK = :id LIMIT 1'
        );
        $stmt->execute(['id' => $accountId]);
        $account = $stmt->fetch();
        if (!$account || $account['VaiTro'] !== 'KhachHang') {
            return 'not_customer';
        }
        if ($account['TrangThai'] !== 'HoatDong') {
            return 'inactive';
        }
        if (mb_strtolower((string) $account['Email']) !== mb_strtolower($email)) {
            return 'email_mismatch';
        }

        $stmt = Database::connection()->prepare(
            'UPDATE TaiKhoan SET GoogleID = :googleId
             WHERE MaTK = :id AND (GoogleID IS NULL OR GoogleID = :sameGoogleId)'
        );
        $stmt->execute(['googleId' => $googleId, 'sameGoogleId' => $googleId, 'id' => $accountId]);
        if ($stmt->rowCount() === 1) {
            return 'linked';
        }

        $current = $this->findForGoogleLogin($googleId);

        return $current && $current['MaTK'] === $accountId ? 'linked' : 'already_linked';
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
