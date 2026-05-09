<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;

use Travelpayouts\components\formatters\AirportNameFormatter;
use Travelpayouts\components\formatters\DirectionNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper;

class ColumnOriginDestination extends GridColumn
{
    const ONE_WAY_ARROW = '<i class="tp-i-tabler:arrow-right"></i>';
    const ROUND_TRIP_ARROW = '<i class="tp-i-tabler:arrows-horizontal"></i>';

    protected $locale = 'en';
    /**
     * @var string
     * @required
     */
    public $originAttribute;
    /**
     * @var string
     * @required
     */
    public $destinationAttribute;

    public $delimiter = self::ROUND_TRIP_ARROW;

    public $useAirportNameFormatter = false;

    public function getDataCellValue($model, $key, $index)
    {
        $originValue = $model->{$this->originAttribute};
        $destinationValue = $model->{$this->destinationAttribute};

        return HtmlHelper::tagArrayContent('div', ['class' => 'tp-flex tp-items-center tp-gap-1'], [
            HtmlHelper::tag('span', ['class' => 'tp-whitespace-nowrap'], $this->getName($originValue)),
            HtmlHelper::tag('span', [], $this->delimiter),
            HtmlHelper::tag('span', ['class' => 'tp-whitespace-nowrap'], $this->getName($destinationValue)),
        ]);
    }

    protected function getName($value): ?string
    {
        if ($this->useAirportNameFormatter) {
            $cityCode = AirportNameFormatter::getInstance()->getCityCode($value, $this->locale);
            if ($cityCode) {
                $value = $cityCode;
            }
        }

        return DirectionNameFormatter::getInstance()
            ->getName($value, $this->locale);
    }

    public function getSortOrderValue($model, $key, int $index)
    {
        $originValue = $model->{$this->originAttribute};
        $destinationValue = $model->{$this->destinationAttribute};
        return implode('', [
            $this->getName($originValue),
            $this->getName($destinationValue),
        ]);
    }
}
