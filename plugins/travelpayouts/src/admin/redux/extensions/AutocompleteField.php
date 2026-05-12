<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions;

use Travelpayouts\admin\redux\base\ConfigurableField;
use Travelpayouts\components\widgets\AlertWidget;

/**
 * @deprecated
 */
class AutocompleteField extends ConfigurableField
{
    public const TYPE = 'travelpayouts_autocomplete';

    public function render()
    {
        if (TRAVELPAYOUTS_DEBUG) {
            echo AlertWidget::widget([
                'type' => AlertWidget::TYPE_WARNING,
                'content' => 'AutocompleteField is deprecated',
            ]);
        }
    }
}