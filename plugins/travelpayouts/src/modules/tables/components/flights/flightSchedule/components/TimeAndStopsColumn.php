<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\flightSchedule\components;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\components\formatters\StopsNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\modules\tables\components\flights\flightSchedule\FlightScheduleResponse;

class TimeAndStopsColumn extends GridColumn
{
    public $locale = 'en';

    protected $contentWrap = false;

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($model instanceof FlightScheduleResponse) {
            $stopsCountFormatted = StopsNameFormatter::getInstance()
                ->format($this->getStopsCount($model), $this->locale);
            return Html::tagArrayContent(
                    'div',
                    ['class' => GridColumn::COLUMN_NOWRAP_CLASSNAME . ' tp-time tp-flex tp-items-center tp-gap-1'],
                    [
                        $model->depart_time,
                        '<i class="tp-i-tabler:arrow-right"></i>',
                        $model->arrival_time,
                        $model->arrival_day_indicator ? Html::tag('sup', ['class' => 'tp-indicator'], '+1') : null,
                    ]
                ) . Html::tag('div', [
                    'class' => 'tp-stops',
                    'style' => Html::cssStyleFromArray(['margin' => '5px 0 0 0']),
                ], $stopsCountFormatted);
        }
        return null;
    }

    protected function getRouteTime(FlightScheduleResponse $model): ?int
    {

        if (is_string($model->depart_time) && is_string($model->arrival_time)) {
            $departTime = Carbon::createFromTimeString($model->depart_time);
            $arrivalTime = Carbon::createFromTimeString($model->arrival_time);
            if ($model->arrival_day_indicator) {
                $arrivalTime->addDays(1);
            }

            return $departTime->diffInMinutes($arrivalTime);
        }

        return null;
    }

    protected function getStopsCount($model): int
    {
        return $model instanceof FlightScheduleResponse ? count($model->stops) : 0;
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return $this->getRouteTime($model);
    }

}
