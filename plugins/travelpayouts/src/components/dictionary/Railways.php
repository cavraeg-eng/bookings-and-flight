<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary;

use Travelpayouts;
use Travelpayouts\components\dictionary\items\Railway;
use Travelpayouts\components\base\dictionary\EmptyItem;
use Travelpayouts\components\base\dictionary\Dictionary;

/**
 * Class Railways
 * @package Travelpayouts\components\dictionary
 * @method Railway|EmptyItem getItem(string $stationId)
 * @method static self getInstance()
 */
class Railways extends Dictionary
{
    public $type = 'railways';
    protected $itemClass = Railway::class;
    public $_pk = 'number';

    protected function fetchData()
    {
        $filePath = Travelpayouts::getAlias('@data/railways.json');
        if (file_exists($filePath)) {
            $file = file_get_contents($filePath);
            return $this->parseJson($file);
        }
    }
}
