<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\exceptions;

use Travelpayouts;

class InvalidConfigException extends TravelpayoutsException
{
    public function getName()
    {
        return Travelpayouts::_x('Invalid Configuration','exception');
    }
}
