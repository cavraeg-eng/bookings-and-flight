<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\snowplow;

class SyncEmitter extends \Travelpayouts\Vendor\Snowplow\Tracker\Emitters\SyncEmitter
{
    /**
     * @var bool
     */
    public $debug = false;
    /**
     * @var array
     */
    public $requests_results = [];
}