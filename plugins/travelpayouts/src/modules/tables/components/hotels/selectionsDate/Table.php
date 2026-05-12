<?php

namespace Travelpayouts\modules\tables\components\hotels\selectionsDate;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\components\formatters\HumanDateFormatter;
use Travelpayouts\components\validators\CarbonDateValidator;
use Travelpayouts\modules\tables\components\api\hotelLook\locationMap\LocationApiModel;
use Travelpayouts\modules\tables\components\hotels\ColumnLabels;

/**
 * Class Table
 * @package Travelpayouts\modules\tables\components\hotels\selectionsDate
 */
class Table extends \Travelpayouts\modules\tables\components\hotels\selectionsDiscount\Table
{
    /**
     * @var string
     */
    public $check_in;
    /**
     * @var string
     */
    public $check_out;
    /**
     * @Inject
     * @var Section
     */
    public $section;

    /**
     * Дата заезда в формате необходимом api
     * @var string
     */
    public $checkOutFormatted;

    /**
     * Дата выезда в формате необходимом api
     * @var string
     */
    public $checkInFormatted;


    public function rules()
    {
        return array_merge(parent::rules(), [
            [['check_in', 'check_out'], 'required'],
            [
                ['check_in'],
                CarbonDateValidator::class,
                'format' => LocationApiModel::DATE_INPUT_FORMAT,
                'outputFormat' => LocationApiModel::DATE_OUTPUT_FORMAT,
                'outputAttribute' => 'checkInFormatted',
            ],
            [
                ['check_out'],
                CarbonDateValidator::class,
                'format' => LocationApiModel::DATE_INPUT_FORMAT,
                'outputFormat' => LocationApiModel::DATE_OUTPUT_FORMAT,
                'outputAttribute' => 'checkOutFormatted',
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_hotels_selections_date_shortcodes'];
    }

    /**
     * @return string
     */
    public function linkMarker()
    {
        return 'tp_hotel_sel_dates';
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return $this->section->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'city',
            'type_selections',
            'type_selections_label',
            'check_in',
            'check_out',
            $this->fieldText()->setLabel(
                Travelpayouts::__('If we don’t have prices in our cache for these dates – no table will be shown')
            ),
            'hr',
            'subid',
            'button_title',
            'title',
            'off_title',
            'hr',
            'number_results',
            'link_without_dates',
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
            ColumnLabels::NAME => 5,
            ColumnLabels::STARS => 4,
            ColumnLabels::RATING => 2,
            ColumnLabels::PRICE_PN => 3,
            ColumnLabels::BUTTON => self::MAX_PRIORITY,
            ColumnLabels::DISTANCE => self::MIN_PRIORITY,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function titleVariables(): array
    {
        return array_merge(parent::titleVariables(), [
            'dates' => $this->getTravelPeriod(),
        ]);
    }

    public function titleVariableLabels(): array
    {
        return array_merge(parent::titleVariableLabels(), [
            'dates' => Travelpayouts::__('Travel dates'),
        ]);
    }

    protected function getTravelPeriod(): string
    {
        return implode(' - ', [
            HumanDateFormatter::getInstance()->format($this->check_in, $this->locale),
            HumanDateFormatter::getInstance()->format($this->check_out, $this->locale),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getApiModel(): LocationApiModel
    {
        $model = parent::getApiModel();
        $model->scenario = LocationApiModel::SCENARIO_WITH_DATES_REQUIRED;
        $model->check_in = $this->checkInFormatted;
        $model->check_out = $this->checkOutFormatted;
        return $model;
    }

}
