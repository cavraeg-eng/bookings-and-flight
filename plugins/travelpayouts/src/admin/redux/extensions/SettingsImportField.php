<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ConfigurableField;

class SettingsImportField extends ConfigurableField
{
    public const TYPE = 'travelpayouts_settings_import';
    public const NONCE_NAME = 'travelpayouts_settings_import_nonce';

    public function render()
    {
        $html = '<div>';
        $html .= '<input type="file" class="tp-input tp-input-file" id="travelpayouts_import_settings_file" />';
        $html .= '<span id="travelpayouts_import_settings_button" class="tp-button tp-button--primary" style="margin: 0 0 0 5px;">' . Travelpayouts::__('Import') . '</span>';
        $html .= '</div>';
        wp_nonce_field('travelpayouts_settings_import', 'travelpayouts_settings_import_nonce');
        echo $html;
    }
}