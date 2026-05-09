<?php
/**
 * Import & Export for Option Panel
 *
 * @package     TravelpayoutsSettingsFramework
 * @author      Dovy Paukstys
 * @version     4.0.0
 */

use Travelpayouts\components\widgets\AlertWidget;

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Travelpayouts_Import_Export', false ) ) {

	/**
	 * Main Redux_Travelpayouts_import_export class
	 *
	 * @since       1.0.0
	 */
	class Redux_Travelpayouts_Import_Export extends Redux_Travelpayouts_Field {
        public $is_field;

        /**
		 * Redux_Travelpayouts_Import_Export constructor.
		 *
		 * @param array  $field Field array.
		 * @param string $value Value array.
		 * @param object $parent TravelpayoutsSettingsFramework object.
		 *
		 * @throws ReflectionException .
		 */
		public function __construct( $field, $value, $parent ) {
			parent::__construct( $field, $value, $parent );

			$this->is_field = $this->parent->extensions['import_export']->is_field;
		}

		/**
		 * Set field defaults.
		 */
		public function set_defaults() {
			// Set default args for this field to avoid bad indexes. Change this to anything you use.
			$defaults = array(
				'options'          => array(),
				'stylesheet'       => '',
				'output'           => true,
				'enqueue'          => true,
				'enqueue_frontend' => true,
			);

			$this->field = wp_parse_args( $this->field, $defaults );
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			$secret = md5( md5( Redux_Travelpayouts_Functions_Ex::hash_key() ) . '-' . $this->parent->args['opt_name'] );

			// No errors please.
			$defaults = [
				'full_width' => true,
				'overflow' => 'inherit',
			];

			$this->field = wp_parse_args($this->field, $defaults);

			$do_close = false;

			$id = $this->parent->args['opt_name'] . '-' . $this->field['id'];
			?>
			<div><h5 class='tp-text-sm tp-mt-0 tp-mb-3'><?php esc_html_e('Import Options', 'redux-framework'); ?></h5>
                <?= AlertWidget::widget([
                    'type' => AlertWidget::TYPE_ERROR,
                    'showRoundel' => true,
                    'content' => esc_html__('WARNING! This will overwrite all existing option values, please proceed with caution!', TRAVELPAYOUTS_TEXT_DOMAIN),
                ]);?>
				<div class='tp-mt-3'>
					<a
						href="javascript:void(0);"
						id="redux-import-code-button"
						class="tp-button tp-button--secondary tp-mr-2">
						<?php esc_html_e('Import from File', 'redux-framework'); ?>
					</a>

					<a
						href="javascript:void(0);"
						id="redux-import-link-button"
						class="tp-button tp-button--secondary">
						<?php esc_html_e('Import from URL', 'redux-framework'); ?>
					</a>
				</div>
				<div id="redux-import-code-wrapper" class='tp-mb-4 tp-mt-3'>
					<p class="tp-my-1 tp-text--bold" id="import-code-description">

						<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
						<?php echo esc_html(apply_filters('redux-import-file-description', esc_html__('Input your backup file below and hit Import to restore your sites options from a backup.', 'redux-framework'))); ?>
					</p>
					<textarea
						id="import-code-value"
						name="<?php echo esc_attr($this->parent->args['opt_name']); ?>[import_code]"
						class="large-text tp-input tp-textarea no-update" rows="2"></textarea>
				</div>
				<div id="redux-import-link-wrapper" class='tp-mb-4 tp-mt-3'>
					<p class="tp-my-1 tp-text--bold" id="import-link-description">
						<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
						<?php echo esc_html(apply_filters('redux-import-link-description', esc_html__('Input the URL to another sites options set and hit Import to load the options from that site.', 'redux-framework'))); ?>
					</p>
					<textarea
						class="large-text tp-input tp-textarea no-update"
						id="import-link-value"
						name="<?php echo esc_attr($this->parent->args['opt_name']); ?>[import_link]"
						rows="2"></textarea>
				</div>
				<div id="redux-import-action" class='tp-mt-3'>
					<input
						type="submit"
						id="redux-import"
						name="import"
						class="tp-button tp-button--error tp-button--lg"
						value="<?php esc_html_e('Import', 'redux-framework'); ?>" />

				</div>
			</div>
			<div class="tp-my-4" style='border-bottom: 1px solid var(--tp-main-stroke-color);'>
			</div>
			<div><h5 class='tp-fs-5 tp-mt-0 tp-mb-3'><?php esc_html_e('Export Options', 'redux-framework'); ?></h5>
				<div class="redux-section-desc">
					<p class="description tp-m-0" style='line-height: 1.5em;'>
						<?php // phpcs:ignore WordPress.NamingConventions.ValidHookName ?>
						<?php echo esc_html(apply_filters('redux-backup-description', esc_html__('Here you can copy/download your current option settings. Keep this safe as you can use it as a backup should anything go wrong, or you can use it to restore your settings on this site (or any other site).', 'redux-framework'))); ?>
					</p>
				</div>
				<?php $link = admin_url('admin-ajax.php?action=Redux_Travelpayouts_download_options-' . $this->parent->args['opt_name'] . '&secret=' . $secret); ?>
				<div class="tp-button-group">
					<a href="javascript:void(0);" id="redux-export-code-copy" class="tp-button tp-button--secondary">
						<?php esc_html_e('Copy Data', 'redux-framework'); ?>
					</a>
					<a href="<?php echo esc_url($link); ?>" id="redux-export-code-dl"
					   class="tp-button tp-button--primary">
						<?php esc_html_e('Download Data File', 'redux-framework'); ?>
					</a>
					<a href="javascript:void(0);" id="redux-export-link" class="tp-button tp-button--secondary">
						<?php esc_html_e('Copy Export URL', 'redux-framework'); ?>
					</a>
				</div>
				<textarea class="large-text tp-input tp-textarea no-update tp-mt-3" id="redux-export-code"
						  rows="10"></textarea>
				<textarea
					class="large-text tp-input tp-textarea no-update tp-mt-3"
					id="redux-export-link-value"
					data-url="<?php echo esc_url($link); ?>"
					rows="2"><?php echo esc_url($link); ?></textarea></div>
			<?php
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {
			wp_enqueue_script(
				'redux-extension-import-export-js',
				$this->url . 'redux-import-export' . Redux_Travelpayouts_Functions::is_min() . '.js',
				array( 'jquery', 'redux-js' ),
				Redux_Travelpayouts_Extension_Import_Export::$version,
				true
			);

			wp_enqueue_style(
				'redux-import-export',
				$this->url . 'redux-import-export.css',
				array(),
				Redux_Travelpayouts_Extension_Import_Export::$version,
				'all'
			);
		}
	}
}
