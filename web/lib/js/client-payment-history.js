$('#bank_id').attr("disabled", true);

function clientTotalDues(clientId, outlet) {

    $.ajax({
        url: baseUrl + 'client/total-dues-by-customer',
        type: 'post',
        data: {customer: clientId, outlet: outlet},
        success: function (response) {
            if (!response.error) {
                $('#totalDues').hide();
                $('#totalDues').empty();
                $('#totalDues').append('<strong>Total Dues: </strong>'+response.amount);
                $('#totalDues').show();
            } else {
                $('#totalDues').hide();
            }
        }
    });
}

$("#clientpaymenthistory-client_id").change(function () {
    $("#loading").hide();
    var clientId = $('#clientpaymenthistory-client_id').val();
    var outlet = $('#clientpaymenthistory-outletid').val();
    if(clientId && outlet){
        clientTotalDues(clientId, outlet);
    }
});


$("#clientpaymenthistory-payment_type_id").change(function () {

    var payentType = $("#clientpaymenthistory-payment_type_id").val();
    $('#bank_id').attr("disabled", true);
    $('#branch_id').attr("disabled", true);
    jQuery.each(type, function (i, val) {
        if (i == payentType && val == bankType) {
            $('#bank_id').attr("disabled", false);
            $('#branch_id').attr("disabled", false);
        }
    });
});


$('body').on('beforeSubmit', 'form#formPaymentReceived', function (event) {
    event.preventDefault();
    var amount = $('#clientpaymenthistory-received_amount').val();
    var r = confirm("Do you want to received "+amount+"?");
    return r == true;
});

$(function(){

    jQuery.each(type, function (i, val) {
        if (val == defaultPaymentType) {
            $('#bank_id').attr("disabled", true);
        }
    });

});