/**
 * Bookings and Flights Content Manager - Admin Scripts
 */

(function($) {
    'use strict';

    /**
     * Image Upload Handler
     */
    class ImageUploader {
        constructor(button, options) {
            this.button = button;
            this.targetId = button.data('target');
            this.options = $.extend({}, {
                previewSelector: '.bookings_and_flights-image-preview',
                inputSelector: '.bookings_and_flights-image-id',
                removeSelector: '.bookings_and_flights-remove-button',
            }, options);

            this.$container = this.button.closest('.bookings_and_flights-image-upload');
            this.$preview = this.$container.find(this.options.previewSelector);
            this.$input = this.$container.find(this.options.inputSelector);
            this.$removeBtn = this.$container.find(this.options.removeSelector);

            this.init();
        }

        init() {
            this.button.on('click', this.openUploader.bind(this));
            this.$removeBtn.on('click', this.removeImage.bind(this));
        }

        openUploader(e) {
            e.preventDefault();

            // If media frame already exists, reopen it
            if (this.frame) {
                this.frame.open();
                return;
            }

            // Create the media frame
            this.frame = wp.media({
                title: bookings_and_flightsAdmin.uploaderTitle || 'Select Image',
                button: {
                    text: bookings_and_flightsAdmin.uploaderButton || 'Use This Image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            // When an image is selected
            this.frame.on('select', this.onSelect.bind(this));

            // Open the modal
            this.frame.open();
        }

        onSelect() {
            const attachment = this.frame.state().get('selection').first().toJSON();

            // Set input value
            this.$input.val(attachment.id);

            // Update preview
            this.$preview.html(`<img src="${attachment.url}" alt="">`);

            // Show remove button
            this.$removeBtn.show();
        }

        removeImage(e) {
            e.preventDefault();

            // Clear input
            this.$input.val('');

            // Reset preview
            this.$preview.html('No image selected');

            // Hide remove button
            this.$removeBtn.hide();
        }
    }

    /**
     * Gallery Upload Handler
     */
    class GalleryUploader {
        constructor($container) {
            this.$container = $container;
            this.$preview = $container.find('.bookings_and_flights-gallery-preview');
            this.$input = $container.find('.bookings_and_flights-gallery-ids');
            this.$addBtn = $container.find('.bookings_and_flights-gallery-button');

            this.init();
        }

        init() {
            this.$addBtn.on('click', this.openUploader.bind(this));
            this.$container.on('click', '.bookings_and_flights-gallery-remove', this.removeImage.bind(this));

            // Make sortable
            this.$preview.sortable({
                items: '.bookings_and_flights-gallery-item',
                cursor: 'move',
                update: this.updateInput.bind(this)
            });
        }

        openUploader(e) {
            e.preventDefault();

            if (this.frame) {
                this.frame.open();
                return;
            }

            this.frame = wp.media({
                title: bookings_and_flightsAdmin.galleryTitle || 'Select Images',
                button: {
                    text: bookings_and_flightsAdmin.galleryButton || 'Add to Gallery'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            this.frame.on('select', this.onSelect.bind(this));
            this.frame.open();
        }

        onSelect() {
            const attachments = this.frame.state().get('selection').toJSON();

            attachments.forEach(attachment => {
                const $item = $(`
                    <div class="bookings_and_flights-gallery-item" data-id="${attachment.id}">
                        <img src="${attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url}" alt="">
                        <button type="button" class="bookings_and_flights-gallery-remove">&times;</button>
                    </div>
                `);
                this.$preview.append($item);
            });

            this.updateInput();
        }

        removeImage(e) {
            e.preventDefault();
            $(e.target).closest('.bookings_and_flights-gallery-item').remove();
            this.updateInput();
        }

        updateInput() {
            const ids = [];
            this.$preview.find('.bookings_and_flights-gallery-item').each(function() {
                ids.push($(this).data('id'));
            });
            this.$input.val(ids.join(','));
        }
    }

    /**
     * Repeater Field Handler
     */
    class RepeaterField {
        constructor($container) {
            this.$container = $container;
            this.$rows = $container.find('.bookings_and_flights-repeater-rows');
            this.$addBtn = $container.find('.bookings_and_flights-repeater-add');
            this.template = $container.find('.bookings_and_flights-repeater-template').html();
            this.fieldName = $container.data('field');

            this.init();
        }

        init() {
            this.$addBtn.on('click', this.addRow.bind(this));
            this.$container.on('click', '.bookings_and_flights-repeater-remove', this.removeRow.bind(this));

            // Make sortable
            this.$rows.sortable({
                handle: '.bookings_and_flights-repeater-handle',
                cursor: 'move',
                placeholder: 'bookings_and_flights-repeater-row ui-sortable-placeholder',
                update: this.reindex.bind(this)
            });
        }

        addRow(e) {
            e.preventDefault();
            const index = this.$rows.find('.bookings_and_flights-repeater-row').length;
            const html = this.template.replace(/\{\{INDEX\}\}/g, index);
            this.$rows.append(html);
        }

        removeRow(e) {
            e.preventDefault();
            if (confirm(bookings_and_flightsAdmin.confirmRemove || 'Are you sure?')) {
                $(e.target).closest('.bookings_and_flights-repeater-row').remove();
                this.reindex();
            }
        }

        reindex() {
            this.$rows.find('.bookings_and_flights-repeater-row').each((index, row) => {
                $(row).find('[name]').each((i, input) => {
                    const name = $(input).attr('name');
                    const newName = name.replace(/\[\d+\]/, `[${index}]`);
                    $(input).attr('name', newName);
                });
            });
        }
    }

    /**
     * oEmbed Field Handler
     *
     * Debounced AJAX preview on URL input.
     */
    class OEmbedField {
        constructor($container) {
            this.$container = $container;
            this.$input = $container.find('.bookings_and_flights-oembed-input');
            this.$preview = $container.find('.bookings_and_flights-oembed-preview');
            this.debounceTimer = null;

            this.init();
        }

        init() {
            this.$input.on('input', this.onInput.bind(this));
        }

        onInput() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.fetchPreview();
            }, 800);
        }

        fetchPreview() {
            const url = this.$input.val().trim();

            if (!url) {
                this.$preview.html(
                    '<p class="bookings_and_flights-oembed-placeholder">Enter a URL above to see a preview.</p>'
                );
                return;
            }

            this.$preview.html(
                '<p class="bookings_and_flights-oembed-loading">Loading preview&hellip;</p>'
            );

            $.post(bookings_and_flightsAdmin.ajaxUrl, {
                action: 'bookings_and_flights_oembed_preview',
                nonce: bookings_and_flightsAdmin.oembedNonce,
                url: url
            })
            .done((response) => {
                if (response.success && response.data.html) {
                    // SECURITY NOTE: response.data.html is WordPress-sanitized wp_oembed_get() output.
                    // If the server endpoint changes, re-evaluate this .html() sink for XSS risk.
                    this.$preview.html(response.data.html);
                } else {
                    const msg = response.data && response.data.message
                        ? response.data.message
                        : 'Unable to load embed preview.';
                    this.$preview.html(
                        '<p class="bookings_and_flights-oembed-error">' + $('<span>').text(msg).html() + '</p>'
                    );
                }
            })
            .fail(() => {
                this.$preview.html(
                    '<p class="bookings_and_flights-oembed-error">Request failed. Please try again.</p>'
                );
            });
        }
    }

    /**
     * Conditional Fields Handler
     *
     * Shows/hides fields based on data-show-when-field and data-show-when-value attributes.
     */
    class ConditionalFields {
        constructor($metaBox) {
            this.$metaBox = $metaBox;
            this.dependents = [];
            this.controllerMap = {};

            this.init();
        }

        init() {
            // Build map of controller fields → dependent fields.
            this.$metaBox.find('[data-show-when-field]').each((i, el) => {
                const $el = $(el);
                const controllerKey = $el.data('show-when-field');
                const requiredValue = String($el.data('show-when-value'));

                if (!this.controllerMap[controllerKey]) {
                    this.controllerMap[controllerKey] = [];
                }

                this.controllerMap[controllerKey].push({
                    $el: $el,
                    requiredValue: requiredValue
                });
            });

            // Bind change/input events on controller fields and evaluate initial state.
            Object.keys(this.controllerMap).forEach((controllerKey) => {
                const $controller = this.$metaBox.find('[name="' + controllerKey + '"]');

                if (!$controller.length) {
                    // Controller not in this meta box — trust server-side evaluation.
                    return;
                }

                $controller.on('change input', () => {
                    this.evaluate(controllerKey);
                });

                this.evaluate(controllerKey);
            });
        }

        getFieldValue(fieldName) {
            const $field = this.$metaBox.find('[name="' + fieldName + '"]');

            if (!$field.length) {
                return '';
            }

            const type = $field.attr('type');

            if ('checkbox' === type) {
                return $field.is(':checked') ? '1' : '0';
            }

            if ('radio' === type) {
                return this.$metaBox.find('[name="' + fieldName + '"]:checked').val() || '';
            }

            return $field.val() || '';
        }

        evaluate(controllerKey) {
            const currentValue = this.getFieldValue(controllerKey);
            const dependents = this.controllerMap[controllerKey] || [];

            dependents.forEach((dep) => {
                if (String(currentValue) === dep.requiredValue) {
                    dep.$el.removeClass('bookings_and_flights-field--hidden');
                } else {
                    dep.$el.addClass('bookings_and_flights-field--hidden');
                }
            });
        }
    }

    /**
     * Tab Manager
     *
     * Handles tabbed section UI with keyboard navigation,
     * sessionStorage persistence, and widget re-initialization.
     */
    class TabManager {
        constructor($container) {
            this.$container = $container;
            this.tabsId = $container.data('tabs-id');
            this.$tabs = $container.find('[role="tab"]');
            this.$panels = $container.find('[role="tabpanel"]');
            this.storageKey = 'bookings_and_flights_active_tab_' + this.tabsId;

            this.init();
        }

        init() {
            this.$tabs.on('click', this.onTabClick.bind(this));
            this.$tabs.on('keydown', this.onKeyDown.bind(this));
            this.restoreActiveTab();
        }

        activateTab($tab) {
            // Deactivate all tabs.
            this.$tabs
                .attr('aria-selected', 'false')
                .attr('tabindex', '-1')
                .removeClass('bookings_and_flights-tabs__tab--active');

            // Hide all panels.
            this.$panels.attr('hidden', '');

            // Activate selected tab.
            $tab
                .attr('aria-selected', 'true')
                .attr('tabindex', '0')
                .addClass('bookings_and_flights-tabs__tab--active');

            // Show corresponding panel.
            const panelId = $tab.attr('aria-controls');
            const $panel = $('#' + panelId);
            $panel.removeAttr('hidden');

            // Re-init color pickers that were in hidden panels.
            if ($.fn.wpColorPicker) {
                $panel.find('.bookings_and_flights-color-picker').not('.wp-color-picker').each(function() {
                    $(this).wpColorPicker();
                });
            }

            // Refresh sortable instances in the panel.
            $panel.find('.ui-sortable').each(function() {
                $(this).sortable('refresh');
            });

            // Persist active tab.
            try {
                sessionStorage.setItem(this.storageKey, $tab.attr('id'));
            } catch (e) {
                // sessionStorage unavailable — silently ignore.
            }
        }

        onTabClick(e) {
            e.preventDefault();
            this.activateTab($(e.currentTarget));
        }

        onKeyDown(e) {
            const $current = $(e.currentTarget);
            const index = this.$tabs.index($current);
            let newIndex;

            switch (e.key) {
                case 'ArrowRight':
                    newIndex = (index + 1) % this.$tabs.length;
                    break;
                case 'ArrowLeft':
                    newIndex = (index - 1 + this.$tabs.length) % this.$tabs.length;
                    break;
                case 'Home':
                    newIndex = 0;
                    break;
                case 'End':
                    newIndex = this.$tabs.length - 1;
                    break;
                default:
                    return;
            }

            e.preventDefault();
            const $newTab = this.$tabs.eq(newIndex);
            $newTab.focus();
            this.activateTab($newTab);
        }

        restoreActiveTab() {
            try {
                const savedTabId = sessionStorage.getItem(this.storageKey);
                if (savedTabId) {
                    const $savedTab = this.$tabs.filter('#' + savedTabId);
                    if ($savedTab.length) {
                        this.activateTab($savedTab);
                        return;
                    }
                }
            } catch (e) {
                // sessionStorage unavailable.
            }
        }
    }

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        // Initialize image uploaders
        $('.bookings_and_flights-upload-button').each(function() {
            new ImageUploader($(this));
        });

        // Initialize gallery uploaders
        $('.bookings_and_flights-gallery-upload').each(function() {
            new GalleryUploader($(this));
        });

        // Initialize repeater fields
        $('.bookings_and_flights-repeater').each(function() {
            new RepeaterField($(this));
        });

        // Initialize color pickers (only in visible containers)
        if ($.fn.wpColorPicker) {
            $('.bookings_and_flights-color-picker').filter(function() {
                return ! $(this).closest('[hidden]').length;
            }).wpColorPicker();
        }

        // Initialize oEmbed fields
        $('.bookings_and_flights-oembed-field').each(function() {
            new OEmbedField($(this));
        });

        // Initialize conditional fields per meta box
        $('.postbox').each(function() {
            const $box = $(this);
            if ($box.find('[data-show-when-field]').length) {
                new ConditionalFields($box);
            }
        });

        // Initialize tab managers
        $('.bookings_and_flights-tabs').each(function() {
            new TabManager($(this));
        });
    });

})(jQuery);
