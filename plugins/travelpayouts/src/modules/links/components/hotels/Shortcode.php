<?php

namespace Travelpayouts\modules\links\components\hotels;

use Travelpayouts;
use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\components\rest\fields\Autocomplete;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\components\validators\CompareValidator;
use Travelpayouts\modules\links\components\BaseLinkShortcode;

/**
 * Class Shortcode
 */
class Shortcode extends BaseLinkShortcode
{
    /**
     * @var string
     */
    public $hotel_id;
    /**
     * @var string
     */
    public $city_id;
    /**
     * @var string
     */
    public $check_in = 1;
    /**
     * @var string
     */
    public $check_out = 12;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                ['hotel_id'],
                'required',
                'when' => function ($model) {
                    return empty($model->city_id);
                },
            ],
            [
                ['city_id'],
                'required',
                'when' => function ($model) {
                    return empty($model->hotel_id);
                },
            ],
            [['check_in'], 'number', 'min' => 0, 'max' => 30, 'integerOnly' => true],
            [['check_out'], 'number', 'min' => 1, 'max' => 30, 'integerOnly' => true],
            [
                ['check_out'],
                'compare',
                'compareAttribute' => 'check_in',
                'type' => CompareValidator::TYPE_NUMBER,
                'operator' => '>',
            ],
        ]);
    }

    /**
     * Формирования урл для отелей из параметров шорткода link
     * @return string
     */
    protected function getUrl(): string
    {
        $marker = UrlHelper::get_marker(
            $this->accountModule->marker,
            $this->subid,
            self::LINK_MARKER
        );

        $params = array_filter(array_merge($this->hotelData(), [
            'checkIn' => $this->date_time_add_days($this->check_in),
            'checkOut' => $this->date_time_add_days($this->check_out),
            'locale' => $this->settingsModule->language,
            'currency' => $this->settingsModule->currency,
        ]));

        $rawHost = !empty($this->accountModule->whiteLabelHotels)
            ? $this->accountModule->whiteLabelHotels
            : 'https://search.hotellook.com';

        return UrlHelper::buildMediaUrl([
            'p' => UrlHelper::LINKS_P,
            'marker' => $marker,
            'u' => UrlHelper::buildUrl($rawHost, $params),
        ]);
    }

    /**
     * Убирает "locationId=" из поля hotel_id оставляя только id, значения старого плагина приходят
     * в виде locationId=123, достаточно просто 123
     * @return mixed
     */
    private function clearHotelId()
    {
        return str_replace(['locationId=', 'hotelId='], '', $this->hotel_id);
    }

    private function location()
    {
        if (preg_match('/^locationId.*/', $this->hotel_id)) {
            return 'locationId';
        }

        return 'hotelId';
    }

    private function hotelData()
    {
        if (!empty($this->city_id)) {
            return ['locationId' => $this->city_id];
        }

        return [$this->location() => $this->clearHotelId()];
    }

    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return Travelpayouts::__('Search for hotels');
    }

    public function gutenbergFields(): array
    {
        return [
            'text_link',
            'result_type' => $this->fieldRadioFields()
                ->setLabel(Travelpayouts::__('Type'))
                ->addOption('hotels', Travelpayouts::__('Hotel'), $this->resolveGutenbergFieldList([
                    'hotel_id' => $this->fieldHotelAutocomplete(),
                ]))
                ->addOption('city', Travelpayouts::__('City'), $this->resolveGutenbergFieldList([
                    'city_id' => $this->fieldCityAutocomplete(),
                ]))
                ->setDefault('hotels'),
            'hr',
            'check_in' => $this->fieldInputNumber()
                ->setMinimum(0)
                ->setMaximum(30),
            'check_out' => $this->fieldInputNumber()
                ->setMinimum(1)
                ->setMaximum(30),
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
            'tp_link_hotels',
        ];
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'hotel_id' => Travelpayouts::__('Hotel'),
            'city_id' => Travelpayouts::__('City'),
            'check_out' => Travelpayouts::__('Check-out: today +'),
            'check_in' => Travelpayouts::__('Check-in: today +'),
            'type' => Travelpayouts::__('Type'),
        ]);
    }

    protected function fieldHotelAutocomplete(): Autocomplete
    {
        return $this->fieldInputAutocomplete()->setAsync([
            'url' => $this->prepareEndpoint('//suggest.travelpayouts.com/search?service=internal_blissey_generator_ac&term=${term}&locale=${locale}&type=hotel'),
            'itemProps' => [
                'value' => '${id}',
                'label' => '${fullName}',
            ],
        ])->setAllowClear(true);
    }

    protected function fieldCityAutocomplete(): Autocomplete
    {
        return $this->fieldInputAutocomplete()->setAsync([
            'url' => $this->prepareEndpoint('//suggest.travelpayouts.com/search?service=internal_blissey_generator_ac&term=${term}&locale=${locale}&type=city'),
            'itemProps' => [
                'value' => '${id}',
                'label' => '${cityName}, ${countryName} (${hotelsCount})',
            ],
        ])->setAllowClear(true);
    }

    public function render()
    {
        return BrandSubscriptionService::isHotelLookSubscribed() ?
            parent::render()
            : $this->createAnchorTag('#');
    }

}
