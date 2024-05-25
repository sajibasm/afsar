/**
 * Created by sajib on 6/20/2015.
 */

/* To initialize BS3 tooltips set this below */
$(function () {
    //$("[data-toggle='tooltip']").tooltip();
});
/* To initialize BS3 popovers set this below */
$(function () {
    //$("[data-toggle='popover']").popover();
    if ($('[data-widget="treeview"]').length > 0) {
        $('[data-widget="treeview"]').each(function () {
            Treeview._jQueryInterface.call($(this), 'init');
        });
    }

});

//var n = new Noty({layout: 'topRight', killer: true});




