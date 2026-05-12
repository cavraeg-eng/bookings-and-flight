<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels;

use Travelpayouts;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\components\grid\columns\ColumnPrice;
use Travelpayouts\components\rest\fields\Autocomplete;
use Travelpayouts\components\rest\fields\SelectAsync;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\helpers\DateHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\CitySuggestApiModel;
use Travelpayouts\modules\tables\components\flights\columns\ColumnDistance;
use Travelpayouts\modules\tables\components\hotels\components\columns\ColumnButton;
use Travelpayouts\modules\tables\components\hotels\components\columns\ColumnDiscount;
use Travelpayouts\modules\tables\components\hotels\components\columns\ColumnRating;
use Travelpayouts\modules\tables\components\hotels\components\columns\ColumnStarRating;
use Travelpayouts\modules\tables\components\settings\HotelSettingsSection;

abstract class HotelTableShortcodeModel extends TableShortcode
{
    /**
     * @Inject
     * @var HotelSettingsSection
     */
    protected $settings;

    /**
     * @Inject
     * @var Travelpayouts\components\Translator
     */
    protected $translator;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $type_selections;
    /**
     * @var string
     */
    public $type_selections_label;

    public $tableWrapperClassName = 'tp-widget-table-hotels';

    public function init()
    {
        parent::init();

        $this->theme = $this->settings->theme;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type_selections', 'type_selections_label'], 'string'],
        ]);
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'city' => Travelpayouts::__('City'),
            'number_results' => Travelpayouts::__('Number of results'),
            'type_selections' => Travelpayouts::__('Selection type'),
            'link_without_dates' => Travelpayouts::__('Land without dates'),
            'type_selections_label' => Travelpayouts::__('Hotel selection custom name'),
            'check_in' => Travelpayouts::__('Check-in date'),
            'check_out' => Travelpayouts::__('Check-out date'),
            'off_title' => Travelpayouts::__('Hide title'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function gridColumns(): array
    {
        return [
            ColumnLabels::NAME => [],
            ColumnLabels::STARS => [
                'class'=> ColumnStarRating::class,
            ],
            ColumnLabels::DISCOUNT => [
                'class'=> ColumnDiscount::class,
            ],
            ColumnLabels::OLD_PRICE_AND_NEW_PRICE => [],
            ColumnLabels::BUTTON => [
                'visible' => !$this->section->getUseRowAsLink(),
            ],
            ColumnLabels::PRICE_PN => [
                'class'=> ColumnPrice::class,
                'currency'=> $this->currency,
            ],
            ColumnLabels::OLD_PRICE_AND_DISCOUNT => [],
            ColumnLabels::DISTANCE => [
                'class'=> ColumnDistance::class,
                'locale'=> $this->locale,
            ],
            ColumnLabels::OLD_PRICE_PN => [
                'class'=> ColumnPrice::class,
                'currency'=> $this->currency,
            ],
            ColumnLabels::RATING => [
                'class'=> ColumnRating::class,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function columnLabels(): array
    {
        return ColumnLabels::getInstance()
            ->getColumnLabels(null, $this->locale);
    }

    protected function getLocationName()
    {
        if ($this->city) {
            $model = new CitySuggestApiModel;
            $model->attributes = [
                'term' => $this->city,
                'locale' => $this->locale,
            ];
            $model->sendRequest();
            $response = $model->getFirstRecord();
            if ($response) {
                return $response->cityName;
            }
        }
        return null;
    }

    protected function getSelectionName()
    {

        $label = $this->type_selections_label;

        if (empty($label)) {
            $key = 'hotel.selections.' . $this->type_selections;
            $domain = 'tables';
            return $this->translator->hasTranslation($key, $domain, $this->locale)
                ? Travelpayouts::t($key, [], $domain, $this->locale) : $this->type_selections;

        }
        return $label;
    }

    /**
     * @inheritDoc
     */
    protected function titleVariables(): array
    {
        return [
            'location' => $this->getLocationName(),
            'selection_name' => $this->getSelectionName(),
        ];
    }

    public function titleVariableLabels(): array
    {
        return array_merge(parent::titleVariableLabels(), [
            'location' => Travelpayouts::__('Location'),
            'selection_name' => Travelpayouts::__('Selection type'),
        ]);
    }

    /**
     * @inheritdoc
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
                'emptyText' => '',
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
     * @return float[]|int[]
     */
    protected function apiModelOptions(): array
    {
        return [
            'cacheTime' => $this->globalSettings->getHotelsCacheTime() * 60 * 60,
        ];
    }


    protected function predefinedGutenbergFields(): array
    {
        return array_merge(parent::predefinedGutenbergFields(), [
            'city' => $this->fieldCityAutocomplete(),
            'type_selections' => $this->fieldSelectionType(),
            'type_selections_label' => $this->fieldInput()
                ->setPlaceholder(Travelpayouts::__("Optional. Enter a custom name to display in the table's title")),
            'check_in' => $this->fieldDatePicker()
                ->setMinDate(DateHelper::modifyAndFormat())
                ->setMaxDate(DateHelper::modifyAndFormat('+1 YEAR')),
            'check_out' => $this->fieldDatePicker()
                ->setMinDate(DateHelper::modifyAndFormat())
                ->setMaxDate(DateHelper::modifyAndFormat('+1 YEAR')),
            'number_results' => $this->fieldInputNumber()->setMaximum(100),
            'link_without_dates' => $this->fieldCheckbox(),
        ]);
    }

    protected function fieldCityAutocomplete(): Autocomplete
    {
        return $this->fieldInputAutocomplete()->setAsync([
            'url' => $this->prepareEndpoint('//suggest.travelpayouts.com/search?service=internal_blissey_generator_ac&term=${term}&locale=${locale}&type=city'),
            'itemProps' => [
                'value' => '${id}',
                'label' => '${cityName}, ${countryName} (${hotelsCount})',
            ],
        ])->setAllowClear(true);
    }

    protected function fieldSelectionType(): SelectAsync
    {
        return $this->fieldSelectAsync()
            ->setPlaceholder(Travelpayouts::__('Selection type'))
            ->setAsync([
                'url' => $this->prepareEndpoint(
                    admin_url('admin-ajax.php' . '?action=travelpayouts_routes&page=hotels/getAvailableSelections/${city}')
                ),
                'optionsPath' => 'data',
                'itemProps' => [
                    'value' => '${id}',
                    'label' => '${label}',
                ],
            ]);
    }

    public static function isActive(): bool
    {
        return BrandSubscriptionService::isHotelLookAvailable();
    }

}
