<?php

namespace Travelpayouts\modules\tables\components\flights\priceCalendarWeek;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\components\validators\ValidatorBooleanString;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\pricesMonthMatrix\PricesMonthMatrixApiModel;
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
    public $stops;
    /**
     * @var string
     */
    public $depart_date_days = '1';
    /**
     * @var string
     */
    public $return_date_days = '12';
    /**
     * @Inject
     * @var Section
     */
    public $section;

    /**
     * @var bool
     */
    protected $one_way;

    public function init()
    {
        parent::init();
        $this->title = $this->section->title;
        $this->button_title = $this->section->button_title;
        $this->subid = $this->section->subid;
        $this->paginate = StringHelper::toBoolean($this->section->use_pagination);
        $this->stops = $this->section->stops;
        $this->depart_date_days = $this->section->depart_date;
        $this->return_date_days = $this->section->return_date;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], 'string', 'length' => 3],
            [['depart_date_days', 'return_date_days'], 'safe'],
            [
                ['destination'],
                'compare',
                'compareAttribute' => 'origin',
                'type' => CompareValidator::TYPE_STRING,
                'operator' => '!==',
            ],
            [['stops'], 'in', 'range' => array_keys(ReduxOptions::stops_number())],
            [['one_way'], ValidatorBooleanString::class],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::DEPARTURE_AT => 6,
            ColumnLabels::NUMBER_OF_CHANGES => 4,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::RETURN_AT => 3,
            ColumnLabels::PRICE => 5,
            ColumnLabels::TRIP_CLASS => 2,
            ColumnLabels::DISTANCE => self::MIN_PRIORITY,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getCollection(): array
    {
        $model = new PricesMonthMatrixApiModel($this->apiModelOptions());
        $model->setResponseClass(PriceCalendarWeekApiResponse::class);
        $model->origin = $this->origin;
        $model->destination = $this->destination;
        $model->currency = $this->currency;
        $model->show_to_affiliates = true;
        $model->month = (new \DateTime())->format('Y-m-d');
        $model->one_way = StringHelper::toBoolean($this->one_way);
        $result = [];
        foreach ($model->getResponseModels() as $responseModel) {
            /** @var PriceCalendarWeekApiResponse $responseModel */
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
                'attribute' => 'departDateWithAddedDays',
            ],
            ColumnLabels::RETURN_AT => [
                'attribute' => 'returnDateWithAddedDays',
            ],
            ColumnLabels::PRICE => [
                'attribute' => 'value',
            ],
            ColumnLabels::BUTTON => [
                'departDate' => function ($model) {
                    /** @var $model PriceCalendarWeekApiResponse */
                    return $model->getDepartDateWithAddedDays();
                },
                'returnDate' => function ($model) {
                    /** @var $model PriceCalendarWeekApiResponse */
                    return $model->getReturnDateWithAddedDays();
                },
                'buttonVariables' => function ($model) {
                    /** @var $model PriceCalendarWeekApiResponse */
                    return $model->buttonVariables();
                },
                'sortProperty' => 'value',
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags(): array
    {
        return ['tp_price_calendar_week_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(2) . $this->section->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'origin',
            'destination',
            'stops',
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

    protected function filters(ArrayQuery $query): void
    {
        $stopsCount = $this->getStopsFilterCount($this->stops);
        if ($this->stops !== null && $stopsCount !== null) {
            $query->andFilterWhere(['<=', 'number_of_changes', $stopsCount]);
        }
    }

    public function buttonVariables(): array
    {
        return PriceCalendarWeekApiResponse::getInstance()->buttonVariables();
    }

}
