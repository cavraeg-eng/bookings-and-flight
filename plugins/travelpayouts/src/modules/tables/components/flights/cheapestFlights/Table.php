<?php

namespace Travelpayouts\modules\tables\components\flights\cheapestFlights;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesCheap\PricesCheapApiModel;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
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
    public $destination;
    /**
     * @var string
     */
    public $filter_airline;
    /**
     * @var string
     */
    public $filter_flight_number;
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
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], 'string', 'length' => 3],
            [
                ['destination'],
                'compare',
                'compareAttribute' => 'origin',
                'type' => CompareValidator::TYPE_STRING,
                'operator' => '!==',
            ],
            [['filter_airline', 'filter_flight_number'], 'string'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_cheapest_flights_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'origin',
            'destination',
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

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(3) . $this->section->getLabel();
    }

    /**
     * @inheritdoc
     */
    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(
            parent::gridColumns(), [
                ColumnLabels::BUTTON => [
                    'departDate' => function ($model) {
                        /** @var $model CheapestFlightsResponse */
                        return $model->departure_at;
                    },
                    'returnDate' => function ($model) {
                        /** @var $model CheapestFlightsResponse */
                        return $model->return_at;
                    },
                    'buttonVariables' => function ($model) {
                        /** @var $model CheapestFlightsResponse */
                        return $model->buttonVariables();
                    },
                ],
                ColumnLabels::AIRLINE_LOGO => [
                    'attribute' => 'airline',
                ],
                ColumnLabels::AIRLINE => [
                    'attribute' => 'airline',
                ],
                ColumnLabels::FLIGHT => [
                    'airlineCodeAttribute' => ColumnLabels::AIRLINE,
                ],
                ColumnLabels::FLIGHT_NUMBER => [
                    'attribute' => 'flightNumber',
                    'headerOptions' => [
                        'class' => 'no-sort',
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::DEPARTURE_AT => 7,
            ColumnLabels::RETURN_AT => 8,
            ColumnLabels::NUMBER_OF_CHANGES => 4,
            ColumnLabels::AIRLINE_LOGO => 5,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::FLIGHT_NUMBER => 2,
            ColumnLabels::FLIGHT => 3,
            ColumnLabels::PRICE => 6,
            ColumnLabels::AIRLINE => self::MIN_PRIORITY,
        ];
    }

    /**
     * @return CheapestFlightsResponse[]
     */
    protected function getCollection(): array
    {
        $model = new PricesCheapApiModel($this->apiModelOptions());
        // подменяем класс для ответа
        $model->responseClass = CheapestFlightsResponse::class;
        $model->currency = $this->currency;
        $model->origin = $this->origin;
        $model->destination = $this->destination;
        /** @var $models CheapestFlightsResponse[] */
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
    protected function filters(ArrayQuery $query): void
    {
        if ($this->filter_airline) {
            $query->andFilterWhere(['airline' => $this->filter_airline]);
        }
        if ($this->filter_flight_number) {
            $query->andFilterWhere(['flight_number' => explode(',', $this->filter_flight_number)]);
        }
    }

    public function buttonVariables(): array
    {
        return CheapestFlightsResponse::getInstance()->buttonVariables();
    }

}
