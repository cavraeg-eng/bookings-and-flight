<?php

namespace Travelpayouts\modules\settings;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\components\dictionary\TravelpayoutsApiData;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\section\FieldsHelper;
use Travelpayouts\components\Translator;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\settings\components\fields\CurrencyField;
use Travelpayouts\modules\settings\components\fields\FlightSourceField;
use Travelpayouts\modules\settings\components\fields\HotelSourceField;

/**
 * Class SettingsForm
 * @package Travelpayouts\src\modules\settings
 */
class SettingsForm extends ModuleSection
{
    /**
     * @var string
     */
    public $date_format_radio = 'd.m.Y';
    /**
     * @var string
     */
    public $date_format = 'd.m.Y';
    /**
     * @var string
     */
    public $distance_units;
    /**
     * @var string
     */
    public $flights_source;
    /**
     * @var string
     */
    public $hotels_source;
    /**
     * @var string
     */
    public $language = Translator::DEFAULT_TRANSLATION;
    /**
     * @var string
     */
    public $origin_case;
    /**
     * @var string
     */
    public $destination_case;
    /**
     * @var string
     */
    public $currency = Settings::DEFAULT_CURRENCY;
    /**
     * @var string
     */
    public $currency_symbol_display = 'after';
    /**
     * @var string
     */
    public $flights_after_url;
    /**
     * @var string
     */
    public $hotels_after_url;
    /**
     * @var string
     */
    public $editor_buttons;
    /**
     * @var string
     */
    public $script_location;
    /**
     * @var array
     */
    public $airline_logo_dimensions;
    /**
     * @var string|bool
     */
    public $redirect = false;
    /**
     * @var string|bool
     */
    public $filter_ref = true;
    /**
     * @var string
     */
    public $target_url;
    /**
     * @var string
     */
    public $nofollow;
    /**
     * @var string
     */
    public $use_fileCache;
    /**
     * @var string
     */
    public $cache_value_flights = '3';
    /**
     * @var string
     */
    public $cache_value_hotels = '24';
    /**
     * @var string
     */
    public $table_btn_event;
    /**
     * @var string
     */
    public $table_load_event;
    /**
     * @var string
     */
    public $disable_tables_debug_notices = true;

    /**
     * @var int
     */
    protected $_airlineLogoWidth;
    /**
     * @var int
     */
    protected $_airlineLogoHeight;
    /**
     * @var string
     */
    protected $_airlineLogoUnits;

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Settings'),
            'icon' => 'tp-i-tabler:settings',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $localizedDate = Carbon::now()->locale($this->language);
        $isSubscribedToHotels = BrandSubscriptionService::isHotelLookAvailable();


        return array_merge(
            [
                'date_format_radio' => $this->fieldRadio()->setTitle(Travelpayouts::__('Date format'))
                    ->setOptions([
                        'j F Y' => $localizedDate->translatedFormat('j F Y') . ' ' . FieldsHelper::pre('j F Y', ['tp-ml-2']),
                        'F j, Y' => $localizedDate->translatedFormat('F j, Y') . ' ' . FieldsHelper::pre('F j, Y', ['tp-ml-2']),
                        'j M Y' => $localizedDate->translatedFormat('j M Y') . ' ' . FieldsHelper::pre('j M Y', ['tp-ml-2']),
                        'j F' => $localizedDate->translatedFormat('j F') . ' ' . FieldsHelper::pre('j F', ['tp-ml-2']),
                        'd-m-y' => $localizedDate->translatedFormat('d-m-y') . ' ' . FieldsHelper::pre('d-m-y', ['tp-ml-2']),
                        'custom' => Travelpayouts::__('Custom'),
                    ])->setDefault('j F Y'),
                'date_format' => $this->fieldInput()->setTitle(Travelpayouts::__('Custom date format'))
                    ->setDefault('d.m.Y')
                    ->setDesc($this->dateFormatDescription())
                    ->setRequired($this->requiredRule('date_format_radio', 'equals', 'custom')),
                'distance_units' => $this->fieldSelect()->setTitle(Travelpayouts::__('Distance units'))
                    ->setOptions([
                        'km' => Travelpayouts::__('Km'),
                        'ml' => Travelpayouts::__('Miles'),
                    ])
                    ->setDefault('km'),
            ],
            FieldsHelper::getMultilingualFields(
                (new FlightSourceField())
                    ->setID('flights_source')
            ),
            FieldsHelper::getMultilingualFields(
                (new HotelSourceField())
                    ->setID('hotels_source')
                ->setHidden(!$isSubscribedToHotels)
            ),
            [
                'language' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Tables and widgets language'))
                    ->setOptions(Travelpayouts::getInstance()->translator->getLocaleNames())
                    ->setDefault($this->getDefaultTableLanguage()),
                'origin_case' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Origin case'))
                    ->setOptions($this->getCasesList())
                    ->setRequired($this->requiredRule('language', 'equals', Translator::RUSSIAN))
                    ->setDefault(TravelpayoutsApiData::CASE_GENITIVE),
                'destination_case' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Destination case'))
                    ->setOptions($this->getCasesList())
                    ->setRequired($this->requiredRule('language', 'equals', Translator::RUSSIAN))
                    ->setDefault(TravelpayoutsApiData::CASE_ACCUSATIVE),
                'currency' => (new CurrencyField()),
                'currency_symbol_display' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Show the currency'))
                    ->setOptions([
                        'after' => Travelpayouts::__('After the price'),
                        'before' => Travelpayouts::__('Before the price'),
                        'hide' => Travelpayouts::__('Hide'),
                        'code_after' => Travelpayouts::__('Сurrency code (after the price)'),
                        'code_before' => Travelpayouts::__('Currency code (before the price)'),
                    ])
                    ->setDefault('after'),
                'flights_after_url' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Action after click (Flights)'))
                    ->setOptions([
                        'search' => Travelpayouts::__('Show the search form'),
                        'results' => Travelpayouts::__('Show search results'),
                    ])
                    ->setDefault('results'),
                'hotels_after_url' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Action after click (Hotels)'))
                    ->setOptions([
                        'city' => Travelpayouts::__('Show the city page'),
                        'hotel' => Travelpayouts::__('Show the hotel page'),
                    ])
                    ->setHidden(!$isSubscribedToHotels)
                    ->setDefault('hotel'),
                'editor_buttons' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Buttons in the editor'))
                    ->setOptions([
                        'default' => Travelpayouts::__('Default'),
                        'compact' => Travelpayouts::__('Compact'),
                        'hide' => Travelpayouts::__('Hide'),
                    ])
                    ->setDefault('compact'),
                'script_location' => $this->fieldSelect()
                    ->setTitle(Travelpayouts::__('Script include'))
                    ->setOptions([
                        'in_header' => Travelpayouts::__('Inside &lt;head&gt; tag'),
                        'in_footer' => Travelpayouts::__('Inside &lt;footer&gt; tag'),
                    ])
                    ->setDefault('in_footer'),
                'redirect' => $this->fieldInlineCheckbox()
                    ->setTitle(Travelpayouts::__('Activate 301 redirect'))
                    ->setSubtitle(Travelpayouts::__('A 301 redirect sends visitors from an old URL to a new one. It also helps keep your site\'s search rankings by passing on the SEO value from the old page to the new one. We recommend not to change this option.'))
                    ->setDefault(true),
                'airline_logo_dimensions' => $this->fieldDimensions()
                    ->setTitle(Travelpayouts::__('Adjust airline\'s logo size'))
                    ->setDefault([
                        'width' => 100,
                        'height' => 35
                    ])
                    ->setSubtitle(Travelpayouts::__('Maximum 300 pixels for width and 200 pixels height')),
                'target_url' => $this->fieldInlineCheckbox()
                    ->setTitle(Travelpayouts::__('Open results in a new window'))
                    ->setDefault(true),
                'filter_ref' => $this->fieldInlineCheckbox()
                    ->setTitle(Travelpayouts::__('Track additional stats'))
                    ->setDefault(true),
                'nofollow' => $this->fieldInlineCheckbox()
                    ->setTitle(Travelpayouts::__('Add the nofollow attribute'))
                    ->setSubtitle(Travelpayouts::__('This attribute prevents unwanted search results from being indexed by search engines. We recommend not to change this setting.'))
                    ->setDefault(true),
                'use_fileCache' => $this->fieldInlineCheckbox()
                    ->setTitle(Travelpayouts::__('Use FileCache'))
                    ->setSubtitle(Travelpayouts::__('This feature helps conserve hosting resources and maintain speed by reducing transient cache records in your options table.'))
                    ->setDefault(false),
                'cache_value_flights' => $this->fieldSlider()
                    ->setTitle(Travelpayouts::__('Cache timeout flights (hours)'))
                    ->setMin(3)
                    ->setMax(48)
                    ->setDefault(3),
                'cache_value_hotels' => $this->fieldSlider()
                    ->setTitle(Travelpayouts::__('Cache timeout hotels (hours)'))
                    ->setMin(24)
                    ->setMax(72)
                    ->setHidden(!$isSubscribedToHotels)
                    ->setDefault(24),
                'table_btn_event' => $this->fieldInput()
                    ->setTitle(Travelpayouts::__('Event tracking. "Find" button'))
                    ->setDesc($this->eventsDescription()),
                'table_load_event' => $this->fieldInput()
                    ->setTitle(Travelpayouts::__('Event tracking. Table is loaded'))
                    ->setDesc($this->eventsDescription()),
                'disable_tables_debug_notices' => $this->fieldInlineCheckbox()
                    ->setTitle(Travelpayouts::__('Disable tables debug notices'))
                    ->setSubtitle(Travelpayouts::__('Select this option to disable tables error notices in admin area.'))
                    ->setDefault(true),
                'settings_import' => $this->fieldImport()
                    ->setTitle(Travelpayouts::__('Import settings from v1'))
                    ->setSubtitle(Travelpayouts::__('Load import settings from Travelpayouts WP Plugin (version up to v. 1) and press import')),
                'clear_tables_cache' => $this->fieldClearCache()
                    ->setTitle(Travelpayouts::__('Clear cache'))
                    ->setSubtitle(Travelpayouts::__('Clear all travelpayouts tables cache from wp_options table'))
            ]
        );
    }

    protected function eventsDescription(): string
    {
        return implode('', [
            Travelpayouts::__('Set a goal in Yandex Metrika or Google Analytics and paste the code in this field to track the event (reaching the goal)'),
            HtmlHelper::tagArrayContent('div', ['class' => 'tp-mt-2'], [
                HtmlHelper::tagArrayContent('div', ['class' => 'tp-text--bold'], [
                    Travelpayouts::__('For example'),
                    ':',
                ]),
                HtmlHelper::tagArrayContent('div', ['class' => 'layouts-row--horizontal tp-my-2'], [
                    HtmlHelper::tag('div', ['class' => 'tp-pre'], "yaCounterXXXXXX.reachGoal('TARGET_NAME');"),
                    HtmlHelper::tag('div', ['class' => 'tp-mx-2'], 'or'),
                    HtmlHelper::tag('div', ['class' => 'tp-pre'], "ga('send', 'event', 'category', 'action');"),
                ]),
            ]),
            HtmlHelper::tagArrayContent('div', ['class' => 'tp-mt-3'], [
                HtmlHelper::tagArrayContent('div', ['class' => 'tp-text--bold'], [
                    Travelpayouts::__('You can also combine multiple events'),
                    ':',
                ]),
                HtmlHelper::tagArrayContent('div', ['class' => 'layouts-row--horizontal tp-mt-2'], [
                    HtmlHelper::tag('div', ['class' => ' tp-pre'], "yaCounterXXXXXX.reachGoal('TARGET_NAME'); ga('send', 'event', 'category', 'action');"),
                ]),
            ]),
        ]);
    }

    protected function dateFormatDescription(): string
    {
        return HtmlHelper::tagArrayContent('div', ['class' => 'tp-mt-3'], [
            Travelpayouts::__('If you want to set a custom date format properly you can check the characters and corresponding formats below'),
            HtmlHelper::tagArrayContent('div', ['class' => 'tp-my-2'], [
                HtmlHelper::tag('div', ['class' => 'tp-text--bold'], Travelpayouts::__('Day')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('j', ['tp-mr-2']) . ' ' . Travelpayouts::__('Day of the month without leading zeros')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('d', ['tp-mr-2']) . ' ' . Travelpayouts::__('Day of the month, 2 digits with leading zeros')),
            ]),

            HtmlHelper::tagArrayContent('div', ['class' => 'tp-my-2'], [
                HtmlHelper::tag('div', ['class' => 'tp-text--bold'], Travelpayouts::__('Month')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('F', ['tp-mr-2']) . ' ' . Travelpayouts::__('A full textual representation of a month, such as January or March')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('m', ['tp-mr-2']) . ' ' . Travelpayouts::__('Numeric representation of a month, with leading zeros')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('M', ['tp-mr-2']) . ' ' . Travelpayouts::__('A short textual representation of a month, three letters')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('n', ['tp-mr-2']) . ' ' . Travelpayouts::__('Numeric representation of a month, without leading zeros')),
            ]),

            HtmlHelper::tagArrayContent('div', ['class' => 'tp-mt-3'], [
                HtmlHelper::tag('div', ['class' => 'tp-text--bold'], Travelpayouts::__('Month')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('Y', ['tp-mr-2']) . ' ' . Travelpayouts::__('A full numeric representation of a year, 4 digits')),
                HtmlHelper::tag('div', ['class' => 'tp-mt-3'], FieldsHelper::pre('y', ['tp-mr-2']) . ' ' . Travelpayouts::__('A two-digit representation of a year')),
            ]),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'settings';
    }

    /**
     * @return bool
     */
    public function getIsTableNoticesDisabled(): bool
    {
        return StringHelper::toBoolean($this->disable_tables_debug_notices);
    }

    /**
     * @return int
     */
    public function getFlightCacheTime(): int
    {
        return $this->cache_value_flights !== null ? (int)$this->cache_value_flights : 3;
    }

    /**
     * @return int
     */
    public function getHotelsCacheTime(): int
    {
        return $this->cache_value_hotels !== null ? (int)$this->cache_value_hotels : 24;
    }

    /**
     * @return int
     */
    public function getAirlineLogoWidth(): int
    {
        if (!$this->_airlineLogoWidth) {
            $this->_airlineLogoWidth = $this->parseLogoDimension('width') ?? 100;
        }
        return $this->_airlineLogoWidth;
    }

    /**
     * @return int
     */
    public function getAirlineLogoHeight(): int
    {
        if (!$this->_airlineLogoHeight) {
            $this->_airlineLogoHeight = $this->parseLogoDimension('height') ?? 35;
        }
        return $this->_airlineLogoHeight;
    }

    /**
     * @return string
     */
    public function getAirlineLogoUnits(): string
    {
        if (!$this->_airlineLogoUnits) {
            if (is_array($this->airline_logo_dimensions)
                && isset($this->airline_logo_dimensions['units'])) {
                $this->_airlineLogoUnits = $this->airline_logo_dimensions['units'];
            } else {
                $this->_airlineLogoUnits = 'px';
            }
        }
        return $this->_airlineLogoUnits;
    }

    protected function parseLogoDimension(string $attribute): ?int
    {
        if (is_array($this->airline_logo_dimensions)
            && isset($this->airline_logo_dimensions[$attribute])) {
            $value = $this->airline_logo_dimensions[$attribute];

            if (preg_match('/\d+/', $value)) {
                return min((int)$value, 500);
            }
        }
        return null;
    }

    public function getFlightHost(): ?string
    {
        $hostList = components\fields\FlightSourceField::optionsList();
        return $hostList[$this->flights_source] ?? null;
    }

    public function getHotelHost(): ?string
    {
        $hostList = components\fields\HotelSourceField::optionsList();
        return $hostList[$this->hotels_source] ?? null;
    }

    /**
     * @return bool
     */
    public function getUseFilterRef(): bool
    {
        return StringHelper::toBoolean($this->filter_ref);
    }

    public function getCasesList(): array
    {
        return [
            TravelpayoutsApiData::CASE_NOMINATIVE => Travelpayouts::__('Nominative'),
            TravelpayoutsApiData::CASE_GENITIVE => Travelpayouts::__('Genitive'),
            TravelpayoutsApiData::CASE_ACCUSATIVE => Travelpayouts::__('Accusative'),
            TravelpayoutsApiData::CASE_DATIVE => Travelpayouts::__('Dative'),
            TravelpayoutsApiData::CASE_INSTRUMENTAL => Travelpayouts::__('Instrumental'),
            TravelpayoutsApiData::CASE_PREPOSITIONAL => Travelpayouts::__('Prepositional'),
        ];
    }

    protected function getDefaultTableLanguage(): string
    {
        return LanguageHelper::isRuDashboard() ? Translator::RUSSIAN : Translator::ENGLISH;
    }
}
