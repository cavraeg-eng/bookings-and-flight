<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\fromOurCityFly;

use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest\PriceLatestApiResponse;

class FromOurCityFlyResponse extends PriceLatestApiResponse
{
    /**
     * @var Table
     */
    public $shortcodeModel;

    public function buttonVariables(): array
    {
        return [
            'price' => function () {
                return PriceFormatter::getInstance()->format($this->value, $this->shortcodeModel->currency);
            },
        ];
    }
}
