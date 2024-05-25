    /**
    * Created by sajib on 6/20/2015.
    */

    $(document).on('pjax:timeout', function(event) {
        // Prevent default timeout redirection behavior
        event.preventDefault();
    });

    $('body').on('change', '#clientpaymenthistory-paytype', function (event) {

        var mode = $('#clientpaymenthistory-paytype').val();

        if(mode=='Auto'){
            $('#clientpaymenthistory-invoices').prop('disabled', true);
            $('.select2-selection__choice').remove();

        }else{
            $('#clientpaymenthistory-invoices').prop('disabled', false);
        }
    });

