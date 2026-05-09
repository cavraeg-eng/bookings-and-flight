<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\settings\components\fields;

use Travelpayouts;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\section\fields\Select;

class CurrencyField extends Select
{
    public const CURRENCY_INR = 'INR';
    public const CURRENCY_ARS = 'ARS';
    public const CURRENCY_QAR = 'QAR';
    public const CURRENCY_KZT = 'KZT';
    public const CURRENCY_SAR = 'SAR';
    public const CURRENCY_BHD = 'BHD';
    public const CURRENCY_UAH = 'UAH';
    public const CURRENCY_PKR = 'PKR';
    public const CURRENCY_SEK = 'SEK';
    public const CURRENCY_LKR = 'LKR';
    public const CURRENCY_AZN = 'AZN';
    public const CURRENCY_NGN = 'NGN';
    public const CURRENCY_CZK = 'CZK';
    public const CURRENCY_OMR = 'OMR';
    public const CURRENCY_XOF = 'XOF';
    public const CURRENCY_GEL = 'GEL';
    public const CURRENCY_RON = 'RON';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_COP = 'COP';
    public const CURRENCY_RUB = 'RUB';
    public const CURRENCY_ISK = 'ISK';
    public const CURRENCY_BYN = 'BYN';
    public const CURRENCY_LYD = 'LYD';
    public const CURRENCY_HUF = 'HUF';
    public const CURRENCY_KWD = 'KWD';
    public const CURRENCY_PHP = 'PHP';
    public const CURRENCY_JPY = 'JPY';
    public const CURRENCY_BDT = 'BDT';
    public const CURRENCY_PLN = 'PLN';
    public const CURRENCY_GBP = 'GBP';
    public const CURRENCY_CNY = 'CNY';
    public const CURRENCY_THB = 'THB';
    public const CURRENCY_KRW = 'KRW';
    public const CURRENCY_NPR = 'NPR';
    public const CURRENCY_ZAR = 'ZAR';
    public const CURRENCY_VND = 'VND';
    public const CURRENCY_CLP = 'CLP';
    public const CURRENCY_MXN = 'MXN';
    public const CURRENCY_CHF = 'CHF';
    public const CURRENCY_BRL = 'BRL';
    public const CURRENCY_KGS = 'KGS';
    public const CURRENCY_PEN = 'PEN';
    public const CURRENCY_NZD = 'NZD';
    public const CURRENCY_EGP = 'EGP';
    public const CURRENCY_SGD = 'SGD';
    public const CURRENCY_MUR = 'MUR';
    public const CURRENCY_NOK = 'NOK';
    public const CURRENCY_CAD = 'CAD';
    public const CURRENCY_MYR = 'MYR';
    public const CURRENCY_BGN = 'BGN';
    public const CURRENCY_RSD = 'RSD';
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_DKK = 'DKK';
    public const CURRENCY_JOD = 'JOD';
    public const CURRENCY_HKD = 'HKD';
    public const CURRENCY_AED = 'AED';
    public const CURRENCY_TRY = 'TRY';
    public const CURRENCY_TJS = 'TJS';
    public const CURRENCY_IQD = 'IQD';
    public const CURRENCY_IDR = 'IDR';
    public const CURRENCY_AMD = 'AMD';
    public const CURRENCY_AUD = 'AUD';
    public const CURRENCY_ILS = 'ILS';

    public function init()
    {
        parent::init();
        $this->options = self::optionsList();
        $this->title = Travelpayouts::__('Currency');
        $this->default = LanguageHelper::isRuDashboard() ? self::CURRENCY_RUB : self::CURRENCY_USD;
    }

    public static function optionsList(): array
    {
        return [
            self::CURRENCY_RUB => Travelpayouts::__('RUB'),
            self::CURRENCY_USD => Travelpayouts::__('USD'),
            self::CURRENCY_EUR => Travelpayouts::__('EUR'),
            self::CURRENCY_AED => Travelpayouts::__('AED'),
            self::CURRENCY_AMD => Travelpayouts::__('AMD'),
            self::CURRENCY_ARS => Travelpayouts::__('ARS'),
            self::CURRENCY_AUD => Travelpayouts::__('AUD'),
            self::CURRENCY_AZN => Travelpayouts::__('AZN'),
            self::CURRENCY_BDT => Travelpayouts::__('BDT'),
            self::CURRENCY_BGN => Travelpayouts::__('BGN'),
            self::CURRENCY_BHD => Travelpayouts::__('BHD'),
            self::CURRENCY_BRL => Travelpayouts::__('BRL'),
            self::CURRENCY_BYN => Travelpayouts::__('BYN'),
            self::CURRENCY_CAD => Travelpayouts::__('CAD'),
            self::CURRENCY_CHF => Travelpayouts::__('CHF'),
            self::CURRENCY_CLP => Travelpayouts::__('CLP'),
            self::CURRENCY_CNY => Travelpayouts::__('CNY'),
            self::CURRENCY_COP => Travelpayouts::__('COP'),
            self::CURRENCY_CZK => Travelpayouts::__('CZK'),
            self::CURRENCY_DKK => Travelpayouts::__('DKK'),
            self::CURRENCY_EGP => Travelpayouts::__('EGP'),
            self::CURRENCY_GBP => Travelpayouts::__('GBP'),
            self::CURRENCY_GEL => Travelpayouts::__('GEL'),
            self::CURRENCY_HKD => Travelpayouts::__('HKD'),
            self::CURRENCY_HUF => Travelpayouts::__('HUF'),
            self::CURRENCY_IDR => Travelpayouts::__('IDR'),
            self::CURRENCY_ILS => Travelpayouts::__('ILS'),
            self::CURRENCY_INR => Travelpayouts::__('INR'),
            self::CURRENCY_IQD => Travelpayouts::__('IQD'),
            self::CURRENCY_ISK => Travelpayouts::__('ISK'),
            self::CURRENCY_JOD => Travelpayouts::__('JOD'),
            self::CURRENCY_JPY => Travelpayouts::__('JPY'),
            self::CURRENCY_KGS => Travelpayouts::__('KGS'),
            self::CURRENCY_KRW => Travelpayouts::__('KRW'),
            self::CURRENCY_KWD => Travelpayouts::__('KWD'),
            self::CURRENCY_KZT => Travelpayouts::__('KZT'),
            self::CURRENCY_LKR => Travelpayouts::__('LKR'),
            self::CURRENCY_LYD => Travelpayouts::__('LYD'),
            self::CURRENCY_MUR => Travelpayouts::__('MUR'),
            self::CURRENCY_MXN => Travelpayouts::__('MXN'),
            self::CURRENCY_MYR => Travelpayouts::__('MYR'),
            self::CURRENCY_NGN => Travelpayouts::__('NGN'),
            self::CURRENCY_NOK => Travelpayouts::__('NOK'),
            self::CURRENCY_NPR => Travelpayouts::__('NPR'),
            self::CURRENCY_NZD => Travelpayouts::__('NZD'),
            self::CURRENCY_OMR => Travelpayouts::__('OMR'),
            self::CURRENCY_PEN => Travelpayouts::__('PEN'),
            self::CURRENCY_PHP => Travelpayouts::__('PHP'),
            self::CURRENCY_PKR => Travelpayouts::__('PKR'),
            self::CURRENCY_PLN => Travelpayouts::__('PLN'),
            self::CURRENCY_QAR => Travelpayouts::__('QAR'),
            self::CURRENCY_RON => Travelpayouts::__('RON'),
            self::CURRENCY_RSD => Travelpayouts::__('RSD'),
            self::CURRENCY_SAR => Travelpayouts::__('SAR'),
            self::CURRENCY_SEK => Travelpayouts::__('SEK'),
            self::CURRENCY_SGD => Travelpayouts::__('SGD'),
            self::CURRENCY_THB => Travelpayouts::__('THB'),
            self::CURRENCY_TJS => Travelpayouts::__('TJS'),
            self::CURRENCY_TRY => Travelpayouts::__('TRY'),
            self::CURRENCY_UAH => Travelpayouts::__('UAH'),
            self::CURRENCY_VND => Travelpayouts::__('VND'),
            self::CURRENCY_XOF => Travelpayouts::__('XOF'),
            self::CURRENCY_ZAR => Travelpayouts::__('ZAR'),
        ];
    }
}