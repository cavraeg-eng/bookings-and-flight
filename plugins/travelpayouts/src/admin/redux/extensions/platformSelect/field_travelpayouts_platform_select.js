(function ($) {
    "use strict";

    $('#wpbody').on('click', '.travelpayouts-reload-platforms-data', function () {
        const button = $(this);

        button.removeClass('travelpayouts-reload-platforms-data');
        button.css('opacity', 0.5);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'travelpayouts_clear_platforms_select_cache',
            },
            success: function (response) {
                var result = jQuery.parseJSON(response);
                if (result.action && result.action == 'reload') {
                    location.reload(true);
                }
            },
            error: function () {
                console.log('Reload platforms cache cancel error');
            },
        });
    });
})(jQuery);