    /**
    * Created by sajib on 6/20/2015.
    */

    $('#bank_id').attr("disabled", true);

    $("#withdraw-type_id").change(function() {

        $('#bank_id').attr("disabled", true);

        var paymentType = $("#withdraw-type_id").val();

        jQuery.each( type, function( i, val ) {
            if(i==paymentType && val==bankType){
                $('#bank_id').attr("disabled", false);
            }
        });

    });


