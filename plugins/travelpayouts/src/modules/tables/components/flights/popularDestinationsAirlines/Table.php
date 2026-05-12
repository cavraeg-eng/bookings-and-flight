<?php

namespace Travelpayouts\modules\tables\components\flights\popularDestinationsAirlines;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\formatters\AirlineNameFormatter;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\airlineDirections\AirlineDirectionsApiModel;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\airlineDirections\AirlineDirectionsApiResponse;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\columns\ColumnOriginDestination;
use Travelpayouts\modules\tables\components\flights\FlightsShortcodeModel;

class Table extends FlightsShortcodeModel
{
    /**
     * @var int
     */
    public $limit = 10;
    /**
     * @var string
     */
    public $airline;
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
        $this->limit = (int)$this->section->pagination_size;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['limit', 'airline'], 'required'],
            [['airline'], 'string'],
            [['limit'], 'number'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_popular_destinations_airlines_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::PLACE => self::MIN_PRIORITY,
            ColumnLabels::DIRECTION => 2,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
        ];
    }

    /**
     * @inheritDoc
     */
    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(parent::gridColumns(), [
            ColumnLabels::PLACE => [
                'value' => static function ($model) {
                    /** @var AirlineDirectionsApiResponse $model */
                    return $model->index + 1;
                },
                'headerOptions' => [
                    'style' => 'max-width: 100px',
                ],
            ],
            ColumnLabels::DIRECTION => [
                'class' => ColumnOriginDestination::class,
                'originAttribute' => 'origin',
                'destinationAttribute' => 'destination',
                'locale' => $this->locale,
                'delimiter' => ColumnOriginDestination::ONE_WAY_ARROW,
                'useAirportNameFormatter' => true,

            ],
            ColumnLabels::BUTTON => [
                'origin' => static function ($model) {
                    /** @var AirlineDirectionsApiResponse $model */
                    return $model->origin;
                },
                'destination' => static function ($model) {
                    /** @var AirlineDirectionsApiResponse $model */
                    return $model->destination;
                },
                'headerOptions' => [
                    'class' => 'no-sort',
                ],
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getCollection(): array
    {
        $model = new AirlineDirectionsApiModel($this->apiModelOptions());
        $model->airline_code = $this->airline;
        $model->limit = $this->limit;
        return $model->getResponseModels();
    }

    /**
     * @inheritDoc
     */
    protected function titleVariables(): array
    {
        return [
            'airline' => is_string($this->airline) ?
                AirlineNameFormatter::getInstance()->getAirlineName(strtoupper($this->airline), $this->locale)
                : '',
        ];
    }

}
