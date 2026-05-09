<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights;

use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\base\dictionary\EmptyItem;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\dictionary\Cities;
use Travelpayouts\components\dictionary\items\City;
use Travelpayouts\components\dictionary\TravelpayoutsApiData;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\grid\columns\ColumnHumanDate;
use Travelpayouts\components\grid\columns\ColumnPrice;
use Travelpayouts\components\grid\GridTitleStyleConfig;
use Travelpayouts\components\ShortcodesTagHelper;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\components\Translator;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\searchForms\components\SearchFormShortcode;
use Travelpayouts\modules\tables\components\flights\columns\ColumnAirline;
use Travelpayouts\modules\tables\components\flights\columns\ColumnAirlineLogo;
use Travelpayouts\modules\tables\components\flights\columns\ColumnButton;
use Travelpayouts\modules\tables\components\flights\columns\ColumnDirection;
use Travelpayouts\modules\tables\components\flights\columns\ColumnDistance;
use Travelpayouts\modules\tables\components\flights\columns\ColumnFlight;
use Travelpayouts\modules\tables\components\flights\columns\ColumnOriginDestination;
use Travelpayouts\modules\tables\components\flights\columns\ColumnPriceDistance;
use Travelpayouts\modules\tables\components\flights\columns\ColumnStops;
use Travelpayouts\modules\tables\components\flights\columns\ColumnTripClass;
use Travelpayouts\modules\tables\components\settings\FlightsSettingsSection;

abstract class FlightsShortcodeModel extends TableShortcode
{

    public $tableWrapperClassName = 'tp-widget-table-flights';
    /**
     * @Inject
     * @var FlightsSettingsSection
     */
    protected $flightsSettings;

    /**
     * @var Cities
     */
    protected $_iataDictionary;

    public function init()
    {
        parent::init();
        $this->theme = $this->flightsSettings->theme;
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'filter_airline' => Travelpayouts::__('Filter by airline'),
            'filter_flight_number' => Travelpayouts::__('Filter by flight # (enter manually)'),
            'stops' => Travelpayouts::__('Number of stops'),
            'limit' => Travelpayouts::__('Limit'),
            'one_way' => Travelpayouts::__('One way'),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function titleVariables(): array
    {
        $variableList = [];

        if (property_exists($this, 'origin')) {
            $variableList['origin'] = [$this, 'resolveCityNameByIATACode'];
        }

        if (property_exists($this, 'destination')) {
            $variableList['destination'] = [$this, 'resolveCityNameByIATACode'];
        }
        return $variableList;
    }

    /**
     * Подготавливаем названия городов из iata кода
     * @param $attribute
     * @return string|null
     */
    protected function resolveCityNameByIATACode($attribute): ?string
    {
        $value = $this->$attribute;
        if ($value && $cityDictionary = $this->getCityDictionaryByIATACode($value)) {
            return $this->useCaseInTitleVariables() ? $this->getTitleVariableCase($attribute, $cityDictionary) : $cityDictionary->getCaseNominative();
        }
        return '';
    }

    /**
     * Используем падежи в таблицах или нет
     * @return bool
     */
    protected function useCaseInTitleVariables(): bool
    {
        switch ($this->locale) {
            case Translator::RUSSIAN:
            case Translator::BELARUSIAN:
            case Translator::TAJIK:
            case Translator::CHECHEN:
            case Translator::KAZAKH:
            case Translator::UZBEK:
            case Translator::UKRAINIAN:
                return true;
            default:
                return false;
        }
    }

    /**
     * @param $value
     * @return City|null
     */
    protected function getCityDictionaryByIATACode($value): ?City
    {
        $dictionary = $this->getIataDictionary();
        $result = $dictionary->getItem($value);
        return !$result instanceof EmptyItem ? $result : null;
    }

    /**
     * Получаем значения аттрибутов в корректных падежах
     * @param string $attribute
     * @param City $dictionaryItem
     * @return string
     */
    protected function getTitleVariableCase(string $attribute, City $dictionaryItem): ?string
    {
        switch ($attribute) {
            case 'origin':
                $case = $this->globalSettings->origin_case ?? TravelpayoutsApiData::CASE_GENITIVE;
                return $dictionaryItem->getName($case);
            case 'destination':
                $case = $this->globalSettings->destination_case ?? TravelpayoutsApiData::CASE_ACCUSATIVE;
                if ($case === TravelpayoutsApiData::CASE_ACCUSATIVE) {
                    $rawTitleText = $this->getRawTableTitleText();
                    // В случае, если в заголовке указан destination с предлогом "в", убираем его
                    $needle = ' в {destination}';
                    if (is_string($rawTitleText) && strpos($rawTitleText, $needle) !== false) {
                        $rawTitleTextWitoutPrefix = str_replace($needle, ' {destination}', $rawTitleText);
                        $this->setRawTitleText($rawTitleTextWitoutPrefix);
                    }
                    return $dictionaryItem->getCaseAccusative();
                }
                return $dictionaryItem->getName($case);
            default:
                return '';
        }
    }

    /**
     * @return Cities
     */
    protected function getIataDictionary(): Cities
    {
        if (!$this->_iataDictionary) {
            $this->_iataDictionary =  Cities::getInstance([
                'lang' => $this->locale,
            ]);
        }
        return $this->_iataDictionary;
    }

    /**
     * Параметры для модели api
     * @return float[]|int[]
     */
    protected function apiModelOptions(): array
    {
        return [
            'cacheTime' => $this->globalSettings->getFlightCacheTime() * 60 * 60,
        ];
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function gridOptions(): array
    {
        $gridColumns = $this->gridColumns();
        /** @var null| ColumnButton $buttonColumnInstance */
        $buttonColumnInstance = null;
        // создаем колонку с кнопкой для получения корректной ссылки
        if (isset($gridColumns[ColumnLabels::BUTTON]) && $this->section->getUseRowAsLink()) {
            $buttonColumnInstance = BaseObject::createObject($gridColumns[ColumnLabels::BUTTON]);
        }

        return array_merge(parent::gridOptions(),
            [
                'emptyText' => $this->getGridEmptyValue(),
                'rowOptions' => function ($model) use ($buttonColumnInstance) {
                    // Добавляем возможность кликать по рядам если getUseRowAsLink === true
                    if ($buttonColumnInstance) {
                        return [
                            'class' => 'travelpayouts-row-link',
                            'data-href' => $buttonColumnInstance->getButtonUrl($model),
                        ];
                    }
                    return [];
                },
            ]
        );
    }

    /**
     * Добавляем значения на случай если таблица не была заполнена
     * @return string
     */
    protected function getGridEmptyValue(): string
    {
        switch ($this->flightsSettings->message_error_switch) {
            case ReduxOptions::SHOW_MESSAGE:
                return $this->getGridEmptyMessage();
            case ReduxOptions::SHOW_SEARCH_FROM:
                return $this->getGridEmptySearchForm();
            default:
                return '';
        }
    }

    /**
     * Отображаем сообщение в случае отсутствия результатов
     * @return string
     */
    protected function getGridEmptyMessage(): string
    {
        $rawErrorMessage = $this->flightsSettings->table_message_error;
        $titleTags = $this->prepareTableTitleTags();

        $origin = $this->origin ?? null;
        $destination = $this->destination ?? null;

        if ($origin || $destination) {
            $linkAttributes = [
                'text_link' => Travelpayouts::__('Find tickets from {origin} {destination}'),
                'origin' => $origin,
                'destination' => $destination,
                'type' => '1',
            ];

            if (preg_match('/\[link title="(.*)"\]/i', $rawErrorMessage, $matches)) {
                $linkAttributes['text_link'] = $matches[1];
            }

            $rawErrorMessage = preg_replace(
                '/\[link(.*)\]/',
                ShortcodesTagHelper::selfClosing('tp_link', $linkAttributes),
                $rawErrorMessage
            );
        }
        $errorMessage = is_string($rawErrorMessage) ? nl2br(StringHelper::formatMessage($rawErrorMessage, $titleTags)) : '';
        return do_shortcode($errorMessage);
    }

    /**
     * Отображаем поисковую форму в случае отсутствия результатов
     * @return string
     */
    private function getGridEmptySearchForm(): string
    {
        $searchFormId = $this->flightsSettings->empty_flights_table_search_form;
        if ($searchFormId !== null) {
            $linkAttributes = array_filter([
                'id' => $searchFormId,
                'origin' => $this->origin ?? null,
                'destination' => $this->destination ?? null,
                'type' => SearchFormShortcode::TYPE_AVIA,
            ]);
            return do_shortcode(
                ShortcodesTagHelper::selfClosing('tp_search_shortcodes', $linkAttributes)
            );
        }
        return '';
    }

    /**
     * @return array
     */
    public function columnLabels(): array
    {
        return ColumnLabels::getInstance()
            ->getColumnLabels(null, $this->locale);
    }

    /**
     * @inheritdoc
     */
    public function gridColumns(): array
    {
        return [
            ColumnLabels::BUTTON => [
                'class' => ColumnButton::class,
                'origin' => $this->origin ?? null,
                'destination' => $this->destination ?? null,
                'locale' => $this->locale,
                'currency' => $this->currency,
                'subid' => $this->subid,
                'value' => $this->getRawButtonTitleText(),
                'linkMarker' => $this->linkMarker(),
                'visible' => !$this->section->getUseRowAsLink(),
                'sortProperty' => 'price',
            ],
            ColumnLabels::DEPARTURE_AT => [
                'class' => ColumnHumanDate::class,
                'contentWrap'=> false,
                'locale' => $this->locale,
            ],
            ColumnLabels::RETURN_AT => [
                'class' => ColumnHumanDate::class,
                'contentWrap'=> false,
                'locale' => $this->locale,
            ],
            ColumnLabels::AIRLINE_LOGO => [
                'class' => ColumnAirlineLogo::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::AIRLINE => [
                'class' => ColumnAirline::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::NUMBER_OF_CHANGES => [
                'class' => ColumnStops::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::FLIGHT => [
                'class' => ColumnFlight::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::FLIGHT_NUMBER => [
                'attribute' => 'flightNumber',
                'headerOptions' => [
                    'class' => 'no-sort',
                ],
            ],
            ColumnLabels::ORIGIN_DESTINATION => [
                'class' => ColumnOriginDestination::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::DESTINATION => [
                'class' => ColumnDirection::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::ORIGIN => [
                'class' => ColumnDirection::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::FOUND_AT => [
                'class' => ColumnHumanDate::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::TRIP_CLASS => [
                'class' => ColumnTripClass::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::DISTANCE => [
                'class' => ColumnDistance::class,
                'locale' => $this->locale,
            ],
            ColumnLabels::PRICE_DISTANCE => [
                'class' => ColumnPriceDistance::class,
                'locale' => $this->locale,
                'currency' => $this->currency,
            ],
            ColumnLabels::PRICE => [
                'class' => ColumnPrice::class,
                'currency' => $this->currency,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCustomGridTitleConfig(): ?GridTitleStyleConfig
    {
        return new GridTitleStyleConfig(
            [
                'className' => $this->flightsSettings->title_custom_class,
                'inlineCss' => $this->flightsSettings->typography,
                'useInlineCss' => $this->flightsSettings->getUseInlineStyles(),
            ]
        );
    }

    /**
     * @param string|null $value
     * @return int|null
     */
    protected function getStopsFilterCount(?string $value): ?int
    {
        switch ($value) {
            case ReduxOptions::STOPS_DIRECT:
                return 0;
            case ReduxOptions::STOPS_ONLY_ONE:
                return 1;
            default:
                return null;
        }
    }


    public function titleVariableLabels(): array
    {
        return [
            'origin' => Travelpayouts::__('Origin'),
            'destination' => Travelpayouts::__('Destination'),
        ];
    }

    protected function predefinedGutenbergFields(): array
    {
        return array_merge(parent::predefinedGutenbergFields(),[
            'origin' => $this->fieldDirectionAutocomplete(),
            'destination' => $this->fieldDirectionAutocomplete(),
            'filter_airline' => $this->fieldInputAutocomplete()->setAsync([
                'url' => $this->prepareEndpoint('//suggest.travelpayouts.com/search?service=internal_airlines&term=${term}&locale=${locale}'),
                'itemProps' => [
                    'value' => '${slug}',
                    'label' => '${title} [${slug}]',
                ],
            ])->setAllowClear(true),
            'filter_flight_number' => $this->fieldInputTag(),
            'stops' => $this->fieldSelect()->setOptions(ReduxOptions::stops_number()),
            'limit' => $this->fieldInputNumber()->setMaximum(100),
            'one_way' => $this->fieldCheckbox(),
        ]);
    }

    /**
     * @param $number
     * @return string
     */
    protected function numberPrefix($number): string
    {
        return $number . '. ';
    }
}
