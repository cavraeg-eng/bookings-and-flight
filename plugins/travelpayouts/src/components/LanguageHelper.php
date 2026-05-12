<?php

namespace Travelpayouts\components;

use Travelpayouts;

class LanguageHelper
{
    const DASHBOARD_RUSSIAN = 'ru';
    const DASHBOARD_ENGLISH = 'en';
    const DASHBOARD_UKRAINIAN = 'uk';
    const DASHBOARD_BELORUSSIAN = 'be';
    const DASHBOARD_BULGARIAN = 'bg';
    const TABLE_DEFAULT = 'en';

    protected static $fallbackLocales = [
        self::DASHBOARD_BELORUSSIAN => self::DASHBOARD_RUSSIAN,
        self::DASHBOARD_UKRAINIAN => self::DASHBOARD_RUSSIAN,
    ];

    /**
     * true если язык ru другие false
     *
     * @return bool
     */
    public static function isRuDashboard()
    {
        return self::dashboardLocale() === self::DASHBOARD_RUSSIAN;
    }

    /**
     * TODO пока что не используется, возможно для фидбек формы и в других местах стоит использовать
     *
     * @return bool
     */
    public static function isRuFallbackDashboard()
    {
        return self::fallbackLocaleDashboard() === self::DASHBOARD_RUSSIAN;
    }

    /**
     * @return string
     */
    public static function fallbackLocaleDashboard()
    {
        $locale = self::dashboardLocale();
        switch ($locale) {
            case self::DASHBOARD_RUSSIAN:
            case self::DASHBOARD_BELORUSSIAN:
            case self::DASHBOARD_BULGARIAN:
            case self::DASHBOARD_UKRAINIAN:
                return self::DASHBOARD_RUSSIAN;
            default:
                return self::DASHBOARD_ENGLISH;
        }
    }

    /**
     * Если язык админки ru врнет ru все остальные en
     *
     * @return string
     */
    public static function binaryLocaleDashboard()
    {
        return self::isRuDashboard() ? self::DASHBOARD_RUSSIAN : self::DASHBOARD_ENGLISH;
    }

    /**
     * Возвращает язык адмики wp который выбран в настройках wp
     *
     * @param bool $short
     * @return false|string|void
     */
    public static function dashboardLocale($short = true)
    {
        $locale = get_bloginfo('language');

        if (!$short) {
            return $locale;
        }

        return self::shortLocale($locale);
    }

    /**
     * Возвращает язык пользователя из настроек wp
     *
     * @param bool $short
     * @return false|string
     */
    public static function userLocale($short = true)
    {
        $locale = get_user_locale();

        if (!$short) {
            return $locale;
        }

        return self::shortLocale($locale);
    }

    /**
     * @param null $directLocale
     * @return string
     */
    public static function tableLocale($directLocale = null)
    {
        if (!empty($directLocale)) {
            return $directLocale;
        }

        return Travelpayouts::getInstance()->settings
            ? Travelpayouts::getInstance()->settings->getLanguage()
            : Translator::ENGLISH;
    }

    /**
     * Получаем локаль из translator компонента
     * @param bool $fallbackLocale - возвращает оригинальный код локали или код фоллбек языка
     * @return mixed|string
     */
    public static function tableTranslatorLocale($fallbackLocale = true)
    {
        return Travelpayouts::getInstance()->translator
            ? Travelpayouts::getInstance()->translator->getLocaleCode($fallbackLocale)
            : Translator::ENGLISH;
    }

    protected static function shortLocale($locale)
    {
        return substr($locale, 0, 2);
    }

    public static function getLocaleNameFormat($locale): string
    {
        switch ($locale) {
            case Translator::ARABIC:
                return 'ar_TN';
            case Translator::CATALAN:
                return 'ca_ES';
            case Translator::CHINESE_SIMPLIFIED:
                return 'zh_CN';
            case Translator::CHINESE_TRADITIONAL:
                return 'zh_TW';
            case Translator::CZECH:
                return 'cs_CZ';
            case Translator::DANISH:
                return 'da_DK';
            case Translator::DUTCH:
                return 'nl_NL';
            case Translator::FINNISH:
                return 'fi_FI';
            case Translator::FRENCH:
                return 'fr_FR';
            case Translator::GERMAN:
                return 'de_DE';
            case Translator::HUNGARIAN:
                return 'hu_HU';
            case Translator::ITALIAN:
                return 'it_IT';
            case Translator::JAPANESE:
                return 'ja_JP';
            case Translator::LATVIAN:
                return 'lv_LV';
            case Translator::POLISH:
                return 'pl_PL';
            case Translator::PORTUGUESE:
                return 'pt_PT';
            case Translator::RUSSIAN:
                return 'ru_RU';
            case Translator::SPANISH:
                return 'es_ES';
            case Translator::SWEDISH:
                return 'sv_SE';
            case Translator::UKRAINIAN:
                return 'uk_UA';
            case Translator::THAI:
                return 'th_TH';
            case Translator::TURKISH:
                return 'tr_TR';
            case Translator::VIETNAMESE:
                return 'vi_VN';
            case Translator::GEORGIAN:
                return 'ka_GE';
            default:
                return 'en_US';
        }
    }

    /**
     * Возвращает id поля для текущего языка {field_id} + _{currentLang}
     * ничего не делает если текущий язык это дефолтный язык плагина мультиязычности
     *
     * @param $option
     * @param null $tableLocale
     * @return string
     */
    public static function optionWithLanguage($option, $tableLocale = null)
    {
        $langData = Travelpayouts::getInstance()->multiLang->data;

        if (isset($langData['current'])) {
            $current = $langData['current'];
        } else {
            $current = null;
        }

        if (!empty($tableLocale)) {
            $current = $tableLocale;
        }

        if (empty($langData) || empty($current) || $current === $langData['default']) {
            return $option;
        }

        return $option . '_' . $current;
    }

    public static function optionData($data, $optionName, $locale)
    {
        $option = self::optionWithLanguage($optionName, $locale);
        $default = $data->get($optionName);

        return $data->get($option, $default);
    }
}
