<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Exception;
use Travelpayouts\Vendor\Symfony\Component\Translation\Loader\ArrayLoader;
use Travelpayouts\Vendor\Symfony\Component\Translation\Loader\YamlFileLoader;
use Travelpayouts\Vendor\Symfony\Component\Translation\Translator as SymfonyTranslator;
use Travelpayouts;
use Travelpayouts\helpers\ArrayHelper;

/**
 * Class Translator
 * @package Travelpayouts\components
 * @property string $locale
 */
class Translator extends BaseInjectedObject
{
    public const DEFAULT_TRANSLATION = self::ENGLISH;
    public const CZECH = 'cs';
    public const DANISH = 'da';
    public const GERMAN = 'de';
    public const GREEK = 'el';
    public const ENGLISH = 'en';
    public const SPANISH = 'es-ES';
    public const FINNISH = 'fi';
    public const FRENCH = 'fr';
    public const HEBREW = 'he';
    public const HUNGARIAN = 'hu';
    public const ITALIAN = 'it';
    public const JAPANESE = 'ja';
    public const KOREAN = 'ko';
    public const DUTCH = 'nl';
    public const NORWEGIAN = 'no';
    public const POLISH = 'pl';
    public const PORTUGUESE_BRAZILIAN = 'pt-BR';
    public const PORTUGUESE = 'pt-PT';
    public const ROMANIAN = 'ro';
    public const RUSSIAN = 'ru';
    public const SWEDISH = 'sv-SE';
    public const TURKISH = 'tr';
    public const UKRAINIAN = 'uk';
    public const VIETNAMESE = 'vi';
    public const CHINESE_SIMPLIFIED = 'zh-CN';
    public const CHINESE_TRADITIONAL = 'zh-TW';
    public const THAI = 'th';
    public const TAJIK = 'tg';
    public const CATALAN = 'ca';
    public const BELARUSIAN = 'be';
    public const BOSNIAN = 'bs';
    public const KAZAKH = 'kk';
    public const UZBEK = 'uz';
    public const CHECHEN = 'ce';
    public const MONTENEGRIN = 'me';
    public const SERBIAN_LATIN = 'sr-CS';
    public const CROATIAN = 'hr';
    public const ARABIC = 'ar';
    public const GEORGIAN = 'ka';
    public const LATVIAN = 'lv';
    public const LITHUANIAN = 'lt';
    public const SLOVENIAN = 'sl';
    public const BULGARIAN = 'bg';
    public const MALAY = 'ms';
    public const SLOVAK = 'sk';

    /**
     * @var
     */
    public $defaultLocale;
    /**
     * @var
     */
    public $translationsFolder;

    /**
     * right-to-left
     * @var string[]
     */
    protected $rtlLocales = [self::ARABIC, self::HEBREW];

    /**
     * @var SymfonyTranslator
     */
    protected $_translator;

    /**
     * @var array
     */
    protected $_supportedLocales;

    /**
     * @Inject("supportedLocales")
     * @var array
     */
    protected $locales;

    protected $loadedLocales = [];

    public function init()
    {
        $translator = new SymfonyTranslator($this->defaultLocale);
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addLoader('array', new ArrayLoader());
        $translator->setFallbackLocales([self::ENGLISH]);
        $this->_translator = $translator;
        $this->translationsFolder = Travelpayouts::getAlias($this->translationsFolder);
        $this->setLocale($this->defaultLocale);
    }

    public function setLocale($locale): void
    {
        $this->loadLocale($locale);
        $this->getTranslator()->setLocale($locale);
    }

    /**
     * @param bool $returnOriginalLocaleCode - возвращает оригинальный код локали или код фоллбек языка
     * @return string
     */
    public function getLocaleCode(bool $returnOriginalLocaleCode = true): string
    {
        $locale = $this->getTranslator()->getLocale();
        if (isset($this->locales[$locale]['fallback']) && !$returnOriginalLocaleCode) {
            $fallbackLocale = $this->locales[$locale]['fallback'];
            // Проверяем, что фоллбек язык существует
            return isset($this->locales[$fallbackLocale]) ? $fallbackLocale : self::ENGLISH;
        }

        return $locale;
    }

    /**
     * Получаем список поддерживаемых локалей
     * @return string[]
     */
    public function getLocaleNames(): array
    {
        if (!$this->_supportedLocales) {
            try {
                $result = [];
                foreach ($this->locales as $localeId => $locale) {
                    $result[$localeId] = $locale['localeName'];
                }
                return $result;
            } catch (Exception $e) {
                return [];
            }
        }

        return $this->_supportedLocales;
    }

    /**
     * Загружаем локаль, если она еще не загружена
     * @param string $localeId
     * @return void
     */
    protected function loadLocale(string $localeId): void
    {
        if (isset($this->locales[$localeId]) && !in_array($localeId, $this->loadedLocales, true)) {
            $locale = $this->locales[$localeId];
            if (isset($locale['fallback'])) {
                // Если фоллбек язык найден, используем его, иначе используем английский
                $locale = $this->locales[$locale['fallback']] ?? $this->locales[self::ENGLISH];
            }
            $this->addTranslation($localeId, $locale['code']);
            $this->loadedLocales[] = $localeId;
        }
    }

    /**
     * @param string $localeName
     * @param string $fileName
     * @return void
     */
    protected function addTranslation(string $localeName, string $fileName): void
    {
        $locPath = $this->translationsFolder . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($locPath)) {
            $domainsList = glob($locPath . '/*.yml');
            foreach ($domainsList as $path) {
                $domain = basename($path, '.yml');
                $this->getTranslator()->addResource('yaml', $path, $localeName, $domain);
            }
        }
    }

    /**
     * @return SymfonyTranslator
     */
    protected function getTranslator(): SymfonyTranslator
    {
        return $this->_translator;
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        if ($locale) {
            $this->loadLocale($locale);
        }
        return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Проверяем, имеет ли сообщение перевод
     * @param string $id
     * @param string|null $domain
     * @param string|null $locale
     * @return bool
     */
    public function hasTranslation(string $id, string $domain = null, string $locale = null): bool
    {
        if ($locale) {
            $this->loadLocale($locale);
        }
        return $this->getTranslator()->getCatalogue($locale)->has((string)$id, $domain);
    }

    /**
     * Добавляем кастомные переводы из массива
     * @param array<string,array<string,string> $localeStrings
     * @param string $domain
     */
    public function addArrayTranslations(array $localeStrings, string $domain): void
    {
        if (ArrayHelper::isAssociative($localeStrings)) {
            foreach ($localeStrings as $localeName => $translatedStrings) {
                if (ArrayHelper::isAssociative($translatedStrings)) {
                    $this->getTranslator()->addResource('array', $translatedStrings, $localeName, $domain);
                }
            }
        }
    }
}
