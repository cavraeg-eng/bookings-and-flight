<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\settings\components\fields;

use Travelpayouts;
use Travelpayouts\components\section\fields\Select;

class FlightSourceField extends Select
{
    public const FLIGHTS_SOURCE_AVIASALES_RU = 0;
    public const FLIGHTS_SOURCE_JETRADAR_SG_ = 23;
    public const FLIGHTS_SOURCE_AVIASALES_COM = 29;
    public const FLIGHTS_SOURCE_JETRADAR_CH = 5;
    public const FLIGHTS_SOURCE_AVIASALES_BY = 26;
    public const FLIGHTS_SOURCE_JETRADAR_COM_AU = 10;
    public const FLIGHTS_SOURCE_JETRADAR_COM = 1;
    public const FLIGHTS_SOURCE_JETRADAR_PT_ = 15;
    public const FLIGHTS_SOURCE_JETRADAR_IN_ = 19;
    public const FLIGHTS_SOURCE_JETRADAR_CO_TH = 24;
    public const FLIGHTS_SOURCE_JETRADAR_GR = 9;
    public const FLIGHTS_SOURCE_JETRADAR_IT = 14;
    public const FLIGHTS_SOURCE_JETRADAR_ES = 12;
    public const FLIGHTS_SOURCE_AVIASALES_UA = 28;
    public const FLIGHTS_SOURCE_JETRADAR_PL_ = 22;
    public const FLIGHTS_SOURCE_JETRADAR_AT = 6;
    public const FLIGHTS_SOURCE_AVIASALES_UZ = 30;
    public const FLIGHTS_SOURCE_CA_JETRADAR_COM = 4;
    public const FLIGHTS_SOURCE_JETRADAR_DE = 11;
    public const FLIGHTS_SOURCE_AVIASALES_KG = 27;
    public const FLIGHTS_SOURCE_IE_JETRADAR_COM_ = 16;
    public const FLIGHTS_SOURCE_JETRADAR_BE = 7;
    public const FLIGHTS_SOURCE_JETRADAR_CO_NL = 8;
    public const FLIGHTS_SOURCE_JETRADAR_COM_BR = 3;
    public const FLIGHTS_SOURCE_JETRADAR_FR = 13;
    public const FLIGHTS_SOURCE_JETRADAR_CO_UK = 17;
    public const FLIGHTS_SOURCE_JETRADAR_HK_ = 18;
    public const FLIGHTS_SOURCE_AVIASALES_KZ = 2;
    public const FLIGHTS_SOURCE_JETRADAR_CO_NZ = 20;
    public const FLIGHTS_SOURCE_JETRADAR_PH = 21;

    public function init()
    {
        parent::init();
        $this->title = Travelpayouts::__('Host (flights)');
        $this->default = self::FLIGHTS_SOURCE_AVIASALES_RU;
        $this->options = self::optionsList();
    }

    public static function optionsList()
    {
        return [
            self::FLIGHTS_SOURCE_AVIASALES_RU => 'aviasales.ru',
            self::FLIGHTS_SOURCE_AVIASALES_BY => 'aviasales.by',
            self::FLIGHTS_SOURCE_AVIASALES_KG => 'aviasales.kg',
            self::FLIGHTS_SOURCE_AVIASALES_KZ => 'aviasales.kz',
            self::FLIGHTS_SOURCE_AVIASALES_UZ => 'aviasales.uz',
            self::FLIGHTS_SOURCE_AVIASALES_UA => 'aviasales.ua',
            self::FLIGHTS_SOURCE_AVIASALES_COM => 'aviasales.com',
            self::FLIGHTS_SOURCE_JETRADAR_COM => 'jetradar.com',
            self::FLIGHTS_SOURCE_JETRADAR_COM_BR => 'jetradar.com.br',
            self::FLIGHTS_SOURCE_CA_JETRADAR_COM => 'ca.jetradar.com',
            self::FLIGHTS_SOURCE_JETRADAR_CH => 'jetradar.ch',
            self::FLIGHTS_SOURCE_JETRADAR_AT => 'jetradar.at',
            self::FLIGHTS_SOURCE_JETRADAR_BE => 'jetradar.be',
            self::FLIGHTS_SOURCE_JETRADAR_CO_NL => 'jetradar.co.nl',
            self::FLIGHTS_SOURCE_JETRADAR_GR => 'jetradar.gr',
            self::FLIGHTS_SOURCE_JETRADAR_COM_AU => 'jetradar.com.au',
            self::FLIGHTS_SOURCE_JETRADAR_DE => 'jetradar.de',
            self::FLIGHTS_SOURCE_JETRADAR_ES => 'jetradar.es',
            self::FLIGHTS_SOURCE_JETRADAR_FR => 'jetradar.fr',
            self::FLIGHTS_SOURCE_JETRADAR_IT => 'jetradar.it',
            self::FLIGHTS_SOURCE_JETRADAR_PT_ => 'jetradar.pt',
            self::FLIGHTS_SOURCE_IE_JETRADAR_COM_ => 'ie.jetradar.com',
            self::FLIGHTS_SOURCE_JETRADAR_CO_UK => 'jetradar.co.uk',
            self::FLIGHTS_SOURCE_JETRADAR_HK_ => 'jetradar.hk',
            self::FLIGHTS_SOURCE_JETRADAR_IN_ => 'jetradar.in',
            self::FLIGHTS_SOURCE_JETRADAR_CO_NZ => 'jetradar.co.nz',
            self::FLIGHTS_SOURCE_JETRADAR_PH => 'jetradar.ph',
            self::FLIGHTS_SOURCE_JETRADAR_PL_ => 'jetradar.pl',
            self::FLIGHTS_SOURCE_JETRADAR_SG_ => 'jetradar.sg',
            self::FLIGHTS_SOURCE_JETRADAR_CO_TH => 'jetradar.co.th',
        ];
    }
}
