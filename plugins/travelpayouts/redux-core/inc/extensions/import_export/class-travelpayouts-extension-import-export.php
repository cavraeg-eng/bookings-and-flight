<?php
/**
 * Redux_Travelpayouts Import/Export Extention Class
 *
 * @class Redux_Travelpayouts_Extension_Import_Export
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Travelpayouts_Extension_Import_Export', false ) ) {

	/**
	 * Main TravelpayoutsSettingsFramework import_export extension class
	 *
	 * @since       3.1.6
	 */
	class Redux_Travelpayouts_Extension_Import_Export extends Redux_Travelpayouts_Extension_Abstract {

		/**
		 * Ext version.
		 *
		 * @var string
		 */
		public static $version = '4.0';

		/**
		 * Is field bit.
		 *
		 * @var bool
		 */
		public $is_field = false;

		/**
		 * Class Constructor. Defines the args for the extions class
		 *
		 * @since       1.0.0
		 * @access      public
		 *
		 * @param       object $parent TravelpayoutsSettingsFramework object.
		 *
		 * @return      void
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent, __FILE__ );

			$this->add_field( 'import_export' );

			add_action(
				'wp_ajax_Redux_Travelpayouts_link_options-' . $this->parent->args['opt_name'],
				array(
					$this,
					'link_options',
				)
			);

			add_action(
				'wp_ajax_nopriv_Redux_Travelpayouts_link_options-' . $this->parent->args['opt_name'],
				array(
					$this,
					'link_options',
				)
			);

			add_action(
				'wp_ajax_Redux_Travelpayouts_download_options-' . $this->parent->args['opt_name'],
				array(
					$this,
					'download_options',
				)
			);

			add_action(
				'wp_ajax_nopriv_Redux_Travelpayouts_download_options-' . $this->parent->args['opt_name'],
				array(
					$this,
					'download_options',
				)
			);

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( 'redux_travelpayouts/options/' . $this->parent->args['opt_name'] . '/import', array( $this, 'remove_cookie' ) );

			$this->is_field = Redux_Travelpayouts_Helpers::is_field_in_use( $parent, 'import_export' );

			if ( ! $this->is_field && $this->parent->args['show_import_export'] ) {
				$this->add_section();
			}

			add_filter( 'upload_mimes', array( $this, 'custom_upload_mimes' ) );
		}

		/**
		 * Adds the appropriate mime types to WordPress
		 *
		 * @param array $existing_mimes .
		 *
		 * @return array
		 */
		public function custom_upload_mimes( $existing_mimes = array() ) {
			$existing_mimes['redux'] = 'application/redux';

			return $existing_mimes;
		}

		/**
		 * Add section to panel.
		 */
		public function add_section() {
			$this->parent->sections[] = array(
				'id'         => 'import/export',
				'title'      => esc_html__( 'Import / Export', 'redux-framework' ),
				'heading'    => '',
				'icon'       => 'tp-i-tabler:database-export',
				'customizer' => false,
				'fields'     => array(
					array(
						'id'         => 'Redux_Travelpayouts_import_export',
						'type'       => 'import_export',
						'full_width' => true,
					),
				),
			);
		}

		/**
		 * Import link options.
		 */
		public function link_options() {
			if ( ! isset( $_GET['secret'] ) || md5( md5( Redux_Travelpayouts_Functions_Ex::hash_key() ) . '-' . $this->parent->args['opt_name'] ) !== $_GET['secret'] ) { // phpcs:ignore WordPress.Security.NonceVerification
				wp_die( 'Invalid Secret for options use' );
				exit;
			}

			$var                 = $this->parent->options;
			$var['redux-backup'] = 1;

			if ( isset( $var['Redux_Travelpayouts_imported'] ) ) {
				unset( $var['Redux_Travelpayouts_imported'] );
			}

			echo wp_json_encode( $var );

			die();
		}

		/**
		 * Import download options.
		 */
		public function download_options() {
			if ( ! isset( $_GET['secret'] ) || md5( md5( Redux_Travelpayouts_Functions_Ex::hash_key() ) . '-' . $this->parent->args['opt_name'] ) !== $_GET['secret'] ) { // phpcs:ignore WordPress.Security.NonceVerification
				wp_die( 'Invalid Secret for options use' );
				exit;
			}

			$this->parent->get_options();
			$backup_options                 = $this->parent->options;
			$backup_options['redux-backup'] = 1;

			if ( isset( $backup_options['Redux_Travelpayouts_imported'] ) ) {
				unset( $backup_options['Redux_Travelpayouts_imported'] );
			}

			// No need to escape this, as it's been properly escaped previously and through json_encode.
			$content = wp_json_encode( $backup_options );

			if ( isset( $_GET['action'] ) && 'Redux_Travelpayouts_download_options-' . $this->parent->args['opt_name'] === $_GET['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification
				header( 'Content-Description: File Transfer' );
				header( 'Content-type: application/txt' );
				header( 'Content-Disposition: attachment; filename="Redux_Travelpayouts_options_"' . $this->parent->args['opt_name'] . '_backup_' . gmdate( 'd-m-Y' ) . '.json' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate' );
				header( 'Pragma: public' );

				echo( $content ); // phpcs:ignore WordPress.Security.EscapeOutput

				exit;
			} else {
				header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
				header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . 'GMT' );
				header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
				header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				header( 'Cache-Control: post-check=0, pre-check=0', false );
				header( 'Pragma: no-cache' );

				// Can't include the type. Thanks old Firefox and IE. BAH.
				// header('Content-type: application/json');.
				echo( $content ); // phpcs:ignore WordPress.Security.EscapeOutput

				exit;
			}
		}

		/**
		 * Remove current tab cookie.
		 */
		public function remove_cookie() {
			// Remove the import/export tab cookie.
			if ( isset( $_COOKIE ) && isset( $_COOKIE[ 'Redux_Travelpayouts_current_tab_' . $this->parent->args['opt_name'] ] ) && 'import_export_default' === $_COOKIE[ 'Redux_Travelpayouts_current_tab_' . $this->parent->args['opt_name'] ] ) {
				setcookie( 'Redux_Travelpayouts_current_tab_' . $this->parent->args['opt_name'], '', 1, '/' );
				$_COOKIE[ 'Redux_Travelpayouts_current_tab_' . $this->parent->args['opt_name'] ] = 1;
			}
		}
	}

	if ( ! class_exists( 'TravelpayoutsSettingsFramework_extension_import_export' ) ) {
		class_alias( 'Redux_Travelpayouts_Extension_Import_Export', 'TravelpayoutsSettingsFramework_extension_import_export' );
	}
}
