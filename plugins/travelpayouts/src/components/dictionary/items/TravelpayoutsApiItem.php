<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary\items;

use Travelpayouts\components\base\dictionary\Item;

/**
 * Class TravelpayoutsApiItem
 * @package Travelpayouts\components\dictionary\items
 * @property-read array $translations
 * @property-read string $translation
 */
abstract class TravelpayoutsApiItem extends Item
{

    public function init()
    {
        parent::init();
        if (!$this->_lang) {
            throw new Exception("[{$this->getClassName()}]: \$_lang must be set");
        }
    }

    public function get_translations($lang = false)
    {
        return $this->dataDot->get('name_translations');
    }

    public function get_translation()
    {
        $fallbackLanguage = 'en';
        $language = $this->_lang;

        $hasTranslation = $this->dataDot->has("name_translations.{$language}");
        return $hasTranslation
            ? $this->dataDot->get("name_translations.{$language}", '')
            : $this->dataDot->get("name_translations.{$fallbackLanguage}", '');
    }
}
