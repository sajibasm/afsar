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


    $('body').on('beforeSubmit', 'form#fromBankReconciliation', function (event) {
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
            text: 'Do you want to create the payment?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, create it!',
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

