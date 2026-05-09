<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\subscriptions;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;
use Travelpayouts\modules\widgets\components\WidgetDirectionsTrait;

class Widget extends BaseWidgetShortcodeModel
{
    use WidgetDimensionTrait;
    use WidgetDirectionsTrait;

    public $originIata;
    public $destinationIata;
    public $backgroundColor;

    protected $_widget_url = '//www.travelpayouts.com/subscription_widget/widget.js?';
    protected $_marker_postfix = '_subscr';

    /**
     * @Inject
     * @param TpSubscriptionsWidget $value
     */
    public function setSection($value)
    {
        $this->section = $value;
    }

    public function rules()
    {
        return [
            [
                [
                    'origin',
                    'destination',
                ],
                'required',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'responsive',
                    'width',
                    'subid',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'marker',
                    'currency',
                    'host',
                    'backgroundColor',
                    'originIata',
                    'destinationIata',
                ],
                'required',
                'on' => [self::SCENARIO_RENDER],
            ],
            [
                [
                    'width',

                ],
                'safe',
                'on' => [self::SCENARIO_RENDER],
            ],
        ];
    }

    public function init()
    {
        $sectionData = $this->section_data;
        $origin = $this->get_direction_from_autocomplete_json(
            $sectionData->get('city_departure')
        );
        $destination = $this->get_direction_from_autocomplete_json(
            $sectionData->get('city_arrive')
        );

        $this->set_origin($origin);
        $this->set_destination($destination);
        $this->backgroundColor = $this->section_data->get('bg_pallet');
    }


    public function get_marker()
    {
        return parent::get_marker() . '.$69';
    }

    public function get_host()
    {
        if (!$this->_host) {
            $currentLabel = $this->flights_white_label;
            if ($currentLabel && !empty($currentLabel)) {
                $this->_host = $currentLabel;
            } else {
                $this->_host = 'hydra.aviasales.ru';
            }
        }
        return $this->_host;
    }

    public function render()
    {
        $this->scenario = self::SCENARIO_RENDER;
        if ($this->validate()) {
            $url = implode('', [
                $this->widget_url,
                http_build_query($this->render_attributes),
            ]);

            return $this->render_script($url);

        }
        return $this->render_errors();
    }


    public function set_origin($value)
    {
        $this->originIata = $value;
    }

    public function set_destination($value)
    {
        $this->destinationIata = $value;
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_subscriptions_widget'];
    }

    /**
     * @inheritdoc
     */
    public static function isActive(): bool
    {
        return TpSubscriptionsWidget::isActive();
    }
}
