<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary;

use Travelpayouts\components\dictionary\items\Airport;
use Travelpayouts\components\base\dictionary\EmptyItem;

/**
 * Class Airports
 * @package Travelpayouts\components\dictionary
 * @method Airport|EmptyItem getItem(string $id)
 * @method static self getInstance(array $config)
 */
class Airports extends TravelpayoutsApiData
{
    public $type = 'airports';
    protected $itemClass = Airport::class;
}
