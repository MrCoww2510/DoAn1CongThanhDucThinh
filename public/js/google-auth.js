(() => {
    'use strict';

    // Cùng một script phục vụ nút đăng nhập và nút liên kết trên trang tài khoản.
    const widgets = [...document.querySelectorAll('[data-google-auth]')];
    if (!widgets.length) return;

    const setMessage = (widget, message, isError = false) => {
        const target = widget.querySelector('[data-google-message]');
        if (!target) return;
        target.textContent = message;
        target.classList.toggle('is-error', isError);
        target.classList.toggle('is-success', !isError && message !== '');
    };

    const sendCredential = async (widget, credential) => {
        const button = widget.querySelector('[data-google-button]');
        setMessage(widget, 'Đang xác minh tài khoản Google…');
        button?.setAttribute('aria-busy', 'true');
        try {
            // Gửi nguyên ID Token về PHP; không giải mã hay tin claim ở phía trình duyệt.
            const body = new URLSearchParams({
                credential,
                _csrf: widget.dataset.csrf || '',
            });
            const response = await fetch(widget.dataset.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body,
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Đăng nhập Google thất bại.');
            setMessage(widget, result.message || 'Đăng nhập thành công.');
            if (result.redirect) window.location.assign(result.redirect);
            else window.setTimeout(() => window.location.reload(), 700);
        } catch (error) {
            setMessage(widget, error.message || 'Không thể kết nối máy chủ. Vui lòng thử lại.', true);
        } finally {
            button?.removeAttribute('aria-busy');
        }
    };

    const initialize = (attempt = 0) => {
        // GIS được tải async nên chờ thư viện sẵn sàng trước khi dựng nút.
        if (!window.google?.accounts?.id) {
            if (attempt < 50) window.setTimeout(() => initialize(attempt + 1), 200);
            else widgets.forEach(widget => setMessage(widget, 'Không tải được dịch vụ Google. Vui lòng tải lại trang.', true));
            return;
        }

        widgets.forEach((widget, index) => {
            const clientId = widget.dataset.clientId || '';
            if (!clientId) {
                setMessage(widget, 'Google Login chưa được cấu hình Client ID.', true);
                return;
            }
            const button = widget.querySelector('[data-google-button]');
            if (!button) return;
            window.google.accounts.id.initialize({
                client_id: clientId,
                callback: response => sendCredential(widget, response.credential),
                auto_select: false,
                cancel_on_tap_outside: true,
            });
            window.google.accounts.id.renderButton(button, {
                type: 'standard',
                theme: 'outline',
                size: 'large',
                text: index === 0 ? 'signin_with' : 'continue_with',
                shape: 'rectangular',
                width: Math.max(200, Math.min(360, Math.floor(button.getBoundingClientRect().width || 320))),
                logo_alignment: 'left',
            });
        });
    };

    initialize();
})();
