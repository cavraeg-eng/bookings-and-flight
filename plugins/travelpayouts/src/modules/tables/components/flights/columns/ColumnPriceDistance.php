<?php

namespace Travelpayouts\modules\tables\components\flights\columns;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\modules\settings\SettingsForm;

class ColumnPriceDistance extends GridColumn
{
    protected $locale = 'en';
    /**
     * @var string
     * @required
     */
    protected $currency;
    /**
     * @var string
     * @required
     */
    protected $priceAttribute;
    /**
     * @var string
     * @required
     */
    protected $distanceAttribute;

    /**
     * @var string
     */
    protected $distanceUnit;

    /**
     * @Inject
     * @var SettingsForm
     */
    public $settings;

    public function init()
    {
        if (!$this->distanceUnit) {

            $this->distanceUnit = $this->settings->distance_units;
        }
    }

    public function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if ($value) {
            $distanceUnit = $this->distanceUnit;

            if (($this->locale === 'ru') && $distanceUnit === ReduxOptions::DISTANCE_KM) {
                $distanceUnit = 'км';
            }

            $prefix = '';
            if ($value < 1) {
                $prefix = '<';
                $value = 1;
            }

            return $prefix . PriceFormatter::getInstance()->format(
                    $value,
                    strtolower($this->currency)
                ) . '/' . $distanceUnit;
        }
        return null;
    }

    protected function getPrice($model): int
    {
        return (int)$model->{$this->priceAttribute};
    }

    protected function getDistance($model): int
    {
        $value = $model->{$this->distanceAttribute};
        return $value ? (int)$value : 1;
    }

    public function getDataCellValue($model, $key, $index)
    {
        $price = $this->getPrice($model);
        $distance = $this->getDistance($model);
        if (!$price && $distance === 1) {
            return null;
        }

        if ($this->distanceUnit === ReduxOptions::DISTANCE_MILES) {
            $distance = round($distance / 1.609);
        }

        return $price / $distance;
    }

    /**
     * @param $model
     * @param $key
     * @param int $index
     * @return float
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return (float)$this->getDataCellValue($model, $key, $index);
    }

}
