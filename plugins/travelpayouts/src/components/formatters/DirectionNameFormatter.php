<?php

namespace Travelpayouts\components\formatters;

use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\dictionary\Cities;
use Travelpayouts\traits\SingletonTrait;

class DirectionNameFormatter extends BaseInjectedObject
{
    use SingletonTrait;

    /**
     * @var Cities[]
     */
    protected $_iataDictionaries = [];

    public function getName($value, string $locale): ?string
    {
        if ($value) {
            return $this->getIataDictionary($locale)->getItem($value)->getCaseNominative();
        }
        return null;
    }

    /**
     * @param string $locale
     * @return Cities
     */
    protected function getIataDictionary(string $locale): Cities
    {
        if (empty($this->_iataDictionaries) || !$this->_iataDictionaries[$locale]) {
            $this->_iataDictionaries = array_merge($this->_iataDictionaries, [
                $locale => Cities::getInstance(['lang' => $locale]),
            ]);
        }
        return $this->_iataDictionaries[$locale];
    }
}
