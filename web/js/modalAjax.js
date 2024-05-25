/**
 * Created by sajib on 6/20/2015.
 */
$(function(){


    $(document).on('click', '#approveConfirmationClose', function(event) {
        $('#approvedModal').modal('toggle');
    });


    $(document).on('click', '#approveConfirmation', function(event) {
        event.preventDefault();
        var target = $(this).data("link");
        var gridViewId = $(this).data("view");
        $.ajax({
            url: target,
            type: 'get',
            success: function (response) {
                if(!response.Error) {
                    $('#approvedModal').modal('toggle');
                    $.pjax.reload({container:'#'+gridViewId}); // Reload GridView
                }
            }
        });
    });

    $(document).on('click', '#cancelConfirmation', function(event) {
        event.preventDefault();
        var target = $(this).data("link");
        var gridViewId = $(this).data("view");
        $.ajax({
            url: target,
            type: 'get',
            success: function (response) {
                if(!response.Error) {
                    $('#approvedModal').modal('toggle');
                    $.pjax.reload({container:'#'+gridViewId}); // Reload GridView
                }
            }
        });
    });


    $(document).on('click', '.approvedButton', function(event) {
        event.preventDefault();
        var target = $(this).attr('href');
        $('#approvedModal').modal('show').find('.modal-body').load($(this).attr('href'));
    });


    $(document).on('click', 'button[data-pjax]', function(event) {
        event.preventDefault();
        var target = $(this).attr('value');
        $("#modal .modal-body").load(target, function() {
            $("#modal").modal("show");
        });
    });



    $('body').on('beforeSubmit', 'form#formAjaxCreate', function () {
        var form = $(this);
        //console.log(form.serialize());
        // return false if form still have some validation errors
        if (form.find('.has-error').length) {
            return false;
        }

        // submit form
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function (response) {

                if(response.error) {
                    $.each(response.message, function( index, value ) {
                        $('.field-'+index).addClass('has-error');
                        $('.field-'+index).find('.help-block').append(value);
                    });
                } else {
                    $("#modal .modal-body").load(form.attr('action'), function() {
                        $("#modal").modal("show");
                    });

                    $.pjax.reload({container:'#pjaxGridView'}); // Reload GridView
                }

            }
        });

        return false;
    });

});