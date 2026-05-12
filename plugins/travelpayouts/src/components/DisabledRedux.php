<?php

namespace Travelpayouts\components;

class DisabledRedux
{
    public static function setOption($optName, $key, $option)
    {
        $redux = get_option($optName);
        $redux[$key] = $option;

        return update_option($optName, $redux);
    }

    public static function getOption($key, $default)
    {
        $reduxOptions = new \Travelpayouts\Vendor\Adbar\Dot(get_option(TRAVELPAYOUTS_REDUX_OPTION));

        return $reduxOptions->get($key, $default);
    }
}
