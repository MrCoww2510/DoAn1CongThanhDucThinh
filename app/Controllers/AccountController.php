<?php

// Controller quản lý hồ sơ khách hàng và địa chỉ nhận hàng.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\DeliveryAddress;

final class AccountController extends Controller
{
    // Xác thực phiên và lấy tài khoản đang thao tác.
    private function requireAccountUser(): array
    {
        $user = $_SESSION['user'] ?? null;

        if (
            !is_array($user)
            || empty($user['MaND'])
            || !in_array($user['VaiTro'] ?? '', ['KhachHang', 'QuanTriVien'], true)
        ) {
            $this->redirect('login');
        }

        return $user;
    }

    // Từ chối yêu cầu nếu mã CSRF không khớp với phiên.
    private function verifyCsrf(): void
    {
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
    }

    // Chuẩn bị dữ liệu và hiển thị trang tương ứng.
    public function index(): void
    {
        $user = $this->requireAccountUser();
        $role = (string) $user['VaiTro'];
        $profile = (new Account())->findCustomerProfile((string) $user['MaND'], $role);

        if ($profile === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy tài khoản',
                'title' => 'Không tìm thấy tài khoản',
                'message' => 'Không tìm thấy thông tin tài khoản khách hàng.',
            ]);

            return;
        }

        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        $googleConfig = require BASE_PATH.'/config/google.php';
        $flash = $_SESSION['_account_flash'] ?? null;
        unset($_SESSION['_account_flash']);

        $form = [
            'name' => (string) $profile['HoTen'],
            'email' => (string) ($profile['Email'] ?? ''),
            'phone' => (string) ($profile['SDT'] ?? ''),
        ];

        $this->view('account/index', [
            'pageTitle' => 'Quản lý tài khoản',
            'user' => $user,
            'profile' => $profile,
            'form' => $form,
            'addresses' => $role === 'KhachHang'
                ? $this->addressBook((string) $user['MaND'], (string) ($profile['DiaChiNhanHang'] ?? ''))
                : [],
            'addressBookEnabled' => $role === 'KhachHang',
            'addressDraft' => null,
            'flash' => $flash,
            'csrfToken' => $_SESSION['_csrf'],
            'googleClientId' => (string) ($googleConfig['client_id'] ?? ''),
        ]);
    }

    // Cập nhật dữ liệu sau khi kiểm tra các điều kiện nghiệp vụ.
    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        $this->verifyCsrf();
        $user = $this->requireAccountUser();
        $role = (string) $user['VaiTro'];
        $form = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
        ];
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
        $errors = [];

        if ($form['name'] === '' || mb_strlen($form['name']) > 100) {
            $errors[] = 'Họ tên là bắt buộc và không được vượt quá 100 ký tự.';
        }

        if (
            mb_strlen($form['email']) > 100
            || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $errors[] = 'Địa chỉ email không hợp lệ.';
        }

        $normalizedPhone = preg_replace('/[\s().-]+/', '', $form['phone']);
        if (
            !is_string($normalizedPhone)
            || (
                $normalizedPhone !== ''
                && (
                    strlen($normalizedPhone) > 15
                    || preg_match('/\A\+?[0-9]{8,15}\z/', $normalizedPhone) !== 1
                )
            )
        ) {
            $errors[] = 'Số điện thoại cần gồm 8–15 chữ số, có thể bắt đầu bằng dấu +.';
        } else {
            $form['phone'] = $normalizedPhone;
        }

        $changingPassword = $currentPassword !== ''
            || $newPassword !== ''
            || $confirmPassword !== '';

        if ($changingPassword) {
            if ($currentPassword === '') {
                $errors[] = 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu.';
            }
            if (strlen($newPassword) < 8) {
                $errors[] = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
            }
        }

        if ($errors !== []) {
            $this->renderForm($user, $form, $errors);

            return;
        }

        try {
            (new Account())->updateCustomerProfile(
                (string) $user['MaND'],
                $form['name'],
                $form['email'],
                $form['phone'],
                $changingPassword ? $currentPassword : null,
                $changingPassword ? $newPassword : null,
                $role
            );

            $_SESSION['user']['HoTen'] = $form['name'];
            $_SESSION['user']['Email'] = $form['email'];
            $_SESSION['user']['SDT'] = $form['phone'];
            $_SESSION['_account_flash'] = [
                'type' => 'success',
                'message' => 'Thông tin tài khoản đã được cập nhật.',
            ];
            $this->redirect('account', 303);
        } catch (\PDOException $exception) {
            error_log('[Account update] '.$exception->getMessage());
            $this->renderForm(
                $user,
                $form,
                ['Không thể cập nhật tài khoản. Vui lòng kiểm tra thông tin và thử lại.']
            );
        } catch (\RuntimeException $exception) {
            $this->renderForm($user, $form, [$exception->getMessage()]);
        } catch (\Throwable $exception) {
            error_log('[Account update] '.$exception->getMessage());
            $this->renderForm(
                $user,
                $form,
                ['Đã xảy ra lỗi khi cập nhật tài khoản. Vui lòng thử lại.']
            );
        }
    }

    // Nhận và lưu địa chỉ giao hàng mới của khách hàng.
    public function addAddress(): void
    {
        $this->mutateAddress('add');
    }

    // Nhận dữ liệu và cập nhật địa chỉ giao hàng đã lưu.
    public function updateAddress(): void
    {
        $this->mutateAddress('update');
    }

    // Xóa địa chỉ giao hàng thuộc về khách hàng hiện tại.
    public function deleteAddress(): void
    {
        $this->mutateAddress('delete');
    }

    // Xử lý thêm, sửa hoặc xóa địa chỉ theo thao tác được yêu cầu.
    private function mutateAddress(string $operation): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        $this->verifyCsrf();
        $user = $this->requireAccountUser();
        if (($user['VaiTro'] ?? '') !== 'KhachHang') {
            http_response_code(403);
            exit('Chỉ khách hàng mới có thể quản lý sổ địa chỉ.');
        }

        $addressId = trim((string) ($_POST['address_id'] ?? ''));
        $address = trim((string) ($_POST['address'] ?? ''));
        $currentAddress = (string) ($_POST['current_address'] ?? '');
        $addressDraftValue = $operation === 'delete' ? $currentAddress : $address;
        $error = '';

        if ($operation !== 'delete' && ($address === '' || mb_strlen($address) > 255)) {
            $error = 'Địa chỉ là bắt buộc và không được vượt quá 255 ký tự.';
        } elseif ($operation === 'update' && $addressId === '') {
            $error = 'Địa chỉ cần cập nhật không hợp lệ.';
        } elseif ($operation === 'delete' && $addressId === '') {
            $error = 'Địa chỉ cần xóa không hợp lệ.';
        }

        if ($error !== '') {
            $this->renderForm(
                $user,
                [],
                [$error],
                ['id' => $addressId, 'address' => $addressDraftValue]
            );

            return;
        }

        try {
            $addresses = new DeliveryAddress();
            if ($operation === 'add') {
                $addresses->addForCustomer((string) $user['MaND'], $address);
            } elseif ($addressId === 'profile') {
                $updated = $operation === 'update'
                    ? $addresses->updateProfileAddressForCustomer(
                        (string) $user['MaND'],
                        $currentAddress,
                        $address
                    )
                    : $addresses->deleteProfileAddressForCustomer(
                        (string) $user['MaND'],
                        $currentAddress
                    );
                if (!$updated) {
                    throw new \RuntimeException('Không tìm thấy địa chỉ cần cập nhật.');
                }
            } else {
                if (!ctype_digit($addressId) || (int) $addressId < 1) {
                    throw new \RuntimeException('Địa chỉ cần thao tác không hợp lệ.');
                }
                $updated = $operation === 'update'
                    ? $addresses->updateForCustomer((int) $addressId, (string) $user['MaND'], $address)
                    : $addresses->deleteForCustomer((int) $addressId, (string) $user['MaND']);
                if (!$updated) {
                    throw new \RuntimeException('Không tìm thấy địa chỉ trong sổ địa chỉ của bạn.');
                }
            }

            $_SESSION['_account_flash'] = [
                'type' => 'success',
                'message' => $operation === 'add'
                    ? 'Đã thêm địa chỉ giao hàng.'
                    : ($operation === 'update' ? 'Đã cập nhật địa chỉ giao hàng.' : 'Đã xóa địa chỉ giao hàng.'),
            ];
            $this->redirect('account', 303);
        } catch (\PDOException $exception) {
            error_log('[Account address] '.$exception->getMessage());
            $this->renderForm(
                $user,
                [],
                ['Không thể lưu địa chỉ. Địa chỉ có thể đã tồn tại hoặc thông tin đã thay đổi.'],
                ['id' => $addressId, 'address' => $addressDraftValue]
            );
        } catch (\RuntimeException $exception) {
            $this->renderForm($user, [], [$exception->getMessage()], [
                'id' => $addressId,
                'address' => $addressDraftValue,
            ]);
        } catch (\Throwable $exception) {
            error_log('[Account address] '.$exception->getMessage());
            $this->renderForm(
                $user,
                [],
                ['Đã xảy ra lỗi khi quản lý địa chỉ. Vui lòng thử lại.'],
                ['id' => $addressId, 'address' => $addressDraftValue]
            );
        }
    }

    // Kết hợp địa chỉ hồ sơ với các địa chỉ nhận hàng đã lưu.
    private function addressBook(string $customerId, string $profileAddress): array
    {
        $addresses = (new DeliveryAddress())->findForCustomer($customerId);
        foreach ($addresses as $address) {
            if ((string) $address['DiaChi'] === $profileAddress && $profileAddress !== '') {
                return $addresses;
            }
        }

        if ($profileAddress !== '') {
            array_unshift($addresses, ['MaDiaChi' => 'profile', 'DiaChi' => $profileAddress]);
        }

        return $addresses;
    }

    // Chuẩn bị dữ liệu và hiển thị biểu mẫu tài khoản.
    private function renderForm(
        array $user,
        array $form,
        array $errors,
        ?array $addressDraft = null
    ): void {
        $profile = (new Account())->findCustomerProfile(
            (string) $user['MaND'],
            (string) $user['VaiTro']
        );

        if ($profile === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy tài khoản',
                'title' => 'Không tìm thấy tài khoản',
                'message' => 'Không tìm thấy thông tin tài khoản khách hàng.',
            ]);

            return;
        }

        if ($form === []) {
            $form = [
                'name' => (string) $profile['HoTen'],
                'email' => (string) ($profile['Email'] ?? ''),
                'phone' => (string) ($profile['SDT'] ?? ''),
            ];
        }

        $this->view('account/index', [
            'pageTitle' => 'Quản lý tài khoản',
            'profile' => $profile,
            'form' => $form,
            'addresses' => ($user['VaiTro'] ?? '') === 'KhachHang'
                ? $this->addressBook((string) $user['MaND'], (string) ($profile['DiaChiNhanHang'] ?? ''))
                : [],
            'addressBookEnabled' => ($user['VaiTro'] ?? '') === 'KhachHang',
            'addressDraft' => $addressDraft,
            'errors' => $errors,
            'csrfToken' => (string) ($_SESSION['_csrf'] ?? ''),
        ]);
    }
}
