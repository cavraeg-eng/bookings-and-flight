<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\tables;

use Travelpayouts;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\Translator;
use Travelpayouts\traits\SingletonTrait;

abstract class BaseColumnLabels
{
    use SingletonTrait;

    protected $domain = 'tables';

    protected $dashboardLocale = Translator::DEFAULT_TRANSLATION;

    public function __construct()
    {
        $this->dashboardLocale = LanguageHelper::isRuDashboard() ? LanguageHelper::DASHBOARD_RUSSIAN : LanguageHelper::DASHBOARD_ENGLISH;
    }

    /**
     * Набор ключей для symfony translator
     * @return string[]
     */
    public function translationKeys()
    {
        return [];
    }

    /**
     * Переводы для строк указанные по умолчанию
     * @return string[]
     */
    public function defaultTranslations()
    {
        return [];
    }

    /**
     * @param null|string[] $names
     * @param null|string $locale
     * @return string[]
     */
    public function getColumnLabels($names = null, $locale = null)
    {
        $labelsList =  $this->translatedStrings($locale);
        if (is_array($names)) {
            $result = [];
            foreach ($names as $key) {
                $result[$key] = array_key_exists($key, $labelsList) ? $labelsList[$key] : null;
            }
            return $result;
        }
        return $labelsList;
    }

    /**
     * @param string[]|null $names
     * @return string[]
     */
    public function getDashboardColumnLabels(array $names = null): array
    {
        $translations = $this->defaultTranslations();
        if(!$names) {
            return $translations;
        }

        return array_filter($translations, static function($key) use ($names) {
            return in_array($key, $names);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param null|string $locale
     * @return array
     */
    protected function translatedStrings($locale = null)
    {
        $result = [];
        foreach ($this->translationKeys() as $column => $value) {
            $result[$column] = Travelpayouts::t($value, [], $this->domain, $locale);
        }
        return $result;
    }
}
