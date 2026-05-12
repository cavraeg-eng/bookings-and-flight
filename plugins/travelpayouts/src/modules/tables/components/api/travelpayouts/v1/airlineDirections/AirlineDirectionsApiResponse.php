<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\airlineDirections;

use Travelpayouts\components\api\ApiResponseObject;

class AirlineDirectionsApiResponse extends ApiResponseObject
{
    public $index;
    public $origin;
    public $destination;
    public $popularity;

    public function setKey($value)
    {
        if (is_string($value) && preg_match('/^(\w{3})-(\w{3})$/', $value, $matches)) {
            [, $origin, $destination] = $matches;
            $this->origin = $origin;
            $this->destination = $destination;
        }
    }

    public function setValue($value)
    {
        if (is_int($value)) {
            $this->popularity = $value;
        }
    }

}
