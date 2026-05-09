<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary\items;
/**
 * Class Country
 * @package Travelpayouts\src\components\dictionary\items
 * @property-read string $name
 * @property-read string $currency
 */
class Country extends TravelpayoutsApiItem
{

    public function getName($case = false)
    {
        if ($case) {
            $case_path = "cases.{$case}";
            if ($this->dataDot->has($case_path))
                return $this->dataDot->get($case_path);
        }
        return $this->dataDot->get('name', '');
    }

    public function get_name($case = false)
    {
        return $this->getName($case);
    }

    public function getCurrency(){
        return $this->dataDot->get('currency', '');
    }

    public function get_currency()
    {
        return $this->getCurrency();
    }
}
