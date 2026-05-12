<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary;

use Travelpayouts\components\dictionary\items\City;
use Travelpayouts\components\base\dictionary\EmptyItem;

/**
 * Class Cities
 * @package Travelpayouts\components\dictionary
 * @method City|EmptyItem getItem(string $id)
 * @method static self getInstance(array $config)
 */
class Cities extends TravelpayoutsApiData
{
    public $type = 'cities';
    protected $itemClass = City::class;
}
