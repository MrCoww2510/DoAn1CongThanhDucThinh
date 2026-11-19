// Điều khiển hộp thoại xác nhận yêu cầu hủy đơn hàng.
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

    /** Mở hộp thoại để khách xem lại trước khi gửi yêu cầu hủy đơn. */
    openButton.addEventListener('click', () => {
        dialog.showModal();
    });

    /** Đóng hộp thoại và trả tiêu điểm về nút đã mở hộp thoại. */
    closeButton.addEventListener('click', () => {
        dialog.close();
        openButton.focus();
    });

    /** Gửi biểu mẫu hủy đơn sau khi đóng hộp thoại xác nhận. */
    confirmButton.addEventListener('click', () => {
        dialog.close();
        form.requestSubmit();
    });

    /** Khôi phục tiêu điểm bàn phím khi hộp thoại đóng bằng bất kỳ cách nào. */
    dialog.addEventListener('close', () => {
        openButton.focus();
    });
})();
