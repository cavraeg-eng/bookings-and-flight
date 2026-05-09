<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\formatters;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\dictionary\Railways;
use Travelpayouts\traits\SingletonTrait;

class StationNameFormatter extends BaseInjectedObject
{
    use SingletonTrait;

    /**
     * @Inject
     * @var Railways
     */
    protected $dictionary;

    /**
     * Получаем название станции из id
     * @param $value
     * @return string|null
     */
    public function format($value): ?string
    {
        return is_string($value) || is_int($value) ? $this->dictionary->getItem($value)->getName() : null;
    }

}
