<?php

// Controller xử lý đăng ký, đăng nhập và đăng xuất tài khoản.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use Google\Client as GoogleClient;

final class AuthController extends Controller
{
    /** Xác minh ID Token tại backend bằng Google API Client và đúng audience của ứng dụng. */
    private function verifiedGoogleIdentity(): ?array
    {
        // GIS gửi ID Token trong POST; giới hạn kích thước và xác thực CSRF trước khi gọi Google.
        $token = (string) ($_POST['credential'] ?? '');
        $csrf = (string) ($_POST['_csrf'] ?? '');
        $sessionCsrf = (string) ($_SESSION['_csrf'] ?? '');
        if ($token === '' || strlen($token) > 12000 || $csrf === '' || $sessionCsrf === ''
            || !hash_equals($sessionCsrf, $csrf)) {
            return null;
        }

        // verifyIdToken kiểm tra chữ ký, hạn dùng và audience theo Client ID cấu hình.
        $googleConfig = require BASE_PATH.'/config/google.php';
        $clientId = (string) ($googleConfig['client_id'] ?? '');
        if ($clientId === '' || !is_file(BASE_PATH.'/vendor/autoload.php')) {
            return null;
        }
        require_once BASE_PATH.'/vendor/autoload.php';
        $client = new GoogleClient(['client_id' => $clientId]);
        try {
            $payload = $client->verifyIdToken($token);
        } catch (\Throwable $exception) {
            error_log('[GoogleToken] '.$exception->getMessage());

            return null;
        }
        // Chỉ dùng email đã được Google xác minh; dữ liệu profile từ trình duyệt không được tin cậy.
        if (!is_array($payload)
            || empty($payload['sub'])
            || empty($payload['email'])
            || !in_array($payload['email_verified'] ?? false, [true, 1, '1', 'true'], true)
            || !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)
            || mb_strlen((string) $payload['email']) > 100) {
            return null;
        }

        return [
            'sub' => (string) $payload['sub'],
            'email' => mb_strtolower(trim((string) $payload['email'])),
            'name' => trim((string) ($payload['name'] ?? '')),
            'picture' => filter_var((string) ($payload['picture'] ?? ''), FILTER_VALIDATE_URL)
                ? (string) $payload['picture'] : '',
        ];
    }

    /** Gửi kết quả JSON cho GIS frontend mà không tiết lộ lỗi nội bộ. */
    private function googleJson(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** Đăng nhập hoặc tạo khách hàng từ ID Token đã xác minh. */
    public function googleLogin(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->googleJson(['message' => 'Phương thức không được hỗ trợ.'], 405);
        }
        try {
            $identity = $this->verifiedGoogleIdentity();
            if ($identity === null) {
                $this->googleJson(['message' => 'Không thể xác minh tài khoản Google. Vui lòng thử lại.'], 400);
            }

            // Ưu tiên tìm theo Google sub; nếu email đã có thì yêu cầu đăng nhập cũ rồi liên kết thủ công.
            $model = new Account();
            $account = $model->findForGoogleLogin($identity['sub']);
            if ($account === null) {
                $emailAccount = $model->findByEmail($identity['email']);
                if ($emailAccount !== null) {
                    $message = $emailAccount['TrangThai'] !== 'HoatDong'
                        ? 'Tài khoản hiện không hoạt động.'
                        : ($emailAccount['VaiTro'] === 'KhachHang'
                        ? 'Email này đã có tài khoản. Hãy đăng nhập bằng mật khẩu trước, sau đó liên kết Google tại trang tài khoản.'
                        : 'Email này đã được sử dụng cho một tài khoản không thể đăng nhập bằng Google.');
                    $this->googleJson(['message' => $message], 409);
                }
                $result = $model->registerGoogle($identity['sub'], $identity['name'] ?: $identity['email'], $identity['email']);
                if ($result['status'] === 'existing') {
                    $this->googleJson(['message' => 'Email này đã có tài khoản. Hãy đăng nhập bằng mật khẩu rồi liên kết Google tại trang tài khoản.'], 409);
                }
                $account = $result['account'];
            }

            if (!is_array($account) || ($account['VaiTro'] ?? '') !== 'KhachHang') {
                $this->googleJson(['message' => 'Tài khoản này không được phép đăng nhập bằng Google.'], 403);
            }
            if (($account['TrangThai'] ?? '') !== 'HoatDong') {
                $this->googleJson(['message' => 'Tài khoản hiện không hoạt động.'], 403);
            }

            // Không cho Google cấp role; phiên chỉ được tạo sau khi xác nhận đây là khách đang hoạt động.
            session_regenerate_id(true);
            $account['GooglePicture'] = $identity['picture'];
            $_SESSION['user'] = $account;
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
            $app = require BASE_PATH.'/config/app.php';
            $this->googleJson(['redirect' => rtrim((string) ($app['base_url'] ?? ''), '/').'/']);
        } catch (\Throwable $exception) {
            error_log('[GoogleLogin] '.$exception->getMessage());
            $this->googleJson(['message' => 'Đăng nhập Google chưa thể hoàn tất. Vui lòng thử lại sau.'], 503);
        }
    }

    /** Liên kết Google chỉ sau khi người dùng đã đăng nhập tài khoản hiện hữu. */
    public function linkGoogle(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->googleJson(['message' => 'Phương thức không được hỗ trợ.'], 405);
        }
        // Chỉ phiên khách hàng đã đăng nhập mới được yêu cầu liên kết tài khoản.
        $user = $_SESSION['user'] ?? null;
        if (!is_array($user) || empty($user['MaTK']) || ($user['VaiTro'] ?? '') !== 'KhachHang') {
            $this->googleJson(['message' => 'Hãy đăng nhập tài khoản khách hàng trước khi liên kết Google.'], 401);
        }
        try {
            $identity = $this->verifiedGoogleIdentity();
            if ($identity === null) {
                $this->googleJson(['message' => 'Không thể xác minh tài khoản Google hoặc phiên đã hết hạn.'], 400);
            }
            $result = (new Account())->linkGoogle((string) $user['MaTK'], $identity['sub'], $identity['email']);
            $messages = [
                'linked' => 'Đã liên kết tài khoản Google thành công.',
                'email_mismatch' => 'Email Google phải trùng với email của tài khoản đang đăng nhập.',
                'already_linked' => 'Tài khoản Google này đã được liên kết với tài khoản khác.',
                'inactive' => 'Tài khoản hiện không hoạt động.',
                'not_customer' => 'Chỉ tài khoản khách hàng mới được liên kết Google.',
            ];
            $success = $result === 'linked';
            $this->googleJson(['message' => $messages[$result] ?? 'Không thể liên kết tài khoản Google.', 'success' => $success], $success ? 200 : 409);
        } catch (\Throwable $exception) {
            error_log('[GoogleLink] '.$exception->getMessage());
            $this->googleJson(['message' => 'Không thể liên kết Google lúc này. Vui lòng thử lại sau.'], 503);
        }
    }

    // Xác thực thông tin đăng nhập và tạo phiên người dùng.
    public function login(): void
    {
        if (isset($_SESSION['user'])) {
            $this->redirectByRole($_SESSION['user']['VaiTro']);
        }

        $error = '';
        $identity = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $identity = trim((string) ($_POST['identity'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $csrf = (string) ($_POST['_csrf'] ?? '');

            if (
                !isset($_SESSION['_csrf'])
                || !hash_equals($_SESSION['_csrf'], $csrf)
            ) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Hãy tải lại trang.';
            } elseif ($identity === '' || $password === '') {
                $error = 'Vui lòng nhập tên đăng nhập/email và mật khẩu.';
            } else {
                $account = (new Account())->findForLogin($identity);

                if (
                    $account === null
                    || !password_verify($password, $account['MatKhau'])
                ) {
                    $error = 'Thông tin đăng nhập không chính xác.';
                } elseif ($account['TrangThai'] !== 'HoatDong') {
                    $error = 'Tài khoản hiện không hoạt động.';
                } else {
                    session_regenerate_id(true);

                    unset($account['MatKhau']);

                    $_SESSION['user'] = $account;

                    // Tao token CSRF moi sau khi dang nhap thanh cong.
                    // Nut dang xuat trong layout can token nay.
                    $_SESSION['_csrf'] = bin2hex(random_bytes(32));

                    $this->redirectByRole($account['VaiTro']);
                }
            }
        }

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $googleConfig = require BASE_PATH.'/config/google.php';

        $this->view('auth/login', [
            'pageTitle' => 'Đăng nhập',
            'error' => $error,
            'identity' => $identity,
            'csrfToken' => $_SESSION['_csrf'],
            'googleClientId' => (string) ($googleConfig['client_id'] ?? ''),
        ]);
    }

    // Kiểm tra dữ liệu và tạo tài khoản khách hàng mới.
    public function register(): void
    {
        if (isset($_SESSION['user'])) {
            $this->redirectByRole($_SESSION['user']['VaiTro']);
        }

        $error = '';
        $name = '';
        $username = '';
        $email = '';
        $phone = '';

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $username = trim((string) ($_POST['username'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');
            $csrf = (string) ($_POST['_csrf'] ?? '');

            if (
                !isset($_SESSION['_csrf'])
                || !hash_equals($_SESSION['_csrf'], $csrf)
            ) {
                $error = 'Phiên gửi biểu mẫu không hợp lệ. Hãy tải lại trang.';
            } elseif (
                $name === ''
                || $username === ''
                || $email === ''
                || $password === ''
            ) {
                $error = 'Vui lòng điền đầy đủ các trường bắt buộc.';
            } elseif (
                mb_strlen($name) > 100
                || mb_strlen($username) > 50
                || mb_strlen($email) > 100
                || mb_strlen($phone) > 15
            ) {
                $error = 'Một hoặc nhiều trường vượt quá độ dài cho phép.';
            } elseif (
                !preg_match('/\A[a-zA-Z0-9_.-]{4,50}\z/', $username)
            ) {
                $error = 'Tên đăng nhập cần từ 4 ký tự, chỉ gồm chữ, số, dấu chấm, gạch ngang hoặc gạch dưới.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Địa chỉ email không hợp lệ.';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } elseif (strlen($password) < 8) {
                $error = 'Mật khẩu phải có ít nhất 8 ký tự.';
            } else {
                $model = new Account();

                if ($model->usernameExists($username)) {
                    $error = 'Tên đăng nhập đã được sử dụng.';
                } elseif ($model->emailExists($email)) {
                    $error = 'Email đã được sử dụng.';
                } else {
                    try {
                        $model->register(
                            $name,
                            $username,
                            $email,
                            $phone,
                            $password
                        );

                        unset($_SESSION['_csrf']);
                        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
                        $_SESSION['_register_success'] = true;

                        $this->redirect('login');
                    } catch (\PDOException $exception) {
                        error_log('[Register] '.$exception->getMessage());
                        $error = 'Không thể tạo tài khoản. Hãy kiểm tra dữ liệu hoặc thử lại.';
                    } catch (\Throwable $exception) {
                        error_log('[Register] '.$exception->getMessage());
                        $error = 'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.';
                    }
                }
            }
        }

        $this->view('auth/register', [
            'pageTitle' => 'Đăng ký tài khoản',
            'error' => $error,
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'csrfToken' => $_SESSION['_csrf'],
        ]);
    }

    // Kết thúc phiên đăng nhập sau khi kiểm tra yêu cầu và CSRF.
    public function logout(): void
    {
        // Chỉ chấp nhận POST
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        // Chỉ coi là đã đăng nhập khi session có thông tin tài khoản hợp lệ.
        if (
            !isset($_SESSION['user'])
            || !is_array($_SESSION['user'])
            || empty($_SESSION['user']['MaTK'])
        ) {
            unset($_SESSION['user']);

            $config = require BASE_PATH.'/config/app.php';
            $baseUrl = rtrim($config['base_url'] ?? '', '/');

            header('Location: '.$baseUrl.'/login');
            exit;
        }

        // Kiểm tra CSRF khi đang đăng nhập
        $csrf = (string) ($_POST['_csrf'] ?? '');
        $sessionCsrf = (string) ($_SESSION['_csrf'] ?? '');

        if (
            $csrf === ''
            || $sessionCsrf === ''
            || !hash_equals($sessionCsrf, $csrf)
        ) {
            http_response_code(403);
            exit('Phiên không hợp lệ. Hãy tải lại trang rồi thử lại.');
        }

        // Xóa thông tin đăng nhập
        unset($_SESSION['user']);

        // Tạo token mới cho session hiện tại
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));

        $config = require BASE_PATH.'/config/app.php';
        $baseUrl = rtrim($config['base_url'] ?? '', '/');

        header('Location: '.$baseUrl.'/');
        exit;
    }

    // Chuyển người dùng sau đăng nhập theo vai trò tài khoản.
    private function redirectByRole(string $role): never
    {
        if ($role === 'QuanTriVien') {
            $this->redirect('admin');
        }

        if ($role === 'NhanVien') {
            $this->redirect('');
        }

        $this->redirect('');
    }
}
