<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response;

use Travelpayouts;
use Travelpayouts\components\api\ApiResponseObject;

class Category extends ApiResponseObject implements ITrainCategory
{
    public const TYPE_PLAZCARD = 'plazcard';
    public const TYPE_COUPE = 'coupe';
    public const TYPE_SEDENTARY = 'sedentary';
    public const TYPE_LUX = 'lux';
    public const TYPE_SOFT = 'soft';
    public const TYPE_COMMON = 'common';

    /**
     * @var float|null
     */
    public $price;
    /**
     * @var string
     */
    public $type;

    protected function wagonLabels(): array
    {
        return [
            self::TYPE_PLAZCARD => Travelpayouts::_x('Plazcard', 'railway wagon type'),
            self::TYPE_COUPE => Travelpayouts::_x('Coupe', 'railway wagon type'),
            self::TYPE_SEDENTARY => Travelpayouts::_x('Sedentary', 'railway wagon type'),
            self::TYPE_LUX => Travelpayouts::_x('Lux', 'railway wagon type'),
            self::TYPE_SOFT => Travelpayouts::_x('Soft', 'railway wagon type'),
            self::TYPE_COMMON => Travelpayouts::_x('Common', 'railway wagon type'),
        ];
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        $labels = $this->wagonLabels();
        return $labels[$this->type] ?? '-';
    }

    public function getValue(): ?float
    {
        return $this->price;
    }

}
