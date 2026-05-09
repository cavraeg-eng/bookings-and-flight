<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components;

use Travelpayouts\components\grid\ButtonModel;
use Travelpayouts\components\tables\enrichment\UrlHelper;

class RailwayButtonModel extends ButtonModel
{
    /**
     * @var
     */
    public $origin;
    /**
     * @var
     */
    public $destination;

    public function getUrl(): string
    {
        $customUrlParams = [
            'nnst1' => $this->origin,
            'nnst2' => $this->destination,
        ];

        $params = [
            'shmarker' => $this->getMarker(),
            'promo_id' => UrlHelper::TUTU_PROMO_ID,
            'source_type' => 'customlink',
            'type' => 'click',
            'custom_url' => UrlHelper::buildUrl(UrlHelper::TUTU_CUSTOM_URL_HOST, $customUrlParams),
        ];

        return UrlHelper::buildUrl(UrlHelper::TUTU_URL_HOST, $params);
    }
}
