(() => {
    const dialog = document.querySelector('[data-order-cancel-dialog]');
    const form = document.querySelector('[data-order-cancel-form]');
    const openButton = document.querySelector('[data-order-cancel-open]');
    const closeButton = document.querySelector('[data-order-cancel-close]');
    const confirmButton = document.querySelector('[data-order-cancel-confirm]');

    if (
        !(dialog instanceof HTMLDialogElement)
        || !(form instanceof HTMLFormElement)
        || !(openButton instanceof HTMLButtonElement)
        || !(closeButton instanceof HTMLButtonElement)
        || !(confirmButton instanceof HTMLButtonElement)
    ) {
        return;
    }

    openButton.addEventListener('click', () => {
        dialog.showModal();
    });

    closeButton.addEventListener('click', () => {
        dialog.close();
        openButton.focus();
    });

    confirmButton.addEventListener('click', () => {
        dialog.close();
        form.requestSubmit();
    });

    dialog.addEventListener('close', () => {
        openButton.focus();
    });
})();
