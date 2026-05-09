<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions\platformSelect;

use Travelpayouts;
use Travelpayouts\admin\redux\extensions\sortBy\SortByField;

class PlatformSelectField extends SortByField
{
    public const TYPE = 'travelpayouts_platform_select';

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     * @since ReduxFramework 1.0.0
     */
    public function render()
    {
        $sortable = (isset($this->field['sortable']) && $this->field['sortable']) ? ' select2-sortable"' : "";

        if (!empty($sortable)) { // Dummy proofing  :P
            $this->field['multi'] = true;
        }

        if (!empty($this->field['data']) && empty($this->field['options'])) {
            if (empty($this->field['args'])) {
                $this->field['args'] = [];
            }

            if ($this->field['data'] == "elusive-icons" || $this->field['data'] == "elusive-icon" || $this->field['data'] == "elusive") {
                $icons_file = ReduxFramework::$_dir . 'inc/fields/select/elusive-icons.php';
                /**
                 * filter 'redux-font-icons-file}'
                 * @param array $icon_file File for the icons
                 */
                $icons_file = apply_filters('redux-font-icons-file', $icons_file);

                /**
                 * filter 'redux/{opt_name}/field/font/icons/file'
                 * @param array $icon_file File for the icons
                 */
                $icons_file = apply_filters("redux/{$this->parent->args['opt_name']}/field/font/icons/file", $icons_file);
                if (file_exists($icons_file)) {
                    require_once $icons_file;
                }
            }

            $this->field['options'] = $this->parent->get_wordpress_data($this->field['data'], $this->field['args']);
        }

        if (!empty($this->field['data']) && ($this->field['data'] == "elusive-icons" || $this->field['data'] == "elusive-icon" || $this->field['data'] == "elusive")) {
            $this->field['class'] .= " font-icons";
        }
        //if

        if (!empty($this->field['options'])) {
            $multi = (isset($this->field['multi']) && $this->field['multi']) ? ' multiple="multiple"' : "";

            if (!empty($this->field['width'])) {
                $width = ' style="' . $this->field['width'] . '"';
            } else {
                $width = ' style="width: 40%;"';
            }

            $nameBrackets = "";
            if (!empty($multi)) {
                $nameBrackets = "[]";
            }

            $placeholder = (isset($this->field['placeholder'])) ? esc_attr($this->field['placeholder']) : Travelpayouts::__('Select an item');

            if (isset($this->field['select2'])) { // if there are any let's pass them to js
                $select2_params = json_encode($this->field['select2']);
                $select2_params = htmlspecialchars($select2_params, ENT_QUOTES);

                echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
            }

            if (isset($this->field['multi']) && $this->field['multi'] && isset($this->field['sortable']) && $this->field['sortable'] && !empty($this->value) && is_array($this->value)) {
                $origOption = $this->field['options'];
                $this->field['options'] = [];

                foreach ($this->value as $value) {
                    $this->field['options'][$value] = $origOption[$value];
                }

                if (count($this->field['options']) < count($origOption)) {
                    foreach ($origOption as $key => $value) {
                        if (!in_array($key, $this->field['options'])) {
                            $this->field['options'][$key] = $value;
                        }
                    }
                }
            }

            $sortable = (isset($this->field['sortable']) && $this->field['sortable']) ? ' select2-sortable"' : "";
            echo '<div class="tp-flex tp-items-center tp-gap-4">';
            echo '<select ' . $multi . ' id="' . $this->field['id'] . '-select" data-placeholder="' . $placeholder . '" name="' . $this->field['name'] . $this->field['name_suffix'] . $nameBrackets . '" class="tp-select redux-select-item ' . $this->field['class'] . $sortable . '"' . $width . ' rows="6">';

            foreach ($this->field['options'] as $k => $v) {

                if (is_array($v)) {
                    echo '<optgroup label="' . $k . '">';

                    foreach ($v as $opt => $val) {
                        $this->make_option($opt, $val, $k);
                    }

                    echo '</optgroup>';

                    continue;
                }

                $this->make_option($k, $v);
            }

            echo '</select>';
            echo '<span class="travelpayouts-reload-platforms-data tp-button tp-button--secondary"><i class="tp-i-tabler:refresh"></i></span>';
            echo '</div>';
        } else {
            echo '<strong>' . Travelpayouts::__('No items of this type were found.') . '</strong>';
        }
    }

    private function make_option($id, $value, $group_name = '')
    {
        if (is_array($this->value)) {
            $selected = (is_array($this->value) && in_array($id, $this->value)) ? ' selected="selected"' : '';
        } else {
            $selected = selected($this->value, $id, false);
        }

        echo '<option value="' . $id . '"' . $selected . '>' . $value . '</option>';
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-travelpayouts-settings-platform-js',
            $this->url . 'field_travelpayouts_platform_select.min.js',
            ['jquery'],
            time(),
            true
        );
    }
}