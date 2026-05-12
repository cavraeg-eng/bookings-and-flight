<?php

namespace Travelpayouts\modules\tables\components\flights\inOurCityFly;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\validators\ValidatorBooleanString;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest\PricesLatestApiModel;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\columns\ColumnOriginDestination;
use Travelpayouts\modules\tables\components\flights\FlightsShortcodeModel;
use Travelpayouts\modules\tables\components\flights\fromOurCityFly\FromOurCityFlyResponse;

class Table extends FlightsShortcodeModel
{
    /**
     * @var string
     */
    public $destination;
    /**
     * @var int
     */
    public $limit = 10;
    /**
     * @var string
     */
    public $stops;
    /**
     * @var string
     */
    public $period_type;
    /**
     * @var bool
     */
    public $one_way = false;
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
            [['limit', 'destination'], 'required'],
            [['destination'], 'string', 'length' => 3],
            [['limit'], 'number'],
            [['period_type'], 'string'],
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
        return ['tp_in_our_city_fly_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(11) . $this->section->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'destination',
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

    /**
     * @inheritDoc
     */
    protected function getCollection(): array
    {
        $model = new PricesLatestApiModel($this->apiModelOptions());
        $model->scenario = PricesLatestApiModel::SCENARIO_WITH_REQUIRED_DESTINATION;
        $model->currency = $this->currency;
        $model->destination = $this->destination;
        $model->period_type = $this->period_type;
        $model->limit = $this->limit;
        $model->one_way = $this->getOneWay();
        $model->setResponseClass(FromOurCityFlyResponse::class);
        /** @var FromOurCityFlyResponse[] $responseModels */
        $responseModels = $model->getResponseModels();
        $result = [];
        foreach ($responseModels as $responseModel) {
            $responseModel->shortcodeModel = $this;
            $result[] = $responseModel;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::ORIGIN => 9,
            ColumnLabels::DEPARTURE_AT => 7,
            ColumnLabels::RETURN_AT => 8,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::DESTINATION => self::MIN_PRIORITY,
            ColumnLabels::FOUND_AT => 2,
            ColumnLabels::PRICE => 6,
            ColumnLabels::NUMBER_OF_CHANGES => 5,
            ColumnLabels::TRIP_CLASS => self::MIN_PRIORITY,
            ColumnLabels::DISTANCE => 3,
            ColumnLabels::PRICE_DISTANCE => 4,
            ColumnLabels::ORIGIN_DESTINATION => self::MIN_PRIORITY,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function filters(ArrayQuery $query): void
    {
        $stopsCount = $this->getStopsFilterCount($this->stops);
        if ($this->stops !== null && $stopsCount !== null) {
            $query->andFilterWhere(['<=', 'number_of_changes', $stopsCount]);
        }
    }

    /**
     * @inheritDoc
     */
    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(parent::gridColumns(), [
            ColumnLabels::ORIGIN => [
                'attribute' => 'origin',
            ],
            ColumnLabels::DESTINATION => [
                'attribute' => 'destination',
            ],
            ColumnLabels::FOUND_AT => [
                'attribute' => 'found_at',
            ],
            ColumnLabels::NUMBER_OF_CHANGES => [
                'attribute' => 'number_of_changes',
            ],
            ColumnLabels::TRIP_CLASS => [
                'attribute' => 'trip_class',
            ],
            ColumnLabels::DISTANCE => [
                'attribute' => 'distance',
            ],
            ColumnLabels::PRICE => [
                'attribute' => 'value',
            ],
            ColumnLabels::DEPARTURE_AT => [
                'attribute' => 'depart_date',
            ],
            ColumnLabels::RETURN_AT => [
                'attribute' => 'return_date',
                'visible' => !$this->getOneWay()
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
                'origin' => function ($model) {
                    /** @var $model FromOurCityFlyResponse */
                    return $model->origin;
                },
                'sortProperty' => 'value',
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
        ]);
    }

    public function buttonVariables(): array
    {
        return FromOurCityFlyResponse::getInstance()->buttonVariables();
    }

}
