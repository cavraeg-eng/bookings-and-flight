<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions\linksForms;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ConfigurableField;

class LinksForms extends ConfigurableField
{
    public const TYPE = 'travelpayouts_links_forms';

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     * @return      void
     * @since       1.0.0
     * @access      public
     */
    public function render()
    {

        $defaults = [
            'show' => [
                'title' => true,
                'description' => true,
                'url' => true,
            ],
            'content_title' => Travelpayouts::__('Link'),
        ];

        $this->field = wp_parse_args($this->field, $defaults);

        echo '<div class="redux-slides-accordion" data-new-content-title="' . esc_attr(sprintf(Travelpayouts::__('New %s'), $this->field['content_title'])) . '">';

        $x = 0;

        if (isset ($this->value) && is_array($this->value) && !empty ($this->value)) {

            $slides = $this->value;

            foreach ($slides as $slide) {

                if (empty ($slide)) {
                    continue;
                }

                $defaults = [
                    'arl_url' => '',
                    'arl_anchor' => '',
                    'arl_event' => '',
                    'arl_nofollow' => '',
                    'arl_replace' => '',
                    'arl_target_blank' => '',
                    'sort' => '',
                    'select' => [],
                ];
                $slide = wp_parse_args($slide, $defaults);

                echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . $this->field['id'] . '"><h3><span class="redux-slides-header">' . $slide['arl_url'] . '</span></h3><div>';

                echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';

                $placeholder = Travelpayouts::__('Link');
                $key = 'arl_url';
                $id = $this->field['id'] . '-' . $key . '_' . $x;
                echo '<li><label for="' . $id . '">' . $placeholder . '</label><br><input type="text" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" value="' . esc_attr($slide[$key]) . '" class="large-text slide-title" /></li>';

                $placeholder = Travelpayouts::__('Anchor phrases');
                $key = 'arl_anchor';
                $id = $this->field['id'] . '-' . $key . '_' . $x;
                echo '<li><label for="' . $id . '">' . $placeholder . '</label><br><textarea name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-' . $key . '_' . $x . '" class="large-text" rows="6">' . esc_attr($slide[$key]) . '</textarea></li>';

                $placeholder = Travelpayouts::__('Onclick event');
                $key = 'arl_event';
                $id = $this->field['id'] . '-' . $key . '_' . $x;
                echo '<li><label for="' . $id . '">' . $placeholder . '</label><br><textarea name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-' . $key . '_' . $x . '" class="large-text" rows="6">' . esc_attr($slide[$key]) . '</textarea></li>';

                $placeholder = Travelpayouts::__('Add nofollow attribute');
                $key = 'arl_nofollow';
                $id = $this->field['id'] . '-' . $key . '_' . $x;
                if (isset($slide[$key])) {
                    $checked = (esc_attr($slide[$key]) == 'on') ? 'checked' : '';
                } else {
                    $checked = 'checked';
                }
                echo '<li><input type="checkbox" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" ' . $checked . ' /><label for="' . $id . '">' . $placeholder . '</label></li>';

                $placeholder = Travelpayouts::__('Replace existing links');
                $key = 'arl_replace';
                $id = $this->field['id'] . '-' . $key . '_' . $x;
                if (isset($slide[$key])) {
                    $checked = (esc_attr($slide[$key]) == 'on') ? 'checked' : '';
                } else {
                    $checked = '';
                }
                echo '<li><input type="checkbox" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" ' . $checked . ' /><label for="' . $id . '">' . $placeholder . '</label></li>';

                $placeholder = Travelpayouts::__('Open in new window');
                $key = 'arl_target_blank';
                $id = $this->field['id'] . '-' . $key . '_' . $x;
                if (isset($slide[$key])) {
                    $checked = (esc_attr($slide[$key]) == 'on') ? 'checked' : '';
                } else {
                    $checked = 'checked';
                }
                echo '<li><input type="checkbox" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" ' . $checked . ' /><label for="' . $id . '">' . $placeholder . '</label></li>';

                echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $slide['sort'] . '" />';
                echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . Travelpayouts::__('Delete') . '</a></li>';
                echo '</ul></div></fieldset></div>';
                $x++;
            }
        }

        if ($x == 0) {
            echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . $this->field['id'] . '"><h3><span class="redux-slides-header">' . esc_attr(sprintf(Travelpayouts::__('New %s'), $this->field['content_title'])) . '</span></h3><div>';

            echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';
            $placeholder = Travelpayouts::__('Link');
            $key = 'arl_url';
            $id = $this->field['id'] . '-' . $key . '_' . $x;
            echo '<li><label for="' . $id . '">' . $placeholder . '</label><br><input type="text" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" value="" class="large-text slide-title" /></li>';

            $placeholder = Travelpayouts::__('Anchor phrases');
            $key = 'arl_anchor';
            $id = $this->field['id'] . '-' . $key . '_' . $x;
            echo '<li><label for="' . $id . '">' . $placeholder . '</label><br><textarea name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-' . $key . '_' . $x . '" class="large-text" rows="6"></textarea></li>';

            $placeholder = Travelpayouts::__('Onclick event');
            $key = 'arl_event';
            $id = $this->field['id'] . '-' . $key . '_' . $x;
            echo '<li><label for="' . $id . '">' . $placeholder . '</label><br><textarea name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-' . $key . '_' . $x . '" class="large-text" rows="6"></textarea></li>';

            $placeholder = Travelpayouts::__('Add the nofollow attribute');
            $key = 'arl_nofollow';
            $id = $this->field['id'] . '-' . $key . '_' . $x;
            echo '<li><input type="checkbox" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" checked /><label for="' . $id . '">' . $placeholder . '</label></li>';

            $placeholder = Travelpayouts::__('Replace existing links');
            $key = 'arl_replace';
            $id = $this->field['id'] . '-' . $key . '_' . $x;
            echo '<li><input type="checkbox" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" /><label for="' . $id . '">' . $placeholder . '</label></li>';

            $placeholder = Travelpayouts::__('Open in a new window');
            $key = 'arl_target_blank';
            $id = $this->field['id'] . '-' . $key . '_' . $x;
            echo '<li><input type="checkbox" id="' . $id . '" name="' . $this->field['name'] . '[' . $x . '][' . $key . ']' . $this->field['name_suffix'] . '" checked /><label for="' . $id . '">' . $placeholder . '</label></li>';

            echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $x . '" />';
            echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . Travelpayouts::__('Delete') . '</a></li>';
            echo '</ul></div></fieldset></div>';
        }
        echo '</div><a href="javascript:void(0);" class="button redux-slides-add button-primary" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->field['name'] . '[title][]' . $this->field['name_suffix'] . '">' . sprintf(Travelpayouts::__('Add %s'), $this->field['content_title']) . '</a><br/>';
    }

    /**
     * Enqueue Function.
     * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
     * @return      void
     * @since       1.0.0
     * @access      public
     */
    public function enqueue()
    {
        wp_enqueue_style(
            'redux-field-travelpayouts-links-forms-css',
            $this->url . 'field_travelpayouts_links_forms.css',
            [],
            time(),
            'all'
        );

        wp_enqueue_script(
            'redux-field-travelpayouts-links-forms-js',
            $this->url . 'field_travelpayouts_links_forms.js',
            ['jquery'],
            time(),
            true
        );
    }
}