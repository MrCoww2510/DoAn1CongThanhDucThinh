// Điều khiển hộp thoại chọn, thêm và xác nhận địa chỉ giao hàng.
(() => {
    const dialog = document.querySelector('[data-address-dialog]');
    const openButton = document.querySelector('[data-open-address-dialog]');
    const closeButton = document.querySelector('[data-close-address-dialog]');
    const list = document.querySelector('[data-address-list]');
    const listActions = document.querySelector('[data-address-list-actions]');
    const newAddressFields = document.querySelector('[data-new-address-fields]');
    const newAddressActions = document.querySelector('[data-new-address-actions]');
    const showNewAddressButton = document.querySelector('[data-show-new-address]');
    const cancelNewAddressButton = document.querySelector('[data-cancel-new-address]');
    const confirmAddressButton = document.querySelector('[data-confirm-address]');
    const addressDisplay = document.querySelector('[data-selected-address]');
    const recipientDisplay = document.querySelector('[data-selected-recipient]');
    const phoneDisplay = document.querySelector('[data-selected-phone]');
    const recipientInput = document.querySelector('#recipient-name');
    const phoneInput = document.querySelector('#recipient-phone');
    const dialogTitle = document.querySelector('[data-dialog-title]');

    if (
        !(dialog instanceof HTMLDialogElement)
        || !(openButton instanceof HTMLButtonElement)
        || !(closeButton instanceof HTMLButtonElement)
        || !(list instanceof HTMLElement)
        || !(listActions instanceof HTMLElement)
        || !(newAddressFields instanceof HTMLElement)
        || !(newAddressActions instanceof HTMLElement)
        || !(showNewAddressButton instanceof HTMLButtonElement)
        || !(cancelNewAddressButton instanceof HTMLButtonElement)
        || !(confirmAddressButton instanceof HTMLButtonElement)
        || !(addressDisplay instanceof HTMLElement)
        || !(recipientDisplay instanceof HTMLElement)
        || !(phoneDisplay instanceof HTMLElement)
        || !(recipientInput instanceof HTMLInputElement)
        || !(phoneInput instanceof HTMLInputElement)
        || !(dialogTitle instanceof HTMLElement)
    ) {
        return;
    }

    const addressOptions = Array.from(
        list.querySelectorAll('input[name="deliveryAddressId"]')
    );
    /** Cập nhật thông tin người nhận ở phần xem trước và trong từng lựa chọn địa chỉ. */
    const syncContactDetails = () => {
        const name = recipientInput.value.trim() || 'Người nhận';
        const phone = phoneInput.value.trim();
        recipientDisplay.textContent = name;
        phoneDisplay.textContent = phone;

        // Hiển thị cùng một tên người nhận trong toàn bộ các lựa chọn của hộp thoại.
        list.querySelectorAll('[data-dialog-recipient]').forEach((element) => {
            element.textContent = name;
        });
        // Đồng bộ số điện thoại vào các dòng thông tin liên hệ tương ứng.
        list.querySelectorAll('[data-dialog-phone]').forEach((element) => {
            element.textContent = phone;
        });
    };

    /** Đồng bộ địa chỉ được chọn với phần xem trước và trạng thái đánh dấu trên danh sách. */
    const syncSelectedAddress = () => {
        const selected = addressOptions.find((option) => option.checked);

        // Đánh dấu trực quan đúng lựa chọn đang được chọn.
        addressOptions.forEach((option) => {
            option.closest('.checkout-address-option')
                ?.classList.toggle('is-selected', option.checked);
        });

        if (selected instanceof HTMLInputElement) {
            addressDisplay.textContent = selected.dataset.addressValue ?? '';
        }
    };

    /** Chuyển hộp thoại về chế độ chọn địa chỉ đã lưu. */
    const showAddressList = () => {
        list.hidden = false;
        listActions.hidden = false;
        newAddressFields.hidden = true;
        newAddressActions.hidden = true;
        dialogTitle.textContent = 'Chọn địa chỉ nhận hàng';
    };

    /** Chuyển hộp thoại sang nhập địa chỉ mới và đặt con trỏ vào ô địa chỉ. */
    const showNewAddressForm = () => {
        list.hidden = true;
        listActions.hidden = true;
        newAddressFields.hidden = false;
        newAddressActions.hidden = false;
        dialogTitle.textContent = 'Thêm địa chỉ mới';
        newAddressFields.querySelector('textarea')?.focus();
    };

    /** Mở hộp thoại ở chế độ chọn địa chỉ đã lưu. */
    openButton.addEventListener('click', () => {
        showAddressList();
        dialog.showModal();
    });

    /** Đóng hộp thoại mà không thay đổi lựa chọn hiện tại. */
    closeButton.addEventListener('click', () => {
        dialog.close();
    });

    showNewAddressButton.addEventListener('click', showNewAddressForm);
    cancelNewAddressButton.addEventListener('click', showAddressList);
    /** Đóng hộp thoại sau khi đồng bộ địa chỉ đang chọn lên biểu mẫu thanh toán. */
    confirmAddressButton.addEventListener('click', () => {
        syncSelectedAddress();
        dialog.close();
    });

    addressOptions.forEach((option) => {
        option.addEventListener('change', syncSelectedAddress);
    });

    recipientInput.addEventListener('input', syncContactDetails);
    phoneInput.addEventListener('input', syncContactDetails);
    syncSelectedAddress();
    syncContactDetails();

    if (dialog.hasAttribute('data-reopen-new-address')) {
        showNewAddressForm();
        dialog.showModal();
    }
})();
