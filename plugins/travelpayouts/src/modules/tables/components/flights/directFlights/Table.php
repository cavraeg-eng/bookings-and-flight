<?php

namespace Travelpayouts\modules\tables\components\flights\directFlights;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesDirect\PricesDirectApiModel;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\columns\ColumnDirection;
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
    public $filter_airline;
    /**
     * @var string
     */
    public $filter_flight_number;
    /**
     * @var int
     */
    public $limit = 10;
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
        $this->limit = (int)$this->section->pagination_size;
        $this->paginate = StringHelper::toBoolean($this->section->use_pagination);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin'], 'required'],
            [['origin'], 'string', 'length' => 3],
            [['filter_airline', 'filter_flight_number'], 'string'],
            [['limit'], 'number'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_direct_flights_shortcodes'];
    }


    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(7) . $this->section->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'origin',
            'limit',
            'hr',
            'subid',
            'button_title',
            'title',
            'off_title',
            'hr',
            'filter_airline',
            'filter_flight_number',
            'hr',
            'currency',
            'locale',
            'paginate',
            'disable_header',
        ];
    }

    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::DESTINATION => 9,
            ColumnLabels::DEPARTURE_AT => 7,
            ColumnLabels::RETURN_AT => 8,
            ColumnLabels::AIRLINE_LOGO => 5,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::FLIGHT_NUMBER => 3,
            ColumnLabels::FLIGHT => 2,
            ColumnLabels::PRICE => 6,
            ColumnLabels::AIRLINE => self::MIN_PRIORITY,
            ColumnLabels::ORIGIN_DESTINATION => 4,
        ];
    }

    protected function getCollection(): array
    {
        $model = new PricesDirectApiModel($this->apiModelOptions());
        $model->origin = $this->origin;
        $model->currency = $this->currency;
        $model->responseClass = DirectFlightsResponse::class;
        /** @var $models DirectFlightsResponse[] */
        $models = $model->getResponseModels();
        foreach ($models as $responseModel) {
            $responseModel->shortcodeModel = $this;
        }
        return $models;
    }

    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(parent::gridColumns(), [
            ColumnLabels::DESTINATION => [
                'class'=> ColumnDirection::class,
            ],
            ColumnLabels::DEPARTURE_AT => [
                'attribute'=> 'departure_at'
            ],
            ColumnLabels::RETURN_AT => [
                'attribute'=> 'return_at'
            ],
            ColumnLabels::AIRLINE_LOGO => [
                'attribute' => 'airline',
            ],
            ColumnLabels::BUTTON => [
                'departDate' => function ($model) {
                    /** @var $model DirectFlightsResponse */
                    return $model->departure_at;
                },
                'returnDate' => function ($model) {
                    /** @var $model DirectFlightsResponse */
                    return $model->return_at;
                },
                'buttonVariables' => function ($model) {
                    /** @var $model DirectFlightsResponse */
                    return $model->buttonVariables();
                },
                'sortProperty' => 'price',
            ],
            ColumnLabels::FLIGHT_NUMBER => [
                'attribute'=> 'fullFlightNumber'
            ],
            ColumnLabels::FLIGHT => [
                'airlineCodeAttribute' => 'airline',
                'attribute'=> 'flight_number',
            ],
            ColumnLabels::AIRLINE => [
                'attribute' => 'airline',
            ],
            ColumnLabels::ORIGIN_DESTINATION => [
                'originAttribute' => 'origin',
                'destinationAttribute' => 'destination',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function filters(ArrayQuery $query): void
    {
        if ($this->filter_airline) {
            $query->andFilterWhere(['airline' => $this->filter_airline]);
        }
        if ($this->filter_flight_number) {
            $query->andFilterWhere(['flight_number' => explode(',', $this->filter_flight_number)]);
        }

        if ($this->limit) {
            $query->limit($this->limit);
        }
    }

    /**
     * @inheritDoc
     */
    protected function getGridEmptyMessage(): string
    {
        return '';
    }

    public function buttonVariables(): array
    {
        return DirectFlightsResponse::getInstance()->buttonVariables();
    }

}
