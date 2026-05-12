<?php

namespace Travelpayouts\modules\tables\components\flights\columns;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\modules\settings\SettingsForm;

class ColumnDistance extends GridColumn
{
    protected $locale = 'en';

    /**
     * @Inject
     * @var SettingsForm
     */
    public $settings;

    public function renderDataCellContent($model, $key, $index): string
    {
        $distance = $this->getDataCellValue($model, $key, $index);
        $distanceUnit = $this->settings->distance_units;

        if ($distanceUnit === ReduxOptions::DISTANCE_MILES) {
            $distance = round($distance / 1.609);
        }

        if (($this->locale === 'ru') && $distanceUnit === ReduxOptions::DISTANCE_KM) {
            $distanceUnit = 'км';
        }

        return $distance . ' ' . $distanceUnit;
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return (int) parent::getSortOrderValue($model, $key, $index);
    }

}
