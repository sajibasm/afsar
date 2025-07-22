const csrfToken = $('meta[name="csrf-token"]').attr("content");

$(function () {
    "use strict";

    // Price Auto-fill on Size Change
    $(document.body).on('change', '#size_id', function () {
        const itemId = $('#item_id').val();
        const brandId = $('#brand_id').val();
        const sizeId = $('#size_id').val();

        if (itemId && brandId && sizeId && typeof transferOutlet !== 'undefined') {
            $.ajax({
                url: productPrice,
                type: 'POST',
                data: { sizeId, transferOutlet },
                success: function (res) {
                    const successBox = $('#success-message');
                    const errorBox = $('#danger-message');

                    if (res.success) {
                        $('#productstockitemsdraft-cost_price').val(res.cost);
                        $('#productstockitemsdraft-wholesale_price').val(res.wholesale);
                        $('#productstockitemsdraft-retail_price').val(res.retail);

                        errorBox.hide();
                        successBox.text(res.message).fadeIn(400);
                    } else {
                        successBox.hide();
                        errorBox.text(res.message).fadeIn(400);

                        $('#productstockitemsdraft-cost_price, #productstockitemsdraft-wholesale_price, #productstockitemsdraft-retail_price').val(0);
                    }
                }
            });
        }
    });

    // Prevent PJAX timeout
    $(document).on('pjax:timeout', function (event) {
        event.preventDefault();
    });

    // Show modal via PJAX
    if ($.support.pjax) {
        $(document).on('click', 'button[data-pjax]', function (e) {
            e.preventDefault();
            const target = $(this).attr('value');
            $("#modal .modal-body").load(target, function () {
                $("#modal").modal("show");
            });
        });
    }

    // Create stock transfer confirmation
    $('body').on('beforeSubmit', '#formAjaxSaveStock', function (e) {
        e.preventDefault();

        const totalQty = parseInt($('#productstockitemsdraft-totalquantity').val());
        if (totalQty === 0) {
            $('#cartError').fadeToggle(700);
            return false;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to create stock transfer?',
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
                $('#formAjaxSaveStock')[0].submit(); // Trigger actual form submission
            }
        });

        return false; // Prevent default for now
    });


    // Handle Enter key to submit stock movement form
    $('body').on('keypress', '#productstockitemsdraft-new_quantity', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            const form = $("form#formAjaxStockProductMovement");

            if (form.find('.has-error').length) return false;

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function (res) {
                    const errorBox = $("#errorSummary");
                    if (res.error) {
                        errorBox.html('<p>Please fix the following errors:</p><ul></ul>').show();
                        $.each(res.message, (i, msg) => {
                            errorBox.find('ul').append(`<li>${msg}</li>`);
                        });
                    } else {
                        errorBox.hide();
                        $.pjax.reload({ container: '#stock', timeout: 10000 });
                    }
                }
            });
            return false;
        }
    });

    // Stock single item update
    $('body').on('beforeSubmit', 'form#stockUpdateSingleItem', function (e) {
        e.preventDefault();
        const form = $(this);
        if (form.find('.has-error').length) return false;

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                const errorBox = $("#errorSummary");

                if (res.error) {
                    errorBox.html('<p>Please fix the following errors:</p><ul></ul>').show();
                    $.each(res.message, (i, msg) => {
                        errorBox.find('ul').append(`<li>${msg}</li>`);
                    });
                } else {
                    errorBox.hide();
                    $("#modal").modal("hide");
                    $.pjax.reload({ container: '#stock', timeout: 5000 });
                }
            }
        });

        return false;
    });

    // Stock update full form
    $('body').on('beforeSubmit', 'form#formAjaxStockUpdate', function () {
        const form = $(this);
        if (form.find('.has-error').length) return false;

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                const errorBox = $("#errorSummary");

                if (res.error) {
                    errorBox.html('<p>Please fix the following errors:</p><ul></ul>').show();
                    $.each(res.message, (i, msg) => {
                        errorBox.find('ul').append(`<li>${msg}</li>`);
                    });
                } else {
                    errorBox.hide();
                    $("#modal .modal-body").load(form.attr('action'), function () {
                        $("#modal").modal("show");
                    });
                    $.pjax.reload({ container: '#stock', timeout: 10000 });
                }
            }
        });

        return false;
    });
});
