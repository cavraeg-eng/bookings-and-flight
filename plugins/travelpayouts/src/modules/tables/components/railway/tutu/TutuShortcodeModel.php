<?php

namespace Travelpayouts\modules\tables\components\railway\tutu;
use Travelpayouts\Vendor\apimatic\jsonmapper\JsonMapperException;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\formatters\StationNameFormatter;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response\TrainsSuggestApiResponse;
use Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\TrainsSuggestApiModel;
use Travelpayouts\modules\tables\components\railway\ColumnLabels;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnButton;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnDuration;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnPrice;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnRoute;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnStation;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnTime;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnTrainNumber;
use Travelpayouts\modules\tables\components\railway\RailwayShortcodeModel;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnRouteShort;
use Travelpayouts\modules\tables\components\railway\components\columns\ColumnRouteInfo;

class TutuShortcodeModel extends RailwayShortcodeModel
{
    /**
     * @var int
     */
    public $origin;
    /**
     * @var int
     */
    public $destination;
    /**
     * @var string
     */
    public $filter_train_number;
    /**
     * @Inject
     * @var Section
     */
    public $section;

    public function init()
    {
        parent::init();
        $this->theme = 'default-theme';
        $this->title = $this->section->title;
        $this->button_title = $this->section->button_title;
        $this->paginate = StringHelper::toBoolean($this->section->use_pagination);
        $this->subid = '';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin', 'destination'], 'required'],
            [
                ['origin', 'destination'],
                'number',
                'numberPattern' => '/^\d{7}$/',
                'message' => '{attribute} is invalid.',
            ],
            [['filter_train_number'], 'string'],
            [
                ['destination'],
                'compare',
                'compareAttribute' => 'origin',
                'type' => CompareValidator::TYPE_NUMBER,
                'operator' => '!==',
            ],
        ]);
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'origin' => Travelpayouts::__('Train station of origin'),
            'destination' => Travelpayouts::__('Train station of destination'),
            'filter_train_number' => Travelpayouts::__('Filter by train number or name (enter manually)'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return [
            'tp_tutu',
            'tp_tutu_shortcodes',
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->section->getLabel();
    }

    public function gutenbergFields(): array
    {
        return [
            'origin' => $this->fieldInputAutocomplete()->setAsync([
                'url' => $this->prepareEndpoint('//suggest.travelpayouts.com/search?service=tutu&term=${term}&locale=${locale}'),
                'itemProps' => [
                    'value' => '${value}',
                    'label' => '${title} [${value}]',
                ],
            ]),
            'destination' => $this->fieldInputAutocomplete()->setAsync([
                'url' => $this->prepareEndpoint('//suggest.travelpayouts.com/search?service=tutu&term=${term}&locale=${locale}'),
                'itemProps' => [
                    'value' => '${value}',
                    'label' => '${title} [${value}]',
                ],
            ]),
            'hr',
            'filter_train_number' => $this->fieldInputTag()
                ->setPlaceholder(Travelpayouts::__('Enter train numbers or names separated by commas'))
                ->setDelimiter(',')
            ,
            'hr',
            'subid',
            'button_title',
            'title',
            'off_title',
            'hr',
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
            ColumnLabels::TRAIN => 13,
            ColumnLabels::ROUTE => 11,
            ColumnLabels::ROUTE_SHORT => 14,
            ColumnLabels::ROUTE_INFO => 15,
            ColumnLabels::DEPARTURE => 10,
            ColumnLabels::ARRIVAL => 9,
            ColumnLabels::DURATION => 8,
            ColumnLabels::PRICES => 12,
            // dates имеет наивысший приоритет отображения, является action column (кнопкой)
            ColumnLabels::DATES => self::MAX_PRIORITY,
            ColumnLabels::ORIGIN => 6,
            ColumnLabels::DESTINATION => 5,
            ColumnLabels::DEPARTURE_TIME => 4,
            ColumnLabels::ARRIVAL_TIME => 3,
            ColumnLabels::ROUTE_FIRST_STATION => 2,
            ColumnLabels::ROUTE_LAST_STATION => self::MIN_PRIORITY,
        ];
    }

    /**
     * @return TutuApiResponse[]
     * @throws JsonMapperException
     */
    protected function getCollection(): array
    {
        $model = new TrainsSuggestApiModel();
        $model->term = $this->origin;
        $model->term2 = $this->destination;
        $response = $model->getMappedResponse(TrainsSuggestApiResponse::class);
        if (is_array($response->trips)) {
            $result = [];
            foreach ($response->trips as $trip) {
                $model = new TutuApiResponse();
                $model->shortcodeModel = $this;
                $model->setResponseModel($trip);
                $result[] = $model;
            }
            return $result;
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function filters(ArrayQuery $query): void
    {
        if ($this->filter_train_number) {
            $query->andFilterWhere(['trainNumber' => explode(',', $this->filter_train_number)]);
        }
    }

    /**
     * @inheritDoc
     */
    public function gridColumns(): array
    {
        return [
            ColumnLabels::TRAIN => [
                'class' => ColumnTrainNumber::class,
                'attribute' => 'trainNumber',
                'trainNameAttribute' => 'name',
            ],
            ColumnLabels::ROUTE => [
                'class' => ColumnRoute::class,
                'attribute' => 'stations',
            ],
            ColumnLabels::ROUTE_INFO => [
                'class' => ColumnRouteInfo::class,
                'attribute' => 'stations',
                'origin' => $this->origin,
                'destination' => $this->destination,
                'label' => $this->getColumnLabel(ColumnLabels::ROUTE),
            ],
            ColumnLabels::ROUTE_SHORT => [
                'class' => ColumnRouteShort::class,
                'origin' => $this->origin,
                'destination' => $this->destination,
                'label' => $this->getColumnLabel(ColumnLabels::ROUTE),
            ],
            ColumnLabels::DEPARTURE => [
                'class' => ColumnTime::class,
                'attribute' => 'departureTime',
            ],
            ColumnLabels::ARRIVAL => [
                'class' => ColumnTime::class,
                'attribute' => 'arrivalDate',
                'compareAttribute'=> 'departureDate',
            ],
            ColumnLabels::DEPARTURE_TIME => [
                'class' => ColumnTime::class,
                'attribute' => 'departureTime',
            ],
            ColumnLabels::ARRIVAL_TIME => [
                'class' => ColumnTime::class,
                'attribute' => 'arrivalTime',
            ],
            ColumnLabels::DURATION => [
                'class'=> ColumnDuration::class,
                'attribute'=> 'travelTimeInSeconds',
            ],
            ColumnLabels::PRICES => [
                'class' => ColumnPrice::class,
                'attribute' => 'categories',
            ],
            ColumnLabels::DATES => [
                'class' => ColumnButton::class,
                'value' => $this->getRawButtonTitleText(),
                'subid' => $this->subid,
                'linkMarker' => $this->linkMarker(),
                'origin' => $this->origin,
                'destination' => $this->destination,
                'buttonVariables' => function ($model) {
                    /** @var $model TutuApiResponse */
                    return $model->buttonVariables();
                },
                'visible' => !$this->section->getUseRowAsLink(),
                'sortProperty' => [TutuApiResponse::class, 'getMinimalPrice'],
            ],
            ColumnLabels::ORIGIN => [
                'class'=> ColumnStation::class,
                'attribute'=> 'departureStation',
            ],
            ColumnLabels::DESTINATION => [
                'class'=> ColumnStation::class,
                'attribute'=> 'arrivalStation',
            ],
            ColumnLabels::ROUTE_FIRST_STATION => [
                'class'=> ColumnStation::class,
                'attribute'=> 'runDepartureStation',
            ],
            ColumnLabels::ROUTE_LAST_STATION => [
                'class'=> ColumnStation::class,
                'attribute'=> 'runArrivalStation',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function columnLabels(): array
    {
        return ColumnLabels::getInstance()->getColumnLabels(null, 'ru');
    }

    /**
     * @inheritDoc
     */
    protected function titleVariables(): array
    {
        return [
            'origin' => StationNameFormatter::getInstance()->format($this->origin),
            'destination' => StationNameFormatter::getInstance()->format($this->destination),
        ];
    }

    public function titleVariableLabels(): array
    {
        return array_merge(parent::titleVariableLabels(), [
            'origin' => Travelpayouts::__('Origin'),
            'destination' => Travelpayouts::__('Destination'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function gridOptions(): array
    {
        $gridColumns = $this->gridColumns();
        /** @var null| ColumnButton $buttonColumnInstance */
        $buttonColumnInstance = null;
        // создаем колонку с кнопкой для получения корректной ссылки
        if (isset($gridColumns[ColumnLabels::DATES]) && $this->section->getUseRowAsLink()) {
            $buttonColumnInstance = BaseObject::createObject($gridColumns[ColumnLabels::DATES]);
        }

        return array_merge(parent::gridOptions(),
            [
                'emptyText' => '',
                'rowOptions' => function ($model) use ($buttonColumnInstance) {
                    // Добавляем возможность кликать по рядам если getUseRowAsLink === true
                    if ($buttonColumnInstance) {
                        return [
                            'class' => 'travelpayouts-row-link',
                            'data-href' => $buttonColumnInstance->getButtonUrl($model),
                        ];
                    }
                    return [];
                },
            ]
        );
    }

    public function buttonVariables(): array
    {
        return TutuApiResponse::getInstance()->buttonVariables();
    }

}
