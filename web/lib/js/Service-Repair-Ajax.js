    /**
    * Created by sajib on 6/20/2015.
    */
    $(document).on('pjax:timeout', function(event) {
        // Prevent default timeout redirection behavior
        event.preventDefault();
    });



    $(document).on('click', 'button[data-pjax]', function (event) {
        event.preventDefault();
        var target = $(this).attr('value');
        $("#modal .modal-body").load(target, function () {
            $("#modal").modal("show");
        });
    });


    function checkMaxRefund()
    {
        var refund = parseFloat($('#service-refund').val());
        var maxRefundAmount = parseFloat($('#maxRefundAmount').val());
        $('.field-salesreturn-refund_amount .help-block').empty();

        if(refund>maxRefundAmount){
            $('.field-salesreturn-refund_amount .help-block').append('Should Less then or equal  '+maxRefundAmount);
            $('.field-salesreturn-refund_amount').removeClass('has-success').addClass('has-error');
            $('#total-amount').val(refund);
            return false;
        }else{
            $('#total-amount').val(refund);
            $('.field-salesreturn-refund_amount').removeClass('has-error').addClass('has-success');
            return true;
        }
    }

    //Event for change Prduct-Size Dropdown box.
    $('body').on('change', '#service-refund', function (event) {

        checkMaxRefund();

    });


    $('body').on('beforeSubmit', 'form#formReturnService', function (event) {
        event.preventDefault();
        var form = $(this);

        //console.log(form.serialize());
        // return false if form still have some validation errors
        if (form.find('.has-error').length) {
            return false;
        }

        return true;
    });

