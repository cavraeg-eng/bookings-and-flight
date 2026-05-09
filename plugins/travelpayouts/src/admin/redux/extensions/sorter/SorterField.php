<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions\sorter;

use Travelpayouts\admin\redux\base\ConfigurableField;

class SorterField extends ConfigurableField
{
    public const TYPE = 'travelpayouts_sorter';
    /**
     * @var array
     */
    public $columnsOptions = [];
    public $limits;
    public $options;
    public $data;
    public $id;
    public $name;

    private function replace_id_with_slug($arr)
    {
        $new_arr = [];
        if (!empty($arr)) {
            foreach ($arr as $id => $name) {

                if (is_numeric($id)) {
                    $slug = strtolower($name);
                    $slug = str_replace(' ', '-', $slug);

                    $new_arr[$slug] = $name;
                } else {
                    $new_arr[$id] = $name;
                }
            }
        }

        return $new_arr;
    }

    private function is_value_empty($val)
    {
        if (!empty($val)) {
            foreach ($val as $section => $arr) {
                if (!empty($arr)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Field Render Function.
     * Takes the vars and outputs the HTML for the field in the settings
     * @since 1.0.0
     */
    public function render()
    {
        if (!is_array($this->value) && isset($this->field['options'])) {
            $this->value = $this->field['options'];
        }

        if (!isset($this->field['args'])) {
            $this->field['args'] = [];
        }

        if (isset($this->field['data'])) {
            $this->field['options'] = $this->parent->options_defaults[$this->field['id']];
        }

        // Make sure to get list of all the default blocks first
        $all_blocks = !empty($this->field['options']) ? $this->field['options'] : [];
        $temp = []; // holds default blocks
        $temp2 = []; // holds saved blocks

        foreach ($all_blocks as $blocks) {
            $temp = array_merge($temp, $blocks);
        }

        $temp = $this->replace_id_with_slug($temp);

        if ($this->is_value_empty($this->value)) {
            if (!empty($this->field['options'])) {
                $this->value = $this->field['options'];
            }
        }

        $sortlists = $this->value;
        if (!empty($sortlists)) {
            foreach ($sortlists as $section => $arr) {
                $arr = $this->replace_id_with_slug($arr);
                $sortlists[$section] = $arr;
                $this->value[$section] = $arr;
            }
        }

        if (is_array($sortlists)) {
            foreach ($sortlists as $sortlist) {
                $temp2 = array_merge($temp2, $sortlist);
            }

            // now let's compare if we have anything missing
            foreach ($temp as $k => $v) {
                // k = id/slug
                // v = name

                if (!empty($temp2)) {
                    if (!array_key_exists($k, $temp2)) {
                        if (isset($sortlists['Disabled'])) {
                            $sortlists['Disabled'][$k] = $v;
                        } else {
                            $sortlists['disabled'][$k] = $v;
                        }
                    }
                }
            }

            // now check if saved blocks has blocks not registered under default blocks
            foreach ($sortlists as $key => $sortlist) {
                // key = enabled, disabled, backup
                // sortlist = id => name

                foreach ($sortlist as $k => $v) {
                    // k = id
                    // v = name
                    if (!array_key_exists($k, $temp)) {
                        unset($sortlist[$k]);
                    }
                }
                $sortlists[$key] = $sortlist;
            }

            // assuming all sync'ed, now get the correct naming for each block
            foreach ($sortlists as $key => $sortlist) {
                foreach ($sortlist as $k => $v) {
                    $sortlist[$k] = $temp[$k];
                }
                $sortlists[$key] = $sortlist;
            }

            if ($sortlists) {

                echo '<fieldset id="' . esc_attr($this->field['id']) . '" class="redux-sorter-container redux-sorter redux-container-sorter tp-drag-list">';
                foreach ($sortlists as $group => $sortlist) {
                    $groupLabel = isset($this->field['columnsOptions']) && $this->field['columnsOptions'][$group] && $this->field['columnsOptions'][$group]['label'] ?
                        $this->field['columnsOptions'][$group]['label'] :
                        $group;
                    $filled = '';

                    if (isset($this->field['limits'][$group]) && count($sortlist) >= $this->field['limits'][$group]) {
                        $filled = ' filled';
                    }

                    echo '<ul id="' . esc_attr($this->field['id'] . '_' . $group) . '" class="tp-card sortlist_' . esc_attr($this->field['id'] . $filled) . '" data-id="' . esc_attr($this->field['id']) . '" data-group-id="' . esc_attr($group) . '">';
                    echo '<div class="tp-card-header">' . esc_html($groupLabel) . '</div>';

                    if (!isset($sortlist['placebo'])) {
                        $sortlist = array_merge(['placebo' => 'placebo'], $sortlist);
                    }

                    foreach ($sortlist as $key => $list) {

                        echo '<input class="sorter-placebo" type="hidden" name="' . esc_attr($this->field['name']) . '[' . $group . '][placebo]' . esc_attr($this->field['name_suffix']) . '" value="placebo">';

                        if ($key != 'placebo') {

                            //echo '<li id="' . $key . '" class="sortee">';
                            echo '<li id="sortee-' . esc_attr($key) . '" class="sortee" data-id="' . esc_attr($key) . '">';
                            echo '<input class="position ' . esc_attr($this->field['class']) . '" type="hidden" name="' . esc_attr($this->field['name'] . '[' . $group . '][' . $key . ']' . $this->field['name_suffix']) . '" value="' . esc_attr($list) . '">';
                            echo esc_html($list);
                            echo '</li>';
                        }
                    }

                    echo '</ul>';
                }
                echo '</fieldset>';
            }
        }
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-sorter-js',
            $this->url . 'travelpayouts_sorter.min.js',
            ['jquery', 'redux-js', 'jquery-ui-sortable'],
            time(),
            true
        );
    }

    /**
     * Functions to pass data from the PHP to the JS at render time.
     * @return array Params to be saved as a javascript object accessable to the UI.
     * @since  Redux_Framework 3.1.5
     */
    public function localize($field, $value = '')
    {

        $params = [];

        if (isset($field['limits']) && !empty($field['limits'])) {
            $params['limits'] = $field['limits'];
        }

        if (empty($value)) {
            $value = $this->value;
        }
        $params['val'] = $value;

        return $params;
    }
}