<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\flightSchedule;

use Travelpayouts\components\api\ApiResponseObject;
use Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response\Flight;
use Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response\FlightDetail;
use Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response\FlightDirect;
use Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response\FlightStop;

/**
 * @property-read  string| null $airline_logo
 * @property-read  string| null $airline_code
 * @property-read  string| null $airline_name
 * @property-read  int| null $flight_number
 * @property FlightDirect|Flight $responseModel
 */
class FlightScheduleResponse extends FlightDirect
{
    /**
     * @var Table
     */
    public $shortcodeModel;

    /**
     * @var FlightDetail[]
     */
    public $details = [];

    /**
     * @var FlightStop[]
     */
    public $stops = [];
    /**
     * @var FlightDetail
     */
    protected $_detail;

    /**
     * @var FlightDirect|Flight
     */
    protected $_responseModel;

    /**
     * @return Flight|FlightDirect
     */
    public function getResponseModel()
    {
        return $this->_responseModel;
    }

    /**
     * @param Flight|FlightDirect $responseModel
     */
    public function setResponseModel(ApiResponseObject $responseModel): void
    {
        self::configure($this, get_object_vars($responseModel));

        $this->_responseModel = $responseModel;
    }

    public function getAirlineCode(): ?string
    {
        return $this->airline_code
            ?? $this->getFlightDetail()->airline_code
            ?? null;
    }

    public function getAirlineName(): ?string
    {
        return $this->airline_name
            ?? $this->getFlightDetail()->airline_name
            ?? null;
    }

    public function getAirlineLogo(): ?string
    {
        return $this->airline_logo
            ?? $this->getFlightDetail()->airline_logo
            ?? null;
    }

    public function getFlightNumber(): ?int
    {
        return $this->flight_number
            ?? $this->getFlightDetail()->flight_number
            ?? null;
    }

    public function getFullFlightNumber()
    {
        return $this->getAirlineCode() . ' ' . $this->getFlightNumber();
    }

    public function getRoute(): string
    {
        return $this->origin_iata . ' &#8212; ' . $this->destination_iata;
    }

    /**
     * @return null|FlightDetail
     */
    protected function getFlightDetail(): ?FlightDetail
    {
        if (!$this->_detail && $this->responseModel instanceof Flight) {
            $details = $this->details;
            if (count($details)) {
                [$firstDetail] = $details;
                $this->_detail = $firstDetail;
            }
        }
        return $this->_detail;
    }

}
