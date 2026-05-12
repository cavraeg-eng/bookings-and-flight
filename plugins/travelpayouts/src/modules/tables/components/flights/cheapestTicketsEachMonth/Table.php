<?php

namespace Travelpayouts\modules\tables\components\flights\cheapestTicketsEachMonth;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesMonthly\PricesMonthlyApiModel;
use Travelpayouts\modules\tables\components\flights\cheapestTicketEachDayMonth\CheapestTicketEachDayMonthApiResponse;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\FlightsShortcodeModel;
use Travelpayouts\modules\tables\components\flights\popularRoutesFromCity\PopularRoutesFromCityApiResponse;

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
     * @var string
     */
    public $stops;
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
            [
                [
                    'origin',
                    'destination',
                    'currency',
                ],
                'string',
                'length' => 3,
            ],
            [
                ['destination'],
                'compare',
                'compareAttribute' => 'origin',
                'type' => CompareValidator::TYPE_STRING,
                'operator' => '!==',
            ],
            [['filter_airline', 'filter_flight_number'], 'string'],
            [['stops'], 'in', 'range' => array_keys(ReduxOptions::stops_number())],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_cheapest_tickets_each_month_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(5) . $this->section->getLabel();
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
            'stops',
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
        $model = new PricesMonthlyApiModel($this->apiModelOptions());
        $model->setResponseClass(CheapestTicketEachDayMonthApiResponse::class);
        $model->currency = $this->currency;
        $model->origin = $this->origin;
        $model->destination = $this->destination;
        /** @var CheapestTicketEachDayMonthApiResponse[] $responseModels */
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
     * @inheritDoc
     */
    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(parent::gridColumns(), [
            ColumnLabels::DEPARTURE_AT => [
                'attribute' => 'departure_at',
            ],
            ColumnLabels::RETURN_AT => [
                'attribute' => 'return_at',
            ],
            ColumnLabels::NUMBER_OF_CHANGES => [
                'attribute' => 'transfers',
            ],
            ColumnLabels::AIRLINE_LOGO => [
                'attribute' => 'airline',
            ],
            ColumnLabels::BUTTON => [
                'departDate' => function ($model) {
                    /** @var $model PopularRoutesFromCityApiResponse */
                    return $model->departure_at;
                },
                'returnDate' => function ($model) {
                    /** @var $model PopularRoutesFromCityApiResponse */
                    return $model->return_at;
                },
                'buttonVariables' => function ($model) {
                    /** @var $model PopularRoutesFromCityApiResponse */
                    return $model->buttonVariables();
                },
            ],
            ColumnLabels::FLIGHT_NUMBER => [
                'attribute' => 'fullFlightNumber',
            ],
            ColumnLabels::FLIGHT => [
                'attribute'=>'flight_number',
                'airlineCodeAttribute' => 'airline',
            ],
            ColumnLabels::PRICE => [
                'attribute' => 'price',
            ],
            ColumnLabels::AIRLINE => [
                'attribute' => 'airline',
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function filters(ArrayQuery $query): void
    {
        if ($this->filter_airline) {
            $query->andFilterWhere(['airline' => $this->filter_airline]);
        }
        if ($this->filter_flight_number) {
            $query->andFilterWhere(['flight_number' => explode(',', $this->filter_flight_number)]);
        }

        $stopsCount = $this->getStopsFilterCount($this->stops);
        if ($this->stops !== null && $stopsCount !== null) {
            $query->andFilterWhere(['<=', 'transfers', $stopsCount]);
        }
    }

    public function buttonVariables(): array
    {
        return CheapestTicketEachDayMonthApiResponse::getInstance()->buttonVariables();
    }

}
