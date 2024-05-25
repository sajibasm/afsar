    /**
    * Created by sajib on 6/20/2015.
    */

    var csrfToken = $('meta[name="csrf-token"]').attr('content');


    function LC_Warehouse_Supplier_Invoice_Toggle(type) {

        console.log(type);

        if (type==='import') {


             $('#productstock-lc_id').prop('disabled', false);
             $('#productstock-warehouse_id').prop('disabled', false);

             $('#productstock-buyer_id').prop('disabled', true);
             $('#productstock-buyer_id').val('').trigger('change');

        } else if(type==='local') {
            $('#productstock-buyer_id').prop('disabled', false);
            $('#productstock-lc_id').prop('disabled', true);
            $('#productstock-lc_id').val('').trigger('change');
            $('#productstock-warehouse_id').prop('disabled', true);
            $('#productstock-warehouse_id').val('').trigger('change');

        }else{

             $('#productstock-lc_id').prop('disabled', true);
             $('#productstock-lc_id').val('').trigger('change');
             $('#productstock-warehouse_id').prop('disabled', true);
             $('#productstock-warehouse_id').val('').trigger('change');

             $('#productstock-buyer_id').prop('disabled', true);
        }

    }

    $(function() {
        "use strict";
        jQuery.isFunction( LC_Warehouse_Supplier_Invoice_Toggle($('#productstock-type').val()));
    });


    $(document).on('pjax:timeout', function(event) {
        // Prevent default timeout redirection behavior
        event.preventDefault();
    });

    if ($.support.pjax) {
        $(document).on('click', 'button[data-pjax]', function (event) {
            event.preventDefault();
            var target = $(this).attr('value');
            $('#modal .modal-body').load(target, function () {
                $('#modal').modal('show');
            });
        });
    }


    $('body').on('change', '#size_id', function (event) {
        //$('#size_id').change(function (event) {
        var size_id = $('#size_id').val();
        

        if(size_id>0){
            //if (request) {
            //    request.abort();
            //}
            // setup some local variables
            var $form = $(this);

            // Serialize the data in the form
            //var serializedData = $form.serialize();

            var $inputs = $form.find("input, select, button, textarea");

            $inputs.prop("disabled", true);

            request = $.ajax({
                url: ajaxRequestUrl,
                type: "GET",
                data: { sizeId : size_id }
            });

            // Callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR){
                // Log a message to the console
                //console.log(response);
                if(response){
                    $('#productstockitemsdraft-cost_price').val(response.cost);
                    $('#productstockitemsdraft-wholesale_price').val(response.wholesale);
                    $('#productstockitemsdraft-retail_price').val(response.retail);
                    $('#productstockitemsdraft-alert_quantity').val(response.alert);
                }
            });

            // Callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown){
                // Log the error to the console
                console.error( "The following error occurred: "+ textStatus, errorThrown );
            });

            // Callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // Readable the inputs
                $inputs.prop("disabled", false);
            });

            // Prevent default posting of form
            event.preventDefault();

        }

    });




    $('#productstock-type').change(function () {
        jQuery.isFunction( LC_Warehouse_Supplier_Invoice_Toggle($('#productstock-type').val()));
    });



    $('body').on('beforeSubmit', 'form#formAjaxSaveStock', function (event) {


        event.preventDefault();
        var r = confirm('Do you want to create stock?');
        if(r === false){
            return false;
        }

        var form = $('#formAjaxSaveStock');
        var type = $('#productstock-type').val();

        var totalItem = $('#productstockitemsdraft-totalquantity').val();

        if(totalItem==0){
            $('#cartError').toggle(700);
            return false;
        }
        return true;
    });


    //update single product
    $('body').on('beforeSubmit', 'form#stockUpdateSingleItem', function (event) {

        event.preventDefault();

        var form = $(this);
        //console.log(form.serialize());
        // return false if form still have some validation errors
        if (form.find('.has-error').length) {
            return false;
        }

        // submit form
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function (response) {

                if (response.error) {
                    $('#errorSummary').empty();
                    $('#errorSummary').show();

                    var list = $('#errorSummary').append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                    $.each(response.message, function (index, value) {
                        list.append('<li>' + value + '</li>');
                    });

                } else {
                    $('#errorSummary').hide();
                    $('#modal').modal('hide');
                    $.pjax.reload({container: '#stock', 'timeout': 5000});

                }
            }
        });
        return false;
    });

    //Add Product Stock Action
    $('body').on('beforeSubmit', 'form#formAjaxStock', function (event) {

        event.preventDefault();

        var form = $(this);

        console.log(form.serialize());
        // return false if form still have some validation errors
        if (form.find('.has-error').length) {
            console.log('Error');
            console.log(form);

            //return false;
        }


        // submit form
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function (response) {

                if (response.error) {
                    $('#errorSummary').empty();
                    $('#errorSummary').show();

                    var list = $('#errorSummary').append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                    $.each(response.message, function (index, value) {
                        list.append('<li>' + value + '</li>');
                    });

                } else {
                    $('#errorSummary').hide();
                    $.pjax.reload({container: '#stock', 'timeout': 10000});
                }
            }
        });
        return false;
    });


    $('body').on('beforeSubmit', 'form#formAjaxStockUpdate', function () {
        var form = $(this);
        //console.log(form.serialize());
        // return false if form still have some validation errors
        if (form.find('.has-error').length) {
            return false;
        }

        // submit form
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function (response) {

                if (response.error) {

                    $('#errorSummary').empty();

                    $('#errorSummary').show();

                    var list = $('#errorSummary').append('<p>Please fix the following errors:</p><ul></ul>').find('ul');

                    $.each(response.message, function (index, value) {
                        list.append('<li>' + value + '</li>');
                    });

                } else {
                    $('#errorSummary').hide();
                    $('#modal .modal-body').load(form.attr('action'), function () {
                        $('#modal').modal('show');
                    });
                    $.pjax.reload({container: '#stock', 'timeout': 10000}); // Reload GridView
                }
            }
        });

        return false;
    });
