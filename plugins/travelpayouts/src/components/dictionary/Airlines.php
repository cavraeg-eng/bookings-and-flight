<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary;

use Travelpayouts\components\dictionary\items\Airline;
use Travelpayouts\components\base\dictionary\EmptyItem;

/**
 * Class Airlines
 * @package Travelpayouts\components\dictionary
 * @method Airline|EmptyItem getItem(string $id)
 * @method static self getInstance(array $config)
 */
class Airlines extends TravelpayoutsApiData
{
    public $type = 'airlines';
    protected $itemClass = Airline::class;

}
