<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\helpers;

class DateHelper
{
    public static function modifyAndFormat($modify = '', $format = 'd-m-Y'): string
    {
        $now = new \DateTime();
        $date = $now;
        if (!empty($modify)) {
            $date = $now->modify($modify);
        }

        return $date->format($format);
    }
}