<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;

use Travelpayouts\components\formatters\AirlineNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;

class ColumnAirline extends GridColumn
{
    protected $locale = 'en';

    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return $this->getAirlineName($value);
    }

    public function getAirlineName($value): ?string
    {
        return AirlineNameFormatter::getInstance()
            ->getAirlineName($value, $this->locale);
    }

    /**
     * @inheritdoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        return $this->getAirlineName($value);
    }
}
