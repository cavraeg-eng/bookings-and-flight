(function ($) {
    "use strict";

    $("#travelpayouts_travelpayouts_clear_tables_cache_button").on("click", function () {
        clearTablesCache();
    });

    function clearTablesCache() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'travelpayouts_clear_tables_cache',
            },
            cache: false,
            dataType: 'json',
            success: function (data, textStatus, jqXHR) {
                window.onbeforeunload = function () {
                    window.scrollTo(0, 0);
                }
                location.reload(true);
            },
            error: function () {
                console.log('clear cache error');
            }
        });
    }
})(jQuery);