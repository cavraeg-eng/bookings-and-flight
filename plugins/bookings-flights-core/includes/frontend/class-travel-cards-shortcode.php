<?php
/**
 * Affiliate travel cards shortcode.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Services\Affiliate_Link_Service;

defined( 'ABSPATH' ) || exit;

final class Travel_Cards_Shortcode {

	public static function render( array|string $attributes = array() ): string {
		$attributes = shortcode_atts(
			array(
				'post_id'      => '0',
				'origin'       => '',
				'destination'  => '',
				'label'        => __( 'Compare flight options', 'bookings-flights-core' ),
				'description'  => '',
				'subid_source' => '',
				'provider'     => Affiliate_Link_Service::PROVIDER_TRAVELPAYOUTS,
			),
			(array) $attributes,
			'baf_travel_cards'
		);

		$post_id = absint( $attributes['post_id'] );

		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}

		$service = new Affiliate_Link_Service();
		$card    = $service->build_card(
			array(
				'post_id'      => $post_id,
				'origin'       => $attributes['origin'],
				'destination'  => $attributes['destination'],
				'label'        => $attributes['label'],
				'description'  => $attributes['description'],
				'subid_source' => $attributes['subid_source'],
				'provider'     => $attributes['provider'],
			)
		);

		wp_enqueue_style( Frontend_Manager::ASSET_HANDLE );

		if ( is_wp_error( $card ) ) {
			return self::render_notice( $card );
		}

		return self::render_card( $card );
	}

	private static function render_card( array $card ): string {
		$title_id = wp_unique_id( 'baf-travel-card-title-' );

		ob_start();
		?>
		<div class="baf-travel-card-shell">
			<section class="baf-travel-card" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
				<div class="baf-travel-card__body">
					<p class="baf-travel-card__eyebrow"><?php echo esc_html( $card['provider_label'] ); ?></p>
					<h2 id="<?php echo esc_attr( $title_id ); ?>" class="baf-travel-card__title"><?php echo esc_html( $card['title'] ); ?></h2>
					<?php if ( '' !== $card['description'] ) : ?>
						<p class="baf-travel-card__description"><?php echo esc_html( $card['description'] ); ?></p>
					<?php endif; ?>
					<?php echo wp_kses_post( Affiliate_Link_Service::disclosure_markup() ); ?>
				</div>
				<a class="baf-travel-card__button" href="<?php echo esc_url( $card['handoff_url'] ); ?>" rel="sponsored nofollow noopener">
					<?php echo esc_html( $card['label'] ); ?>
				</a>
			</section>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	private static function render_notice( \WP_Error $error ): string {
		$message = $error->get_error_message();

		return '<div class="baf-travel-card-shell"><div class="baf-travel-card baf-travel-card--notice" role="status"><p>' . esc_html( $message ) . '</p></div></div>';
	}
}