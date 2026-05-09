(function ($) {
    "use strict";

    $('#wpbody').on('click', '.travelpayouts-migrate-search-forms', function () {
        $(this).removeClass('travelpayouts-migrate-search-forms');
        $(this).addClass('travelpayouts-button-loading');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'travelpayouts_migrate_search_forms',
            },
            success: function (response) {
                var result = jQuery.parseJSON(response);
                if (result.action && result.action == 'reload') {
                    location.reload(true);
                }
            },
            error: function () {
                console.log('Migration error');
            },
        });
    });
})(jQuery);