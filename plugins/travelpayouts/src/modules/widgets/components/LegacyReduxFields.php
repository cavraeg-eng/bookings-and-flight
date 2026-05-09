<?php

namespace Travelpayouts\modules\widgets\components;

use Exception;
use Travelpayouts;
use Travelpayouts\admin\redux\extensions\AutocompleteField;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\LanguageHelper;

class LegacyReduxFields
{
    const WIDGET_PREVIEW_TYPE_IFRAME = 'iframe';
    const WIDGET_PREVIEW_TYPE_SCRIPT = 'iframe_script';

    const RADIO_LAYOUT_DEFAULT = 'full';
    const RADIO_LAYOUT_INLINE = 'inline';

    /**
     * @param $prefix
     * @param $id
     * @param bool $path
     * @return string
     */
    public static function get_ID($prefix, $id, $path = false): string
    {
        $path_prefix = '';
        if ($path) {
            $path_prefix = str_replace('/', '_', $path) . '_';
        }

        return $path_prefix . $prefix . '_' . $id;
    }

    /**
     * @param $prefix
     * @param $with_default
     * @param string $required
     * @return array
     */
    public static function width_toggle($prefix, $with_default, $required = ''): array
    {
        return [
            self::select(
                'scalling_width_toggle',
                Travelpayouts::__('Stretch width'),
                [
                    ReduxOptions::STRETCH_WIDTH_YES => Travelpayouts::__('Yes'),
                    ReduxOptions::STRETCH_WIDTH_NO => Travelpayouts::__('No'),
                ],
                ReduxOptions::STRETCH_WIDTH_NO
            ),
            [
                'id' => 'scalling_width',
                'type' => 'dimensions',
                'select2' => self::select2Options(),
                'units' => ['px'],
                'height' => false,
                'title' => Travelpayouts::__('Width'),
                'default' => [
                    'width' => $with_default,
                ],
                'required' => [
                    $required,
                    'equals',
                    false,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public static function flight_directions($attributes = [])
    {
        if (empty($attributes)) {
            $attributes = [

                'url' => '//autocomplete.travelpayouts.com/places2?locale=ru&types[]=city&term={{query}}',
                'optionLabel' => '{{name}}, {{country_name}}',
                'noOptionsMessage' => Travelpayouts::__('Not found'),
                'loadingMessage' => Travelpayouts::__('Loading results'),
                'placeholder' => Travelpayouts::__('Select...'),
            ];
        }

        return [
            self::tp_autocomplete(
                'city_departure',
                $attributes,
                Travelpayouts::__('Directions'),
                '',
                Travelpayouts::__('City of departure'),
                Travelpayouts::__('Phuket')
            ),
            self::tp_autocomplete(
                'city_arrive',
                $attributes,
                '',
                '',
                Travelpayouts::__('City of arrival'),
                Travelpayouts::__('Bangkok')
            ),
        ];
    }

    /**
     * @param $id
     * @param $title
     * @param string $subtitle
     * @param string $desc
     * @param string $default
     * @return array
     */
    public static function text($id, $title, $subtitle = '', $desc = '', $default = '', $required = []): array
    {
        return [
            'id' => $id,
            'type' => 'text',
            'title' => $title,
            'subtitle' => $subtitle,
            'desc' => $desc,
            'default' => $default,
            'required' => $required,
        ];
    }

    public static function tp_autocomplete($id, $attributes, $title, $subtitle = '', $desc = '', $default = ''): array
    {
        return [
            'id' => $id,
            'attributes' => $attributes,
            'type' => AutocompleteField::TYPE,
            'title' => $title,
            'subtitle' => $subtitle,
            'desc' => $desc,
            'default' => $default,
        ];
    }

    public static function raw($id, $content): array
    {
        return [
            'id' => $id,
            'type' => 'raw',
            'content' => $content,
            'full_width' => false,
        ];
    }

    public static function get_image_url($img): string
    {
        return Travelpayouts::getAlias('@webImages') . '/' . $img;
    }

    /**
     * @param $id
     * @param $title
     * @param $width
     * @param $height
     * @param string $subtitle
     * @return array
     */
    public static function dimensions($id, $title, $width, $height, $subtitle = ''): array
    {
        return [
            'id' => $id,
            'type' => 'dimensions',
            'units' => ['px'],
            'title' => $title,
            'subtitle' => $subtitle,
            'default' => [
                'width' => $width,
                'height' => $height,
            ],
        ];
    }

    /**
     * @param $id
     * @param $title
     * @param string $subtitle
     * @param bool $default
     * @return array
     */
    public static function checkbox($id, $title, $subtitle = '', $default = false): array
    {
        return [
            'id' => $id,
            'type' => 'checkbox',
            'title' => $title,
            'subtitle' => $subtitle,
            'default' => $default,
        ];
    }

    public static function switcher($id, $title, $subtitle = '', $default = false, $onTitle = null, $offTitle = null): array
    {
        return [
            'id' => $id,
            'type' => 'switch',
            'title' => $title,
            'subtitle' => $subtitle,
            'default' => $default,
            'on' => $onTitle ? $onTitle : Travelpayouts::_x('on', 'admin.switcher'),
            'off' => $offTitle ? $offTitle : Travelpayouts::_x('off', 'admin.switcher'),
        ];
    }

    /**
     * @param $id
     * @param $title
     * @param string $subtitle
     * @param string $default
     * @param bool $required
     * @return array
     */
    public static function color($id, $title, $subtitle = '', $default = '#FFFFFF', $required = false): array
    {
        return [
            'id' => $id,
            'type' => 'color',
            'title' => $title,
            'subtitle' => $subtitle,
            'default' => $default,
            'validate' => 'color',
            'required' => $required,
        ];
    }

    /**
     * @param $id
     * @param $title
     * @param $desc
     * @param $options
     * @param $default
     * @return array
     */
    public static function color_scheme($id, $title, $desc, $options, $default): array
    {
        return [
            'id' => $id,
            'type' => 'palette',
            'title' => $title,
            'desc' => $desc,
            'default' => $default,
            'palettes' => $options,
        ];
    }

    /**
     * @param array $options
     * @param null $default
     * @return array
     */
    public static function widget_design($options, $default = null): array
    {
        return self::select(
            'widget_design',
            Travelpayouts::__('Widget design'),
            $options,
            $default
        );
    }

    /**
     * @param $id
     * @param $title
     * @param $default
     * @param $min
     * @param $max
     * @param bool $required
     * @return array
     */
    public static function simple_text_slider($id, $title, $default, $min, $max, $required = false): array
    {
        return [
            'id' => $id,
            'type' => 'slider',
            'title' => $title,
            'default' => $default,
            'min' => $min,
            'step' => 1,
            'max' => $max,
            'display_value' => 'text',
            'required' => $required,
        ];
    }

    /**
     * @param $id
     * @param $title
     * @param $default
     * @param $min
     * @param $step
     * @param $max
     * @param $display
     * @param $handles
     * @return array
     */
    public static function slider($id, $title, $default, $min, $step, $max, $display, $handles): array
    {
        return [
            'id' => $id,
            'type' => 'slider',
            'title' => $title,
            'default' => $default,
            'min' => $min,
            'step' => $step,
            'max' => $max,
            'display_value' => $display,
            'handles' => $handles,
        ];
    }

    /**
     * @param $id
     * @param $title
     * @param $options
     * @param bool $default
     * @param string $layout
     * @return array
     */
    public static function radio($id, $title, $options, $default = false, $layout = self::RADIO_LAYOUT_DEFAULT): array
    {
        return [
            'id' => $id,
            'type' => 'radio',
            'title' => $title,
            'options' => $options,
            'default' => $default,
            'multi_layout' => $layout,
        ];
    }

    /**
     * @param $id
     * @param $title
     * @param $options
     * @param string $default
     * @param string $subtitle
     * @param string $desc
     * @return array
     */
    public static function select($id, $title, $options, $default = '', $subtitle = '', $desc = ''): array
    {
        return [
            'id' => $id,
            'type' => 'select',
            'title' => $title,
            'subtitle' => $subtitle,
            'desc' => $desc,
            'options' => $options,
            'select2' => self::select2Options(),
            'default' => $default,
        ];
    }

    /**
     * @param $prefix
     * @param $type
     * @param $src
     * @param $attributes
     * @return array
     * @throws Exception
     * @deprecated
     */
    public static function widget_preview($prefix, $type, $src, $attributes = []): array
    {
        return [];
    }

    /**
     * @param $src
     * @return string
     */
    private static function prepareWidgetSrc($src): string
    {
        $locale = LanguageHelper::tableLocale();
        $currency = Travelpayouts::getInstance()->settings->data->get(
            'currency',
            ReduxOptions::getDefaultCurrencyCode()
        );

        return str_replace(
            [
                '{locale}',
                '{currency}',
                '{scripts_locale}',
            ],
            [
                $locale,
                strtolower($currency),
                $locale == LanguageHelper::DASHBOARD_RUSSIAN ? 'scripts' : 'scripts_' . $locale,
            ],
            $src
        );
    }

    public static function select2Options(): array
    {
        return [
            'theme' => 'travelpayouts',
            'allowClear' => false,
            'minimumResultsForSearch' => 10,
        ];
    }

    public static function pre($content, $classNames = []): string
    {
        $classNames = array_merge(['tp-pre'], $classNames);
        return HtmlHelper::tag('span', ['class' => implode(' ', $classNames)], $content);
    }

    public static function poweredBy(): array
    {
        return self::checkbox('powered_by', Travelpayouts::__('Add referral link (Powered by Travelpayouts)'), '', true);
    }
}