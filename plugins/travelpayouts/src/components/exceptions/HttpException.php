<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\exceptions;

use Travelpayouts;
use Travelpayouts\components\web\Response;

class HttpException extends TravelpayoutsException
{
    public $statusCode;

    public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $status;
        parent::__construct($message, $code, $previous);
    }

    public function getName()
    {
        if (isset(Response::$httpStatuses[$this->statusCode])) {
            return Response::$httpStatuses[$this->statusCode];
        }

        return Travelpayouts::_x('Error', 'exception');
    }
}
