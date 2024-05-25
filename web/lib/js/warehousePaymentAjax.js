    /**
    * Created by sajib on 6/20/2015.
    */

    $('#bank_id').attr("disabled", true);

    $("#warehousepayment-payment_type").change(function() {

        $('#bank_id').attr("disabled", true);

        var paymentType = $("#warehousepayment-payment_type").val();

        jQuery.each( type, function( i, val ) {
            if(i==paymentType && val==bankType){
                $('#bank_id').attr("disabled", false);
            }
        });

    });


