<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\exceptions;

use Travelpayouts;

class TravelpayoutsException extends \Exception
{
    public function getName()
    {
        return Travelpayouts::__('Error');
    }
}
