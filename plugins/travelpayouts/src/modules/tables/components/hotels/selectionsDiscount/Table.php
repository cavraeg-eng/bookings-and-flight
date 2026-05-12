<?php

namespace Travelpayouts\modules\tables\components\hotels\selectionsDiscount;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\validators\ValidatorBooleanString;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\hotelLook\locationMap\LocationApiModel;
use Travelpayouts\modules\tables\components\api\hotelLook\locationMap\LocationMapApiResponse;
use Travelpayouts\modules\tables\components\hotels\ColumnLabels;
use Travelpayouts\modules\tables\components\hotels\components\columns\ColumnButton;
use Travelpayouts\modules\tables\components\hotels\components\columns\ColumnPriceDiscount;
use Travelpayouts\modules\tables\components\hotels\HotelTableShortcodeModel;

/**
 * Class Table
 * @package Travelpayouts\modules\tables\components\hotels\selectionsDiscount
 */
class Table extends HotelTableShortcodeModel
{
    /**
     * @var string
     */
    public $city;
    /**
     * @var int
     */
    public $number_results = 20;
    /**
     * @var string
     */
    public $type_selections;
    /**
     * @var string
     */
    public $type_selections_label = '';
    /**
     * @var string|bool
     */
    public $link_without_dates = false;
    /**
     * @Inject
     * @var Section
     */
    public $section;

    public function init()
    {
        parent::init();
        $this->title = $this->section->title;
        $this->button_title = $this->section->button_title;
        $this->subid = $this->section->subid;
        $this->paginate = StringHelper::toBoolean($this->section->use_pagination);
        $this->number_results = (int)$this->section->pagination_size;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['city', 'number_results', 'type_selections'], 'required'],
            [['city', 'type_selections', 'type_selections_label'], 'string'],
            [['number_results'], 'number'],
            [['link_without_dates'], ValidatorBooleanString::class],
        ]);
    }

    /**
     * @return bool
     */
    public function getLinksWithoutDates(): bool
    {
        return StringHelper::toBoolean($this->link_without_dates);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_hotels_selections_discount_shortcodes'];
    }

    /**
     * @return string
     */
    public function linkMarker()
    {
        return 'tp_hotel_sel_disc';
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->section->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'city',
            'type_selections',
            'type_selections_label',
            'hr',
            'subid',
            'button_title',
            'title',
            'off_title',
            'hr',
            'number_results',
            'link_without_dates',
            'hr',
            'currency',
            'locale',
            'paginate',
            'disable_header',
        ];
    }

    /**
     * @inheritDoc
     */
    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(parent::gridColumns(), [
            ColumnLabels::NAME => [
                'attribute' => 'name',
            ],
            ColumnLabels::STARS => [
                'attribute' => 'stars',
            ],
            ColumnLabels::DISCOUNT => [],
            ColumnLabels::OLD_PRICE_AND_NEW_PRICE => [
                'class' => ColumnPriceDiscount::class,
                'currency' => $this->currency,
                'attribute' => 'price',
                'oldPriceAttribute' => 'oldPrice',
            ],
            ColumnLabels::BUTTON => [
                'class' => ColumnButton::class,
                'value' => $this->getRawButtonTitleText(),
                'buttonVariables' => function ($model) {
                    /** @var $model SelectionsDiscountApiResponse */
                    return $model->buttonVariables();
                },
                'buttonModelAttribute' => 'buttonModel',
                'cityId' => $this->city,
                'withoutDates' => $this->getLinksWithoutDates(),
                'currency' => $this->currency,
                'subid' => $this->subid,
                'locale' => $this->locale,
                'linkMarker' => $this->linkMarker(),
                'sortProperty' => [SelectionsDiscountApiResponse::class, 'getPrice'],
            ],
            ColumnLabels::PRICE_PN => [
                'attribute' => 'pricePerNight',
            ],
            ColumnLabels::OLD_PRICE_AND_DISCOUNT => [
                'class' => ColumnPriceDiscount::class,
                'currency' => $this->currency,
                'attribute' => 'price',
                'oldPriceAttribute' => 'oldPrice',
                'discountAttribute' => 'discount',
            ],
            ColumnLabels::DISTANCE => [
                'attribute' => 'distance',
            ],
            ColumnLabels::OLD_PRICE_PN => [
                'attribute' => 'oldPrice',
            ],
            ColumnLabels::RATING => [
                'attribute' => 'rating',
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::NAME => 9,
            ColumnLabels::STARS => 8,
            ColumnLabels::DISCOUNT => 6,
            ColumnLabels::OLD_PRICE_AND_NEW_PRICE => 7,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::PRICE_PN => 3,
            ColumnLabels::OLD_PRICE_AND_DISCOUNT => 2,
            ColumnLabels::DISTANCE => self::MIN_PRIORITY,
            ColumnLabels::OLD_PRICE_PN => 4,
            ColumnLabels::RATING => 5,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getCollection(): array
    {
        $model = $this->getApiModel();
        if ($model->validate()) {
            $responseModels = $model->getMappedResponse(LocationMapApiResponse::class);
            $result = [];
            if (is_array($responseModels->items)) {
                foreach ($responseModels->items as $responseModel) {
                    $model = new SelectionsDiscountApiResponse();
                    $model->shortcodeModel = $this;
                    $model->responseModel = $responseModel;
                    $result[] = $model;
                }
            }
            return $result;
        }
        return [];
    }

    /**
     * @return LocationApiModel
     */
    protected function getApiModel(): LocationApiModel
    {
        $model = new LocationApiModel($this->apiModelOptions());
        $model->id = $this->city;
        $model->limit = 100;
        $model->currency = $this->currency;
        $model->language = $this->locale;
        $model->type = $this->type_selections;

        return $model;
    }

    public function buttonVariables(): array
    {
        return SelectionsDiscountApiResponse::getInstance()->buttonVariables();
    }

}
