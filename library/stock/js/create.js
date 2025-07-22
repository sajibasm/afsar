function showErrors(container, messages) {
    container.empty().show();
    let list = container.append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
    $.each(messages, function (index, value) {
        list.append('<li>' + value + '</li>');
    });
}


function handleAjaxFormSubmit(form, reloadContainer = '#stock', closeModal = true) {
    if (form.find('.has-error').length) return false;

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            if (response.error) {
                showErrors($('#errorSummary'), response.message);
            } else {
                $('#errorSummary').hide();

                // ✅ Show SweetAlert2 success toast
                showMessage('success', 'Item added successfully');

                if (closeModal) $('#modal').modal('hide');
                if (reloadContainer) $.pjax.reload({container: reloadContainer, timeout: 10000});
            }
        },
        error: function () {
            showMessage('error', 'An unexpected error occurred.');
        }
    });

    return false;
}


$('body').on('beforeSubmit', 'form#formAjaxSaveStock', function (event) {
    event.preventDefault();
    if ($('#productstockitemsdraft-totalquantity').val() == 0) {
        $('#cartError').toggle(700);
        return false;
    }
    return handleAjaxFormSubmit($(this));
});

$('body').on('beforeSubmit', 'form#stockUpdateSingleItem', function (event) {
    event.preventDefault();
    return handleAjaxFormSubmit($(this));
});

$('body').on('beforeSubmit', 'form#formAjaxStock', function (event) {
    event.preventDefault();
    return handleAjaxFormSubmit($(this));
});

$('body').on('beforeSubmit', 'form#formAjaxStockUpdate', function (event) {
    event.preventDefault();
    return handleAjaxFormSubmit($(this), '#stock', false); // Don't close modal, just reload
});


function LC_Warehouse_Supplier_Invoice_Toggle(type) {
    if (type === 'import') {
        $('#productstock-lc_id, #productstock-warehouse_id').prop('disabled', false);
        $('#productstock-buyer_id').prop('disabled', true).val('').trigger('change');
    } else if (type === 'local') {
        $('#productstock-buyer_id').prop('disabled', false);
        $('#productstock-lc_id, #productstock-warehouse_id').prop('disabled', true).val('').trigger('change');
    } else {
        $('#productstock-lc_id, #productstock-warehouse_id, #productstock-buyer_id')
            .prop('disabled', true).val('').trigger('change');
    }
}

$(function () {
    LC_Warehouse_Supplier_Invoice_Toggle($('#productstock-type').val());
});

$('#productstock-type').change(function () {
    LC_Warehouse_Supplier_Invoice_Toggle($(this).val());
});


if ($.support.pjax) {
    $(document).on('click', 'button[data-pjax]', function (event) {
        event.preventDefault();
        $('#modal .modal-body').load($(this).attr('value'), function () {
            $('#modal').modal('show');
        });
    });
}

$('body').on('change', '#size_id', function (event) {
    let size_id = $(this).val();
    if (size_id > 0) {
        let $inputs = $(this).closest('form').find("input, select, button, textarea").prop("disabled", true);
        $.ajax({
            url: ajaxRequestUrl,
            type: "GET",
            data: { sizeId: size_id },
            success: function (res) {
                if (res) {
                    $('#productstockitemsdraft-cost_price').val(res.cost);
                    $('#productstockitemsdraft-wholesale_price').val(res.wholesale);
                    $('#productstockitemsdraft-retail_price').val(res.retail);
                    $('#productstockitemsdraft-alert_quantity').val(res.alert);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error: " + textStatus, errorThrown);
            },
            complete: function () {
                $inputs.prop("disabled", false);
            }
        });
    }
});
