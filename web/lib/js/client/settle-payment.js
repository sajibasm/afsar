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



    $('body').on('beforeSubmit', 'form#fromSettlePayment', function (event) {
        event.preventDefault();

        var $form = $(this);

        // ✅ Prevent infinite loop using data attribute
        if ($form.data('submitted') === true) {
            return true; // allow form to submit naturally
        }

        // ✅ Check for validation errors first
        if ($form.find('.has-error').length > 0) {
            return false;
        }

        // ✅ Show confirmation only if not already confirmed
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to settle the payment?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, settle it!',
            cancelButtonText: 'No',
            customClass: {
                popup: 'swal2-confirm-popup',
                title: 'swal2-confirm-title',
                htmlContainer: 'swal2-confirm-text',
                confirmButton: 'swal2-confirm-btn',
                cancelButton: 'swal2-cancel-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $form.data('submitted', true);  // ✅ Set flag to skip confirmation next time
                $form.submit();                 // ✅ Trigger submission again
            }
        });

        return false;  // Always stop the first submission
    });