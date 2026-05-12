<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Strings;

use function htmlspecialchars;

/**
 * The Yii string helper provides static methods allowing you to deal with strings more efficiently.
 */
class StringHelper
{
    /**
     * Safely casts a float to string independent of the current locale.
     * The decimal separator will always be `.`.
     * @param float|int $number a floating point number or integer.
     * @return string the string representation of the number.
     */
    public static function floatToString($number)
    {
        // . and , are the only decimal separators known in ICU data,
        // so its safe to call str_replace here
        return str_replace(',', '.', (string)$number);
    }

    /**
     * Convert special characters to HTML entities
     * @param string $string string to process
     * @param int $flags A bitmask of one or more flags
     * @param string|null $encoding Optional, defaults to "UTF-8"
     * @param bool $double_encode if set to false, method will not encode existing HTML entities
     * @return string
     * @see https://php.net/manual/en/function.htmlspecialchars.php
     */
    public static function htmlspecialchars($string, $flags, $encoding = null, $double_encode = true)
    {
        return empty($encoding) && $double_encode
            ? htmlspecialchars($string, $flags)
            : htmlspecialchars($string, $flags, $encoding ?: ini_get('default_charset'), $double_encode);
    }
}
