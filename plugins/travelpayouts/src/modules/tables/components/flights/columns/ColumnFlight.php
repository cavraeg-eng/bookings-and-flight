<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;

use Travelpayouts\components\formatters\AirlineNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;

class ColumnFlight extends GridColumn
{
    protected $locale = 'en';
    /**
     * @var string
     * @required
     */
    protected $airlineCodeAttribute;

    protected $contentBreakWords = false;

    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if ($value) {
            $airlineName = $this->getAirlineName($model) ?? '';
            $airlineCode = $this->getAirlineCode($model) ?? '';
            return "$airlineName <br/> ($airlineCode $value)";
        }
        return $value;
    }

    /**
     * @param $model
     * @return string|null
     */
    protected function getAirlineName($model): ?string
    {
        return AirlineNameFormatter::getInstance()
            ->getAirlineName($this->getAirlineCode($model), $this->locale);
    }

    /**
     * @param $model
     * @return mixed
     */
    protected function getAirlineCode($model)
    {
        $airlineAttribute = $this->airlineCodeAttribute;
        return $model->$airlineAttribute;
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return $this->getAirlineName($model);
    }

}
