/**
 * Created by sajib on 6/20/2015.
 */

var csrfToken = $('meta[name="csrf-token"]').attr("content");

// Apply CSRF token to all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': csrfToken
    }
});

$(function () {
    "use strict";

    let isSubmitting = false;

    // Prevent PJAX timeout redirect
    $(document).on('pjax:timeout', function(event) {
        event.preventDefault();
    });

    // PJAX modal button handler
    if ($.support.pjax) {
        $(document).on('click', 'button[data-pjax]', function (event) {
            event.preventDefault();
            var target = $(this).attr('value');
            $('#modal .modal-body').load(target, function () {
                $('#modal').modal('show');
            });
        });
    }


    // Handle size_id price loading
    $(document.body).on('change', '#size_id', function () {
        var sizeId = $('#size_id').val();
        if (sizeId) {
            $.ajax({
                url: priceUrl,
                type: 'POST',
                data: { sizeId: sizeId },
                success: function (response) {
                    if (response.success) {
                        $('#productstockitemsdraft-cost_price').val(response.cost);
                        $('#productstockitemsdraft-wholesale_price').val(response.wholesale);
                        $('#productstockitemsdraft-retail_price').val(response.retail);
                        showMessage('info', response.message);
                    } else {
                        $('#productstockitemsdraft-cost_price').val(0);
                        $('#productstockitemsdraft-wholesale_price').val(0);
                        $('#productstockitemsdraft-retail_price').val(0);
                        showMessage('error', response.message);
                    }
                }
            });
        }
    });

    // Main form submit handler (used by both Enter key and Add button)
    function submitStockTransferForm(event) {
        if (isSubmitting) return false;
        isSubmitting = true;
        event.preventDefault();

        var form = $("#formStockTransfer");

        if (form.find('.has-error').length) {
            isSubmitting = false;
            return false;
        }

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.error) {
                    $("#errorSummary").empty().show();
                    var list = $("#errorSummary").append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                    $.each(response.message, function (index, value) {
                        list.append('<li>' + value + '</li>');
                    });
                } else {
                    $("#errorSummary").hide();
                    showMessage('success', 'Draft item added successfully');

                    // Reset form fields
                    $('#item_id').val(null).trigger('change');
                    $('#brand_id').val(null).trigger('change');
                    $('#size_id').val(null).trigger('change');
                    $('#productstockitemsdraft-new_quantity').val('');

                    // Reload PJAX draft list container
                    $.pjax.reload({container: '#stockTransfer', timeout: 3000});
                }
            },
            error: function () {
                showMessage('error', 'An error occurred while submitting the form.');
            },
            complete: function () {
                isSubmitting = false;
            }
        });

        return false;
    }

    // Trigger AJAX on Enter key in quantity input
    $('body').on('keypress', '#productstockitemsdraft-new_quantity', function (event) {
        if (event.which === 13) {
            submitStockTransferForm(event);
        }
    });

    // Trigger same AJAX on button click
    $('body').on('click', '#btnAddItem', function (event) {
        submitStockTransferForm(event);
    });
});
