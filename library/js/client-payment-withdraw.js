

$("#payment_type").change(function() {


    var payentType = $("#payment_type").val();
    var isFound = false;

    console.log(payentType);

    jQuery.each( type, function( i, val ) {
        if(i==payentType && val=='Bank'){
            isFound = true;
        }
        //$( '#' + i ).append( document.createTextNode( " - " + val ) );
    });

    if(isFound){
        $('#bank_id').attr("disabled", false);
        $('#branch_id').attr("disabled", false);
    }else{
        $('#bank_id').attr("disabled", true);
        $('#branch_id').attr("disabled", true);
    }

});

