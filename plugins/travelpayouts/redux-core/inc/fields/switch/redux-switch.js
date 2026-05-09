/*global Redux_Travelpayouts_change, redux*/

/**
 * Switch
 * Dependencies        : jquery
 * Feature added by    : Smartik - http://smartik.ws/
 * Date            : 03.17.2013
 */

(function ($) {
    'use strict';

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.switch = redux.field_objects.switch || {};

    redux.field_objects.switch.init = function (selector) {
        selector = $.redux.getSelector(selector, 'switch');

        $(selector).each(function () {
            var el = $(this);
            var parent = el;

            if (!el.hasClass('redux-field-container')) {
                parent = el.parents('.redux-field-container:first');
            }

            if (parent.is(':hidden')) {
                return;
            }

            if (parent.hasClass('redux-field-init')) {
                parent.removeClass('redux-field-init');
            } else {
                return;
            }

            el.find('.tp-switch-item--enable').on('click', function () {
                var parent;
                var obj;
                var $fold;
                console.log('click');
                if ($(this).hasClass('tp-switch-item--active')) {
                    return;
                }

                parent = $(this).parents('.tp-switch');

                $('.tp-switch-item--disable', parent).removeClass(
                    'tp-switch-item--active',
                );
                $(this).addClass('tp-switch-item--active');
                $('.checkbox-input', parent).val(1).trigger('change');

                Redux_Travelpayouts_change($('.checkbox-input', parent));

                // Fold/unfold related options.
                obj = $(this);
                $fold = '.f_' + obj.data('id');

                el.find($fold).slideDown('normal', 'swing');
            });

            el.find('.tp-switch-item--disable').on('click', function () {
                var parent;
                var obj;
                var $fold;

                if ($(this).hasClass('tp-switch-item--active')) {
                    return;
                }

                parent = $(this).parents('.tp-switch');

                $('.tp-switch-item--enable', parent).removeClass(
                    'tp-switch-item--active',
                );
                $(this).addClass('tp-switch-item--active');
                $('.checkbox-input', parent).val(0).trigger('change');

                Redux_Travelpayouts_change($('.checkbox-input', parent));

                // Fold/unfold related options.
                obj = $(this);
                $fold = '.f_' + obj.data('id');

                el.find($fold).slideUp('normal', 'swing');
            });

            el.find('.tp-switch-itemspan').find().attr('unselectable', 'on');
        });
    };
})(jQuery);
