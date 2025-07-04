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


    //Event for change Prduct-Size Dropdown box.
    $('body').on('change', '#salesreturn-cut_off_amount', function (event) {
        //$('#size_id').change(function (event) {
        var cutOff = $('#salesreturn-cut_off_amount').val();
        var refund = $('#salesreturn-refund_amount').val();
        var total = refund - cutOff;

        if(cutOff<refund){
            console.log('yes');
            $('.field-salesreturn-cut_off_amount .help-block').append('Cutoff amount should not greater then '+cutOff);
            $('.field-salesreturn-cut_off_amount').removeClass('has-success').addClass('has-error');
        }else{
            console.log('no');

            $('#salesreturn-total_amount').val(total);
            $('.field-salesreturn-cut_off_amount').removeClass('has-error').addClass('has-success');
            $('.field-salesreturn-cut_off_amount .help-block').append('');
        }

    });


    $('body').on('beforeSubmit', 'form#formReturn', function (event) {

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
                    $('.field-salesdetails-quantity').removeClass('has-success').addClass('has-error');
                    $('.field-salesdetails-quantity .help-block').append(response.message);

                } else {
                    $('.field-salesdetails-quantity').removeClass('has-error').addClass('has-success');
                    $("#modal").modal("hide");
                    $.pjax.reload({container: '#returnCart', 'timeout': 5000});

                }
            }
        });
        return false;
    });



    $('body').on('beforeSubmit', 'form#formSalesReturn', function (event) {
        event.preventDefault();
        var amount = $('#salesreturn-refund_amount').val();
        var r = confirm("Do you want to create this return product and cash back amount is: "+amount+"?");
        return r == true;
    });

