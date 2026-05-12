<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary\items;

/**
 * Class Airport
 * @package Travelpayouts\components\dictionary\items
 * @property-read $name
 */
class Airport extends TravelpayoutsApiItem
{
    protected $fallbackLocale = 'en';

    public function getName()
    {
        $name = $this->dataDot->get('name');
        if ($name === null) {
            $fallback_path = "name_translations.{$this->fallbackLocale}";
            if ($this->dataDot->has($fallback_path)) {
                return $this->dataDot->get($fallback_path);
            }
        }
        return $name;
    }

    public function getCityCode()
    {
        return $this->dataDot->get('city_code');
    }
}
