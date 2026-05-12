<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions\clearTableCache;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ConfigurableField;

class ClearTableCacheField extends ConfigurableField
{
    public const TYPE = 'travelpayouts_clear_tables_cache';

    public function render()
    {
        $html = '<div>';
        $html .= '<span id="travelpayouts_travelpayouts_clear_tables_cache_button" class="tp-button tp-button--primary" style="margin: 0 0 0 5px;">' . Travelpayouts::__('Delete cache') . '</span>';
        $html .= '</div>';
        echo $html;
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-travelpayouts-clear-tables-cache-js',
            $this->url . 'field_travelpayouts_clear_tables_cache.min.js',
            ['jquery'],
            time(),
            true
        );
    }
}