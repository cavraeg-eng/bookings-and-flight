<?php

namespace Travelpayouts\modules\tables\components\flights\ourSiteSearch;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\validators\ValidatorBooleanString;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest\PriceLatestApiResponse;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest\PricesLatestApiModel;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\columns\ColumnOriginDestination;
use Travelpayouts\modules\tables\components\flights\FlightsShortcodeModel;
use Travelpayouts\modules\tables\components\flights\fromOurCityFly\FromOurCityFlyResponse;

class Table extends FlightsShortcodeModel
{
    /**
     * @var int
     */
    public $limit = 10;
    /**
     * @var string
     */
    public $stops;
    /**
     * @var bool
     */
    protected $one_way = false;
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
        $this->stops = $this->section->stops;
        $this->limit = (int)$this->section->pagination_size;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['limit'], 'required'],
            [['limit'], 'number'],
            [['stops'], 'in', 'range' => array_keys(ReduxOptions::stops_number())],
            [['one_way'], ValidatorBooleanString::class],
        ]);
    }

    /**
     * @return bool
     */
    public function getOneWay(): bool
    {
        return StringHelper::toBoolean($this->one_way);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_our_site_search_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(9) . $this->section->getLabel();
    }

    /**
     * @inheritdoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::ORIGIN_DESTINATION => 11,
            ColumnLabels::DEPARTURE_AT => 9,
            ColumnLabels::RETURN_AT => 10,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::ORIGIN => 7,
            ColumnLabels::DESTINATION => 6,
            ColumnLabels::FOUND_AT => 2,
            ColumnLabels::PRICE => 8,
            ColumnLabels::NUMBER_OF_CHANGES => 5,
            ColumnLabels::TRIP_CLASS => self::MIN_PRIORITY,
            ColumnLabels::DISTANCE => 3,
            ColumnLabels::PRICE_DISTANCE => 4,
        ];
    }

    /**
     * @return PriceLatestApiResponse[]
     */
    protected function getCollection(): array
    {
        $model = new PricesLatestApiModel($this->apiModelOptions());
        $model->setResponseClass(FromOurCityFlyResponse::class);
        $model->currency = $this->currency;
        $model->one_way = $this->getOneWay();
        $model->limit = $this->limit;
        $model->beginning_of_period = date('Y-m-01');
        $model->period_type = 'month';
        /** @var $models FromOurCityFlyResponse[] */
        $models = $model->getResponseModels();
        foreach ($models as $responseModel) {
            // прокидываем shortcodeModel
            $responseModel->shortcodeModel = $this;
        }
        return $models;
    }

    /**
     * @inheritdoc
     */
    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(
            parent::gridColumns(), [
                ColumnLabels::DEPARTURE_AT => [
                    'attribute' => 'depart_date'
                ],
                ColumnLabels::RETURN_AT => [
                    'attribute' => 'return_date',
                    'visible' => !$this->getOneWay()
                ],
                ColumnLabels::ORIGIN_DESTINATION => [
                    'attribute' => 'originDestination',
                    'originAttribute' => 'origin',
                    'destinationAttribute' => 'destination',
                    'delimiter' => $this->getOneWay() ?
                        ColumnOriginDestination::ONE_WAY_ARROW :
                        ColumnOriginDestination::ROUND_TRIP_ARROW
                ],
                ColumnLabels::PRICE_DISTANCE => [
                    'attribute' => 'priceDistance',
                    'priceAttribute' => 'value',
                    'distanceAttribute' => 'distance',
                ],
                ColumnLabels::PRICE => [
                    'attribute' => 'value'
                ],
                ColumnLabels::BUTTON => [
                    'departDate' => function ($model) {
                        /** @var $model FromOurCityFlyResponse */
                        return $model->depart_date;
                    },
                    'returnDate' => function ($model) {
                        /** @var $model FromOurCityFlyResponse */
                        return $model->return_date;
                    },
                    'buttonVariables' => function ($model) {
                        /** @var $model FromOurCityFlyResponse */
                        return $model->buttonVariables();
                    },
                    'sortProperty' => 'value',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'one_way',
            'stops',
            'limit',
            'hr',
            'subid',
            'button_title',
            'title',
            'off_title',
            'hr',
            'currency',
            'locale',
            'paginate',
            'disable_header',
        ];
    }

    protected function filters(ArrayQuery $query): void
    {
        $stopsCount = $this->getStopsFilterCount($this->stops);
        if ($this->stops !== null && $stopsCount !== null) {
            $query->andFilterWhere(['<=', 'number_of_changes', $stopsCount]);
        }
    }

    public function buttonVariables(): array
    {
        return FromOurCityFlyResponse::getInstance()->buttonVariables();
    }

}
