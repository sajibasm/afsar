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

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: confirmButton,
        cancelButtonText: cancelButton,
        showLoaderOnConfirm: true,                      // ✅ Built-in loading spinner
        allowOutsideClick: () => !Swal.isLoading(),      // ✅ Prevent closing while loading
        customClass: {
            popup: 'swal2-confirm-popup',
            title: 'swal2-confirm-title',
            htmlContainer: 'swal2-confirm-text',
            confirmButton: 'swal2-confirm-btn',
            cancelButton: 'swal2-cancel-btn'
        },
        preConfirm: () => {
            if (useAjax) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { '_csrf': yii.getCsrfToken() },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || 'Operation successful.',
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

                            resolve();  // ✅ End loader
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong.',
                                icon: 'error',
                                customClass: {
                                    popup: 'swal2-confirm-popup',
                                    title: 'swal2-confirm-title',
                                    htmlContainer: 'swal2-confirm-text',
                                    confirmButton: 'swal2-confirm-btn'
                                }
                            });

                            reject();   // ✅ Stop loader on failure
                        }
                    });
                });
            } else {
                window.location.href = url;
            }
        }
    });
});
