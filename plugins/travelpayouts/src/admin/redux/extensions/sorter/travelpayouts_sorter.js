/*global redux, redux_opts*/
/*
 * Field Sorter jquery function
 * Based on
 * [SMOF - Slightly Modded Options Framework](http://aquagraphite.com/2011/09/slightly-modded-options-framework/)
 * Version 1.4.2
 */

(function ($) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.travelpayouts_sorter = redux.field_objects.travelpayouts_sorter || {};

    var scroll = '';

    $(document).ready(
        function () {
        }
    );

    var confirmOnPageExit = function( e ) {

        // Return; // ONLY FOR DEBUGGING.
        // If we haven't been passed the event get the window.event.
        'use strict';

        var message;

        e = e || window.event;

        message = redux.optName.args.save_pending;

        // For IE6-8 and Firefox prior to version 4.
        if ( e ) {
            e.returnValue = message;
        }

        window.onbeforeunload = null;

        // For Chrome, Safari, IE8+ and Opera 12+.
        return message;
    };

    function redux_change( variable ) {
        'use strict';

        (function( $ ) {
            var rContainer;
            var opt_name;
            var parentID;
            var id;
            var th;
            var subParent;
            var errorCount;
            var errorsLeft;
            var warningCount;
            var warningsLeft;

            variable = $( variable );

            rContainer = $( variable ).parents( '.redux-container:first' );

            if ( redux.customizer ) {
                opt_name = $( '.redux-customizer-opt-name' ).data( 'opt-name' );
            } else {
                opt_name = $.redux.getOptName( rContainer );
            }

            $( 'body' ).trigger( 'check_dependencies', variable );

            if ( variable.hasClass( 'compiler' ) ) {
                $( '#redux-compiler-hook' ).val( 1 );
            }

            parentID = $( variable ).closest( '.redux-group-tab' ).attr( 'id' );

            // Let's count down the errors now. Fancy.  ;).
            id = parentID.split( '_' );

            id = id[0];

            th        = rContainer.find( '.redux-group-tab-link-a[data-key="' + id + '"]' ).parents( '.redux-group-tab-link-li:first' );
            subParent = $( '#' + parentID + '_li' ).parents( '.hasSubSections:first' );

            if ( $( variable ).parents( 'fieldset.redux-field:first' ).hasClass( 'redux-field-error' ) ) {
                $( variable ).parents( 'fieldset.redux-field:first' ).removeClass( 'redux-field-error' );
                $( variable ).parent().find( '.redux-th-error' ).slideUp();

                errorCount = ( parseInt( rContainer.find( '.redux-field-errors span' ).text(), 0 ) - 1 );

                if ( errorCount <= 0 ) {
                    $( '#' + parentID + '_li .redux-menu-error' ).fadeOut( 'fast' ).remove();
                    $( '#' + parentID + '_li .redux-group-tab-link-a' ).removeClass( 'hasError' );
                    $( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.redux-field-errors' ).slideUp();
                    $( variable ).parents( '.redux-container:first' ).find( '.redux-field-errors' ).slideUp();
                    $( '#redux_metaboxes_errors' ).slideUp();
                } else {
                    errorsLeft = ( parseInt( th.find( '.redux-menu-error:first' ).text(), 0 ) - 1 );

                    if ( errorsLeft <= 0 ) {
                        th.find( '.redux-menu-error:first' ).fadeOut().remove();
                    } else {
                        th.find( '.redux-menu-error:first' ).text( errorsLeft );
                    }

                    rContainer.find( '.redux-field-errors span' ).text( errorCount );
                }

                if ( 0 !== subParent.length ) {
                    if ( 0 === subParent.find( '.redux-menu-error' ).length ) {
                        subParent.find( '.hasError' ).removeClass( 'hasError' );
                    }
                }
            }

            if ( $( variable ).parents( 'fieldset.redux-field:first' ).hasClass( 'redux-field-warning' ) ) {
                $( variable ).parents( 'fieldset.redux-field:first' ).removeClass( 'redux-field-warning' );
                $( variable ).parent().find( '.redux-th-warning' ).slideUp();

                warningCount = ( parseInt( rContainer.find( '.redux-field-warnings span' ).text(), 0 ) - 1 );

                if ( warningCount <= 0 ) {
                    $( '#' + parentID + '_li .redux-menu-warning' ).fadeOut( 'fast' ).remove();
                    $( '#' + parentID + '_li .redux-group-tab-link-a' ).removeClass( 'hasWarning' );
                    $( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.redux-field-warnings' ).slideUp();
                    $( variable ).parents( '.redux-container:first' ).find( '.redux-field-warnings' ).slideUp();
                    $( '#redux_metaboxes_warnings' ).slideUp();
                } else {

                    // Let's count down the warnings now. Fancy.  ;).
                    warningsLeft = ( parseInt( th.find( '.redux-menu-warning:first' ).text(), 0 ) - 1 );

                    if ( warningsLeft <= 0 ) {
                        th.find( '.redux-menu-warning:first' ).fadeOut().remove();
                    } else {
                        th.find( '.redux-menu-warning:first' ).text( warningsLeft );
                    }

                    rContainer.find( '.redux-field-warning span' ).text( warningCount );
                }

                if ( 0 !== subParent.length ) {
                    if ( 0 === subParent.find( '.redux-menu-warning' ).length ) {
                        subParent.find( '.hasWarning' ).removeClass( 'hasWarning' );
                    }
                }
            }

            // Don't show the changed value notice while save_notice is visible.
            if ( rContainer.find( '.saved_notice:visible' ).length > 0 ) {
                return;
            }

            if ( ! redux.optName.args.disable_save_warn ) {
                rContainer.find( '.redux-save-warn' ).slideDown();
                window.onbeforeunload = confirmOnPageExit;
            }
        })( jQuery );
    }

    redux.field_objects.travelpayouts_sorter.init = function (selector) {
        if (!selector) {
            selector = $(document).find(".redux-group-tab:visible").find('.redux-container-travelpayouts_sorter:visible');
        }

        $(selector).each(
            function () {
                var el = $(this);
                var parent = el;

                if (!el.hasClass('redux-field-container')) {
                    parent = el.parents('.redux-field-container:first');
                }

                if (parent.is(":hidden")) { // Skip hidden fields
                    return;
                }

                if (parent.hasClass('redux-field-init')) {
                    parent.removeClass('redux-field-init');
                } else {
                    return;
                }
                /**    Sorter (Layout Manager) */
                el.find('.redux-sorter').each(
                    function () {
                        var id = $(this).attr('id');
                        el.find('#' + id).find('ul').sortable(
                            {
                                items: 'li',
                                placeholder: "placeholder",
                                connectWith: '.sortlist_' + id,
                                opacity: 0.8,
                                scroll: false,
                                out: function (event, ui) {
                                    if (!ui.helper) return;
                                    if (ui.offset.top > 0) {
                                        scroll = 'down';
                                    } else {
                                        scroll = 'up';
                                    }
                                    redux.field_objects.travelpayouts_sorter.scrolling($(this).parents('.redux-field-container:first'));

                                },
                                over: function (event, ui) {
                                    scroll = '';
                                },

                                deactivate: function (event, ui) {
                                    scroll = '';
                                },

                                stop: function (event, ui) {
                                    var sorter = redux.travelpayouts_sorter[$(this).attr('data-id')];
                                    var id = $(this).find('h3').text();

                                    if (sorter.limits && id && sorter.limits[id]) {
                                        if ($(this).children('li').length >= sorter.limits[id]) {
                                            $(this).addClass('filled');
                                            if ($(this).children('li').length > sorter.limits[id]) {
                                                $(ui.sender).sortable('cancel');
                                            }
                                        } else {
                                            $(this).removeClass('filled');
                                        }
                                    }
                                },

                                update: function (event, ui) {
                                    var sorter = redux.travelpayouts_sorter[$(this).attr('data-id')];
                                    var id = $(this).find('h3').text();

                                    if (sorter.limits && id && sorter.limits[id]) {
                                        if ($(this).children('li').length >= sorter.limits[id]) {
                                            $(this).addClass('filled');
                                            if ($(this).children('li').length > sorter.limits[id]) {
                                                $(ui.sender).sortable('cancel');
                                            }
                                        } else {
                                            $(this).removeClass('filled');
                                        }
                                    }

                                    $(this).find('.position').each(
                                        function () {
                                            //var listID = $( this ).parent().attr( 'id' );
                                            var listID = $(this).parent().attr('data-id');
                                            var parentID = $(this).parent().parent().attr('data-group-id');

                                            redux_change($(this));

                                            var optionID = $(this).parent().parent().parent().attr('id');

                                            $(this).prop(
                                                "name",
                                                redux.args.opt_name + '[' + optionID + '][' + parentID + '][' + listID + ']'
                                            );
                                        }
                                    );
                                }
                            }
                        );
                        el.find(".redux-sorter").disableSelection();
                    }
                );
            }
        );
    };

    redux.field_objects.travelpayouts_sorter.scrolling = function (selector) {
        if (selector === undefined) {
            return;
        }

        var scrollable = selector.find(".redux-sorter");

        if (scroll == 'up') {
            scrollable.scrollTop(scrollable.scrollTop() - 20);
            setTimeout(redux.field_objects.travelpayouts_sorter.scrolling, 50);
        } else if (scroll == 'down') {
            scrollable.scrollTop(scrollable.scrollTop() + 20);
            setTimeout(redux.field_objects.travelpayouts_sorter.scrolling, 50);
        }
    };

})(jQuery);
