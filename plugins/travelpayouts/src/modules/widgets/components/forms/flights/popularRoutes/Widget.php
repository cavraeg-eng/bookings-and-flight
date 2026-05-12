<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\popularRoutes;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions as Options;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\Translator;

class Widget extends BaseWidget
{
    public $destination;

    protected $_widget_url = '//www.travelpayouts.com/weedle/widget.js?';
    protected $_marker_postfix = '_populardest';

    /**
     * @Inject
     * @param TpPopularRoutesWidget $value
     */
    public function setSection($value)
    {
        $this->section = $value;
    }

    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [
                    [
                        'destination',
                    ],
                    'required',
                    'on' => [self::SCENARIO_DEFAULT],
                ],
                [
                    [
                        'destination',
                        'host',
                    ],
                    'required',
                    'on' => [self::SCENARIO_RENDER],
                ],
            ]
        );
    }

    public function init()
    {
        $this->width = $this->section_data->get('scalling_width.width');
        $this->powered_by = $this->bool_to_string($this->section_data->get('powered_by', $this->powered_by));
    }


    public function render()
    {
        $this->scenario = self::SCENARIO_RENDER;
        if ($this->validate()) {
            $url = implode('', [
                $this->widget_url,
                http_build_query($this->render_attributes),
            ]);
            Travelpayouts::getInstance()->assets->loader->registerAsset('public-popular-destinations-widget');
            return $this->render_script($url);

        }
        return $this->render_errors();
    }

    public function get_host()
    {
        if (!$this->_host) {
            $currentLabel = $this->flights_white_label;
            if ($currentLabel && !empty($currentLabel)) {
                $this->_host = $currentLabel;
            } else {
                $this->_host = $this->get_default_white_label();
            }
        }
        return $this->_host;
    }


    protected function get_default_white_label()
    {
        $kzSource = (string)Options::FLIGHTS_SOURCE_AVIASALES_KZ;
        $flightsSource = $this->get_settings_data(
            LanguageHelper::optionWithLanguage('flights_source')
        );

        if ($flightsSource === $kzSource) {
            return 'aviasales.kz';
        }

        switch ($this->locale) {
            case Translator::RUSSIAN:
                return 'hydra.aviasales.ru';
                break;
            default:
                return 'hydra.aviasales.ru';
        }
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_popular_routes_widget'];
    }
}
