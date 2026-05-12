<?php

namespace Travelpayouts\modules\tables\components\flights\priceCalendarMonth;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
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
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], 'string', 'length' => 3],
            [['stops'], 'in', 'range' => array_keys(ReduxOptions::stops_number())],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function gridColumnsPriority(): array
    {
        return [
            ColumnLabels::DEPARTURE_AT => 5,
            ColumnLabels::NUMBER_OF_CHANGES => 3,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::PRICE => 4,
            ColumnLabels::TRIP_CLASS => 2,
            ColumnLabels::DISTANCE => self::MIN_PRIORITY,
        ];
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
            /** @var PriceCalendarMonthApiResponse $responseModel */
            $responseModel->shortcodeModel = $this;
            $result[] = $responseModel;
        }
        return $result;
    }

    protected function filters(ArrayQuery $query): void
    {
        $stopsCount = $this->getStopsFilterCount($this->stops);
        if ($this->stops !== null && $stopsCount !== null) {
            $query->andFilterWhere(['<=', 'number_of_changes', $stopsCount]);
        }
    }

    /**
     * @inheritDoc
     */
    public function gridColumns(): array
    {
        return ArrayHelper::mergeRecursive(parent::gridColumns(), [
            ColumnLabels::DEPARTURE_AT => [
                'attribute' => 'depart_date',
            ],
            ColumnLabels::PRICE => [
                'attribute' => 'value',
            ],
            ColumnLabels::BUTTON => [
                'departDate' => function ($model) {
                    /** @var $model PriceCalendarMonthApiResponse */
                    return $model->depart_date;
                },
                'buttonVariables' => function ($model) {
                    /** @var $model PriceCalendarMonthApiResponse */
                    return $model->buttonVariables();
                },
                'sortProperty' => 'value',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function shortcodeTags(): array
    {
        return [
            'tp_price_calendar_month_shortcodes',
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->numberPrefix(1) . $this->section->getLabel();
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

    public function buttonVariables(): array
    {
        return PriceCalendarMonthApiResponse::getInstance()->buttonVariables();
    }

}
