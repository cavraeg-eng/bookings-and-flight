<?php

namespace Travelpayouts\includes;

use Travelpayouts;
use Travelpayouts\components\snowplow\Tracker;

/**
 * Fired during plugin activation
 *
 * @link       http://www.travelpayouts.com/?locale=en
 * @since      1.0.0
 *
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 * @author     travelpayouts < wpplugin@travelpayouts.com>
 */
class Activator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function onActivate()
    {
        Travelpayouts::getInstance()->snowTracker->trackStructEvent(
            Tracker::CATEGORY_INSTALL,
			Tracker::ACTION_INSTALLED
        );
    }
}
