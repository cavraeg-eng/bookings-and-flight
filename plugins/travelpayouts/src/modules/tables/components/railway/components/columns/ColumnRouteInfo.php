<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;

use Travelpayouts\components\formatters\StationNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response\Station;

class ColumnRouteInfo extends GridColumn
{
    protected $origin;
    protected $destination;

    protected function renderDataCellContent($model, $key, $index): string
    {
        /** @var mixed|Station[] $value */
        $value = $this->getDataCellValue($model, $key, $index);

        $result = [];
        $firstElement = '';
        $lastElement = '';
        $delimiterElement = Html::tag('div', ['class' => 'TP-train-route__delimiter'], '&#8594;');

        if (!empty($value) && is_array($value)) {
            if (count($value) > 1) {
                $first = $value[0]->getName() ?? null;
                if (!empty($first)) {
                    $firstElement = $this->renderStationWrapper($first, true, false, false);
                }

                $last = end($value)->getName() ?? '';
                if (!empty($last)) {
                    $lastElement = $this->renderStationWrapper($last, false, true, false);
                }
            }
        }
        $result[] = $firstElement;
        $result[] = $this->renderStationWrapper(
            $this->stationElement(StationNameFormatter::getInstance()->format($this->origin)) . ' ' .
            $delimiterElement
        );
        $result[] = $this->renderStationWrapper(
            $this->stationElement(StationNameFormatter::getInstance()->format($this->destination))
        );
        $result[] = $lastElement;

        return Html::tagArrayContent('div', ['class' => 'TP-train-routes'], $result);
    }

    /**
     * @param string $name
     * @param bool $main
     * @return string
     */
    protected function stationElement(string $name, bool $main = true): string
    {
        return Html::tag(
            'div',
            [
                'class' => Html::classNames([
                    'TP-train-route__name',
                    $main ? 'TP-train-route__name--main' : null,
                    !$main ? 'TP-train-route__name--secondary' : null,
                ]),
            ],
            $name
        );
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return is_array($value) ? count($value) : 0;
    }

    /**
     * @param string $content
     * @param bool $isFirst
     * @param bool $isLast
     * @param bool $main
     * @return string
     */
    protected function renderStationWrapper($content, bool $isFirst = false, bool $isLast = false, bool $main = true): string
    {
        return Html::tagArrayContent('div', [
            'class' =>
                Html::classNames([
                    'TP-train-route',
                    $isFirst ? 'TP-train-route--first' : null,
                    $isLast ? 'TP-train-route--last' : null,
                    $main ? 'TP-train-route--main' : null,
                    !$main ? 'TP-train-route--secondary' : null,
                ]),
        ], $content);
    }

    /**
     * @inheritDoc
     */
    protected function getComputedCellValue($model, $value)
    {
        if (is_array($value)) {
            return array_values(array_filter($value, static function ($item) {
                if (!$item instanceof Station) {
                    return false;
                }
                return $item->getName() !== null;
            }));
        }
        return [];
    }

}
