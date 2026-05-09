!(function (i) {
    'use strict';
    (redux.field_objects = redux.field_objects || {}),
        (redux.field_objects.switch = redux.field_objects.switch || {}),
        (redux.field_objects.switch.init = function (t) {
            (t = i.redux.getSelector(t, 'switch')),
                i(t).each(function () {
                    var t = i(this),
                        e = t;
                    t.hasClass('redux-field-container') ||
                        (e = t.parents('.redux-field-container:first')),
                        e.is(':hidden') ||
                            (e.hasClass('redux-field-init') &&
                                (e.removeClass('redux-field-init'),
                                t
                                    .find('.tp-switch-item--enable')
                                    .on('click', function () {
                                        var e, s;
                                        console.log('click'),
                                            i(this).hasClass(
                                                'tp-switch-item--active',
                                            ) ||
                                                ((e =
                                                    i(this).parents(
                                                        '.tp-switch',
                                                    )),
                                                i(
                                                    '.tp-switch-item--disable',
                                                    e,
                                                ).removeClass(
                                                    'tp-switch-item--active',
                                                ),
                                                i(this).addClass(
                                                    'tp-switch-item--active',
                                                ),
                                                i('.checkbox-input', e)
                                                    .val(1)
                                                    .trigger('change'),
                                                Redux_Travelpayouts_change(
                                                    i('.checkbox-input', e),
                                                ),
                                                (s =
                                                    '.f_' + i(this).data('id')),
                                                t
                                                    .find(s)
                                                    .slideDown(
                                                        'normal',
                                                        'swing',
                                                    ));
                                    }),
                                t
                                    .find('.tp-switch-item--disable')
                                    .on('click', function () {
                                        var e, s;
                                        i(this).hasClass(
                                            'tp-switch-item--active',
                                        ) ||
                                            ((e =
                                                i(this).parents('.tp-switch')),
                                            i(
                                                '.tp-switch-item--enable',
                                                e,
                                            ).removeClass(
                                                'tp-switch-item--active',
                                            ),
                                            i(this).addClass(
                                                'tp-switch-item--active',
                                            ),
                                            i('.checkbox-input', e)
                                                .val(0)
                                                .trigger('change'),
                                            Redux_Travelpayouts_change(
                                                i('.checkbox-input', e),
                                            ),
                                            (s = '.f_' + i(this).data('id')),
                                            t
                                                .find(s)
                                                .slideUp('normal', 'swing'));
                                    }),
                                t
                                    .find('.tp-switch-itemspan')
                                    .find()
                                    .attr('unselectable', 'on')));
                });
        });
})(jQuery);
