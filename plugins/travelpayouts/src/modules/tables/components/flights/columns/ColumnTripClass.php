<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;

use Travelpayouts;
use Travelpayouts\components\grid\columns\GridColumn;

class ColumnTripClass extends GridColumn
{
    protected $locale = 'en';

    public function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if ($value !== null) {
            $dictionary = [
                Travelpayouts::t('flights.class_name.Economy', [], 'tables', $this->locale),
                Travelpayouts::t('flights.class_name.Business', [], 'tables', $this->locale),
                Travelpayouts::t('flights.class_name.First class', [], 'tables', $this->locale),
            ];
            return $dictionary[$value] ?? null;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return (int)$this->getDataCellValue($model, $key, $index);
    }

}
