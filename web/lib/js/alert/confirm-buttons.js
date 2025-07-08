$(document).on('click', '.btn-confirm', function (e) {
    e.preventDefault();

    const button = $(this);
    const url = button.data('url') || button.attr('href');
    const title = button.data('confirmTitle') || 'Are you sure?';
    const text = button.data('confirmText') || 'This action cannot be undone.';
    const confirmButton = button.data('confirmButton') || 'Yes, proceed!';
    const cancelButton = button.data('cancelButton') || 'Cancel';
    const useAjax = button.data('confirmAjax') === 1 || button.data('confirmAjax') === '1';
    const pjaxContainer = button.data('pjaxId') || false;

    const recordId = button.data('id') || null;   // ✅ Safely grab data-id

    if (useAjax && !recordId) {
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
        return;  // ✅ Stop here if no ID
    }

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
            return new Promise((resolve, reject) => {

                const postData = {
                    '_csrf': yii.getCsrfToken(),
                    'id': recordId  // ✅ Safe: already checked
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: postData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || 'Operation completed successfully.',
                                icon: 'success',
                                customClass: {
                                    popup: 'swal2-confirm-popup',
                                    title: 'swal2-confirm-title',
                                    htmlContainer: 'swal2-confirm-text',
                                    confirmButton: 'swal2-confirm-btn'
                                }
                            });

                            if (pjaxContainer) {
                                $.pjax.reload({ container: pjaxContainer, timeout: 10000 });
                            }

                            resolve();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'An error occurred during the operation.',
                                icon: 'error',
                                customClass: {
                                    popup: 'swal2-confirm-popup',
                                    title: 'swal2-confirm-title',
                                    htmlContainer: 'swal2-confirm-text',
                                    confirmButton: 'swal2-confirm-btn'
                                }
                            });
                            reject();
                        }
                    },
                    error: function(jqXHR) {
                        let message = 'Something went wrong.';
                        if (jqXHR.status === 403) {
                            message = 'You are not authorized to perform this action.';
                        } else if (jqXHR.status === 404) {
                            message = 'Requested resource not found.';
                        } else if (jqXHR.status === 500) {
                            message = 'Internal server error.';
                        }

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

                        reject();
                    }
                });
            });
        }
    });

});
