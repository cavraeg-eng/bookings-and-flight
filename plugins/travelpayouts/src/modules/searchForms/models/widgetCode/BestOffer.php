<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models\widgetCode;

use Travelpayouts\components\InjectedModel;

class BestOffer extends InjectedModel
{
    /**
     * @var string
     */
    public $locale;
    /**
	 * @var string
	 */
	public $currency;
	/**
	 * @var string
	 */
	public $marker;
	/**
	 * @var string
	 */
	public $search_host;
	/**
	 * @var bool
	 */
	public $offers_switch;
	/**
	 * @var string
	 */
	public $api_url;
	/**
	 * @var array
	 */
	public $routes;

}
