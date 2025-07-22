$('#clientpaymenthistorysearch').on('reset', function(e) {
    // Timeout ensures the native reset happens before triggering JS resets
    setTimeout(function () {
        // Reset Select2 fields
        $('#outlet_id').val(null).trigger('change'); // Outlet
        $('#clientpaymenthistorysearch-client_id').val(null).trigger('change'); // Customer
        $('#clientpaymenthistorysearch-received_type').val(null).trigger('change'); // Received Type
        $('#clientpaymenthistorysearch-payment_type_id').val(null).trigger('change'); // Payment Type

        // Reset plain input fields
        $('#clientpaymenthistorysearch-client_payment_history_id').val('');
        $('#clientpaymenthistorysearch-received_amount').val('');

        // Reset Date Range Picker
        let dateRangeInput = $('#clientpaymenthistorysearch-received_at');
        if (dateRangeInput.data('daterangepicker')) {
            let picker = dateRangeInput.data('daterangepicker');
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            dateRangeInput.val('');
        }
    }, 0);
});