/**
 * Created by sajib on 6/20/2015.
 */

$('body').on('change', '#sales-payment_type', function (event) {

    var paymentType = $("#sales-payment_type").val();
    var isFound = false;

    jQuery.each( type, function( i, val ) {
        if(i==paymentType && val==defaultType){
            isFound = true;
        }
    });

    if(isFound){
        $('#bank_id').attr("disabled", false);
        $('#branch_id').attr("disabled", false);
    }else{
        $('#bank_id').attr("disabled", true);
        $('#branch_id').attr("disabled", true);
    }
});


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
    });

}

function lowestPriceCheck() {
    var unitPrice = parseFloat($('#salesdraft-price').val());
    var lowestdPrice = parseFloat($('#salesdraft-lowestpercent').val());
    if(unitPrice<lowestdPrice){
        $('.field-salesdraft-price').addClass('has-error');
        $('.field-salesdraft-price .help-block').append('Price should not be less than '+lowestdPrice);
        return false;
    } else {
        return true;
    }
}

function calculate() {
    var paid = parseFloat($('#sales-paid_amount').val());
    var total = parseFloat($('#sales-total_amount').val());
    var discount = parseFloat($('#sales-discount_amount').val());
    $('#sales-due_amount').val(total-(paid+discount));
}


$(document).on('pjax:success', function() {
    $('#loading').hide();
});


$('body').on('keyup', '#sales-discount_amount', function (event) {
    calculate();
});

$('body').on('keyup', '#sales-paid_amount', function (event) {
    calculate();
});



$('body').on('change', '#sales_amount', function (event) {
    //$('#sales_amount').change(function (event) {
    var salesAmount = $('#sales_amount').val();
    if(salesAmount=='custom'){
        $('#salesdraft-price').val('');
        $('#salesdraft-price').prop('readonly', false);
    }else{
        $('#salesdraft-price').val(salesAmount);
        $('#salesdraft-price').prop('readonly', true);
    }
});

//Event for change Product-Size Dropdown box.
$('body').on('change', '#sales-client_id', function (event) {
    //$('#size_id').change(function (event) {
    var customerId = $('#sales-client_id').val();
    var form = $(this);
    $.ajax({
        url: customerDetails,
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            if(response.client_type=='regular'){
                $("#sales-client_name").prop("readonly", true);
                $('#sales-client_type').val(1);
                $('#sales-client_name').val(response.client_name);
            }else{
                $("#sales-client_name").prop("readonly", false);
                $('#sales-client_type').val(2);
                $('#sales-client_name').val('');
            }

            $('#sales-contact_number').val(response.client_contact_number);
        }
    });
});


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
        // Let's disable the inputs for the duration of the Ajax request.
        // Note: we disable elements AFTER the form data has been serialized.
        // Disabled form elements will not be serialized.
        $inputs.prop("disabled", true);

        // Fire off the request to /form.php
        request = $.ajax({
            url: checkAvailable,
            type: "POST",
            data: { size_id : size_id, outletId: outletId }
        });

        // Callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            //console.log(response);
            if(response.isAvailable){
                $('#danger-message').hide();
                $('#success-message').empty();
                $('#success-message').append(response.message);
                $('#success-message').show(400);
                $('#salesdraft-lowestpercent').val(response.lowestPrice);
                $('#salesdraft-cost_amount').val(response.costAmount);
            }else{
                $('#success-message').hide();
                $('#danger-message').empty();
                $('#salesdraft-lowestpercent').val(0);
                $('#danger-message').append(response.message);
                $('#danger-message').show(400);
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


$('body').on('beforeSubmit', 'form#transport', function (event) {

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
                $("#errorSummary").empty();
                $("#errorSummary").show();
                var list = $("#errorSummary").append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                $.each(response.message, function (index, value) {
                    list.append('<li>' + value + '</li>');
                });
            } else {
                $("#errorSummary").hide();
                $("#modal").modal("hide");
                $.pjax.reload({container: '#salesPjaxGridView', 'timeout': 5000});
            }
        }
    });

    return false;
});

$('body').on('beforeSubmit', 'form#notification', function (event) {

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
                $("#errorSummary").empty();
                $("#errorSummary").show();
                var list = $("#errorSummary").append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                $.each(response.message, function (index, value) {
                    list.append('<li>' + value + '</li>');
                });
            } else {
                $("#errorSummary").hide();
                $("#modal").modal("hide");
                $.pjax.reload({container: '#salesPjaxGridView', 'timeout': 5000});
            }
        }
    });

    return false;
});



//update single product
$('body').on('beforeSubmit', 'form#draftUpdate', function (event) {

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

                if(response.type=='others'){
                    $("#modal").modal("hide");
                    $('#success-message').hide();
                    $('#danger-message').empty();
                    $('#danger-message').append(response.message);
                    $('#danger-message').show(400);
                }else  if(response.type=='model'){
                    $("#errorSummary").empty();
                    $("#errorSummary").show();
                    var list = $("#errorSummary").append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                    $.each(response.message, function (index, value) {
                        list.append('<li>' + value + '</li>');
                    });
                }else{

                }

                $("#loading").hide();

            } else {

                $("#errorSummary").hide();
                $("#modal").modal("hide");

                $.pjax.reload({container: '#sell', 'timeout': 5000});

            }
        }
    });

    return false;
});

//Add Single Product.
$('body').on('beforeSubmit', 'form#formAjaxSell', function (event) {

    console.log("Add Product");
    event.preventDefault();

    if(lowestPriceCheck()){
        var price = $('#salesdraft-price').val();
        var qty = $('#salesdraft-quantity').val();

        var form = $(this);
        //console.log(form.serialize());
        // return false if form still have some validation errors
        // if (form.find('.has-error').length) {
        //     console.log("Error");
        //     return false;
        // }

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
                    if(response.type=='others'){
                        $('#success-message').hide();
                        $('#danger-message').empty();
                        $('#danger-message').append(response.message);
                        $('#danger-message').show(400);
                    }else  if(response.type=='model'){
                        $("#errorSummary").empty();
                        $("#errorSummary").show();
                        var list = $("#errorSummary").append('<p>Please fix the following errors:</p><ul></ul>').find('ul');
                        $.each(response.message, function (index, value) {
                            list.append('<li>' + value + '</li>');
                        });
                    }else{

                    }

                    $("#loading").hide();

                } else {

                    console.log("Reloading");

                    $.pjax.reload({container: '#sell', 'timeout': 10000});

                    $('#sell').on('pjax:success', function () {
                        $("#errorSummary").hide();
                    });
                }
            }
        });
    }

    return false;
});

$('body').on('beforeSubmit', 'form#formAjaxSellCreate', function (event) {
    event.preventDefault();
    var r = confirm("Do you want to create invoice?");
    return r == true;
});
