<?php
/**
 * Settings registration and sanitization.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Settings;

use BAF\Core\Capabilities\Capability_Manager;

defined( 'ABSPATH' ) || exit;

final class Settings_Manager {

	public const OPTION_GENERAL       = 'baf_settings';
	public const OPTION_TRAVELPAYOUTS = 'baf_travelpayouts_settings';
	public const OPTION_AI            = 'baf_ai_settings';
	public const OPTION_CONSENT       = 'baf_consent_settings';
	public const OPTION_TRACKING      = 'baf_tracking_settings';

	private const GROUP_CORE         = 'baf_core_settings';
	private const GROUP_INTEGRATIONS = 'baf_core_integrations';

	public static function bootstrap(): void {
		add_action( 'admin_init', array( self::class, 'register' ) );
		add_filter( 'option_page_capability_' . self::GROUP_CORE, array( self::class, 'settings_capability' ) );
		add_filter( 'option_page_capability_' . self::GROUP_INTEGRATIONS, array( self::class, 'settings_capability' ) );
	}

	public static function register(): void {
		register_setting(
			self::GROUP_CORE,
			self::OPTION_GENERAL,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize_general' ),
				'default'           => self::default_general(),
			)
		);

		register_setting(
			self::GROUP_CORE,
			self::OPTION_CONSENT,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize_consent' ),
				'default'           => self::default_consent(),
			)
		);

		register_setting(
			self::GROUP_CORE,
			self::OPTION_TRACKING,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize_tracking' ),
				'default'           => self::default_tracking(),
			)
		);

		register_setting(
			self::GROUP_INTEGRATIONS,
			self::OPTION_TRAVELPAYOUTS,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize_travelpayouts' ),
				'default'           => self::default_travelpayouts(),
			)
		);

		register_setting(
			self::GROUP_INTEGRATIONS,
			self::OPTION_AI,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( self::class, 'sanitize_ai' ),
				'default'           => self::default_ai(),
			)
		);

		self::register_core_fields();
		self::register_integration_fields();
	}

	public static function settings_capability(): string {
		return Capability_Manager::MANAGE_SETTINGS;
	}

	public static function core_group(): string {
		return self::GROUP_CORE;
	}

	public static function integrations_group(): string {
		return self::GROUP_INTEGRATIONS;
	}

	public static function get_general(): array {
		return wp_parse_args( (array) get_option( self::OPTION_GENERAL, array() ), self::default_general() );
	}

	public static function get_travelpayouts(): array {
		return wp_parse_args( (array) get_option( self::OPTION_TRAVELPAYOUTS, array() ), self::default_travelpayouts() );
	}

	public static function get_ai(): array {
		return wp_parse_args( (array) get_option( self::OPTION_AI, array() ), self::default_ai() );
	}

	public static function get_consent(): array {
		return wp_parse_args( (array) get_option( self::OPTION_CONSENT, array() ), self::default_consent() );
	}

	public static function get_tracking(): array {
		return wp_parse_args( (array) get_option( self::OPTION_TRACKING, array() ), self::default_tracking() );
	}

	public static function sanitize_general( $value ): array {
		$value = is_array( $value ) ? $value : array();

		return array(
			'brand_name'    => sanitize_text_field( (string) ( $value['brand_name'] ?? '' ) ),
			'support_email' => sanitize_email( (string) ( $value['support_email'] ?? '' ) ),
		);
	}

	public static function sanitize_consent( $value ): array {
		$value = is_array( $value ) ? $value : array();

		return array(
			'allow_external_ai'        => self::truthy( $value['allow_external_ai'] ?? false ),
			'allow_provider_requests'  => self::truthy( $value['allow_provider_requests'] ?? false ),
			'consent_notice_override' => wp_kses_post( (string) ( $value['consent_notice_override'] ?? '' ) ),
		);
	}

	public static function sanitize_tracking( $value ): array {
		$value = is_array( $value ) ? $value : array();

		return array(
			'enable_click_tracking' => self::truthy( $value['enable_click_tracking'] ?? false ),
			'anonymize_ip'          => self::truthy( $value['anonymize_ip'] ?? true ),
			'subid_prefix'          => sanitize_key( (string) ( $value['subid_prefix'] ?? '' ) ),
		);
	}

	public static function sanitize_travelpayouts( $value ): array {
		$value    = is_array( $value ) ? $value : array();
		$existing = self::get_travelpayouts();
		$white_label_widget_id = self::sanitize_white_label_widget_id(
			$value['white_label_widget_id'] ?? '',
			(string) $existing['white_label_widget_id']
		);
		$hotel_widget_script_url = self::sanitize_travelpayouts_widget_script_url(
			$value['hotel_widget_script_url'] ?? '',
			(string) $existing['hotel_widget_script_url']
		);

		return array(
			'marker'                  => sanitize_text_field( (string) ( $value['marker'] ?? '' ) ),
			'api_token'               => self::sanitize_secret_value( $value['api_token'] ?? '', (string) $existing['api_token'] ),
			'white_label_widget_id'   => $white_label_widget_id,
			'white_label_results_url' => self::sanitize_public_url( $value['white_label_results_url'] ?? '' ),
			'hotel_widget_script_url' => $hotel_widget_script_url,
		);
	}

	public static function sanitize_ai( $value ): array {
		$value    = is_array( $value ) ? $value : array();
		$existing = self::get_ai();
		$mode     = sanitize_key( (string) ( $value['mode'] ?? 'demo' ) );
		$provider = sanitize_key( (string) ( $value['provider'] ?? '' ) );

		if ( ! in_array( $mode, array( 'demo', 'live' ), true ) ) {
			$mode = 'demo';
		}

		if ( ! in_array( $provider, array( '', 'openai', 'anthropic', 'vercel_ai_gateway' ), true ) ) {
			$provider = '';
		}

		return array(
			'mode'     => $mode,
			'provider' => $provider,
			'api_key'  => self::sanitize_secret_value( $value['api_key'] ?? '', (string) $existing['api_key'] ),
		);
	}

	private static function register_core_fields(): void {
		add_settings_section( 'baf_general_section', __( 'Product basics', 'bookings-flights-core' ), '__return_false', 'baf-settings' );
		add_settings_field( 'baf_brand_name', __( 'Brand name', 'bookings-flights-core' ), array( self::class, 'render_text_field' ), 'baf-settings', 'baf_general_section', array( 'option' => self::OPTION_GENERAL, 'key' => 'brand_name' ) );
		add_settings_field( 'baf_support_email', __( 'Support email', 'bookings-flights-core' ), array( self::class, 'render_email_field' ), 'baf-settings', 'baf_general_section', array( 'option' => self::OPTION_GENERAL, 'key' => 'support_email' ) );

		add_settings_section( 'baf_consent_section', __( 'Consent gates', 'bookings-flights-core' ), array( self::class, 'render_consent_section' ), 'baf-settings' );
		add_settings_field( 'baf_allow_external_ai', __( 'External AI consent', 'bookings-flights-core' ), array( self::class, 'render_checkbox_field' ), 'baf-settings', 'baf_consent_section', array( 'option' => self::OPTION_CONSENT, 'key' => 'allow_external_ai', 'label' => __( 'Allow live AI providers after explicit user/admin approval.', 'bookings-flights-core' ) ) );
		add_settings_field( 'baf_allow_provider_requests', __( 'Provider request consent', 'bookings-flights-core' ), array( self::class, 'render_checkbox_field' ), 'baf-settings', 'baf_consent_section', array( 'option' => self::OPTION_CONSENT, 'key' => 'allow_provider_requests', 'label' => __( 'Allow configured affiliate/search providers to receive required request data.', 'bookings-flights-core' ) ) );
		add_settings_field( 'baf_consent_notice_override', __( 'Consent notice', 'bookings-flights-core' ), array( self::class, 'render_textarea_field' ), 'baf-settings', 'baf_consent_section', array( 'option' => self::OPTION_CONSENT, 'key' => 'consent_notice_override' ) );

		add_settings_section( 'baf_tracking_section', __( 'Tracking defaults', 'bookings-flights-core' ), '__return_false', 'baf-settings' );
		add_settings_field( 'baf_enable_click_tracking', __( 'Click tracking', 'bookings-flights-core' ), array( self::class, 'render_checkbox_field' ), 'baf-settings', 'baf_tracking_section', array( 'option' => self::OPTION_TRACKING, 'key' => 'enable_click_tracking', 'label' => __( 'Prepare affiliate click tracking when future workflows are enabled.', 'bookings-flights-core' ) ) );
		add_settings_field( 'baf_anonymize_ip', __( 'Privacy', 'bookings-flights-core' ), array( self::class, 'render_checkbox_field' ), 'baf-settings', 'baf_tracking_section', array( 'option' => self::OPTION_TRACKING, 'key' => 'anonymize_ip', 'label' => __( 'Anonymize IP-derived data where tracking is stored.', 'bookings-flights-core' ) ) );
		add_settings_field( 'baf_subid_prefix', __( 'SubID prefix', 'bookings-flights-core' ), array( self::class, 'render_text_field' ), 'baf-settings', 'baf_tracking_section', array( 'option' => self::OPTION_TRACKING, 'key' => 'subid_prefix' ) );
	}

	private static function register_integration_fields(): void {
		add_settings_section( 'baf_travelpayouts_section', __( 'Travelpayouts', 'bookings-flights-core' ), array( self::class, 'render_travelpayouts_section' ), 'baf-integrations' );
		add_settings_field( 'baf_travelpayouts_marker', __( 'Marker', 'bookings-flights-core' ), array( self::class, 'render_text_field' ), 'baf-integrations', 'baf_travelpayouts_section', array( 'option' => self::OPTION_TRAVELPAYOUTS, 'key' => 'marker' ) );
		add_settings_field( 'baf_travelpayouts_api_token', __( 'API token', 'bookings-flights-core' ), array( self::class, 'render_secret_field' ), 'baf-integrations', 'baf_travelpayouts_section', array( 'option' => self::OPTION_TRAVELPAYOUTS, 'key' => 'api_token' ) );
		add_settings_field(
			'baf_travelpayouts_white_label_widget_id',
			__( 'White Label Widget code or ID', 'bookings-flights-core' ),
			array( self::class, 'render_white_label_widget_field' ),
			'baf-integrations',
			'baf_travelpayouts_section',
			array(
				'option'      => self::OPTION_TRAVELPAYOUTS,
				'key'         => 'white_label_widget_id',
				'description' => __( 'Paste the full Travelpayouts White Label Widget main code or only the wl_id value. For safety, only the widget ID is stored.', 'bookings-flights-core' ),
			)
		);
		add_settings_field(
			'baf_travelpayouts_white_label_results_url',
			__( 'White Label results URL', 'bookings-flights-core' ),
			array( self::class, 'render_url_field' ),
			'baf-integrations',
			'baf_travelpayouts_section',
			array(
				'option'      => self::OPTION_TRAVELPAYOUTS,
				'key'         => 'white_label_results_url',
				'description' => __( 'Optional. Use this only when Travelpayouts results should open on a separate WordPress results page.', 'bookings-flights-core' ),
			)
		);
		add_settings_field(
			'baf_travelpayouts_hotel_widget_script_url',
			__( 'Trip.com hotel widget code or script URL', 'bookings-flights-core' ),
			array( self::class, 'render_hotel_widget_field' ),
			'baf-integrations',
			'baf_travelpayouts_section',
			array(
					'option'      => self::OPTION_TRAVELPAYOUTS,
					'key'         => 'hotel_widget_script_url',
					'description' => __( 'Paste the Travelpayouts dashboard hotel widget code for Trip.com or another Hotels & Accommodation brand. For safety, only an approved Travelpayouts widget script URL or Trip.com partner iframe URL is stored.', 'bookings-flights-core' ),
				)
			);

		add_settings_section( 'baf_ai_section', __( 'AI provider', 'bookings-flights-core' ), array( self::class, 'render_ai_section' ), 'baf-integrations' );
		add_settings_field( 'baf_ai_mode', __( 'Mode', 'bookings-flights-core' ), array( self::class, 'render_select_field' ), 'baf-integrations', 'baf_ai_section', array( 'option' => self::OPTION_AI, 'key' => 'mode', 'choices' => array( 'demo' => __( 'Demo mode', 'bookings-flights-core' ), 'live' => __( 'Live provider', 'bookings-flights-core' ) ) ) );
		add_settings_field( 'baf_ai_provider', __( 'Provider', 'bookings-flights-core' ), array( self::class, 'render_select_field' ), 'baf-integrations', 'baf_ai_section', array( 'option' => self::OPTION_AI, 'key' => 'provider', 'choices' => array( '' => __( 'Not configured', 'bookings-flights-core' ), 'openai' => 'OpenAI', 'anthropic' => 'Anthropic', 'vercel_ai_gateway' => 'Vercel AI Gateway' ) ) );
		add_settings_field( 'baf_ai_api_key', __( 'API key', 'bookings-flights-core' ), array( self::class, 'render_secret_field' ), 'baf-integrations', 'baf_ai_section', array( 'option' => self::OPTION_AI, 'key' => 'api_key' ) );
	}

	public static function render_text_field( array $args ): void {
		self::render_input_field( $args, 'text' );
	}

	public static function render_email_field( array $args ): void {
		self::render_input_field( $args, 'email' );
	}

	public static function render_url_field( array $args ): void {
		self::render_input_field( $args, 'url' );
	}

	public static function render_secret_field( array $args ): void {
		$settings = self::option_values( (string) $args['option'] );
		$value    = (string) ( $settings[ $args['key'] ] ?? '' );
		$name     = self::field_name( (string) $args['option'], (string) $args['key'] );
		$id       = self::field_id( (string) $args['option'], (string) $args['key'] );
		$label    = '' !== $value ? __( 'Configured. Leave blank to keep the saved secret.', 'bookings-flights-core' ) : __( 'Not configured.', 'bookings-flights-core' );

		printf( '<input type="password" id="%1$s" name="%2$s" value="" class="regular-text code" autocomplete="new-password" placeholder="%3$s" />', esc_attr( $id ), esc_attr( $name ), esc_attr( $label ) );
		printf( '<p class="description">%s</p>', esc_html( $label ) );
	}

	public static function render_checkbox_field( array $args ): void {
		$settings = self::option_values( (string) $args['option'] );
		$name     = self::field_name( (string) $args['option'], (string) $args['key'] );
		$id       = self::field_id( (string) $args['option'], (string) $args['key'] );
		$label    = (string) ( $args['label'] ?? '' );

		printf( '<label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>', esc_attr( $id ), esc_attr( $name ), checked( true, ! empty( $settings[ $args['key'] ] ), false ), esc_html( $label ) );
	}

	public static function render_textarea_field( array $args ): void {
		$settings = self::option_values( (string) $args['option'] );
		$name     = self::field_name( (string) $args['option'], (string) $args['key'] );
		$id       = self::field_id( (string) $args['option'], (string) $args['key'] );
		$value    = (string) ( $settings[ $args['key'] ] ?? '' );

		printf( '<textarea id="%1$s" name="%2$s" class="large-text" rows="4">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
		self::render_description( $args );
	}

	public static function render_white_label_widget_field( array $args ): void {
		$settings = self::option_values( (string) $args['option'] );
		$name     = self::field_name( (string) $args['option'], (string) $args['key'] );
		$id       = self::field_id( (string) $args['option'], (string) $args['key'] );
		$value    = (string) ( $settings[ $args['key'] ] ?? '' );

		printf( '<textarea id="%1$s" name="%2$s" class="large-text code" rows="5">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
		self::render_description( $args );
	}

	public static function render_hotel_widget_field( array $args ): void {
		$settings = self::option_values( (string) $args['option'] );
		$name     = self::field_name( (string) $args['option'], (string) $args['key'] );
		$id       = self::field_id( (string) $args['option'], (string) $args['key'] );
		$value    = (string) ( $settings[ $args['key'] ] ?? '' );

		printf( '<textarea id="%1$s" name="%2$s" class="large-text code" rows="5">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
		self::render_description( $args );
	}

	public static function render_select_field( array $args ): void {
		$settings = self::option_values( (string) $args['option'] );
		$name     = self::field_name( (string) $args['option'], (string) $args['key'] );
		$id       = self::field_id( (string) $args['option'], (string) $args['key'] );
		$value    = (string) ( $settings[ $args['key'] ] ?? '' );

		printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
		foreach ( (array) $args['choices'] as $choice_value => $choice_label ) {
			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( (string) $choice_value ), selected( $value, (string) $choice_value, false ), esc_html( (string) $choice_label ) );
		}
		echo '</select>';
	}

	public static function render_consent_section(): void {
		echo '<p>' . esc_html__( 'Live provider and AI features should remain disabled until explicit consent is configured for the data being sent externally.', 'bookings-flights-core' ) . '</p>';
	}

	public static function render_travelpayouts_section(): void {
		echo '<p>' . esc_html__( 'Stores core Travelpayouts credentials and approved Travelpayouts placement data server-side. Page-type White Label flight and hotel domains are still configured in the official Travelpayouts plugin account settings.', 'bookings-flights-core' ) . '</p>';
		echo '<p>' . esc_html__( 'After saving a White Label Widget ID, place [baf_travelpayouts_white_label] on the WordPress page where the embedded search and results should appear. After saving a Trip.com hotel widget script, place [baf_travelpayouts_hotel_widget] where hotel search should appear.', 'bookings-flights-core' ) . '</p>';
	}

	public static function render_ai_section(): void {
		echo '<p>' . esc_html__( 'Demo mode remains available without live credentials. Live mode must also pass the consent gates before future AI workflows call external providers.', 'bookings-flights-core' ) . '</p>';
	}

	private static function render_input_field( array $args, string $type ): void {
		$settings = self::option_values( (string) $args['option'] );
		$name     = self::field_name( (string) $args['option'], (string) $args['key'] );
		$id       = self::field_id( (string) $args['option'], (string) $args['key'] );
		$value    = (string) ( $settings[ $args['key'] ] ?? '' );

		printf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text" />', esc_attr( $type ), esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
		self::render_description( $args );
	}

	private static function render_description( array $args ): void {
		if ( empty( $args['description'] ) ) {
			return;
		}

		printf( '<p class="description">%s</p>', esc_html( (string) $args['description'] ) );
	}

	private static function option_values( string $option ): array {
		$defaults = array(
			self::OPTION_GENERAL       => self::default_general(),
			self::OPTION_TRAVELPAYOUTS => self::default_travelpayouts(),
			self::OPTION_AI            => self::default_ai(),
			self::OPTION_CONSENT       => self::default_consent(),
			self::OPTION_TRACKING      => self::default_tracking(),
		);

		return wp_parse_args( (array) get_option( $option, array() ), $defaults[ $option ] ?? array() );
	}

	private static function field_name( string $option, string $key ): string {
		return $option . '[' . $key . ']';
	}

	private static function field_id( string $option, string $key ): string {
		return $option . '_' . $key;
	}

	private static function sanitize_secret_value( $value, string $existing ): string {
		$value = is_string( $value ) ? trim( $value ) : '';

		if ( '' === $value ) {
			return $existing;
		}

		return sanitize_text_field( $value );
	}

	private static function sanitize_white_label_widget_id( $value, string $existing = '' ): string {
		$value = is_string( $value ) ? trim( html_entity_decode( $value, ENT_QUOTES ) ) : '';

		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '/wl[_-]?id\s*[=:]\s*[\'"]?([A-Za-z0-9_-]+)/i', $value, $matches ) ) {
			return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $matches[1] );
		}

		if ( preg_match( '/data-wl-id\s*=\s*[\'"]?([A-Za-z0-9_-]+)/i', $value, $matches ) ) {
			return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $matches[1] );
		}

		if ( preg_match( '/^[A-Za-z0-9_-]+$/', $value ) ) {
			return $value;
		}

		if ( self::looks_like_white_label_page_template( $value ) ) {
			add_settings_error(
				self::OPTION_TRAVELPAYOUTS,
				'baf_white_label_page_template_pasted',
				__( 'White Label Widget code was not saved because this looks like a Travelpayouts Page-type template. Paste it in the Travelpayouts White Label Page design/template settings, or paste the Widget-type code that contains wl_id here.', 'bookings-flights-core' ),
				'error'
			);

			return $existing;
		}

		add_settings_error(
			self::OPTION_TRAVELPAYOUTS,
			'baf_white_label_widget_id_invalid',
			__( 'White Label Widget code was not saved because no wl_id value could be found. Paste the Travelpayouts Widget code or only the wl_id.', 'bookings-flights-core' ),
			'error'
		);

		return $existing;
	}

	private static function sanitize_public_url( $value ): string {
		$value = is_string( $value ) ? trim( $value ) : '';

		if ( '' === $value ) {
			return '';
		}

		if ( str_starts_with( $value, '/' ) ) {
			$value = home_url( $value );
		}

		$url    = esc_url_raw( $value );
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );

		if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
			return '';
		}

		return $url;
	}

	private static function sanitize_travelpayouts_widget_script_url( $value, string $existing = '' ): string {
		$value = is_string( $value ) ? trim( html_entity_decode( $value, ENT_QUOTES ) ) : '';

		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '/src\s*=\s*[\'"]([^\'"]+)[\'"]/i', $value, $matches ) ) {
			$value = (string) $matches[1];
		} elseif ( preg_match( '/src\s*=\s*([^>\s]+)/i', $value, $matches ) ) {
			$value = trim( (string) $matches[1], '\'"' );
		}

		if ( str_starts_with( $value, '//' ) ) {
			$value = 'https:' . $value;
		}

		$url  = esc_url_raw( $value );
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		$path = strtolower( (string) wp_parse_url( $url, PHP_URL_PATH ) );

		$is_widget_script = self::is_travelpayouts_widget_host( $host ) && self::is_travelpayouts_widget_script_path( $path );
		$is_tripcom_embed = self::is_tripcom_partner_embed_url( $host, $path );

		if ( ! $is_widget_script && ! $is_tripcom_embed ) {
			add_settings_error(
				self::OPTION_TRAVELPAYOUTS,
				'baf_hotel_widget_script_invalid',
				__( 'Trip.com hotel widget code was not saved because it was not a recognized Travelpayouts widget script or Trip.com partner embed URL. Paste the full Travelpayouts widget script code from the dashboard.', 'bookings-flights-core' ),
				'error'
			);

			return $existing;
		}

		return $url;
	}

	private static function is_travelpayouts_widget_host( string $host ): bool {
		if ( in_array( $host, array( 'tp.media', 'tpwgt.com', 'tpwgts.com' ), true ) ) {
			return true;
		}

		return str_ends_with( $host, '.tp.media' ) || str_ends_with( $host, '.tpwgt.com' ) || 'travelpayouts.com' === $host || str_ends_with( $host, '.travelpayouts.com' );
	}

	private static function is_travelpayouts_widget_script_path( string $path ): bool {
		if ( str_ends_with( $path, '.js' ) ) {
			return true;
		}

		return str_starts_with( $path, '/content' ) || str_starts_with( $path, '/wl_web/' );
	}

	private static function is_tripcom_partner_embed_url( string $host, string $path ): bool {
		return ( 'trip.com' === $host || str_ends_with( $host, '.trip.com' ) ) && str_starts_with( $path, '/partners/ad/' );
	}

	private static function looks_like_white_label_page_template( string $value ): bool {
		return str_contains( $value, '[:embed_script:]' )
			|| str_contains( $value, '[:route_info:]' )
			|| str_contains( $value, '[:current_year:]' )
			|| ( str_contains( $value, 'tpwl-search' ) && str_contains( $value, 'tpwl-tickets' ) );
	}

	private static function truthy( $value ): bool {
		return in_array( $value, array( true, 1, '1', 'yes', 'on' ), true );
	}

	private static function default_general(): array {
		return array(
			'brand_name'    => 'Bookings and Flights',
			'support_email' => '',
		);
	}

	private static function default_travelpayouts(): array {
		return array(
			'marker'                  => '',
			'api_token'               => '',
			'white_label_widget_id'   => '',
			'white_label_results_url' => '',
			'hotel_widget_script_url' => '',
		);
	}

	private static function default_ai(): array {
		return array(
			'mode'     => 'demo',
			'provider' => '',
			'api_key'  => '',
		);
	}

	private static function default_consent(): array {
		return array(
			'allow_external_ai'        => false,
			'allow_provider_requests'  => false,
			'consent_notice_override' => '',
		);
	}

	private static function default_tracking(): array {
		return array(
			'enable_click_tracking' => false,
			'anonymize_ip'          => true,
			'subid_prefix'          => 'baf',
		);
	}
}
