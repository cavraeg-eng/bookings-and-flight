<?php

namespace Travelpayouts\modules\tables\components\flights\flightSchedule;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\components\validators\ValidatorBooleanString;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\FlightScheduleApiModel;
use Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response\FlightScheduleApiResponse;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;
use Travelpayouts\modules\tables\components\flights\columns\ColumnAirline;
use Travelpayouts\modules\tables\components\flights\columns\ColumnAirlineLogo;
use Travelpayouts\modules\tables\components\flights\flightSchedule\components\ScheduleColumn;
use Travelpayouts\modules\tables\components\flights\flightSchedule\components\TimeAndStopsColumn;
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
     * @var bool|string
     */
    protected $off_subtitle = false;
    /**
     * @var bool|string
     */
    protected $one_way = false;
    /**
     * @Inject
     * @var Section
     */
    public $section;

    /**
     * @var FlightScheduleApiResponse
     */
    protected $_response;

    public function init()
    {
        parent::init();
        $this->title = $this->section->title;
        $this->button_title = $this->section->button_title;
        $this->subid = $this->section->subid;
        $this->paginate = StringHelper::toBoolean($this->section->use_pagination);
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'off_subtitle' => Travelpayouts::__('Hide subtitle'),
        ]);
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
            [['filter_airline'], 'string'],
            [['one_way', 'off_subtitle'], ValidatorBooleanString::class],
        ]);
    }

    /**
     * @return bool
     */
    public function getOffSubtitle(): bool
    {
        return StringHelper::toBoolean($this->off_subtitle);
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
        return ['tp_flights_schedule_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(12) . $this->section->getLabel();
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
            'one_way',
            'hr',
            'locale',
            'paginate',
            'disable_header',
        ];
    }

    protected function getResponse(): ?FlightScheduleApiResponse
    {
        if (!$this->_response) {
            $model = new FlightScheduleApiModel($this->apiModelOptions());
            $model->service = 'api_flight_schedule';
            $model->origin = $this->origin;
            $model->destination = $this->destination;
            $model->locale = $this->locale;
            $model->airline = $this->filter_airline;
            $model->non_direct_flights = !$this->getOneWay();
            $mappedResponse = $model->getMappedResponse(FlightScheduleApiResponse::class);
            if ($mappedResponse !== null) {
                $this->_response = $mappedResponse;
            }
        }
        return $this->_response;
    }

    protected function getCollection(): array
    {
        $response = $this->getResponse();
        if ($response) {
            $responseModels = $this->getOneWay() ? $response->direct_flights : $response->flights;
            $result = [];
            if (is_array($responseModels)) {
                foreach ($responseModels as $responseModel) {
                    $model = new FlightScheduleResponse();
                    $model->shortcodeModel = $this;
                    $model->responseModel = $responseModel;
                    $result[] = $model;
                }
            }
            return $result;
        }
        return [];
    }

    protected function titleVariables(): array
    {
        return [
            'flights_number' => function () {
                return $this->getResponse()->title->flights_number;
            },
            'min_flight_duration' => function () {
                return $this->getResponse()->title->flightDurationText($this->locale);
            },
            'flights_every_day' => function () {
                return $this->getResponse()->title->flights_every_day
                    ? ' ' . Travelpayouts::t('flights.title.every_day', [], 'tables', $this->locale)
                    : '';
            },
        ];
    }

    public function titleVariableLabels(): array
    {
        return array_merge(parent::titleVariableLabels(), [
            'flights_number' => Travelpayouts::__('Flights count'),
            'min_flight_duration' => Travelpayouts::__('Min flight duration'),
            'flights_every_day' => Travelpayouts::__('Prints "every day" if flights are every day'),
        ]);
    }

    public function getGridSubtitle(): ?string
    {
        if ($this->getOffSubtitle()) {
            return null;
        }

        return HtmlHelper::tagArrayContent('div', ['class' => 'tp-flex tp-items-center'], [
            HtmlHelper::tag(
                'span',
                ['class' => 'tp-origin-city tp-font-bold'],
                $this->getResponse()->subtitle->origin->city
            ) . ', ' . HtmlHelper::tag(
                'span',
                ['class' => 'tp-origin-country'],
                $this->getResponse()->subtitle->origin->country
            ),
            '<i class="tp-i-tabler:arrow-right tp-mx-2"></i>',
            HtmlHelper::tag(
                'span',
                ['class' => 'tp-destination-city tp-font-bold'],
                $this->getResponse()->subtitle->destination->city

            ) . ', ' . HtmlHelper::tag(
                'span',
                ['class' => 'tp-destination-country'],
                $this->getResponse()->subtitle->destination->country
            ),
        ]);
    }

    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::TIME_AND_STOPS => 7,
            ColumnLabels::SCHEDULE => 6,
            ColumnLabels::FULL_AIRLINE_LOGO => 5,
            ColumnLabels::COMPACT_AIRLINE_LOGO => 4,
            ColumnLabels::FLIGHT_NUMBER => 3,
            ColumnLabels::ROUTE => 2,
            ColumnLabels::AIRLINE_NAME => self::MIN_PRIORITY,
        ];
    }

    public function gridColumns(): array
    {
        return Travelpayouts\helpers\ArrayHelper::mergeRecursive(
            parent::gridColumns(), [
                ColumnLabels::FLIGHT_NUMBER => [
                    'attribute' => 'fullFlightNumber',
                ],
                ColumnLabels::SCHEDULE => [
                    'class' => ScheduleColumn::class,
                    'attribute' => 'op_days',
                    'locale' => $this->locale,
                ],
                ColumnLabels::AIRLINE_NAME => [
                    'class' => ColumnAirline::class,
                    'locale' => $this->locale,
                    'attribute' => 'airlineCode',
                ],
                ColumnLabels::FULL_AIRLINE_LOGO => [
                    'class' => ColumnAirlineLogo::class,
                    'locale' => $this->locale,
                    'attribute' => 'airlineCode',
                ],
                ColumnLabels::TIME_AND_STOPS => [
                    'class' => TimeAndStopsColumn::class,
                    'locale' => $this->locale,
                ],
                ColumnLabels::COMPACT_AIRLINE_LOGO => [
                    'class' => ColumnAirlineLogo::class,
                    'width' => 32,
                    'height' => 32,
                    'attribute' => 'airlineCode',
                    'locale' => $this->locale,
                    'showAirlineName' => true,
                ],
                ColumnLabels::ROUTE => [],
                ColumnLabels::BUTTON => [
                    'headerOptions' => [
                        'class' => 'no-sort',
                    ],
                ],
            ]
        );
    }
}
