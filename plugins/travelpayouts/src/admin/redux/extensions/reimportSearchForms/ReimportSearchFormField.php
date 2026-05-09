<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions\reimportSearchForms;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ConfigurableField;
use Travelpayouts\components\HtmlHelper;

class ReimportSearchFormField extends ConfigurableField
{
    public const TYPE = 'travelpayouts_reimport_search_forms';

    public function render()
    {
        echo HtmlHelper::tag('span', ['class' => 'tp-button tp-button--secondary travelpayouts-migrate-search-forms'], Travelpayouts::__('Re-import search forms'));
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'redux-field-travelpayouts-reimport-search-forms-js',
            $this->url . 'field_travelpayouts_reimport_search_forms.min.js',
            ['jquery'],
            time(),
            true
        );
    }
}