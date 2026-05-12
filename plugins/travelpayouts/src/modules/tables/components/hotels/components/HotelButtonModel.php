<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\components;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\grid\ButtonModel;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\components\settings\HotelSettingsSection;

/**
 * @property string|Carbon|null $checkInDate
 * @property string|Carbon|null $checkOutDate
 */
class HotelButtonModel extends ButtonModel
{
    /**
     * @var string
     */
    public $withoutDates;

    /**
     * @var integer|mixed
     */
    public $hotelId;
    /**
     * @var string
     */
    public $cityId;
    /**
     * @var string
     */
    public $currency;
    /**
     * @Inject
     * @var HotelSettingsSection
     */
    protected $hotelSettings;
    /**
     * @var string
     */
    public $locale;
    /**
     * @var Carbon|null
     */
    protected $_checkInDate;

    /**
     * @var Carbon|null
     */
    protected $_checkOutDate;

    /**
     * @var bool
     */
    protected $useHotelId = false;

    /**
     * @var bool
     */
    protected $useBooking = false;
    /**
     * @var string
     */
    protected $host;

    protected $apiMarker;

    public function init()
    {
        parent::init();
        if ($this->globalSettings->hotels_after_url === 'hotel') {
            $this->useHotelId = true;
        }

        if (StringHelper::toBoolean($this->hotelSettings->use_booking_com && empty($this->account->hotels_domain))) {
            $this->useBooking = true;
        }

        $hotelsDomain = $this->accountSettings->hotels_domain;
        if (empty($hotelsDomain)) {
            $this->locale = $this->globalSettings->hotels_source;
        } else {
            $this->host = $hotelsDomain;
        }
    }

    /**
     * @return Carbon|null
     */
    public function getCheckInDate(): ?Carbon
    {
        return $this->_checkInDate;
    }

    /**
     * @param string|mixed $value
     */
    public function setCheckInDate($value): void
    {
        $this->_checkInDate = $this->parseDate($value);
    }

    /**
     * @return Carbon|null
     */
    public function getCheckOutDate(): ?Carbon
    {
        return $this->_checkOutDate;
    }

    /**
     * @param string|mixed $value
     */
    public function setCheckOutDate($value): void
    {
        $this->_checkOutDate = $this->parseDate($value);
    }

    protected function parseDate($value): ?Carbon
    {
        return is_string($value) ? Carbon::parse($value) : null;
    }

    /**
     * @return array
     */
    protected function getBookingParams(): array
    {
        return [
            'gateId' => 2,
            'selectedHotelId' => $this->getHotelId(),
            'locationId' => $this->cityId,
            'language' => str_replace('-', '_', $this->locale),
            'currency' => $this->currency,
            'adults' => 2,
            'children' => 0,
            'skipRulerCheck' => 'skip',
            'flags' => ['utm' => 'tp_wp_plugin'],
            'utm_source' => 'tp_wp_plugin',
            'utm_medium' => 'table',
            'utm_campaign' => StringHelper::toBoolean($this->withoutDates)
                ? 'selection'
                : 'selection with dates',
        ];
    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        return [
            'locationId' => $this->cityId,
            'hotelId' => $this->getHotelId(),
            'locale' => $this->locale,
            'currency' => $this->currency,
        ];
    }

    /**
     * @return array
     */
    protected function getCheckDateParams(): array
    {
        $checkInDate = $this->getCheckInDate();
        $checkOutDate = $this->getCheckOutDate();
        return [
            'checkIn' => $checkInDate
                ? $checkInDate->format('Y-m-d')
                : null,
            'checkOut' => $checkOutDate
                ? $checkOutDate->format('Y-m-d')
                : null,
        ];
    }

    public function getUrlParams()
    {
        $params = array_merge(
            $this->useBooking
                ? $this->getBookingParams()
                : $this->getParams(), [
            'trs' => $this->platform,
        ]);

        if (!$this->withoutDates) {
            $params = array_merge($params, $this->getCheckDateParams());
        }

        return $params;
    }

    /**
     * @return int|mixed|null
     */
    protected function getHotelId()
    {
        return $this->useHotelId ? $this->hotelId : null;
    }

    public function getHost(): string
    {
        if (!$this->host) {
            return $this->useBooking
                ? 'https://yasen.hotellook.com/adaptors/location_deeplink'
                : 'https://search.hotellook.com';
        }

        return $this->host;
    }

    public function getUrl(): string
    {
        return UrlHelper::buildMediaUrl([
            'p' => UrlHelper::HOTELS_P,
            'marker' => $this->getMarker(),
            'u' => UrlHelper::buildUrl($this->getHost(), $this->getUrlParams()),
        ]);
    }

}
