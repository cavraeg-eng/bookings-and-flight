<?php
/**
 * AI provider contract.
 *
 * @package BAF\Core
 */

namespace BAF\Core\AI;

defined( 'ABSPATH' ) || exit;

interface AI_Provider_Interface {

	public function provider_id(): string;

	public function mode(): string;

	public function generate_itinerary( array $request ): array|\WP_Error;
}