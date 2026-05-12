<?php

namespace Travelpayouts\modules\tables\components\flights\directFlightsRoute;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\directFlights\DirectFlightsResponse;
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
        return ['tp_direct_flights_route_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(6) . $this->section->getLabel();
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
    protected function filters(ArrayQuery $query): void
    {
        if ($this->filter_airline) {
            $query->andFilterWhere(['airline' => $this->filter_airline]);
        }
        if ($this->filter_flight_number) {
            $query->andFilterWhere(['flight_number' => explode(',', $this->filter_flight_number)]);
        }
    }

    /**
     * @inheritDoc
     */
    protected function getCollection(): array
    {
        $model = new Api($this->apiModelOptions());
        $model->origin = $this->origin;
        $model->destination = $this->destination;
        $model->currency = $this->currency;

        $result = [];
        foreach ($model->getResponseModels() as $responseModel) {
            $responseModel->shortcodeModel = $this;
            $result[] = $responseModel;
        }

        return $result;
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
            ],
            ColumnLabels::FLIGHT_NUMBER => [
                'attribute' => 'fullFlightNumber',
            ],
            ColumnLabels::FLIGHT => [
                'airlineCodeAttribute' => 'airline',
                'attribute' => 'flight_number',
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
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::DEPARTURE_AT => 6,
            ColumnLabels::RETURN_AT => 7,
            ColumnLabels::AIRLINE_LOGO => 4,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::FLIGHT_NUMBER => 2,
            ColumnLabels::FLIGHT => 3,
            ColumnLabels::PRICE => 5,
            ColumnLabels::AIRLINE => self::MIN_PRIORITY,
        ];
    }

    public function buttonVariables(): array
    {
        return DirectFlightsResponse::getInstance()->buttonVariables();
    }

}
