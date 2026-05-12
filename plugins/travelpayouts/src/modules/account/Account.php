<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\account;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\module\ModuleRedux;

/**
 * Class Account
 * @property-read Dot $data
 * @property-read string|null $token
 * @property-read string|null $marker
 * @property-read string|null $whiteLabelFlights
 * @property-read string|null $whiteLabelHotels
 */
class Account extends ModuleRedux
{
    /**
     * @Inject
     * @var AccountForm
     */
    public $section;

    public function registerSection()
    {
        $this->section->register();
    }

    /**
     * @return Dot
     */
    public function getData()
    {
        return $this->section->data;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->data->get('api_token');
    }

    /**
     * @return string|null
     */
    public function getMarker()
    {
        return $this->data->get('api_marker');
    }

    /**
     * @return string|null
     */
    public function getPlatform()
    {
        return $this->data->get('platform');
    }

    /**
     * @return string|null
     */
    public function getWhiteLabelFlights()
    {
        return $this->data->get('flights_domain');
    }

    /**
     * @return string|null
     */
    public function getWhiteLabelHotels()
    {
        return $this->data->get('hotels_domain');
    }
}
