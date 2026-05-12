<?php
/**
 * Affiliate click tracking service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Repositories\Click_Tracking_Repository;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Click_Tracking_Service {

	private Click_Tracking_Repository $repository;

	public function __construct( ?Click_Tracking_Repository $repository = null ) {
		$this->repository = $repository ?? new Click_Tracking_Repository();
	}

	public function track( array $event ): bool|\WP_Error {
		$tracking = Settings_Manager::get_tracking();

		if ( true !== (bool) $tracking['enable_click_tracking'] ) {
			return true;
		}

		return $this->repository->record( $event );
	}
}