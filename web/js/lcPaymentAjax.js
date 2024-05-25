    /**
    * Created by sajib on 6/20/2015.
    */

    $('#bank_id').attr("disabled", true);

    $("#lcpayment-payment_type").change(function() {
        $('#bank_id').attr("disabled", true);
        var payentType = $("#lcpayment-payment_type").val();
        var isFound = false;

        jQuery.each( type, function( i, val ) {
            if(i==payentType && val==bankType){
                $('#bank_id').attr("disabled", false);
            }
        });


    });


