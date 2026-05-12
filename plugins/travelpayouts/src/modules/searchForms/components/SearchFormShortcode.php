<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\components;

use Exception;
use Travelpayouts;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\components\Model;
use Travelpayouts\components\rest\fields\Autocomplete;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\modules\account\Account;
use Travelpayouts\modules\searchForms\models\SearchFormModel;
use Travelpayouts\modules\searchForms\models\WidgetCode;
use Travelpayouts\modules\searchForms\models\widgetCode\Direction;
use Travelpayouts\modules\searchForms\models\widgetCode\HotelCity;
use Travelpayouts\modules\searchForms\SearchFormSection;
use Travelpayouts\modules\widgets\components\WidgetShortcode;

/**
 * Class SearchShortcodes
 * @package Travelpayouts\src\modules\searchForms\components
 * @property HotelCity|null $hotel_city
 * @property Direction|null $origin
 * @property Direction|null $destination
 * @property-read SearchFormModel|null $model
 * @property-read string|null $marker
 * @property-read WidgetCode|null $widgetCode
 */
class SearchFormShortcode extends ShortcodeModel
{
    const TYPE_AVIA = 'avia';
    const TYPE_HOTEL = 'hotel';
    const TYPE_AVIA_HOTEL = 'avia_hotel';
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $slug;
    /**
     * @var string
     */
    public $subid;
    /**
     * @var bool
     */
    public $applyParamsFromCode;

    /**
     * @var HotelCity|null
     */
    protected $_hotel_city;
    /**
     * @var string|null
     */
    protected $_marker;

    /**
     * @var Direction|null
     */
    protected $_origin;
    /**
     * @var Direction|null
     */
    protected $_destination;
    /**
     * @return Direction|null
     * /**
     * @Inject
     * @var Account
     */
    protected $accountModule;
    /**
     * @var SearchFormModel|null
     */
    protected $_model;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                ['id'],
                'idOrSlugValidator',
                'params' => [
                    'attributeList' => [
                        'id',
                        'slug',
                    ],
                ],
                'except' => [self::SCENARIO_GENERATE_SHORTCODE],
                'skipOnEmpty' => false,
            ],
            [
                [
                    'slug',
                    'subid',
                    'applyparamsfromcode',
                ],
                'string',
            ],
            [
                ['applyParamsFromCode'],
                'boolean',
                'on' => [self::SCENARIO_GENERATE_SHORTCODE],
            ],
            [
                [
                    'model',
                    'origin',
                    'destination',
                    'hotel_city',
                    'widgetCode',
                ],
                'modelValidator',
            ],
            [
                ['id'],
                'required',
                'on' => [self::SCENARIO_GENERATE_SHORTCODE],
            ],
        ]);
    }

    public function idOrSlugValidator($attribute, $params)
    {
        if (isset($params['attributeList'])) {
            $hasValidAttributes = array_map(function ($key) {
                return (bool)$this->$key;
            }, $params['attributeList']);

            $isValid = in_array(true, $hasValidAttributes, true);

            if (!$isValid) {
                $attributesAsString = implode(', ', $params['attributeList']);
                $this->add_error($attribute, Travelpayouts::__('One of parameters ({attributes}) is required', [
                    'attributes' => $attributesAsString,
                ]));
            } else {
                $model = $this->id ? SearchFormModel::getInstance()->findByPk($this->id) :
                    SearchFormModel::getInstance()->findByColumnValue('slug', $this->slug);
                if (!$model) {
                    $this->add_error('id', Travelpayouts::_x('Can\'t find search form model by id "{formId}"', 'searchform.model.exception', [
                        'formId' => $this->id ?? $this->slug,
                    ]));
                }
            }
        }
    }

    /**
     * @return HotelCity|null
     */
    public function getHotel_city()
    {
        return $this->_hotel_city;
    }

    public function setHotel_city($value)
    {
        if (is_string($value)) {
            $this->_hotel_city = HotelCity::createFromString($value);
        }
    }

    /**
     * @return SearchFormModel|null
     * @throws Exception
     */
    public function getModel()
    {
        if (!$this->_model && ($this->id || $this->slug)) {
            $this->_model = $this->id ?
                SearchFormModel::getInstance()->findByPk($this->id) :
                SearchFormModel::getInstance()->findByColumnValue('slug', $this->slug);
        }
        return $this->_model;
    }

    public function render()
    {
        try {
            return $this->validate() ? $this->mergeAttributesWithWidgetCode()->renderWidget() : $this->renderErrors();
        } catch (Exception $exception) {
            return '';
        }
    }

    public function before_validate()
    {
        /**
         * Если аттрибут applyParamsFromCode представлен, то перезаписываем параметр в модели
         */
        if ($this->applyParamsFromCode !== null) {
            $this->getModel()->applyParams = $this->applyParamsFromCode;
        }
        return parent::before_validate();
    }

    protected function mergeAttributesWithWidgetCode()
    {
        $widgetCode = $this->widgetCode;
        if ($this->widgetCode) {
            switch ($this->getModel()->type) {
                case self::TYPE_AVIA:
                    $widgetCode->hotel = null;
                    break;
                case self::TYPE_HOTEL:
                    $widgetCode->origin = null;
                    $widgetCode->destination = null;
                    break;
            }

            if ($this->hotel_city) {
                $widgetCode->hotel = $this->hotel_city;
            }

            if ($this->origin) {
                $widgetCode->origin = $this->origin;
            }

            if ($this->destination) {
                $widgetCode->destination = $this->destination;
            }

            if ($marker = $this->marker) {
                $widgetCode->marker = $marker;
                if ($widgetCode->best_offer) {
                    $widgetCode->best_offer->marker = $marker;
                }
            }
        }
        return $this;
    }

    public function getMarker()
    {
        if (!$this->_marker) {
            $subIdParams = array_filter([
                $this->accountModule->marker,
                $this->subid,
            ]);

            $this->_marker = implode('.', $subIdParams);
        }
        return $this->_marker;
    }

    protected function renderWidget()
    {
        if ($this->widgetCode) {
            $widgetId = $this->model->widgetId;
            $widgetParams = json_encode($this->widgetCode->toArray());
            $scriptParams = <<<JS
window.TP_FORM_SETTINGS = window.TP_FORM_SETTINGS || {};
window.TP_FORM_SETTINGS["$widgetId"] = $widgetParams;
JS;
            return implode('', [
                Html::script($scriptParams),
                Html::scriptFile($this->getModel()->url, ['async' => 'async']),
            ]);
        }

        // Для новых форм возвращаем только скрипт
        if ($this->model->code_form) {
            $widgetShortcode = new WidgetShortcode([
                'content' => $this->model->code_form,
            ]);
            return $widgetShortcode->render();
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_search_shortcodes'];
    }

    /**
     * @inheritDoc
     */
    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $model = new self();
        $model->attributes = $attributes;
        $model->tag = $tag;
        return $model->render();
    }

    /**
     * @param $value
     */
    public function setOrigin($value)
    {
        if (is_string($value)) {
            $this->_origin = new Direction(['iata' => $value]);
        }
    }

    /**
     * @return Direction|null
     */
    public function getOrigin()
    {
        return $this->_origin;
    }

    /**
     * @param $value
     */
    public function setDestination($value)
    {
        if (is_string($value)) {
            $this->_destination = new Direction(['iata' => $value]);
        }
    }

    /**
     * @return Direction|null
     */
    public function getDestination()
    {
        return $this->_destination;
    }

    public function modelValidator($attribute)
    {
        /** @var Model|null $model */
        $model = $this->$attribute;
        if ($model) {
            $model->validate();
            foreach ($model->getErrors() as $modelAttribute => $errorList) {
                foreach ($errorList as $error) {
                    $this->add_error($attribute, $error);
                }
            }
        }
    }

    /**
     * @return WidgetCode|null
     */
    public function getWidgetCode()
    {
        $model = $this->getModel();
        return $model->widgetCode ?? null;
    }

    public function setApplyparamsfromcode($value)
    {
        $this->applyParamsFromCode = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return Travelpayouts::__('Flights search form');
    }

    /**
     * @inheritDoc
     */
    public function gutenbergExtraData(): array
    {
        return [
            'image' => Travelpayouts::getAlias('@webImages/rest/search_form.png'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        $model = new SearchFormModel();
        $options = $model->selectData($model->getFlightsForms());

        return [
            'id' => $this->fieldSelect()
                ->setLabel(Travelpayouts::__('Select search form'))
                ->setOptions($options)
                ->required(),
            'origin',
            'destination',
            'applyParamsFromCode',
            'subid',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        if ($this->scenario === self::SCENARIO_GENERATE_SHORTCODE) {
            return [
                'id',
                'applyparamsfromcode' => 'applyParamsFromCode',
                'origin' => function () {
                    return $this->origin ? $this->origin->iata : null;
                },
                'destination' => function () {
                    return $this->destination ? $this->destination->iata : null;
                },
                'hotel_city' => function () {
                    return $this->hotel_city ? $this->hotel_city->query : null;
                },
                'type' => function () {
                    return $this->widgetCode ? $this->widgetCode->form_type : null;
                },
                'subid',
            ];
        }

        return $fields;
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'origin' => Travelpayouts::__('Origin'),
            'destination' => Travelpayouts::__('Destination'),
            'subid' => Travelpayouts::__('Sub ID'),
            'hotel_city' => Travelpayouts::__('City or hotel'),
            'applyParamsFromCode' => Travelpayouts::__('Apply settings (origin, destination, etc.) from generated widget code '),
        ]);
    }

    public static function isActive(): bool
    {
        return SearchFormSection::isActive();
    }

    protected function predefinedGutenbergFields(): array
    {
        return array_merge(parent::predefinedGutenbergFields(), [
            'origin' => $this->fieldDirectionAutocomplete(),
            'destination' => $this->fieldDirectionAutocomplete(),
            'applyParamsFromCode' => $this->fieldCheckbox()->setDefault(true),
            'subid' => $this->fieldInput(),
            'hotel_city' => $this->fieldHotelCityAutocomplete(),
        ]);
    }

    public function fieldHotelCityAutocomplete(): Autocomplete
    {
        return $this->fieldInputAutocomplete()->setAsync([
            'url' => admin_url('admin-ajax.php' . '?action=travelpayouts_routes&page=hotellook/hotels-cities/autocomplete&term=${term}'),
            'optionsPath' => 'data',
            'itemProps' => [
                'value' => '${value}',
                'label' => '${label}',
            ],
        ])->setAllowClear(true);
    }

}
