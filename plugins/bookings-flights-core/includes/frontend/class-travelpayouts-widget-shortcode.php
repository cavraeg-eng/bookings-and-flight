<?php
/**
 * Approved Travelpayouts placement shortcode.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_Widget_Shortcode {

	public static function render( array|string $attributes = array() ): string {
		$attributes = shortcode_atts(
			array(
				'placement' => '',
				'key'       => '',
				'id'        => '',
				'surface'   => '',
				'channel'   => '',
				'slug'      => '',
				'class'     => '',
			),
			(array) $attributes,
			'baf_travelpayouts_widget'
		);

		return Travelpayouts_Widget_Renderer::render( $attributes );
	}
}
