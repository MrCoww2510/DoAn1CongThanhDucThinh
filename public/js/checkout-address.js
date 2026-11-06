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
    const syncContactDetails = () => {
        const name = recipientInput.value.trim() || 'Người nhận';
        const phone = phoneInput.value.trim();
        recipientDisplay.textContent = name;
        phoneDisplay.textContent = phone;

        list.querySelectorAll('[data-dialog-recipient]').forEach((element) => {
            element.textContent = name;
        });
        list.querySelectorAll('[data-dialog-phone]').forEach((element) => {
            element.textContent = phone;
        });
    };

    const syncSelectedAddress = () => {
        const selected = addressOptions.find((option) => option.checked);

        addressOptions.forEach((option) => {
            option.closest('.checkout-address-option')
                ?.classList.toggle('is-selected', option.checked);
        });

        if (selected instanceof HTMLInputElement) {
            addressDisplay.textContent = selected.dataset.addressValue ?? '';
        }
    };

    const showAddressList = () => {
        list.hidden = false;
        listActions.hidden = false;
        newAddressFields.hidden = true;
        newAddressActions.hidden = true;
        dialogTitle.textContent = 'Chọn địa chỉ nhận hàng';
    };

    const showNewAddressForm = () => {
        list.hidden = true;
        listActions.hidden = true;
        newAddressFields.hidden = false;
        newAddressActions.hidden = false;
        dialogTitle.textContent = 'Thêm địa chỉ mới';
        newAddressFields.querySelector('textarea')?.focus();
    };

    openButton.addEventListener('click', () => {
        showAddressList();
        dialog.showModal();
    });

    closeButton.addEventListener('click', () => {
        dialog.close();
    });

    showNewAddressButton.addEventListener('click', showNewAddressForm);
    cancelNewAddressButton.addEventListener('click', showAddressList);
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
