    /**
    * Created by sajib on 6/20/2015.
    */

    $(function() {
        "use strict";

        $(".checkme").change(function(){
            var checked = $(this).is(':checked');
            var id = jQuery(this).attr("id");
            var checkBoxClass =  '.checkBox_'+id;
            if(checked){
                $(checkBoxClass).prop("checked", true);
            }else{
                $(checkBoxClass).prop("checked", false);
            }
        });

    });