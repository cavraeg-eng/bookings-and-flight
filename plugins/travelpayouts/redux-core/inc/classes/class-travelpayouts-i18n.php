<?php
/**
 * Load the plugin text domain for translation.
 *
 * @package  Redux_Travelpayouts Framework/Classes
 * @since    3.0.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_I18n', false ) ) {

	/**
	 * Class Redux_Travelpayouts_I18n
	 */
	class Redux_Travelpayouts_I18n extends Redux_Travelpayouts_Class {

		/**
		 * Redux_Travelpayouts_I18n constructor.
		 *
		 * @param object $parent TravelpayoutsSettingsFramework pointer.
		 * @param string $file Translation file.
		 */
		public function __construct( $parent, $file ) {
			parent::__construct( $parent );

			$this->load( $file );
		}

		/**
		 * Load translations.
		 *
		 * @param string $file Path to translation files.
		 */
		private function load( $file ) {
			$domain = 'redux-framework';

			$core = $this->core();

			/**
			 * Locale for text domain
			 * filter 'redux_travelpayouts/textdomain/basepath/{opt_name}'
			 *
			 * @param string     The locale of the blog or from the 'locale' hook
			 * @param string     'redux-framework'  text domain
			 */
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$locale = apply_filters( 'redux_travelpayouts/locale', get_locale(), 'redux-framework' );
			$mofile = $domain . '-' . $locale . '.mo';

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$basepath = apply_filters( "redux_travelpayouts/textdomain/basepath/{$core->args['opt_name']}", Redux_Travelpayouts_Core::$dir );

			$loaded = load_textdomain( $domain, Redux_Travelpayouts_Core::$dir . 'languages/' . $mofile );

			if ( ! $loaded ) {
				$mofile = WP_LANG_DIR . '/plugins/' . $mofile;

				$loaded = load_textdomain( $domain, $mofile );
			}
		}
	}
}
