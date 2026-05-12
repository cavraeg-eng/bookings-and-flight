<?php

namespace Travelpayouts\modules\tables\components\flights\popularRoutesFromCity;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\cityDirections\CityDirectionsApiModel;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\FlightsShortcodeModel;

class Table extends FlightsShortcodeModel
{
    /**
     * @var string
     */
    public $origin;
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
            [['origin'], 'required'],
            [['origin'], 'string', 'length' => 3],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_popular_routes_from_city_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(8) . $this->section->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'origin',
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
            ColumnLabels::AIRLINE => 1,
            ColumnLabels::ORIGIN_DESTINATION => 4,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getCollection(): array
    {
        $model = new CityDirectionsApiModel($this->apiModelOptions());
        $model->origin = $this->origin;
        $model->currency = $this->currency;
        $model->setResponseClass(PopularRoutesFromCityApiResponse::class);
        $result = [];
        foreach ($model->getResponseModels() as $responseModel) {
            /** @var PopularRoutesFromCityApiResponse $responseModel */
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
            ColumnLabels::DESTINATION => [
                'attribute' => 'destination',
            ],
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
            ColumnLabels::ORIGIN_DESTINATION => [
                'originAttribute' => 'origin',
                'destinationAttribute' => 'destination',
            ],
        ]);
    }

    public function buttonVariables(): array
    {
        return PopularRoutesFromCityApiResponse::getInstance()->buttonVariables();
    }

}
