<?php

namespace Travelpayouts\components;

class HtmlHelper
{
    const ID_PREFIX = 'tp';

    /**
     * @var boolean whether to close single tags. Defaults to true. Can be set to false for HTML5.
     */
    protected static $closeSingleTags = true;

    /**
     * @var boolean whether to render special attributes value. Defaults to true. Can be set to false for HTML5.
     */
    protected static $renderSpecialAttributesValue = true;

    /**
     * Encodes special characters into HTML entities.
     * The {@link CApplication::charset application charset} will be used for encoding.
     * @param string $text data to be encoded
     * @return string the encoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encode($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'utf-8');
    }

    /**
     * Generates an HTML element.
     * @param string $tag the tag name
     * @param array $htmlOptions the element attributes. The values will be HTML-encoded using {@link encode()}.
     * If an 'encode' attribute is given and its value is false,
     * the rest of the attribute values will NOT be HTML-encoded.
     * Since version 1.1.5, attributes whose value is null will not be rendered.
     * @param mixed $content the content to be enclosed between open and close element tags. It will not be
     *     HTML-encoded. If false, it means there is no body content.
     * @param boolean $closeTag whether to generate the close tag.
     * @return string the generated HTML element tag
     */
    public static function tag($tag, $htmlOptions = [], $content = false, $closeTag = true)
    {
        $html = '<' . $tag . self::renderAttributes($htmlOptions);
        if ($content === false)
            return $closeTag && self::$closeSingleTags ? $html . ' />' : $html . '>';
        else
            return $closeTag ? $html . '>' . $content . '</' . $tag . '>' : $html . '>' . $content;
    }

    /**
     * @param $tag
     * @param array $htmlOptions
     * @param bool|array|string $content
     * @param bool $closeTag
     * @return string
     * @see tag()
     */
    public static function tagArrayContent($tag, $htmlOptions = [], $content = false, $closeTag = true)
    {
        if (is_array($content)) {
            $content = implode('', $content);
        }

        return self::tag($tag, $htmlOptions, $content, $closeTag);
    }

    /**
     * Generates an open HTML element.
     * @param string $tag the tag name
     * @param array $htmlOptions the element attributes. The values will be HTML-encoded using {@link encode()}.
     * If an 'encode' attribute is given and its value is false,
     * the rest of the attribute values will NOT be HTML-encoded.
     * Since version 1.1.5, attributes whose value is null will not be rendered.
     * @return string the generated HTML element tag
     */
    public static function openTag($tag, $htmlOptions = [])
    {
        return '<' . $tag . self::renderAttributes($htmlOptions) . '>';
    }

    /**
     * Generates a close HTML element.
     * @param string $tag the tag name
     * @return string the generated HTML element tag
     */
    public static function closeTag($tag)
    {
        return '</' . $tag . '>';
    }

    /**
     * Encloses the given JavaScript within a script tag.
     * @param string $text the JavaScript to be enclosed
     * @param array $htmlOptions additional HTML attributes (see {@link tag})
     * @return string the enclosed JavaScript
     */
    public static function script($text, array $htmlOptions = [])
    {
        $defaultHtmlOptions = [
            'type' => 'text/javascript',
        ];
        $htmlOptions = array_merge($defaultHtmlOptions, $htmlOptions);
        return self::tag('script', $htmlOptions, "\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n");
    }

    /**
     * Includes a JavaScript file.
     * @param string $url URL for the JavaScript file
     * @param array $htmlOptions additional HTML attributes (see {@link tag})
     * @return string the JavaScript file tag
     */
    public static function scriptFile($url, array $htmlOptions = [])
    {
        $defaultHtmlOptions = [
            'type' => 'text/javascript',
            'src' => $url,
        ];
        $htmlOptions = array_merge($defaultHtmlOptions, $htmlOptions);
        return self::tag('script', $htmlOptions, '');
    }

    /**
     * Renders the HTML tag attributes.
     * Since version 1.1.5, attributes whose value is null will not be rendered.
     * Special attributes, such as 'checked', 'disabled', 'readonly', will be rendered
     * properly based on their corresponding boolean value.
     * @param array $htmlOptions attributes to be rendered
     * @return string the rendering result
     */
    public static function renderAttributes($htmlOptions)
    {
        static $specialAttributes = [
            'autofocus' => 1,
            'autoplay' => 1,
            'async' => 1,
            'checked' => 1,
            'controls' => 1,
            'declare' => 1,
            'default' => 1,
            'defer' => 1,
            'disabled' => 1,
            'formnovalidate' => 1,
            'hidden' => 1,
            'ismap' => 1,
            'itemscope' => 1,
            'loop' => 1,
            'multiple' => 1,
            'muted' => 1,
            'nohref' => 1,
            'noresize' => 1,
            'novalidate' => 1,
            'open' => 1,
            'readonly' => 1,
            'required' => 1,
            'reversed' => 1,
            'scoped' => 1,
            'seamless' => 1,
            'selected' => 1,
            'typemustmatch' => 1,
        ];

        if ($htmlOptions === [])
            return '';

        $html = '';
        if (isset($htmlOptions['encode'])) {
            $raw = !$htmlOptions['encode'];
            unset($htmlOptions['encode']);
        } else
            $raw = false;

        foreach ($htmlOptions as $name => $value) {
            if (isset($specialAttributes[$name])) {
                if ($value === false && $name === 'async') {
                    $html .= ' ' . $name . '="false"';
                } elseif ($value) {
                    $html .= ' ' . $name;
                    if (self::$renderSpecialAttributesValue)
                        $html .= '="' . $name . '"';
                }
            } elseif ($value !== null)
                $html .= ' ' . $name . '="' . ($raw ? $value : self::encode($value)) . '"';
        }

        return $html;
    }

    /**
     * Generates an input HTML tag.
     * This method generates an input HTML tag based on the given input name and value.
     * @param string $type the input type (e.g. 'text', 'radio')
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes for the HTML tag (see {@link tag}).
     * @return string the generated input tag
     */
    protected static function inputField($type, $name, $value, $htmlOptions)
    {
        $htmlOptions['type'] = $type;
        $htmlOptions['value'] = $value;
        $htmlOptions['name'] = $name;
        if (!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif ($htmlOptions['id'] === false)
            unset($htmlOptions['id']);
        return self::tag('input', $htmlOptions);
    }

    /**
     * Generates a text area input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
     * @return string the generated text area
     * @see clientChange
     * @see inputField
     */
    public static function textArea($name, $value = '', $htmlOptions = [])
    {
        $htmlOptions['name'] = $name;
        if (!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif ($htmlOptions['id'] === false)
            unset($htmlOptions['id']);
        return self::tag('textarea', $htmlOptions, isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $value : self::encode($value));
    }

    /**
     * Generates a text area input.
     * @param string $name the input name
     * @param string $value the input value
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * attributes are also recognized (see {@link clientChange} and {@link tag} for more details.)
     * @return string the generated text area
     * @see clientChange
     * @see inputField
     */
    public static function input($name, $value = '', $htmlOptions = [])
    {
        $htmlOptions['name'] = $name;
        if (!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif ($htmlOptions['id'] === false)
            unset($htmlOptions['id']);
        return self::tag('input', $htmlOptions, isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $value : self::encode($value));
    }

    /**
     * Generates a valid HTML ID based on name.
     * @param string $name name from which to generate HTML ID
     * @return string the ID generated based on name.
     */
    public static function getIdByName($name)
    {
        return str_replace(['[]', '][', '[', ']', ' '], ['', '_', '_', '', '_'], $name);
    }

    /**
     * Generates a drop down list.
     * @param string $name the input name
     * @param string $select the selected value
     * @param array $data data for generating the list options (value=>display).
     * You may use {@link listData} to generate this data.
     * Please refer to {@link listOptions} on how this data is used to generate the list options.
     * Note, the values and labels will be automatically HTML-encoded by this method.
     * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
     * attributes are recognized. See {@link clientChange} and {@link tag} for more details.
     * In addition, the following options are also supported specifically for dropdown list:
     * <ul>
     * <li>encode: boolean, specifies whether to encode the values. Defaults to true.</li>
     * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty. Note, the
     *     prompt text will NOT be HTML-encoded.</li>
     * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
     * The 'empty' option can also be an array of value-label pairs.
     * Each pair will be used to render a list option at the beginning. Note, the text label will NOT be
     *     HTML-encoded.</li>
     * <li>options: array, specifies additional attributes for each OPTION tag.
     *     The array keys must be the option values, and the array values are the extra
     *     OPTION tag attributes in the name-value pairs. For example,
     * <pre>
     *     array(
     *         'value1'=>array('disabled'=>true,'label'=>'value 1'),
     *         'value2'=>array('label'=>'value 2'),
     *     );
     * </pre>
     * </li>
     * </ul>
     * Since 1.1.13, a special option named 'unselectValue' is available. It can be used to set the value
     * that will be returned when no option is selected in multiple mode. When set, a hidden field is
     * rendered so that if no option is selected in multiple mode, we can still obtain the posted
     * unselect value. If 'unselectValue' is not set or set to NULL, the hidden field will not be rendered.
     * @return string the generated drop down list
     * @see clientChange
     * @see inputField
     * @see listData
     */
    public static function dropDownList($name, $select, $data, $htmlOptions = [])
    {
        $htmlOptions['name'] = $name;

        if (!isset($htmlOptions['id']))
            $htmlOptions['id'] = self::getIdByName($name);
        elseif ($htmlOptions['id'] === false)
            unset($htmlOptions['id']);

        self::clientChange('change', $htmlOptions);
        $options = "\n" . self::listOptions($select, $data, $htmlOptions);
        $hidden = '';

        if (!empty($htmlOptions['multiple'])) {
            if (substr($htmlOptions['name'], -2) !== '[]')
                $htmlOptions['name'] .= '[]';

            if (isset($htmlOptions['unselectValue'])) {
                $hiddenOptions = isset($htmlOptions['id']) ? ['id' => self::ID_PREFIX . $htmlOptions['id']] : ['id' => false];
                if (!empty($htmlOptions['disabled']))
                    $hiddenOptions['disabled'] = $htmlOptions['disabled'];
                $hidden = self::hiddenField(substr($htmlOptions['name'], 0, -2), $htmlOptions['unselectValue'], $hiddenOptions);
                unset($htmlOptions['unselectValue']);
            }
        }
        // add a hidden field so that if the option is not selected, it still submits a value
        return $hidden . self::tag('select', $htmlOptions, $options);
    }

    /**
     * Generates the list options.
     * @param mixed $selection the selected value(s). This can be either a string for single selection or an array for
     *     multiple selections.
     * @param array $listData the option data (see {@link listData})
     * @param array $htmlOptions additional HTML attributes. The following two special attributes are recognized:
     * <ul>
     * <li>encode: boolean, specifies whether to encode the values. Defaults to true.</li>
     * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty. Note, the
     *     prompt text will NOT be HTML-encoded.</li>
     * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
     * The 'empty' option can also be an array of value-label pairs.
     * Each pair will be used to render a list option at the beginning. Note, the text label will NOT be
     *     HTML-encoded.</li>
     * <li>options: array, specifies additional attributes for each OPTION tag.
     *     The array keys must be the option values, and the array values are the extra
     *     OPTION tag attributes in the name-value pairs. For example,
     * <pre>
     *     array(
     *         'value1'=>array('disabled'=>true,'label'=>'value 1'),
     *         'value2'=>array('label'=>'value 2'),
     *     );
     * </pre>
     * </li>
     * <li>key: string, specifies the name of key attribute of the selection object(s).
     * This is used when the selection is represented in terms of objects. In this case,
     * the property named by the key option of the objects will be treated as the actual selection value.
     * This option defaults to 'primaryKey', meaning using the 'primaryKey' property value of the objects in the
     *     selection. This option has been available since version 1.1.3.</li>
     * </ul>
     * @return string the generated list options
     */
    public static function listOptions($selection, $listData, &$htmlOptions)
    {
        $raw = isset($htmlOptions['encode']) && !$htmlOptions['encode'];
        $content = '';
        if (isset($htmlOptions['prompt'])) {
            $content .= '<option value="">' . strtr($htmlOptions['prompt'], [
                    '<' => '&lt;',
                    '>' => '&gt;',
                ]) . "</option>\n";
            unset($htmlOptions['prompt']);
        }
        if (isset($htmlOptions['empty'])) {
            if (!is_array($htmlOptions['empty']))
                $htmlOptions['empty'] = ['' => $htmlOptions['empty']];
            foreach ($htmlOptions['empty'] as $value => $label)
                $content .= '<option value="' . self::encode($value) . '">' . strtr($label, [
                        '<' => '&lt;',
                        '>' => '&gt;',
                    ]) . "</option>\n";
            unset($htmlOptions['empty']);
        }

        if (isset($htmlOptions['options'])) {
            $options = $htmlOptions['options'];
            unset($htmlOptions['options']);
        } else
            $options = [];

        $key = isset($htmlOptions['key']) ? $htmlOptions['key'] : 'primaryKey';
        if (is_array($selection)) {
            foreach ($selection as $i => $item) {
                if (is_object($item))
                    $selection[$i] = $item->$key;
            }
        } elseif (is_object($selection))
            $selection = $selection->$key;

        foreach ($listData as $key => $value) {
            if (is_array($value)) {
                $content .= '<optgroup label="' . ($raw ? $key : self::encode($key)) . "\">\n";
                $dummy = ['options' => $options];
                if (isset($htmlOptions['encode']))
                    $dummy['encode'] = $htmlOptions['encode'];
                $content .= self::listOptions($selection, $value, $dummy);
                $content .= '</optgroup>' . "\n";
            } else {
                $attributes = ['value' => (string)$key, 'encode' => !$raw];
                if (!is_array($selection) && !strcmp($key, $selection) || is_array($selection) && in_array($key, $selection))
                    $attributes['selected'] = 'selected';
                if (isset($options[$key]))
                    $attributes = array_merge($attributes, $options[$key]);
                $content .= self::tag('option', $attributes, $raw ? (string)$value : self::encode((string)$value)) . "\n";
            }
        }

        unset($htmlOptions['key']);

        return $content;
    }

    public static function reactWidget($widgetId, $props = [], $content = false, $closeTag = true)
    {
        // Properties that's no need to mutate
        $habitatProps = array_filter($props, static function ($propKey) {
            return 0 === strpos($propKey, "data-");
        }, ARRAY_FILTER_USE_KEY);

        $baseWidgetProps = [
            'data-component' => $widgetId,
            'data-props' => array_diff_key($props, $habitatProps),
        ];

        $widgetProps = array_map(
            static function ($propertyValue) {
                if (is_array($propertyValue)) {
                    return json_encode($propertyValue);
                }
                return $propertyValue;
            },
            array_merge_recursive($habitatProps, $baseWidgetProps)
        );

        return self::tag('div', $widgetProps, $content, $closeTag);
    }

    /**
     * Сливаем массив в строку, обычно используется для вывода аттрибута class в теге
     * @param $classNames
     * @return string
     */
    public static function classNames($classNames)
    {
        return is_array($classNames) ?
            trim(implode(' ', array_filter($classNames)))
            : $classNames;
    }

    /**
     * Merges already existing CSS classes with new one.
     * This method provides the priority for named existing classes over additional.
     * @param array $existingClasses already existing CSS classes.
     * @param array $additionalClasses CSS classes to be added.
     * @return array merge result.
     */
    private static function mergeCssClasses(array $existingClasses, array $additionalClasses)
    {
        foreach ($additionalClasses as $key => $class) {
            if (is_int($key) && !in_array($class, $existingClasses)) {
                $existingClasses[] = $class;
            } elseif (!isset($existingClasses[$key])) {
                $existingClasses[$key] = $class;
            }
        }

        return $existingClasses;
    }

    /**
     * Adds a CSS class (or several classes) to the specified options.
     *
     * If the CSS class is already in the options, it will not be added again.
     * If class specification at given options is an array, and some class placed there with the named (string) key,
     * overriding of such key will have no effect. For example:
     *
     * ```php
     * $options = ['class' => ['persistent' => 'initial']];
     * Html::addCssClass($options, ['persistent' => 'override']);
     * var_dump($options['class']); // outputs: array('persistent' => 'initial');
     * ```
     *
     * @param array $options the options to be modified.
     * @param string|array $class the CSS class(es) to be added
     */
    public static function addCssClass(&$options, $class)
    {
        if (isset($options['class'])) {
            if (is_array($options['class'])) {
                $options['class'] = self::mergeCssClasses($options['class'], (array) $class);
            } else {
                $classes = preg_split('/\s+/', $options['class'], -1, PREG_SPLIT_NO_EMPTY);
                $options['class'] = implode(' ', self::mergeCssClasses($classes, (array) $class));
            }
        } else {
            $options['class'] = $class;
        }
    }


    /**
     * Converts a CSS style array into a string representation.
     *
     * For example,
     *
     * ```php
     * print_r(Html::cssStyleFromArray(['width' => '100px', 'height' => '200px']));
     * // will display: 'width: 100px; height: 200px;'
     * ```
     *
     * @param array $style the CSS style array. The array keys are the CSS property names,
     * and the array values are the corresponding CSS property values.
     * @return string the CSS style string. If the CSS style is empty, a null will be returned.
     */
    public static function cssStyleFromArray(array $style)
    {
        $result = '';
        foreach ($style as $name => $value) {
            $result .= "$name: $value; ";
        }
        // return null if empty to avoid rendering the "style" attribute
        return $result === '' ? null : rtrim($result);
    }
}
