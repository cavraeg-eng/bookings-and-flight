<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\hotelLook;

use Travelpayouts\components\Translator;
use Travelpayouts\modules\tables\components\api\BaseTokenApiModel;

abstract class BaseHotelLookApiModel extends BaseTokenApiModel
{
    private $languageMapping = [
        'ru' => 'ru',
        // ru (русский);
        'en' => 'en',
        // en (английский);
        'th' => 'th',
        // th (тайский);
        'de' => 'de',
        // de (немецкий);
        'es-ES' => 'es',
        // es (испанский);
        'fr' => 'fr',
        // fr (французкий);
        'it' => 'it',
        // it (итальянский);
        'pl' => 'pl',
        // pl (польский).
        'uk' => 'ru',
        // uk (украинский)
        'uz' => 'ru',
        // uz (узбекский)
        'kk' => 'ru',
        // kk (казахский)
        'ce' => 'ru',
        // ce (чеченский)
        'tg' => 'ru',
        // tg (таджикский)
    ];

    /**
     * @var string
     * language of response (pt, en, fr, de, id, it, pl, es, th, ru)
     * @see getLanguage()
     * @see setLanguage()
     */
    private $_language = Translator::DEFAULT_TRANSLATION;


    public function getLanguage()
    {
        return $this->_language;
    }

    public function setLanguage($value)
    {
        if (isset($this->languageMapping[$value])) {
            $this->_language = $this->languageMapping[$value];
        }
    }
}
