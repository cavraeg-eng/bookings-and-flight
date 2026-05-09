<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\formatters;

use Travelpayouts\components\BaseObject;
use Travelpayouts\components\dictionary\Airports;
use Travelpayouts\traits\SingletonTrait;

class AirportNameFormatter extends BaseObject
{
    use SingletonTrait;

    /**
     * @var Airports[]
     */
    protected $_dictionaries = [];

    public function format($value, string $locale)
    {
        $this->getItem($value, $locale);
        /** @var string| null $name */
        $name = $this->getDictionary($locale)->getItem($value)->name;
        return $name ?? $this->getDictionary('en')->getItem($value)->name;
    }

    public function getCityCode($value, string $locale)
    {
        /** @var string| null $name */
        $name = $this->getDictionary($locale)->getItem($value)->getCityCode();
        return $name ?? $this->getDictionary('en')->getItem($value)->getCityCode();
    }

    protected function getDictionary(string $locale): Airports
    {
        if (!isset($this->_dictionaries[$locale])) {
            $this->_dictionaries = array_merge($this->_dictionaries, [
                $locale => Airports::getInstance(['lang' => $locale]),
            ]);
        }

        return $this->_dictionaries[$locale];
    }
}
