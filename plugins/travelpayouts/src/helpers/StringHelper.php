<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\helpers;

class StringHelper
{
    public static function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param $value
     * @return bool
     */
    public static function toBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function random($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public static function notNullOrEmpty($value)
    {
        return $value !== null && $value !== '';
    }

    /**
     * Formats a message using
     * @param string $message
     * @param array $params
     * @return string
     */
    public static function formatMessage(string $message, array $params): string
    {
        $placeholders = [];
        foreach ((array)$params as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }

        return ($placeholders === [])
            ? $message
            : strtr($message, $placeholders);
    }

    public static function camelize(string $input, string $separator = '_'): string
    {
        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }

}
