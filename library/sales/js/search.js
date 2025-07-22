$('#sales-search').on('reset', function() {

    // Reset Select2 dropdowns
    $('#outlet_id').val(null).trigger('change');                             // Store
    $('#salessearch-client_id').val(null).trigger('change');                 // Customer (DepDrop + Select2)
    $('#salessearch-transport_id').val(null).trigger('change');              // Transport
    $('#salessearch-payment_type').val(null).trigger('change');              // Received Type
    $('#salessearch-invoicetype').val(null).trigger('change');               // Type
    $('#salessearch-user_id').val(null).trigger('change');                   // User
    $('#salessearch-payment_condition_search').val(null).trigger('change');                   // User

    // Reset Date Range Picker
    if ($('#salessearch-created_at').data('daterangepicker')) {
        let picker = $('#salessearch-created_at').data('daterangepicker');
        picker.setStartDate(moment());
        picker.setEndDate(moment());
        $('#salessearch-created_at').val('');
    }

});