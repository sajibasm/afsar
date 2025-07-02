const csrfToken = $('meta[name="csrf-token"]').attr('content');

$(function () {
    'use strict';

    // On size change, fetch product price
    $(document.body).on('change', '#size_id', function () {
        const itemId = $('#item_id').val();
        const brandId = $('#brand_id').val();
        const sizeId = $('#size_id').val();

        if (itemId && brandId && sizeId && typeof transferOutlet !== 'undefined') {
            $.ajax({
                url: productPrice,
                type: 'POST',
                data: { sizeId, transferOutlet },
                success(response) {
                    if (response.success) {
                        $('#productstockitemsdraft-cost_price').val(response.cost);
                        $('#productstockitemsdraft-wholesale_price').val(response.wholesale);
                        $('#productstockitemsdraft-retail_price').val(response.retail);
                        $('#danger-message').hide();
                        $('#success-message').text(response.message).fadeIn(400);
                    } else {
                        $('#success-message, #danger-message').hide();
                        $('#danger-message').text(response.message).fadeIn(400);
                        $('#productstockitemsdraft-cost_price, #productstockitemsdraft-wholesale_price, #productstockitemsdraft-retail_price').val(0);
                    }
                }
            });
        }
    });
});

// PJAX timeout handling
$(document).on('pjax:timeout', function (e) {
    e.preventDefault();
});

// PJAX modal loader
if ($.support.pjax) {
    $(document).on('click', 'button[data-pjax]', function (e) {
        e.preventDefault();
        const target = $(this).attr('value');
        $('#modal .modal-body').load(target, () => $('#modal').modal('show'));
    });
}

// Create stock transfer form (with SweetAlert confirmation)
$('body').on('beforeSubmit', '#formAjaxSaveStock', function (e) {
    e.preventDefault();
    const $form = $(this);
    const totalQty = parseInt($('#productstockitemsdraft-totalquantity').val(), 10);

    if (isNaN(totalQty) || totalQty === 0) {
        $('#cartError').fadeIn(300).delay(1500).fadeOut(300);
        return false;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to create the stock transfer?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'swal2-confirm-popup',
            title: 'swal2-confirm-title',
            htmlContainer: 'swal2-confirm-text',
            confirmButton: 'swal2-confirm-btn',
            cancelButton: 'swal2-cancel-btn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $form[0].submit();
        }
    });

    return false;
});

// Submit stock movement form via AJAX
$('body').on('beforeSubmit', '#formAjaxStockMovementOutlet', function (e) {
    e.preventDefault();
    const $form = $(this);

    if ($form.find('.has-error').length) return false;

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),
        success(response) {
            if (response.error) {
                const $summary = $('#errorSummary');
                $summary.html('<p>Please fix the following errors:</p><ul></ul>').show();
                response.message.forEach(msg => $summary.find('ul').append(`<li>${msg}</li>`));
            } else {
                $('#errorSummary').hide();
                $.pjax.reload({ container: '#stock', timeout: 10000 });
            }
        }
    });

    return false;
});

// Stock update via modal form
$('body').on('beforeSubmit', '#formAjaxStockUpdate', function () {
    const $form = $(this);
    if ($form.find('.has-error').length) return false;

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),
        success(response) {
            if (response.error) {
                const $summary = $('#errorSummary');
                $summary.html('<p>Please fix the following errors:</p><ul></ul>').show();
                response.message.forEach(msg => $summary.find('ul').append(`<li>${msg}</li>`));
            } else {
                $('#errorSummary').hide();
                $('#modal .modal-body').load($form.attr('action'), () => $('#modal').modal('show'));
                $.pjax.reload({ container: '#stock', timeout: 10000 });
            }
        }
    });

    return false;
});
