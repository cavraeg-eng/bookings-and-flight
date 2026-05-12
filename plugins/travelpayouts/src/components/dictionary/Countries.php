<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary;

use Travelpayouts\components\dictionary\items\Country;
use Travelpayouts\components\base\dictionary\EmptyItem;

/**
 * Class Countries
 * @package Travelpayouts\components\dictionary
 * @method Country|EmptyItem getItem(string $id)
 * @method static self getInstance(array $config)
 */
class Countries extends TravelpayoutsApiData
{
    public $type = 'countries';
    protected $itemClass = Country::class;
}
