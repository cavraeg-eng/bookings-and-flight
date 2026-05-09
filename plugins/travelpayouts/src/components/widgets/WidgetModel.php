<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\widgets;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\base\SectionFields;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\modules\account\Account;
use Travelpayouts\modules\settings\Settings;

/**
 * Class WidgetModel
 * @package Travelpayouts\src\components\widgets
 * @property-read string $flights_white_label
 * @property-read string $hotels_white_label
 * @property Dot $section_data
 * @property-read array $render_attributes
 * @property string $currency
 * @property-read string $widget_id
 * @property string $option_marker
 * @property string $locale
 * @property string $widget_url
 * @property SectionFields $section
 * @property bool $debug
 */
abstract class WidgetModel extends ShortcodeModel
{
    const EXTRA_MARKER = '.wpplugin';
    const SCENARIO_RENDER = 'render';
    const DEFAULT_LOCALE = 'en';
    const DEFAULT_CURRENCY = 'usd';

    public $subid;

    protected $_marker;
    protected $_locale;
    protected $_flights_domain;
    protected $_hotels_domain;
    protected $_currency;
    protected $_widget_url;
    protected $_host;
    protected $_marker_postfix = '';

    /**
     * @var SectionFields|null
     */
    protected $section;
    /**
     * @Inject
     * @var Account
     */
    protected $accountModule;
    /**
     * @Inject
     * @var Settings
     */
    protected $settingsModule;
    /**
     * @var bool
     */
    private $_debug;

    public function get_option_marker()
    {
        $accountMarker = $this->accountModule->marker;
        if (empty($accountMarker)) {
            $accountMarker = '';
        }

        if (!$this->_marker) {
            $this->_marker = $accountMarker . self::EXTRA_MARKER;
        }

        return $this->_marker;
    }

    public function get_currency()
    {
        if (!$this->_currency) {
            $this->_currency = strtolower($this->settingsModule->currency);
        }
        return $this->_currency;
    }

    public function set_currency($value)
    {
        $this->_currency = $value;
    }

    public function get_locale()
    {
        if (!$this->_locale) {
            $this->_locale = $this->settingsModule->language;
        }
        return $this->_locale;
    }

    /**
     * @return array|mixed|null
     * @deprecated
     */
    public function set_host()
    {
        return $this->accountModule->whiteLabelFlights;
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @return array|mixed|null
     */
    protected function get_account_data($name, $defaultValue = null)
    {
        return $this->accountModule->data->get($name, $defaultValue);
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @return array|mixed|null
     */
    protected function get_settings_data($name, $defaultValue = null)
    {
        return $this->settingsModule->data->get($name, $defaultValue);
    }

    public function get_flights_white_label()
    {
        if (!$this->_flights_domain) {
            $this->_flights_domain = $this->getWhiteLabel($this->accountModule->whiteLabelFlights);
        }
        return $this->_flights_domain;
    }

    public function set_flights_white_label($value)
    {
        $this->_flights_domain = $value;
    }

    public function get_hotels_white_label()
    {
        if (!$this->_hotels_domain) {
            $this->_hotels_domain = $this->getWhiteLabel($this->accountModule->whiteLabelHotels);
        }
        return $this->_hotels_domain;
    }

    protected function getWhiteLabel($whiteLabel)
    {
        if (parse_url($whiteLabel, PHP_URL_SCHEME)) {
            return parse_url($whiteLabel, PHP_URL_HOST);
        }

        return $whiteLabel;
    }

    public function set_hotels_white_label($value)
    {
        $this->_hotels_domain = $value;
    }

    /**
     * @return Dot|null
     */
    protected function get_section_data()
    {
        return $this->section ? $this->section->data : null;
    }


    protected function get_render_attributes()
    {
        $safe = $this->safe_attributes();
        return $this->get_attributes($safe);
    }

    /**
     * id виджета
     * @return mixed
     */
    protected function get_widget_id()
    {
        return $this->section ? $this->section->id : null;
    }

    protected function bool_to_string($value)
    {
        return $value
            ? 'true'
            : 'false';
    }

    protected function string_to_bool($value)
    {
        return (bool)$value;
    }


    protected function render_script($url, $wrapperHtmlOptions = [], $scriptHtmlOptions = [])
    {
        $baseHtmlOptions = [
            'class' => implode(' ', [
                'TPWidget',
                $this->widget_id,
            ]),
        ];

        $baseScriptOptions = [
            'data-cfasync' => 'false',
            'data-wpfc-render' => 'false',
            'charset' => 'UTF-8',
            'async' => '',
        ];
        $scriptContent = Html::scriptFile($url, array_merge($baseScriptOptions, $scriptHtmlOptions));
        return Html::tagArrayContent('div', array_merge($baseHtmlOptions, $wrapperHtmlOptions), [
            $this->getDebugDataTemplate(),
            $scriptContent,
        ]);

    }

    protected function render_iframe($url, $wrapperHtmlOptions = [], $iframeHtmlOptions = [])
    {
        $baseHtmlOptions = [
            'class' => implode(' ', [
                'TPWidget',
                $this->widget_id,
            ]),
        ];

        $iframeContent = Html::tag('iframe', array_merge($iframeHtmlOptions, [
            'src' => $url,
            'scrolling' => 'no',
            'frameborder' => '0',
        ]), '');

        return Html::tagArrayContent('div', array_merge($baseHtmlOptions, $wrapperHtmlOptions), [
            $this->getDebugDataTemplate(),
            $iframeContent,
        ]);
    }


    protected function render_errors()
    {
        $errorTemplate = [
            $this->getDebugDataTemplate(),
            '[' . $this->tag . ']',
        ];
        $errorsList = array_map(function ($errorMessage) {
            return implode(' ', $errorMessage);
        }, $this->getErrors());

        return implode('<br>', array_merge($errorTemplate, $errorsList));
    }

    public function get_marker()
    {
        $marker = $this->get_option_marker();
        if ($this->subid) {
            return implode('_', [
                $marker,
                $this->subid,
            ]);
        }
        return $marker . $this->_marker_postfix;
    }

    protected function add_pixels($value)
    {
        return strpos($value, 'px') === false && strpos($value, '%') == false
            ? $value . 'px'
            : $value;
    }


    public function set_widget_url($value)
    {
        $this->_widget_url = $value;
    }

    public function get_widget_url()
    {
        return $this->_widget_url;
    }

    /**
     * @param SectionFields $value
     * @return void
     */
    abstract public function setSection($value);

    /**
     * @return bool
     */
    protected function getDebug()
    {
        return $this->_debug;
    }

    protected function setDebug($value)
    {
        $this->_debug = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array|null
     */
    protected function getDebugData()
    {
        if ($this->debug) {
            return [
                'shortcodeName' => $this->tag,
                'shortcodeAttributes' => $this->attributes,
            ];
        }
        return null;
    }

    /**
     * Оборачиваем debug данные в pre и отдаем
     * @return string
     */
    protected function getDebugDataTemplate()
    {
        $debugData = $this->getDebugData();
        return $debugData ?
            Html::tagArrayContent('pre', [], print_r($this->getDebugData(), true))
            : '';
    }
}
