<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;

use Travelpayouts\components\formatters\StationNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;

class ColumnRouteShort extends GridColumn
{
    protected $origin;
    protected $destination;

    public function init()
    {
        Html::addCssClass($this->headerOptions, Html::classNames([
            'no-sort',
        ]));
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        $origin = StationNameFormatter::getInstance()->format($this->origin) ?? '-';
        $destination = StationNameFormatter::getInstance()->format($this->destination) ?? '-';

        $originElement = Html::tag(
            'div',
            [
                'class' => Html::classNames([
                    'TP-train-route__name',
                    'TP-train-route__name--main'
                ]),
            ],
            $origin . ' ' . Html::tag('div', ['class' => 'TP-train-route__delimiter'], '&#8594;')
        );

        $destinationElement = Html::tag(
            'div',
            [
                'class' => Html::classNames([
                    'TP-train-route__name',
                    'TP-train-route__name--main'
                ]),
            ],
            $destination
        );

        return Html::tagArrayContent(
            'div',
            ['class' => 'TP-train-routes TP-train-route__name--main'],
            $originElement . $destinationElement
        );
    }
}
