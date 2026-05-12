<?php

namespace Travelpayouts\modules\links\components\flights;

use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\modules\links\components\BaseLinkShortcode;

/**
 * Class LinkFlightsModel
 * @property-read boolean $oneWay
 */
class Shortcode extends BaseLinkShortcode
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
     * @var string|bool
     */
    public $one_way = false;

    public $new_tab = null;
    /**
     * @var string
     */
    public $origin_date = 1;
    /**
     * @var string
     */
    public $destination_date;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin', 'destination', 'origin_date'], 'required'],
            [['origin', 'destination'], 'string', 'length' => 3],
            [['origin_date'], 'number', 'min' => 0, 'max' => 30, 'integerOnly' => true],
            [['one_way'], 'in', 'range' => ['true', 'false', '1', '0']],
            [
                ['destination_date'],
                'number',
                'min' => 1,
                'max' => 30,
                'integerOnly' => true,
                'when' => function ($model) {
                    /** @var self $model */
                    return !$model->oneWay;
                },
            ],
            [
                ['destination_date'],
                'required',
                'when' => function ($model) {
                    /** @var self $model */
                    return !$model->oneWay;
                },
            ],
            [
                ['destination_date'],
                'compare',
                'compareAttribute' => 'origin_date',
                'type' => CompareValidator::TYPE_NUMBER,
                'operator' => '>',
                'when' => function ($model) {
                    /** @var self $model */
                    return !$model->oneWay;
                },
            ],
        ]);
    }

    /**
     * Формирования урл для авиа из параметров шорткода link
     * @return string
     */
    protected function getUrl(): string
    {
        $departDate = $this->date_time_add_days($this->origin_date);
        $returnDate = $this->destination_date ? $this->date_time_add_days($this->destination_date) : null;

        $marker = UrlHelper::get_marker(
            $this->accountModule->marker,
            $this->subid,
            self::LINK_MARKER
        );

        $flightSource = $this->settingsModule->data->get(
            LanguageHelper::optionWithLanguage('flights_source'),
            ReduxOptions::FLIGHTS_SOURCE_AVIASALES_RU
        );

        $params = array_filter([
            'origin_iata' => $this->origin,
            'destination_iata' => $this->destination,
            'currency' => $this->settingsModule->currency,
            'locale' => $this->settingsModule->language,
            'depart_date' => $departDate,
            'return_date' => $returnDate,
            'with_request' => $this->settingsModule->data->get('flights_after_url') === 'results'
                ? 'true' : null,
            'one_way' => $this->oneWay ? 'true' : null,
        ]);

        $rawHost = !empty($this->accountModule->whiteLabelFlights)
            ? $this->accountModule->whiteLabelFlights
            : $this->getDefaultHost($flightSource);

        return UrlHelper::buildMediaUrl([
            'p' => UrlHelper::LINKS_P,
            'marker' => $marker,
            'u' => UrlHelper::buildUrl($rawHost, $params),
        ]);
    }

    /**
     * @param $sourceCode
     * @return string|null
     */
    private function getDefaultHost($sourceCode)
    {
        $flightsSources = ReduxOptions::flight_sources();

        return $flightsSources[$sourceCode] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return Travelpayouts::__('Search for flights');
    }

    /**
     * @inheritDoc
     */
    public function gutenbergFields(): array
    {
        return [
            'text_link',
            'origin' => $this->fieldDirectionAutocomplete(),
            'destination' => $this->fieldDirectionAutocomplete(),
            'hr',
            'one_way' => $this->fieldRadioFields()
                ->addOption('false', Travelpayouts::__('Roundtrip'), $this->resolveGutenbergFieldList([
                    'origin_date' => $this->fieldInputNumber()
                        ->setLabel(Travelpayouts::__('Departure: today +'))
                        ->setDefault(1)
                        ->setMaximum(0)
                        ->setMaximum(30),
                    'destination_date' => $this->fieldInputNumber()
                        ->setLabel(Travelpayouts::__('Return: today +'))
                        ->setDefault(12)
                        ->setMaximum(1)
                        ->setMaximum(30),

                ]))
                ->addOption('true', Travelpayouts::__('One way'), $this->resolveGutenbergFieldList([
                    'origin_date' => $this->fieldInputNumber()
                        ->setLabel(Travelpayouts::__('Departure: today +'))
                        ->setDefault(1)
                        ->setMaximum(0)
                        ->setMaximum(30),
                ]))
                ->setDefault('false'),
            'hr',
            'new_tab',
            'subid',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return [
            'tp_link_flights',
        ];
    }

    public function before_validate()
    {
        if ($this->oneWay) {
            $this->destination_date = null;
        } elseif (!$this->destination_date) {
            $this->destination_date = 12;

        }
        return true;
    }

    /**
     * @return boolean
     */
    public function getOneWay()
    {
        return filter_var(
            $this->one_way,
            FILTER_VALIDATE_BOOLEAN
        );
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'text_link' => Travelpayouts::__('Link text'),
            'origin' => Travelpayouts::__('Origin'),
            'destination' => Travelpayouts::__('Destination'),
            'one_way' => Travelpayouts::__('Type')
        ]);
    }
}
