    /**
    * Created by sajib on 6/20/2015.
    */

    $(document).on('pjax:timeout', function(event) {
        // Prevent default timeout redirection behavior
        event.preventDefault();
    });

    if ($.support.pjax) {

        $.pjax.defaults.timeout = false;

        $(document).on('click', 'button[data-pjax]', function (event) {
            event.preventDefault();
            var target = $(this).attr('value');
            $("#modal .modal-body").load(target, function () {
                $("#modal").modal("show");
            });
        })

    }


    $('body').on('change', '#market_sales_amount', function (event) {
        //$('#sales_amount').change(function (event) {
        var salesAmount = $('#market_sales_amount').val();
        if(salesAmount=='custom'){
            $('#marketbook-price').val('');
            $('#marketbook-price').prop('readonly', false);
        }else{
            $('#marketbook-price').val(salesAmount);
        }
    });


    $('body').on('change', '#marketbook-quantity', function (event) {
    //$('#sales_amount').change(function (event) {
        var qunatity = $('#marketbook-quantity').val();
        var salesAmount = $('#market_sales_amount').val();
        $('#marketbook-total_amount').val(qunatity*salesAmount);

    });


    $('body').on('change', '#market_size_id', function (event) {
        //$('#size_id').change(function (event) {
        var size_id = $('#market_size_id').val();

        if(size_id>0){
            //if (request) {
            //    request.abort();
            //}
            // setup some local variables
            var $form = $(this);

            // Serialize the data in the form
            //var serializedData = $form.serialize();

            var $inputs = $form.find("input, select, button, textarea");
            // Let's disable the inputs for the duration of the Ajax request.
            // Note: we disable elements AFTER the form data has been serialized.
            // Disabled form elements will not be serialized.
            $inputs.prop("disabled", true);

            // Fire off the request to /form.php
            request = $.ajax({
                url: checkAvailable,
                type: "POST",
                data: { size_id : size_id }
            });

            // Callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR){
                // Log a message to the console
                //console.log(response);
                if(response.isAvailable){
                    $('#success-message').empty();
                    $('#success-message').append(response.msg);
                    $('#success-message').show(400);
                    $('#marketbook-cost_amount').val(response.costAmount);
                }else{

                    $('#danger-message').empty();
                    $('#danger-message').append(response.msg);
                    $('#danger-message').show(400);
                }


            });

            // Callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown){
                // Log the error to the console
                console.error(
                    "The following error occurred: "+
                    textStatus, errorThrown
                );
            });

            // Callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // Reenable the inputs
                $inputs.prop("disabled", false);
            });

            // Prevent default posting of form
            event.preventDefault();

        }

    });


    //update single product
    $('body').on('beforeSubmit', 'form', function (event) {

        event.preventDefault();


        console.log("Submit");

        var form = $(this);

        //
        // var form = $(this);
        // //console.log(form.serialize());
        // // return false if form still have some validation errors
        // if (form.find('.has-error').length) {
        //     return false;
        // }

        // submit form
        $.ajax({
            url: baseUrl+'/market-book/add-product',
            type: 'post',
            data: form.serialize(),
            success: function (response) {


                console.log(response);
                return false;

                if (response.error) {
                    $("#errorSummary").empty();
                    $("#errorSummary").show();

                    var list = $("#errorSummary").append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                    $.each(response.message, function (index, value) {
                        list.append('<li>' + value + '</li>');
                    });

                } else {
                    console.log("Error");

                    // $('#loading').hide();
                    // $("#modal").modal("hide");
                    $.pjax.reload({container: '#marketSell', 'timeout': 10000});
                }
            }
        });

        return false;
    });


    //Add Single Product.
    $('body').on('beforeSubmit', 'form#draftUpdateMarket', function (event) {

        //console.log("Add Product");

        event.preventDefault();
        var price = $('#salesdraft-price').val();
        var qty = $('#salesdraft-quantity').val();

        var form = $(this);
        //console.log(form.serialize());
        // return false if form still have some validation errors
        if (form.find('.has-error').length) {
            return false;
        }

        $('#loading').show();
        $('#success-message').hide();
        $('#danger-message').hide();

        // submit form
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function (response) {

                if (response.error) {
                    $("#errorSummary").empty();
                    $("#errorSummary").show();

                    var list = $("#errorSummary").append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                    $.each(response.message, function (index, value) {
                        list.append('<li>' + value + '</li>');
                    });

                } else {
                    $.pjax.reload({container: '#marketSell', 'timeout': 10000});
                    $("#errorSummary").hide();
                    $("#modal").modal("hide");
                    $('#loading').hide();

                }
            }
        });
        return false;
    });
