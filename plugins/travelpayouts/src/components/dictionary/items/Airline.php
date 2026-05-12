<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary\items;

/**
 * Class Airline
 * @package Travelpayouts\components\dictionary\items
 * @property-read $name
 */
class Airline extends TravelpayoutsApiItem
{
    protected $fallbackLocale = 'en';
    public $name;
    /**
     * TODO Исправить в enrichment и тд где не используюется трейт с getterSetter, а используется get_name
     */
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
}
