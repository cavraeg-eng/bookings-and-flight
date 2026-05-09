<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\Vendor\Carbon\CarbonImmutable;
use Travelpayouts\Vendor\Carbon\CarbonInterface;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\api\ApiResponseObject;
use Travelpayouts\components\dictionary\Railways;

class Trip extends ApiResponseObject
{

    /**
     * @var string
     */
    public $arrivalStation;
    /**
     * @var string
     */
    public $arrivalTime;
    /**
     * @var Category[]
     */
    public $categories;
    /**
     * @var string
     */
    public $departureStation;
    /**
     * @var string
     */
    public $departureTime;
    /**
     * @var boolean
     */
    public $firm;
    /**
     * @var string|null
     */
    public $name;
    /**
     * @var string
     */
    public $numberForUrl;
    /**
     * @var string
     */
    public $runArrivalStation;
    /**
     * @var string
     */
    public $runDepartureStation;
    /**
     * @var string
     */
    public $trainNumber;
    /**
     * @var string
     */
    public $travelTimeInSeconds;

    /**
     * @var Station[]
     */
    protected $_stations;

    /**
     * @Inject
     * @var Railways
     */
    protected $dictionary;

    /**
     * Поезд стартует с начальной точки маршрута
     * @return bool
     */
    public function routeStartedAtFirstStation(): bool
    {
        return $this->runDepartureStation === $this->departureStation;
    }

    /**
     * Поезд отправляется в конечную точку маршрута
     * @return bool
     */
    public function routeEndedAtLastStation(): bool
    {
        return $this->runArrivalStation === $this->arrivalStation;
    }

    /**
     * Получаем станции на маршруте
     * @return Station[]
     */
    public function getStations(): array
    {
        if (!$this->_stations) {
            $stations = [];

            if ($this->routeStartedAtFirstStation()) {

                $stations = array_merge($stations, [
                    new Station([
                        'id' => $this->departureStation,
                        'type' => Station::STATION_TYPE_MAIN,
                    ]),
                ]);
            } else {
                $stations = array_merge($stations, [
                    new Station([
                        'id' => $this->runDepartureStation,
                        'type' => Station::STATION_TYPE_SECONDARY,
                    ]),
                    new Station([
                        'id' => $this->departureStation,
                        'type' => Station::STATION_TYPE_MAIN,
                    ]),
                ]);
            }

            if ($this->routeEndedAtLastStation()) {
                $stations = array_merge($stations, [
                    new Station([
                        'id' => $this->arrivalStation,
                        'type' => Station::STATION_TYPE_MAIN,
                    ]),
                ]);
            } else {
                $stations = array_merge($stations, [
                    new Station([
                        'id' => $this->arrivalStation,
                        'type' => Station::STATION_TYPE_MAIN,
                    ]),
                    new Station([
                        'id' => $this->runArrivalStation,
                        'type' => Station::STATION_TYPE_SECONDARY,
                    ]),
                ]);
            }
            $this->_stations = $stations;
        }
        return $this->_stations;
    }

    public function getDepartureDate(): CarbonImmutable
    {
        return Carbon::createFromTimeString($this->departureTime)->toImmutable();
    }

    public function getArrivalDate(): CarbonImmutable
    {
        return $this->getDepartureDate()->addSeconds((int)$this->travelTimeInSeconds)
            ->toImmutable();
    }
}
