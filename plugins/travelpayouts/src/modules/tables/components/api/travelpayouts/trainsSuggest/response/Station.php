<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response;

use Travelpayouts\components\api\ApiResponseObject;
use Travelpayouts\components\formatters\StationNameFormatter;

class Station extends ApiResponseObject
{
    public const STATION_TYPE_MAIN = 0;
    public const STATION_TYPE_SECONDARY = 1;

    public $id;
    public $type;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return StationNameFormatter::getInstance()->format($this->id);
    }
}
