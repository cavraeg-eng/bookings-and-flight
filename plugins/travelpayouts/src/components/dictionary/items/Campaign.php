<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary\items;

use Travelpayouts\components\base\dictionary\Item;

/**
 * Class Campaign
 * @package Travelpayouts\components\dictionary\items
 * @property-read string $id
 * @property-read string $name
 * @property-read array $domain
 * @property-read string $promoId
 * @property-read string $redirectType
 */
class Campaign extends Item
{
    /**
     * @return string
     */
    public function get_id()
    {
        return $this->dataDot->get('campaign_id');

    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->dataDot->get('campaign_name');
    }

    /**
     * @return array
     */
    public function get_domain()
    {
        return $this->dataDot->get('campaign_domain', []);
    }

    /**
     * @return string
     */
    public function get_promoId()
    {
        return $this->dataDot->get('campaign_promo_id');
    }

    /**
     * @return string
     */
    public function get_redirectType()
    {
        return $this->dataDot->get('campaign_redirect_link_type');
    }

}
