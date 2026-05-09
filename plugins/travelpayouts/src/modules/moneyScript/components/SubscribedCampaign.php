<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\moneyScript\components;

use Travelpayouts\components\Model;

class SubscribedCampaign extends Model
{
    /**
     * @var string
     */
    public $campaign_id;
    /**
     * @var string
     */
    public $promo_id;
    /**
     * @var string
     */
    public $locale;
    /**
     * @var array
     */
    public $campaign_domains;
}
