<?php
/**
 * REST route registration manager.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

use BAF\Core\Post_Types\Post_Type_Registrar;

defined( 'ABSPATH' ) || exit;

final class Rest_Manager {

	public static function register_routes(): void {
		$controllers = array(
			new Travel_Entity_Controller( Post_Type_Registrar::DESTINATION, 'destinations' ),
			new Travel_Entity_Controller( Post_Type_Registrar::ROUTE, 'routes' ),
			new Affiliate_Click_Controller(),
			new AI_Itinerary_Controller(),
		);

		foreach ( $controllers as $controller ) {
			$controller->register_routes();
		}
	}
}