$(document).on('click', '.btn-confirm', function (e) {
    e.preventDefault();

    const isTruthy = (value) => {
        return value === true || value === 'true' || value === 1 || value === '1';
    };

    const button = $(this);
    const url = button.data('url') || button.attr('href');
    const title = button.data('confirmTitle') || 'Are you sure?';
    const text = button.data('confirmText') || 'This action cannot be undone.';
    const confirmButton = button.data('confirmButton') || 'Yes, proceed!';
    const cancelButton = button.data('cancelButton') || 'Cancel';
    const pjaxContainer = button.data('pjaxId') || false;
    const useAjax = isTruthy(button.data('confirmAjax'));
    const confirm = isTruthy(button.data('confirmSwal'));
    const recordId = button.data('id') || null;

    console.log(button.data());

    const sendAjax = () => {
        if (!recordId) {
            Swal.fire({
                title: 'Error!',
                text: 'Missing record ID. Cannot proceed.',
                icon: 'error',
                customClass: {
                    popup: 'swal2-confirm-popup',
                    title: 'swal2-confirm-title',
                    htmlContainer: 'swal2-confirm-text',
                    confirmButton: 'swal2-confirm-btn'
                }
            });
            return;
        }

        const postData = {
            '_csrf': yii.getCsrfToken(),
            'id': recordId
        };

        Swal.showLoading(); // ✅ Show the Swal spinner

        $.ajax({
            url: url,
            type: 'POST',
            data: postData,
            success: function (response) {
                Swal.fire({
                    title: response.success ? 'Success!' : 'Error!',
                    text: response.message || (response.success ? 'Operation completed successfully.' : 'An error occurred during the operation.'),
                    icon: response.success ? 'success' : 'error',
                    timer: 1500, // Auto close after 2.5 seconds
                    timerProgressBar: true,
                    showConfirmButton: false, // Hide OK button
                    customClass: {
                        popup: 'swal2-confirm-popup',
                        title: 'swal2-confirm-title',
                        htmlContainer: 'swal2-confirm-text',
                        confirmButton: 'swal2-confirm-btn'
                    }
                });

                if (response.success && pjaxContainer) {
                    $.pjax.reload({ container: pjaxContainer, timeout: 10000 });
                }

                resolve(); // ✅ Resolve after success

            },
            error: function (jqXHR) {
                let message = 'Something went wrong.';
                if (jqXHR.status === 403) message = 'You are not authorized to perform this action.';
                else if (jqXHR.status === 404) message = 'Requested resource not found.';
                else if (jqXHR.status === 500) message = 'Internal server error.';

                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    customClass: {
                        popup: 'swal2-confirm-popup',
                        title: 'swal2-confirm-title',
                        htmlContainer: 'swal2-confirm-text',
                        confirmButton: 'swal2-confirm-btn'
                    }
                });

                reject(); // ✅ Reject on error

            }
        });
    };

    const redirectTo = () => {
        window.location.href = url;
    };

    if (confirm) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: confirmButton,
            cancelButtonText: cancelButton,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            customClass: {
                popup: 'swal2-confirm-popup',
                title: 'swal2-confirm-title',
                htmlContainer: 'swal2-confirm-text',
                confirmButton: 'swal2-confirm-btn',
                cancelButton: 'swal2-cancel-btn'
            },
            preConfirm: () => {
                return new Promise((resolve) => {
                    if (useAjax) {
                        sendAjax();
                    } else {
                        redirectTo();
                    }
                });
            }
        });
    } else {
        if (useAjax) {
            sendAjax();
        } else {
            redirectTo();
        }
    }
});
