<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\popularRoutes;

use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\forms\flights\popularRoutes\Widget as WidgetPopularRoutes;

class TpPopularRoutesSummaryWidget extends BaseWidgetShortcodeModel
{
    const DESTINATIONS_DELIMITER = '|';
    public $destinationsList = [];

    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [
                    [
                        'destinations',
                    ],
                    'safe',
                ],
                [
                    ['destinationsList'],
                    'each',
                    'rule' => ['string', 'min' => 3, 'max' => 3],
                    'skipOnEmpty' => false,
                    'stopOnFirstError' => false,
                ],
            ]);
    }

    public function render()
    {
        return $this->validate()
            ? $this->renderDestinationsWidgets()
            : $this->render_errors();
    }

    /**
     * Обрабатываем destinationsList и отрисовываем виджет для каждого из элементов
     * @return string
     */
    private function renderDestinationsWidgets()
    {
        $destinationWidgetList = array_map(function ($destination) {
            $popularRoutesModel = new WidgetPopularRoutes();
            $popularRoutesModel->attributes = array_merge($this->attributes, [
                'destination' => $destination,
                'width' => $this->width,
            ]);
            return Html::tag('div', ['class' => 'TP-PopularRoutesWidget'], $popularRoutesModel->render());
        }, $this->destinationsList);

        return !empty($destinationWidgetList)
            ? Html::tagArrayContent('div', ['class' => 'TP-PopularRoutesWidgets'], $destinationWidgetList)
            : '';
    }


    public function set_destinations($value)
    {
        if (is_string($value)) {
            $valueList = explode(self::DESTINATIONS_DELIMITER, $value);
            $this->destinationsList = array_merge($this->destinationsList, $valueList);
        }
    }

    public function get_destinations()
    {
        return $this->destinationsList;
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return [
            'tp_popular_routes_summary',
            'tp_popular_routes_summary_widget',
        ];
    }

    /**
     * @inheritDoc
     */
    public function setSection($value)
    {
        throw new \Exception('not implemented yet');
    }
}
