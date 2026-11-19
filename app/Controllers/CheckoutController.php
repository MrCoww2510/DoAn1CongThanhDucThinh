<?php

// Controller xử lý quy trình đặt hàng và thanh toán tại bước checkout.

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\Cart;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\Promotion;
use App\Services\SePayPayment;

final class CheckoutController extends Controller
{
    // Yêu cầu đăng nhập và lấy thông tin khách đang đặt hàng.
    private function requireCustomer(): array
    {
        $user = $_SESSION['user'] ?? null;

        if (
            !is_array($user)
            || empty($user['MaND'])
            || ($user['VaiTro'] ?? '') !== 'KhachHang'
        ) {
            $this->redirect('login');
        }

        return $user;
    }

    // Kiểm tra token CSRF của biểu mẫu thanh toán.
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

    // Hiển thị biểu mẫu đặt hàng cùng nội dung giỏ hàng.
    public function index(): void
    {
        $user = $this->requireCustomer();
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));

        $cart = (new Cart())->findItems((string) $user['MaND']);
        $profile = (new Account())->findCustomerProfile((string) $user['MaND']);
        $addresses = $this->findDeliveryAddresses(
            (string) $user['MaND'],
            (string) ($profile['DiaChiNhanHang'] ?? '')
        );
        $selectedAddressId = (string) (
            $_SESSION['_checkout_selected_address_id']
                ?? ($addresses[0]['id'] ?? '')
        );
        unset($_SESSION['_checkout_selected_address_id']);

        $form = array_merge([
            'recipientName' => (string) ($profile['HoTen'] ?? $user['HoTen'] ?? ''),
            'recipientPhone' => (string) ($profile['SDT'] ?? $user['SDT'] ?? ''),
            'deliveryAddressId' => $selectedAddressId,
            'newAddress' => '',
            'paymentMethod' => 'COD',
        ], is_array($_SESSION['_checkout_form'] ?? null) ? $_SESSION['_checkout_form'] : []);
        unset($_SESSION['_checkout_form']);

        $voucherDisplay = $this->voucherDisplay((string) $user['MaND'], (float) $cart['total']);
        $_SESSION['_checkout_quoted_total'] = number_format((float) $voucherDisplay['total'], 2, '.', '');
        $_SESSION['_checkout_quote_customer'] = (string) $user['MaND'];
        $this->view('checkout/index', [
            'pageTitle' => 'Thanh toán',
            'items' => $cart['items'],
            'total' => $cart['total'],
            'csrfToken' => $_SESSION['_csrf'],
            'errors' => [],
            'addresses' => $addresses,
            'form' => $form,
            'voucher' => $voucherDisplay,
        ]);
    }

    // Áp dụng hoặc gỡ voucher; số giảm chỉ được dùng làm hiển thị, đơn hàng sẽ tính lại.
    public function applyVoucher(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }
        $user = $this->requireCustomer();
        $this->verifyCsrf();
        $customerId = (string) $user['MaND'];
        $form = [
            'recipientName' => trim((string) ($_POST['recipientName'] ?? '')),
            'recipientPhone' => trim((string) ($_POST['recipientPhone'] ?? '')),
            'deliveryAddressId' => trim((string) ($_POST['deliveryAddressId'] ?? '')),
            'newAddress' => '',
            'paymentMethod' => trim((string) ($_POST['paymentMethod'] ?? 'COD')),
        ];
        $_SESSION['_checkout_form'] = $form;
        $_SESSION['_checkout_selected_address_id'] = $form['deliveryAddressId'];
        $cart = (new Cart())->findItems($customerId);
        $errors = [];
        if ((string) ($_POST['voucher_action'] ?? '') === 'clear') {
            unset($_SESSION['_checkout_voucher'], $_SESSION['_checkout_voucher_customer']);
        } else {
            $code = strtoupper(trim((string) ($_POST['voucher_code'] ?? '')));
            try {
                (new Promotion())->previewVoucher($code, $customerId, (float) $cart['total']);
                $_SESSION['_checkout_voucher'] = $code;
                $_SESSION['_checkout_voucher_customer'] = $customerId;
            } catch (\RuntimeException $exception) {
                unset($_SESSION['_checkout_voucher'], $_SESSION['_checkout_voucher_customer']);
                $errors[] = $exception->getMessage();
            }
        }
        $this->renderCheckout($cart, $form, $errors, $customerId);
    }

    // Lưu địa chỉ giao hàng mới trong quá trình thanh toán.
    public function addAddress(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        $user = $this->requireCustomer();
        $this->verifyCsrf();
        $address = trim((string) ($_POST['newAddress'] ?? ''));
        $cart = (new Cart())->findItems((string) $user['MaND']);
        $form = [
            'recipientName' => trim((string) ($_POST['recipientName'] ?? '')),
            'recipientPhone' => trim((string) ($_POST['recipientPhone'] ?? '')),
            'deliveryAddressId' => trim((string) ($_POST['deliveryAddressId'] ?? '')),
            'newAddress' => $address,
            'paymentMethod' => trim((string) ($_POST['paymentMethod'] ?? 'COD')),
        ];

        if ($address === '' || mb_strlen($address) > 255) {
            $this->renderCheckout(
                $cart,
                $form,
                ['Địa chỉ mới là bắt buộc và không được vượt quá 255 ký tự.'],
                (string) $user['MaND']
            );

            return;
        }

        try {
            $addressId = (new DeliveryAddress())->addForCustomer(
                (string) $user['MaND'],
                $address
            );
            $profile = (new Account())->findCustomerProfile((string) $user['MaND']);
            $_SESSION['_checkout_selected_address_id'] =
                (string) ($profile['DiaChiNhanHang'] ?? '') === $address
                    ? 'profile'
                    : (string) $addressId;
            $this->redirect('checkout', 303);
        } catch (\PDOException $exception) {
            error_log('[Checkout address] '.$exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể lưu địa chỉ lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        } catch (\RuntimeException $exception) {
            $this->renderCheckout(
                $cart,
                $form,
                [$exception->getMessage()],
                (string) $user['MaND']
            );
        } catch (\Throwable $exception) {
            error_log('[Checkout address] '.$exception->getMessage());
            $this->renderCheckout(
                $cart,
                $form,
                ['Không thể lưu địa chỉ lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        }
    }

    // Kiểm tra dữ liệu nhận hàng và tạo đơn hàng theo phương thức đã chọn.
    public function placeOrder(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }

        $user = $this->requireCustomer();
        $this->verifyCsrf();

        $form = [
            'recipientName' => trim((string) ($_POST['recipientName'] ?? '')),
            'recipientPhone' => trim((string) ($_POST['recipientPhone'] ?? '')),
            'deliveryAddressId' => trim((string) ($_POST['deliveryAddressId'] ?? '')),
            'paymentMethod' => trim((string) ($_POST['paymentMethod'] ?? '')),
        ];
        $errors = [];

        if ($form['recipientName'] === '') {
            $errors[] = 'Vui lòng nhập tên người nhận.';
        } elseif (mb_strlen($form['recipientName']) > 100) {
            $errors[] = 'Tên người nhận không được vượt quá 100 ký tự.';
        }

        $normalizedPhone = preg_replace(
            '/[\s().-]+/',
            '',
            $form['recipientPhone']
        );

        if (
            !is_string($normalizedPhone)
            || strlen($normalizedPhone) > 15
            || preg_match('/\A\+?[0-9]{8,15}\z/', $normalizedPhone) !== 1
        ) {
            $errors[] = 'Số điện thoại cần gồm 8–15 chữ số, có thể bắt đầu bằng dấu +; tối đa 15 ký tự sau chuẩn hóa.';
        } else {
            $form['recipientPhone'] = $normalizedPhone;
        }

        $profile = (new Account())->findCustomerProfile((string) $user['MaND']);
        $deliveryAddress = $this->resolveDeliveryAddress(
            (string) $user['MaND'],
            $form['deliveryAddressId'],
            (string) ($profile['DiaChiNhanHang'] ?? '')
        );

        if ($deliveryAddress === null) {
            $errors[] = 'Vui lòng chọn địa chỉ giao hàng hợp lệ hoặc thêm địa chỉ mới.';
        }

        if (!in_array($form['paymentMethod'], ['COD', 'Online'], true)) {
            $errors[] = 'Phương thức thanh toán không hợp lệ.';
        } elseif (
            $form['paymentMethod'] === 'Online'
            && !(new SePayPayment())->isConfigured()
        ) {
            $errors[] = 'SePay Sandbox chưa được cấu hình đầy đủ. Vui lòng thử lại sau.';
        }

        $cart = (new Cart())->findItems((string) $user['MaND']);

        if ($cart['items'] === []) {
            $errors[] = 'Giỏ hàng đang trống. Hãy chọn sản phẩm trước khi thanh toán.';
        }

        if ($errors !== []) {
            $this->renderCheckout($cart, $form, $errors, (string) $user['MaND']);

            return;
        }

        try {
            $order = (new Order())->placeOrder(
                (string) $user['MaND'],
                $form['recipientName'],
                (string) $deliveryAddress,
                $form['recipientPhone'],
                $form['paymentMethod'],
                (string) ($_SESSION['_checkout_voucher'] ?? ''),
                (($_SESSION['_checkout_quote_customer'] ?? '') === (string) $user['MaND'])
                    ? (string) ($_SESSION['_checkout_quoted_total'] ?? '')
                    : ''
            );
            unset($_SESSION['_checkout_voucher'], $_SESSION['_checkout_voucher_customer'], $_SESSION['_checkout_form'], $_SESSION['_checkout_quoted_total'], $_SESSION['_checkout_quote_customer']);

            if ($form['paymentMethod'] === 'Online') {
                $this->redirect(
                    'payments/sepay/start?id='.rawurlencode($order['MaDH']),
                    303
                );
            }

            $this->redirect(
                'checkout/success?id='.rawurlencode($order['MaDH']),
                303
            );
        } catch (\PDOException $exception) {
            error_log('[Checkout] '.$exception->getMessage());
            $this->renderCheckout(
                (new Cart())->findItems((string) $user['MaND']),
                $form,
                ['Không thể tạo đơn hàng lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        } catch (\RuntimeException $exception) {
            $this->renderCheckout(
                (new Cart())->findItems((string) $user['MaND']),
                $form,
                [$exception->getMessage()],
                (string) $user['MaND']
            );
        } catch (\Throwable $exception) {
            error_log('[Checkout] '.$exception->getMessage());
            $this->renderCheckout(
                (new Cart())->findItems((string) $user['MaND']),
                $form,
                ['Không thể tạo đơn hàng lúc này. Vui lòng thử lại.'],
                (string) $user['MaND']
            );
        }
    }

    // Hiển thị kết quả đặt hàng thành công.
    public function success(): void
    {
        $user = $this->requireCustomer();
        $orderId = trim((string) ($_GET['id'] ?? ''));
        $receipt = $orderId === ''
            ? null
            : (new Order())->findForCustomer(
                $orderId,
                (string) $user['MaND']
            );

        if ($receipt === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'pageTitle' => 'Không tìm thấy đơn hàng',
                'title' => 'Không tìm thấy đơn hàng',
                'message' => 'Đơn hàng không tồn tại hoặc không thuộc tài khoản của bạn.',
            ], 'customer');

            return;
        }

        $this->view('checkout/success', [
            'pageTitle' => 'Đặt hàng thành công',
            'order' => $receipt['order'],
            'items' => $receipt['items'],
        ]);
    }

    // Dựng lại trang thanh toán với dữ liệu và lỗi đã có.
    private function renderCheckout(
        array $cart,
        array $form,
        array $errors,
        string $customerId
    ): void {
        $profile = (new Account())->findCustomerProfile($customerId);

        $voucherDisplay = $this->voucherDisplay($customerId, (float) ($cart['total'] ?? 0));
        $_SESSION['_checkout_quoted_total'] = number_format((float) $voucherDisplay['total'], 2, '.', '');
        $_SESSION['_checkout_quote_customer'] = $customerId;
        $this->view('checkout/index', [
            'pageTitle' => 'Thanh toán',
            'items' => $cart['items'],
            'total' => $cart['total'],
            'csrfToken' => (string) ($_SESSION['_csrf'] ?? ''),
            'errors' => $errors,
            'addresses' => $this->findDeliveryAddresses(
                $customerId,
                (string) ($profile['DiaChiNhanHang'] ?? '')
            ),
            'form' => $form,
            'voucher' => $voucherDisplay,
        ]);
    }

    /**
     * Tính dữ liệu voucher cần hiển thị cho khách; kiểm tra chủ sở hữu dữ liệu trong session và bỏ voucher không còn hợp lệ.
     * @param $customerId Mã khách hàng dùng để giới hạn dữ liệu.
     * @param $subtotal Tổng tiền hàng trước khi áp dụng voucher.
     * @return array Mảng dữ liệu theo cấu trúc được xây dựng trong phương thức.
     */
    private function voucherDisplay(string $customerId, float $subtotal): array
    {
        if (isset($_SESSION['_checkout_quote_customer'])
            && (string) $_SESSION['_checkout_quote_customer'] !== $customerId) {
            unset($_SESSION['_checkout_quoted_total'], $_SESSION['_checkout_quote_customer'], $_SESSION['_checkout_form']);
        }
        if (isset($_SESSION['_checkout_voucher_customer'])
            && (string) $_SESSION['_checkout_voucher_customer'] !== $customerId) {
            unset($_SESSION['_checkout_voucher'], $_SESSION['_checkout_voucher_customer'], $_SESSION['_checkout_quoted_total']);
        }
        $code = (string) ($_SESSION['_checkout_voucher'] ?? '');
        if ($code === '') {
            return ['code' => '', 'discount' => 0.0, 'total' => $subtotal, 'error' => ''];
        }
        try {
            return (new Promotion())->previewVoucher($code, $customerId, $subtotal) + ['error' => ''];
        } catch (\RuntimeException $exception) {
            unset($_SESSION['_checkout_voucher'], $_SESSION['_checkout_voucher_customer']);
            return ['code' => '', 'discount' => 0.0, 'total' => $subtotal, 'error' => $exception->getMessage()];
        }
    }

    /**
     * @return array<int, array{id: string, address: string, isDefault: bool}>
     */
    private function findDeliveryAddresses(string $customerId, string $profileAddress): array
    {
        $addresses = [];

        if (trim($profileAddress) !== '') {
            $addresses[] = [
                'id' => 'profile',
                'address' => $profileAddress,
                'isDefault' => true,
            ];
        }

        foreach ((new DeliveryAddress())->findForCustomer($customerId) as $address) {
            if (
                trim($profileAddress) !== ''
                && (string) $address['DiaChi'] === $profileAddress
            ) {
                continue;
            }

            $addresses[] = [
                'id' => (string) $address['MaDiaChi'],
                'address' => (string) $address['DiaChi'],
                'isDefault' => $addresses === [],
            ];
        }

        return $addresses;
    }

    // Xác định địa chỉ giao hàng theo lựa chọn và dữ liệu gửi lên.
    private function resolveDeliveryAddress(
        string $customerId,
        string $addressId,
        string $profileAddress
    ): ?string {
        if ($addressId === 'profile') {
            return trim($profileAddress) === '' ? null : $profileAddress;
        }

        if ($addressId === '' || !ctype_digit($addressId)) {
            return null;
        }

        return (new DeliveryAddress())->findForCustomerById(
            (int) $addressId,
            $customerId
        );
    }
}
