<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\formatters;

use Travelpayouts;
use Travelpayouts\traits\SingletonTrait;

class StopsNameFormatter extends Travelpayouts\components\BaseObject
{
    use SingletonTrait;

    public function format($value, string $locale): ?string
    {
        if (is_string($value) || is_numeric($value)) {
            $value = (int)$value;

            if ($value < 0) {
                return null;
            }

            if ($value === 0) {
                return Travelpayouts::t('flights.stops.Direct', [], 'tables', $locale);
            }

            $label = $this->pluralizeString($value,
                Travelpayouts::t('flights.stops.nominative', [], 'tables', $locale),
                Travelpayouts::t('flights.stops.genitive', [], 'tables', $locale),
                Travelpayouts::t('flights.stops.prepositional', [], 'tables', $locale)
            );

            return "$value $label";
        }
        return null;
    }

    /**
     * Pluralize a string based on a given value.
     * @param int $value
     * @param string $nominative
     * @param string $genitive
     * @param string $prepositional
     * @return string
     */
    protected function pluralizeString(int $value, string $nominative, string $genitive, string $prepositional): string
    {
        if ($value >= 5 && $value <= 20) {
            return $prepositional;
        }
        $value %= 10;
        if ($value === 1) {
            return $nominative;
        }
        if ($value >= 2 && $value <= 4) {
            return $genitive;
        }
        return $prepositional;
    }

}
