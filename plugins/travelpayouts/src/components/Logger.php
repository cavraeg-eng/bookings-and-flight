<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;
use Travelpayouts\Vendor\Psr\Log\AbstractLogger;
use Travelpayouts\traits\SingletonTrait;

class Logger extends AbstractLogger
{
    use SingletonTrait;

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        //
    }
}