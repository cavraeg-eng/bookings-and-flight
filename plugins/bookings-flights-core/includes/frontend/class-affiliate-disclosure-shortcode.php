<?php
/**
 * Affiliate disclosure shortcode.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Services\Affiliate_Link_Service;

defined( 'ABSPATH' ) || exit;

final class Affiliate_Disclosure_Shortcode {

	public static function render( array|string $attributes = array() ): string {
		$attributes = shortcode_atts(
			array(
				'class' => '',
			),
			(array) $attributes,
			'baf_affiliate_disclosure'
		);

		$class = sanitize_html_class( (string) $attributes['class'] );

		return Affiliate_Link_Service::disclosure_markup( $class );
	}
}