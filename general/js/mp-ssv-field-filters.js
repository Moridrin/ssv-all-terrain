/**
 * Created by moridrin on 25-2-17.
 */
function mp_ssv_auto_enable_filter() {
    var $ = jQuery.noConflict();
    var filterFields = $(".field-filter");
    filterFields.get().forEach(function (entry, index, array) {
        entry.addEventListener('input',
            function () {
                document.getElementById("filter_" + entry.id).checked = true;
            }
        );
    });
}
