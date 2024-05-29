function cal() {
    var salesAmount = $('#salesdraft-sales_amount').val();
    var quantity = $('#salesdraft-quantity').val();
    $('#salesdraft-total_amount').val(salesAmount*quantity);
}


$('body').on('change', '#salesdraft-quantity', function (event) {
    var salesAmount = $('#salesdraft-sales_amount').val();
    var quantity = $('#salesdraft-quantity').val();
    $('#salesdraft-total_amount').val(salesAmount*quantity);
});

$('body').on('change', '#salesdraft-sales_amount', function (event) {
    var salesAmount = $('#salesdraft-sales_amount').val();
    var quantity = $('#salesdraft-quantity').val();
    $('#salesdraft-total_amount').val(salesAmount*quantity);
});
