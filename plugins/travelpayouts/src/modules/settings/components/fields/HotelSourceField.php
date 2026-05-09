<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\settings\components\fields;

use Travelpayouts;
use Travelpayouts\components\section\fields\Select;

class HotelSourceField extends Select
{

    public function init()
    {
        parent::init();
        $this->options = self::optionsList();
        $this->title = Travelpayouts::__('Host (hotels)');
        $this->default = 'ru-RU';
    }

    public static function optionsList()
    {
        $languages = [
            'en-GB',
            'en-US',
            'pt-BR',
            'pt-PT',
            'id-ID',
            'fr-FR',
            'it-IT',
            'it-IT',
            'de-DE',
            'pl-PL',
            'es-ES',
            'th-TH',
            'th-TH',
            'en-AU',
            'en-CA',
            'en-IE',
        ];

        $sources = [
            'ru-RU' => 'hotellook.ru',
        ];
        foreach ($languages as $language) {
            $sources[$language] = 'hotellook.com ' . $language;
        }

        return $sources;
    }
}
