<?php
/**
 * Export/Import Class
 *
 * Provides a generic JSON export/import system for all page meta fields,
 * global options, and CPTs declared in config — with media sideloading,
 * taxonomy support, deterministic identity matching, and host-aware URL rewriting.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/export-import/trait-export-methods.php';
require_once __DIR__ . '/export-import/trait-import-methods.php';
require_once __DIR__ . '/export-import/trait-media-methods.php';
require_once __DIR__ . '/export-import/trait-utility-methods.php';

/**
 * Export/Import handler.
 */
class Bookings_And_Flights_Export_Import {

    /**
     * Current schema version for the export JSON structure.
     */
    const SCHEMA_VERSION = 1;

    /**
     * Single instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * URL-keyed cache of sideloaded attachment IDs to avoid re-downloading.
     *
     * @var array
     */
    private $sideload_cache = array();

    /**
     * Warnings collected during import.
     *
     * @var array
     */
    private $warnings = array();

    use Bookings_And_Flights_Export_Import_Export_Methods;
    use Bookings_And_Flights_Export_Import_Import_Methods;
    use Bookings_And_Flights_Export_Import_Media_Methods;
    use Bookings_And_Flights_Export_Import_Utility_Methods;

    /**
     * Get singleton instance.
     *
     * @return self
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor.
     */
    private function __construct() {}

    /**
     * Initialize hooks.
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_tools_page' ) );
        add_action( 'admin_init', array( $this, 'handle_actions' ) );
        add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
    }

    // =========================================================================
    // ADMIN PAGE
    // =========================================================================

    /**
     * Register the tools page.
     */
    public function add_tools_page() {
        add_management_page(
            __( 'Bookings and Flights Export/Import', 'bookings_and_flights-content-manager' ),
            __( 'Bookings and Flights Export/Import', 'bookings_and_flights-content-manager' ),
            'manage_options',
            'bookings_and_flights-export-import',
            array( $this, 'render_tools_page' )
        );
    }

    /**
     * Render the tools page.
     */
    public function render_tools_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Bookings and Flights Export/Import', 'bookings_and_flights-content-manager' ); ?></h1>

            <div style="display:flex;gap:2rem;flex-wrap:wrap;margin-top:1.5rem;">

                <!-- Export -->
                <div style="flex:1;min-width:320px;background:#fff;border:1px solid #c3c4c7;padding:1.5rem;">
                    <h2><?php esc_html_e( 'Export', 'bookings_and_flights-content-manager' ); ?></h2>
                    <p><?php esc_html_e( 'Download a JSON file containing all page content, global options, and custom post type data.', 'bookings_and_flights-content-manager' ); ?></p>
                    <form method="post">
                        <?php wp_nonce_field( 'bookings_and_flights_export', 'bookings_and_flights_export_nonce' ); ?>
                        <input type="hidden" name="bookings_and_flights_action" value="export">
                        <?php submit_button( __( 'Download Export File', 'bookings_and_flights-content-manager' ), 'primary', 'submit', false ); ?>
                    </form>
                </div>

                <!-- Import -->
                <div style="flex:1;min-width:320px;background:#fff;border:1px solid #c3c4c7;padding:1.5rem;">
                    <h2><?php esc_html_e( 'Import', 'bookings_and_flights-content-manager' ); ?></h2>
                    <p><?php esc_html_e( 'Upload a previously exported JSON file to restore content on this site.', 'bookings_and_flights-content-manager' ); ?></p>
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field( 'bookings_and_flights_import', 'bookings_and_flights_import_nonce' ); ?>
                        <input type="hidden" name="bookings_and_flights_action" value="import">
                        <p>
                            <input type="file" name="import_file" accept=".json">
                            <br>
                            <span class="description">
                                <?php
                                $max_import_display = min(
                                    wp_max_upload_size(),
                                    (int) apply_filters( 'bookings_and_flights_max_import_bytes', 10 * MB_IN_BYTES )
                                );
                                printf(
                                    /* translators: %s: max upload size */
                                    esc_html__( 'Maximum file size: %s', 'bookings_and_flights-content-manager' ),
                                    esc_html( size_format( $max_import_display ) )
                                );
                                ?>
                            </span>
                        </p>
                        <?php submit_button( __( 'Upload & Import', 'bookings_and_flights-content-manager' ), 'primary', 'submit', false ); ?>
                    </form>
                </div>

            </div>
        </div>
        <?php
    }

    /**
     * Route form submissions.
     */
    public function handle_actions() {
        if ( ! isset( $_POST['bookings_and_flights_action'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $action = sanitize_text_field( wp_unslash( $_POST['bookings_and_flights_action'] ) );

        if ( 'export' === $action ) {
            $this->handle_export();
        } elseif ( 'import' === $action ) {
            $this->handle_import();
        }
    }

    // =========================================================================
    // EXPORT
    // =========================================================================

    /**
     * Handle the export action — build and stream JSON.
     */
    private function handle_export() {
        check_admin_referer( 'bookings_and_flights_export', 'bookings_and_flights_export_nonce' );

        $data = array(
            'plugin'         => 'bookings_and_flights-content-manager',
            'schema_version' => self::SCHEMA_VERSION,
            'plugin_version' => defined( 'BOOKINGS_AND_FLIGHTS_VERSION' ) ? BOOKINGS_AND_FLIGHTS_VERSION : '1.0.0',
            'exported_at'    => gmdate( 'c' ),
            'site_url'       => home_url(),
            'pages'          => $this->export_pages(),
            'global_options' => $this->export_global_options(),
            'custom_post_types' => $this->export_custom_post_types(),
        );

        $data = apply_filters( 'bookings_and_flights_export_data', $data );

        $json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

        $filename = 'bookings_and_flights-export-' . gmdate( 'Y-m-d' ) . '.json';

        nocache_headers();
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Content-Length: ' . strlen( $json ) );

        echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }
}
