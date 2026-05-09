<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesCalendar;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

/**
 * @method PricesCalendarApiResponse[] getResponseModels()
 */
class PricesCalendarApiModel extends BaseTravelpayoutsApiModel
{
    protected $responseClass = PricesCalendarApiResponse::class;
    const CALENDAR_TYPE_DEPARTURE = 'departure_date';
    const CALENDAR_TYPE_RETURN = 'return_date';

    public $currency = 'RUB';
    public $origin;
    public $destination;
    /**
     * @var string
     * Field used to build the calendar. Equal to either: departure_date or return_date
     */
    public $calendar_type = self::CALENDAR_TYPE_DEPARTURE;
    /**
     * @var string
     * Day or month of return (yyyy-mm-dd or yyyy-mm). Pay attention! If the return_date is not specified, you will get
     *     the "One way" flights.
     */
    public $return_date;

    /**
     * @return array|array[]
     * @see $calendar_type
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['currency', 'origin', 'destination'], 'required'],
            [['currency', 'origin', 'destination'], 'string', 'length' => 3],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'http://api.travelpayouts.com/v1/prices/calendar';
    }
}
