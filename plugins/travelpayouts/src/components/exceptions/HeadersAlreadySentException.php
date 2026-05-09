<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\exceptions;

class HeadersAlreadySentException extends TravelpayoutsException
{
    public function __construct($file, $line)
    {
        $message = TRAVELPAYOUTS_DEBUG ? "Headers already sent in {$file} on line {$line}." : 'Headers already sent.';
        parent::__construct($message);
    }
}
