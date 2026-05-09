<?php

namespace Travelpayouts\modules\tables\components\flights\fromOurCityFly;

use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\validators\ValidatorBooleanString;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest\PriceLatestApiResponse;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest\PricesLatestApiModel;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\columns\ColumnOriginDestination;
use Travelpayouts\modules\tables\components\flights\FlightsShortcodeModel;

class Table extends FlightsShortcodeModel
{
    /**
     * @var string
     */
    public $origin;
    /**
     * @var string
     */
    public $limit;
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
            [['limit', 'origin'], 'required'],
            [['origin'], 'string', 'length' => 3],
            [['limit'], 'number'],
            [['period_type'], 'in', 'range' => PricesLatestApiModel::availablePeriodTypes()],
            [['one_way'], ValidatorBooleanString::class],
            [['stops'], 'in', 'range' => array_keys(ReduxOptions::stops_number())],
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
        return ['tp_from_our_city_fly_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(10) . $this->section->getLabel();
    }

    /**
     * @inheritdoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::DESTINATION => 9,
            ColumnLabels::DEPARTURE_AT => 7,
            ColumnLabels::RETURN_AT => 8,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::ORIGIN => self::MIN_PRIORITY,
            ColumnLabels::FOUND_AT => 3,
            ColumnLabels::PRICE => 7,
            ColumnLabels::NUMBER_OF_CHANGES => 6,
            ColumnLabels::TRIP_CLASS => 2,
            ColumnLabels::DISTANCE => 4,
            ColumnLabels::PRICE_DISTANCE => 5,
            ColumnLabels::ORIGIN_DESTINATION => self::MIN_PRIORITY,
        ];
    }

    /**
     * @return PriceLatestApiResponse[]
     * @throws InvalidConfigException
     */
    protected function getCollection(): array
    {
        $model = new PricesLatestApiModel($this->apiModelOptions());
        $model->setResponseClass(FromOurCityFlyResponse::class);
        $model->currency = $this->currency;
        $model->origin = $this->origin;
        $model->beginning_of_period = date('Y-m-01');
        $model->period_type = $this->period_type;
        $model->one_way = $this->getOneWay();
        $model->limit = $this->limit;
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
                ColumnLabels::PRICE => [
                    'attribute' => 'value'
                ],
                ColumnLabels::DEPARTURE_AT => [
                    'attribute' => 'depart_date'
                ],
                ColumnLabels::RETURN_AT => [
                    'attribute' => 'return_date',
                    'visible' => !$this->getOneWay()
                ],
                ColumnLabels::BUTTON => [
                    'destination' => function ($model) {
                        /** @var $model FromOurCityFlyResponse */
                        return $model->destination;
                    },
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
                ColumnLabels::ORIGIN_DESTINATION => [
                    'attribute' => 'originDestination',
                    'originAttribute' => 'origin',
                    'destinationAttribute' => 'destination',
                    'delimiter' => $this->getOneWay() ?
                        ColumnOriginDestination::ONE_WAY_ARROW :
                        ColumnOriginDestination::ROUND_TRIP_ARROW,
                ],
                ColumnLabels::PRICE_DISTANCE => [
                    'attribute' => 'priceDistance',
                    'priceAttribute' => 'value',
                    'distanceAttribute' => 'distance',
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
            'origin',
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
    protected function filters(ArrayQuery $query): void
    {
        $stopsCount = $this->getStopsFilterCount($this->stops);
        if ($this->stops !== null && $stopsCount !== null) {
            $query->andFilterWhere(['<=', 'number_of_changes', $stopsCount]);
        }
        $query->andFilterWhere(['>=', 'depart_date', date('Y-m-d')]);
    }

    public function buttonVariables(): array
    {
        return FromOurCityFlyResponse::getInstance()->buttonVariables();
    }

}
