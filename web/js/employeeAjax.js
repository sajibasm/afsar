    /**
    * Created by sajib on 6/20/2015.
    */

    function checkSalary(employeeId, month, year) {
        $.ajax({
            url: salaryCheck,
            type: 'post',
            data: {employeeId: employeeId, year: year, month:month},
            success: function (response) {
                if(response.success) {
                    $('#salaryhistory-remaining_salary').val(response.remaining);
                    $('#salaryhistory-withdraw_amount').val(response.paid);
                }
            }
        });
    }


    $('body').on('click', '#payrollSlip', function (event) {
        //$('#size_id').change(function (event) {
        var employeeId = $('#slipEmployee').val();
        var month = $('#slipMonth').val();
        var year = $('#slipYear').val();

        var data = {
            month: month,
            year: year,
            employeeId: employeeId
        };

        var params = jQuery.param(data);
        var url = baseUrl+'/salary-history/slip?'+params;
        var win = window.open(url, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Please allow popups for this website');
        }

    });

    $('body').on('change', '#salaryhistory-month', function (event) {
        //$('#size_id').change(function (event) {
        var employeeId = $('#salaryhistory-employee_id').val();
        var month = $('#salaryhistory-month').val();
        var year = $('#salaryhistory-year').val();
        if(month && year && employeeId){
            checkSalary(employeeId, month, year);
        }
    });

    $('body').on('change', '#salaryhistory-year', function (event) {
        //$('#size_id').change(function (event) {
        var employeeId = $('#salaryhistory-employee_id').val();
        var month = $('#salaryhistory-month').val();
        var year = $('#salaryhistory-year').val();
        if(month && year && employeeId){
            checkSalary(employeeId, month, year);
        }
    });

    //Event for change Prduct-Size Dropdown box.
    $('body').on('change', '#salaryhistory-employee_id', function (event) {
        //$('#size_id').change(function (event) {
        var employeeId = $('#salaryhistory-employee_id').val();
        var month = $('#salaryhistory-month').val();
        var year = $('#salaryhistory-year').val();
        if(month && year && employeeId){
            checkSalary(employeeId, month, year);
        }
    });


    $('#bank_id').attr("disabled", true);

    $("#salaryhistory-payment_type").change(function() {
        $('#bank_id').attr("disabled", true);
        var payentType = $("#salaryhistory-payment_type").val();
        var isFound = false;

        jQuery.each( type, function( i, val ) {
            if(i==payentType && val==bankType){
                $('#bank_id').attr("disabled", false);
            }
        });
    });


