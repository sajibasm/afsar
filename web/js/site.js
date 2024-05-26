    /**
    * Created by sajib on 6/20/2015.
    */

    $(document).on('pjax:timeout', function(event) {
        // Prevent default timeout redirection behavior
        event.preventDefault();
    });


    function dailySalesUpdate(){

        var request = $.ajax({
            url: dailySummery,
            type: "GET"
        });

        // Callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            // Log a message to the console
            $('#dailySales').empty();
            $('#dailySales').append(response.sales);

            $('#dailyDues').empty();
            $('#dailyDues').append(response.salesDue);

            $('#dailySalesCash').empty();
            $('#dailySalesCash').append(response.salesPaid);

            $('#dailySalesReturn').empty();
            $('#dailySalesReturn').append(response.salesReturn);

            $('#dailyCashHand').empty();
            $('#dailyCashHand').append(response.cashHand);

            $('#dailyDueCollection').empty();
            $('#dailyDueCollection').append(response.dueReceived);

            $('#dailyWithdraw').empty();
            $('#dailyWithdraw').append(response.withdraw);

            $('#dailyExpense').empty();
            $('#dailyExpense').append(response.expense);

            $('#dailyCashIn').empty();
            $('#dailyCashIn').append(response.cashIn);

            $('#dailyCashOut').empty();
            $('#dailyCashOut').append(response.cashOut);

            $('#dailyBankIn').empty();
            $('#dailyBankIn').append(response.bankIn);

            $('#dailyBankOut').empty();
            $('#dailyBankOut').append(response.bankOut);


        });

        // Callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            // Log the error to the console
            console.error( "The following error occurred: "+ textStatus, errorThrown );
        });
    }


    $(function() {

        dailySalesUpdate();

        setInterval(function () {
            dailySalesUpdate();
        }, 10000);
    });
