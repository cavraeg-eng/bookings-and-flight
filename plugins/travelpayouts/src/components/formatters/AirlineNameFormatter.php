<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\formatters;

use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\dictionary\Airlines;
use Travelpayouts\traits\SingletonTrait;

class AirlineNameFormatter extends BaseInjectedObject
{
    use SingletonTrait;

    /**
     * @var Airlines[]
     */
    protected $_dictionaries = [];

    public function getAirlineName($value, string $locale): ?string
    {
        /** @var string| null $name */
        $name = $this->getAirlineDictionary($locale)->getItem($value)->name;
        return $name ?? $this->getAirlineDictionary('en')->getItem($value)->name;
    }

    protected function getAirlineDictionary(string $locale): Airlines
    {
        if (!isset($this->_dictionaries[$locale])) {
            $this->_dictionaries[$locale] = Airlines::getInstance(['lang' => $locale]);
        }

        return $this->_dictionaries[$locale];
    }
}
