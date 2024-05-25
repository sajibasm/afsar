    /**
    * Created by sajib on 6/20/2015.
    */

    $('#bank_id').attr("disabled", true);

    $("#bankreconciliation-payment_type").change(function() {

        var paymentType = $("#bankreconciliation-payment_type").val();
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


