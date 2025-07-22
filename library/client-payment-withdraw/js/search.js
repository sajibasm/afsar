$('#client_payment_refund').on('reset', function(e) {
    setTimeout(function () {
        // Reset Select2 field
        $('#customerwithdrawsearch-outletid').val(null).trigger('change'); // Outlet

        // Reset plain inputs
        $('#customerwithdrawsearch-remarks').val('');
        $('#customerwithdrawsearch-payment_history_id').val('');
        $('#customerwithdrawsearch-amount').val('');

        // Reset Date Range Picker
        let dateRangeInput = $('#customerwithdrawsearch-created_at');
        if (dateRangeInput.data('daterangepicker')) {
            let picker = dateRangeInput.data('daterangepicker');
            picker.setStartDate(moment());
            picker.setEndDate(moment());
            dateRangeInput.val('');
        }
    }, 0);
});