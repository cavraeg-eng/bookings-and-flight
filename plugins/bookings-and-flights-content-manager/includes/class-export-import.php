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

    /**
     * Export all pages with registered template fields.
     *
     * @return array
     */
    private function export_pages() {
        $pages = array();
        $fields_registry = Bookings_And_Flights_Fields::get_instance();
        $templates = $fields_registry->get_templates();

        foreach ( $templates as $template ) {
            $query = new WP_Query( array(
                'post_type'      => 'page',
                'posts_per_page' => -1,
                'post_status'    => 'any',
                'meta_key'       => '_wp_page_template',
                'meta_value'     => $template,
                'no_found_rows'  => true,
            ) );

            if ( ! $query->have_posts() ) {
                continue;
            }

            $fields = $fields_registry->get_fields( $template );

            foreach ( $query->posts as $page ) {
                $page_key = $template . '/' . $page->post_name;
                $field_values = array();

                foreach ( $fields as $key => $field ) {
                    $value = get_post_meta( $page->ID, Bookings_And_Flights_Config::meta_key( $key ), true );
                    $value = maybe_unserialize( $value );
                    $value = $this->resolve_media_for_export( $value, $field );
                    $field_values[ $key ] = $value;
                }

                $pages[ $page_key ] = array(
                    'post_name'      => $page->post_name,
                    'source_post_id' => $page->ID,
                    'template'       => $template,
                    'fields'         => $field_values,
                );
            }

            wp_reset_postdata();
        }

        return apply_filters( 'bookings_and_flights_export_pages', $pages );
    }

    /**
     * Export global options.
     *
     * @return array
     */
    private function export_global_options() {
        $options = get_option( Bookings_And_Flights_Options::OPTION_NAME, array() );
        $fields  = Bookings_And_Flights_Options::get_fields();

        foreach ( $fields as $key => $field ) {
            if ( ! isset( $options[ $key ] ) ) {
                continue;
            }

            $type = isset( $field['type'] ) ? $field['type'] : 'text';

            if ( 'image' === $type ) {
                $attachment_id = absint( $options[ $key ] );
                $options[ $key ] = $this->attachment_to_portable( $attachment_id );
            } elseif ( 'gallery' === $type ) {
                $ids = $options[ $key ];
                if ( is_string( $ids ) && ! empty( $ids ) ) {
                    $ids = array_map( 'absint', explode( ',', $ids ) );
                }
                if ( is_array( $ids ) ) {
                    $portable = array();
                    foreach ( $ids as $id ) {
                        $portable[] = $this->attachment_to_portable( absint( $id ) );
                    }
                    $options[ $key ] = array(
                        '_attachment_urls' => $portable,
                        'ids'             => array_map( 'absint', $ids ),
                    );
                }
            }
        }

        return apply_filters( 'bookings_and_flights_export_global_options', $options );
    }

    /**
     * Export all CPTs declared in config.
     *
     * @return array
     */
    private function export_custom_post_types() {
        $cpt_data    = array();
        $definitions = Bookings_And_Flights_Config::get_cpt_definitions();

        foreach ( $definitions as $post_type => $definition ) {
            $orderby = isset( $definition['orderby'] ) ? $definition['orderby'] : 'menu_order';
            $order   = isset( $definition['order'] ) ? $definition['order'] : 'ASC';

            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => -1,
                'post_status'    => 'any',
                'orderby'        => $orderby,
                'order'          => $order,
                'no_found_rows'  => true,
            ) );

            $posts = array();

            foreach ( $query->posts as $post ) {
                $entry = array(
                    'source_post_id' => $post->ID,
                    'title'          => $post->post_title,
                    'post_name'      => $post->post_name,
                    'post_content'   => $post->post_content,
                    'post_excerpt'   => $post->post_excerpt,
                    'status'         => $post->post_status,
                    'menu_order'     => $post->menu_order,
                );

                // Featured image.
                $thumb_id = get_post_thumbnail_id( $post->ID );
                $entry['featured_image'] = $thumb_id ? $this->attachment_to_portable( $thumb_id ) : null;

                // Meta.
                $entry['meta'] = $this->resolve_cpt_meta_for_export( $post->ID, $definition );

                // Taxonomies.
                $entry['taxonomies'] = array();
                $taxonomies = isset( $definition['taxonomies'] ) ? $definition['taxonomies'] : array();
                foreach ( $taxonomies as $taxonomy ) {
                    $terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'all' ) );
                    if ( is_wp_error( $terms ) ) {
                        continue;
                    }
                    $entry['taxonomies'][ $taxonomy ] = array();
                    foreach ( $terms as $term ) {
                        $entry['taxonomies'][ $taxonomy ][] = array(
                            'slug' => $term->slug,
                            'name' => $term->name,
                        );
                    }
                }

                $posts[] = $entry;
            }

            wp_reset_postdata();

            $posts = apply_filters( 'bookings_and_flights_export_cpt_posts', $posts, $post_type );
            $cpt_data[ $post_type ] = $posts;
        }

        return $cpt_data;
    }

    // =========================================================================
    // IMPORT
    // =========================================================================

    /**
     * Handle the import action.
     */
    private function handle_import() {
        check_admin_referer( 'bookings_and_flights_import', 'bookings_and_flights_import_nonce' );

        $this->warnings = array();
        $success_count  = 0;

        // --- Validate file upload ---
        if (
            ! isset( $_FILES['import_file']['error'] )
            || UPLOAD_ERR_OK !== (int) $_FILES['import_file']['error']
            || empty( $_FILES['import_file']['tmp_name'] )
            || ! is_uploaded_file( $_FILES['import_file']['tmp_name'] )
            || ! is_readable( $_FILES['import_file']['tmp_name'] )
        ) {
            $this->set_transient_notice( 'error', __( 'Upload failed. Please try again.', 'bookings_and_flights-content-manager' ) );
            $this->redirect_back();
            return;
        }

        $file = $_FILES['import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

        // --- Enforce import size limit (filterable, defaults to 10 MB) ---
        $max_import_bytes = min(
            wp_max_upload_size(),
            (int) apply_filters( 'bookings_and_flights_max_import_bytes', 10 * MB_IN_BYTES )
        );

        if ( $file['size'] > $max_import_bytes ) {
            $this->set_transient_notice( 'error', __( 'The uploaded file exceeds the maximum upload size.', 'bookings_and_flights-content-manager' ) );
            $this->redirect_back();
            return;
        }

        $filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
        if ( 'json' !== $filetype['ext'] && 'application/json' !== $filetype['type'] ) {
            // wp_check_filetype_and_ext may not recognize .json on all hosts; fall back to extension check.
            $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
            if ( 'json' !== $ext ) {
                $this->set_transient_notice( 'error', __( 'Invalid file type. Please upload a .json file.', 'bookings_and_flights-content-manager' ) );
                $this->redirect_back();
                return;
            }
        }

        $raw  = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $data = json_decode( $raw, true );

        if ( null === $data ) {
            $this->set_transient_notice(
                'error',
                sprintf(
                    /* translators: %s: JSON error message */
                    __( 'Invalid JSON: %s', 'bookings_and_flights-content-manager' ),
                    json_last_error_msg()
                )
            );
            $this->redirect_back();
            return;
        }

        // --- Validate top-level structure ---
        if (
            ! is_array( $data )
            || ! isset( $data['plugin'], $data['schema_version'] )
            || ( isset( $data['pages'] ) && ! is_array( $data['pages'] ) )
            || ( isset( $data['global_options'] ) && ! is_array( $data['global_options'] ) )
            || ( isset( $data['custom_post_types'] ) && ! is_array( $data['custom_post_types'] ) )
        ) {
            $this->set_transient_notice( 'error', __( 'Malformed export file: unexpected top-level structure.', 'bookings_and_flights-content-manager' ) );
            $this->redirect_back();
            return;
        }

        // --- Validate plugin slug ---
        if ( 'bookings_and_flights-content-manager' !== $data['plugin'] ) {
            $this->set_transient_notice( 'error', __( 'This file was not exported by this plugin.', 'bookings_and_flights-content-manager' ) );
            $this->redirect_back();
            return;
        }

        // --- Schema version check ---
        $schema = isset( $data['schema_version'] ) ? (int) $data['schema_version'] : 0;

        if ( $schema > self::SCHEMA_VERSION ) {
            $this->set_transient_notice(
                'error',
                sprintf(
                    /* translators: %1$d: file schema version, %2$d: current schema version */
                    __( 'This export file uses schema version %1$d, but this plugin only supports up to version %2$d. Please update the plugin.', 'bookings_and_flights-content-manager' ),
                    $schema,
                    self::SCHEMA_VERSION
                )
            );
            $this->redirect_back();
            return;
        }

        if ( $schema < self::SCHEMA_VERSION ) {
            $data = apply_filters( 'bookings_and_flights_import_migrate', $data, $schema );
        }

        // --- Load media functions ---
        if ( ! function_exists( 'media_handle_sideload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
        }

        // --- URL rewriting setup ---
        $source_url = isset( $data['site_url'] ) ? $data['site_url'] : '';
        $target_url = home_url();

        // --- Enforce soft complexity limits (filterable) ---
        $max_pages     = (int) apply_filters( 'bookings_and_flights_import_max_pages', 200 );
        $max_cpt_items = (int) apply_filters( 'bookings_and_flights_import_max_cpt_items', 500 );

        if ( ! empty( $data['pages'] ) && count( $data['pages'] ) > $max_pages ) {
            $data['pages'] = array_slice( $data['pages'], 0, $max_pages, true );
            $this->warnings[] = sprintf(
                /* translators: %d: max pages limit */
                __( 'Pages truncated to %d items (import limit).', 'bookings_and_flights-content-manager' ),
                $max_pages
            );
        }

        if ( ! empty( $data['custom_post_types'] ) ) {
            foreach ( $data['custom_post_types'] as $pt => $items ) {
                if ( is_array( $items ) && count( $items ) > $max_cpt_items ) {
                    $data['custom_post_types'][ $pt ] = array_slice( $items, 0, $max_cpt_items );
                    $this->warnings[] = sprintf(
                        /* translators: %1$s: post type, %2$d: max items limit */
                        __( 'CPT "%1$s" truncated to %2$d items (import limit).', 'bookings_and_flights-content-manager' ),
                        $pt,
                        $max_cpt_items
                    );
                }
            }
        }

        // --- Step 1: Global options ---
        if ( ! empty( $data['global_options'] ) ) {
            $success_count += $this->import_global_options( $data['global_options'], $source_url, $target_url );
        }

        // --- Step 2: Pages ---
        if ( ! empty( $data['pages'] ) ) {
            $success_count += $this->import_pages( $data['pages'], $source_url, $target_url );
        }

        // --- Step 3: CPTs ---
        if ( ! empty( $data['custom_post_types'] ) ) {
            $success_count += $this->import_custom_post_types( $data['custom_post_types'], $source_url, $target_url );
        }

        // --- Step 4: PRG redirect ---
        $message = sprintf(
            /* translators: %d: number of items imported */
            __( 'Import complete. %d item(s) processed successfully.', 'bookings_and_flights-content-manager' ),
            $success_count
        );

        if ( ! empty( $this->warnings ) ) {
            $message .= "\n" . implode( "\n", $this->warnings );
        }

        $type = empty( $this->warnings ) ? 'success' : 'warning';
        $this->set_transient_notice( $type, $message );
        $this->redirect_back();
    }

    /**
     * Import global options.
     *
     * @param array  $options    Exported options data.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     * @return int Number of items processed.
     */
    private function import_global_options( $options, $source_url, $target_url ) {
        $fields    = Bookings_And_Flights_Options::get_fields();
        $sanitized = array();

        foreach ( $fields as $key => $field ) {
            if ( ! array_key_exists( $key, $options ) ) {
                continue;
            }

            $value = $options[ $key ];
            $type  = isset( $field['type'] ) ? $field['type'] : 'text';

            if ( 'image' === $type ) {
                $value = $this->resolve_portable_image( $value, 0 );
            } elseif ( 'gallery' === $type ) {
                $value = $this->resolve_portable_gallery( $value, 0 );
            } else {
                $value = $this->rewrite_urls( $value, $source_url, $target_url );
            }

            $sanitized[ $key ] = Bookings_And_Flights_Sanitizer::sanitize( $value, $type, $field );
        }

        update_option( Bookings_And_Flights_Options::OPTION_NAME, $sanitized );

        return 1;
    }

    /**
     * Import pages.
     *
     * @param array  $pages      Exported pages data.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     * @return int Number of pages processed.
     */
    private function import_pages( $pages, $source_url, $target_url ) {
        $fields_registry = Bookings_And_Flights_Fields::get_instance();
        $count = 0;

        foreach ( $pages as $page_key => $page_data ) {
            $template  = isset( $page_data['template'] ) ? $page_data['template'] : '';
            $post_name = isset( $page_data['post_name'] ) ? $page_data['post_name'] : '';

            if ( empty( $template ) ) {
                $this->warnings[] = sprintf(
                    /* translators: %s: page key */
                    __( 'Skipped page "%s": no template specified.', 'bookings_and_flights-content-manager' ),
                    $page_key
                );
                continue;
            }

            // Find page by template + slug.
            $page_id = $this->find_page_by_template_and_slug( $template, $post_name );

            if ( ! $page_id ) {
                $this->warnings[] = sprintf(
                    /* translators: %1$s: template, %2$s: slug */
                    __( 'Skipped page: no page found with template "%1$s" and slug "%2$s".', 'bookings_and_flights-content-manager' ),
                    $template,
                    $post_name
                );
                continue;
            }

            $fields = $fields_registry->get_fields( $template );

            if ( empty( $page_data['fields'] ) ) {
                continue;
            }

            foreach ( $page_data['fields'] as $key => $value ) {
                if ( ! isset( $fields[ $key ] ) ) {
                    continue;
                }

                $field = $fields[ $key ];
                $type  = isset( $field['type'] ) ? $field['type'] : 'text';

                $value = $this->resolve_media_for_import( $value, $field );
                $value = $this->rewrite_urls_for_field( $value, $type, $source_url, $target_url );
                $value = Bookings_And_Flights_Sanitizer::sanitize( $value, $type, $field );

                update_post_meta( $page_id, Bookings_And_Flights_Config::meta_key( $key ), $value );
            }

            $count++;
        }

        return $count;
    }

    /**
     * Import CPTs.
     *
     * @param array  $cpt_data   Exported CPT data.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     * @return int Number of posts processed.
     */
    private function import_custom_post_types( $cpt_data, $source_url, $target_url ) {
        $definitions = Bookings_And_Flights_Config::get_cpt_definitions();
        $count = 0;

        foreach ( $cpt_data as $post_type => $posts ) {
            if ( ! isset( $definitions[ $post_type ] ) ) {
                $this->warnings[] = sprintf(
                    /* translators: %s: post type */
                    __( 'Skipped CPT "%s": not declared in config.', 'bookings_and_flights-content-manager' ),
                    $post_type
                );
                continue;
            }

            $definition = $definitions[ $post_type ];
            $label      = isset( $definition['label'] ) ? $definition['label'] : $post_type;

            foreach ( $posts as $post_data ) {
                $source_id = isset( $post_data['source_post_id'] ) ? (int) $post_data['source_post_id'] : 0;
                $slug      = isset( $post_data['post_name'] ) ? $post_data['post_name'] : '';
                $title     = isset( $post_data['title'] ) ? $post_data['title'] : '';

                // Match cascade: source_id → slug → title.
                $local_id = $this->find_cpt_post( $post_type, $source_id, $slug, $title );

                $post_content = $this->rewrite_urls(
                    isset( $post_data['post_content'] ) ? $post_data['post_content'] : '',
                    $source_url,
                    $target_url
                );
                $post_excerpt = $this->rewrite_urls(
                    isset( $post_data['post_excerpt'] ) ? $post_data['post_excerpt'] : '',
                    $source_url,
                    $target_url
                );
                $post_status = isset( $post_data['status'] ) ? $post_data['status'] : 'publish';

                // Optional strict import mode (default off). Enable via filter to sanitize
                // CPT core fields from untrusted export files.
                if ( apply_filters( 'bookings_and_flights_import_strict_mode', false ) ) {
                    $allowed_statuses = apply_filters(
                        'bookings_and_flights_import_allowed_statuses',
                        array( 'publish', 'draft', 'private', 'pending' )
                    );
                    if ( ! in_array( $post_status, $allowed_statuses, true ) ) {
                        $post_status = 'draft';
                    }
                    $title        = sanitize_text_field( $title );
                    $slug         = sanitize_title( $slug );
                    $post_content = wp_kses_post( $post_content );
                    $post_excerpt = sanitize_textarea_field( $post_excerpt );
                }

                $post_arr = array(
                    'post_type'    => $post_type,
                    'post_title'   => $title,
                    'post_name'    => $slug,
                    'post_content' => $post_content,
                    'post_excerpt' => $post_excerpt,
                    'post_status'  => $post_status,
                    'menu_order'   => isset( $post_data['menu_order'] ) ? (int) $post_data['menu_order'] : 0,
                );

                if ( $local_id ) {
                    $post_arr['ID'] = $local_id;
                    wp_update_post( $post_arr );
                } else {
                    $local_id = wp_insert_post( $post_arr );
                    if ( is_wp_error( $local_id ) ) {
                        $this->warnings[] = sprintf(
                            /* translators: %1$s: label, %2$s: title */
                            __( 'Failed to create %1$s post "%2$s".', 'bookings_and_flights-content-manager' ),
                            $label,
                            $title
                        );
                        continue;
                    }
                }

                // Store source ID for future re-imports.
                update_post_meta( $local_id, '_source_post_id', $source_id );

                // Featured image.
                if ( ! empty( $post_data['featured_image'] ) ) {
                    $thumb_id = $this->resolve_portable_image( $post_data['featured_image'], $local_id );
                    if ( $thumb_id ) {
                        set_post_thumbnail( $local_id, $thumb_id );
                    }
                } else {
                    delete_post_thumbnail( $local_id );
                }

                // Meta.
                $this->import_cpt_meta( $local_id, $post_data, $definition, $source_url, $target_url );

                // Taxonomies.
                $this->import_cpt_taxonomies( $local_id, $post_data, $definition );

                $count++;
            }
        }

        return $count;
    }

    /**
     * Import CPT meta for a single post.
     *
     * @param int    $post_id    Local post ID.
     * @param array  $post_data  Exported post data.
     * @param array  $definition CPT definition from config.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     */
    private function import_cpt_meta( $post_id, $post_data, $definition, $source_url, $target_url ) {
        if ( empty( $post_data['meta'] ) || ! is_array( $post_data['meta'] ) ) {
            return;
        }

        $meta_fields  = isset( $definition['meta_fields'] ) ? $definition['meta_fields'] : array();
        $image_keys   = isset( $definition['image_keys'] ) ? $definition['image_keys'] : array();
        $gallery_keys = isset( $definition['gallery_keys'] ) ? $definition['gallery_keys'] : array();
        $meta_prefix  = isset( $definition['meta_prefix'] ) ? $definition['meta_prefix'] : '';

        // Build complete allowlist.
        $allowlist = array_keys( $meta_fields );
        $allowlist = array_merge( $allowlist, $image_keys, $gallery_keys );

        foreach ( $post_data['meta'] as $key => $value ) {
            // Schema drift detection: warn on prefixed keys not in allowlist.
            if ( ! empty( $meta_prefix ) && 0 === strpos( $key, $meta_prefix ) && ! in_array( $key, $allowlist, true ) ) {
                $this->warnings[] = sprintf(
                    /* translators: %1$s: meta key, %2$s: post title */
                    __( 'Skipped undeclared meta key "%1$s" for post "%2$s" (schema drift).', 'bookings_and_flights-content-manager' ),
                    $key,
                    get_the_title( $post_id )
                );
                continue;
            }

            // Only import declared keys.
            if ( ! in_array( $key, $allowlist, true ) ) {
                continue;
            }

            // Resolve media.
            if ( in_array( $key, $image_keys, true ) ) {
                $value = $this->resolve_portable_image( $value, $post_id );
            } elseif ( in_array( $key, $gallery_keys, true ) ) {
                $value = $this->resolve_portable_gallery( $value, $post_id );
            } else {
                // Rewrite URLs in string meta.
                $type = isset( $meta_fields[ $key ] ) ? $meta_fields[ $key ] : 'text';
                $value = $this->rewrite_urls_for_field( $value, $type, $source_url, $target_url );

                // Sanitize.
                $value = Bookings_And_Flights_Sanitizer::sanitize( $value, $type );
            }

            $value = apply_filters( 'bookings_and_flights_import_pre_save_meta', $value, $key, $definition, $post_id );

            update_post_meta( $post_id, $key, $value );
        }
    }

    /**
     * Import CPT taxonomies for a single post.
     *
     * @param int   $post_id   Local post ID.
     * @param array $post_data Exported post data.
     * @param array $definition CPT definition from config.
     */
    private function import_cpt_taxonomies( $post_id, $post_data, $definition ) {
        if ( empty( $post_data['taxonomies'] ) || ! is_array( $post_data['taxonomies'] ) ) {
            return;
        }

        $declared_taxonomies = isset( $definition['taxonomies'] ) ? $definition['taxonomies'] : array();

        foreach ( $post_data['taxonomies'] as $taxonomy => $terms ) {
            if ( ! in_array( $taxonomy, $declared_taxonomies, true ) ) {
                continue;
            }

            if ( ! taxonomy_exists( $taxonomy ) ) {
                $this->warnings[] = sprintf(
                    /* translators: %s: taxonomy */
                    __( 'Taxonomy "%s" does not exist on this site.', 'bookings_and_flights-content-manager' ),
                    $taxonomy
                );
                continue;
            }

            $term_ids = array();

            foreach ( $terms as $term_data ) {
                $slug = isset( $term_data['slug'] ) ? $term_data['slug'] : '';
                $name = isset( $term_data['name'] ) ? $term_data['name'] : $slug;

                $existing = get_term_by( 'slug', $slug, $taxonomy );

                if ( $existing ) {
                    $term_ids[] = (int) $existing->term_id;
                } else {
                    $result = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
                    if ( is_wp_error( $result ) ) {
                        $this->warnings[] = sprintf(
                            /* translators: %1$s: term name, %2$s: taxonomy, %3$s: error */
                            __( 'Failed to create term "%1$s" in taxonomy "%2$s": %3$s', 'bookings_and_flights-content-manager' ),
                            $name,
                            $taxonomy,
                            $result->get_error_message()
                        );
                        continue;
                    }
                    $term_ids[] = (int) $result['term_id'];
                }
            }

            if ( ! empty( $term_ids ) ) {
                wp_set_object_terms( $post_id, $term_ids, $taxonomy );
            }
        }
    }

    // =========================================================================
    // MEDIA HELPERS
    // =========================================================================

    /**
     * Convert an attachment ID to a portable payload for export.
     *
     * @param int $attachment_id Attachment ID.
     * @return array|null Portable payload or null.
     */
    private function attachment_to_portable( $attachment_id ) {
        $attachment_id = absint( $attachment_id );
        if ( ! $attachment_id ) {
            return null;
        }

        $url = wp_get_attachment_url( $attachment_id );
        if ( ! $url ) {
            return null;
        }

        return array(
            '_attachment_url' => $url,
            '_alt'            => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
            'id'              => $attachment_id,
        );
    }

    /**
     * Resolve media fields for export based on field type.
     *
     * @param mixed $value Raw meta value.
     * @param array $field Field definition.
     * @return mixed Resolved value.
     */
    private function resolve_media_for_export( $value, $field ) {
        $type = isset( $field['type'] ) ? $field['type'] : 'text';

        switch ( $type ) {
            case 'image':
                return $this->attachment_to_portable( absint( $value ) );

            case 'gallery':
                $ids = $value;
                if ( is_string( $ids ) && ! empty( $ids ) ) {
                    $ids = array_map( 'absint', explode( ',', $ids ) );
                }
                if ( ! is_array( $ids ) ) {
                    return $value;
                }
                $portable = array();
                foreach ( $ids as $id ) {
                    $portable[] = $this->attachment_to_portable( absint( $id ) );
                }
                return array(
                    '_attachment_urls' => $portable,
                    'ids'             => array_map( 'absint', $ids ),
                );

            case 'repeater':
                if ( ! is_array( $value ) ) {
                    return $value;
                }
                $subfields = isset( $field['subfields'] ) ? $field['subfields'] : array();
                $resolved  = array();
                foreach ( $value as $row ) {
                    if ( ! is_array( $row ) ) {
                        $resolved[] = $row;
                        continue;
                    }
                    $resolved_row = array();
                    foreach ( $row as $sub_key => $sub_value ) {
                        $sub_field = isset( $subfields[ $sub_key ] ) ? $subfields[ $sub_key ] : array( 'type' => 'text' );
                        $resolved_row[ $sub_key ] = $this->resolve_media_for_export( $sub_value, $sub_field );
                    }
                    $resolved[] = $resolved_row;
                }
                return $resolved;

            default:
                return $value;
        }
    }

    /**
     * Resolve media fields for import based on field type.
     *
     * @param mixed $value Exported value.
     * @param array $field Field definition.
     * @return mixed Resolved value (attachment IDs).
     */
    private function resolve_media_for_import( $value, $field ) {
        $type = isset( $field['type'] ) ? $field['type'] : 'text';

        switch ( $type ) {
            case 'image':
                return $this->resolve_portable_image( $value, 0 );

            case 'gallery':
                return $this->resolve_portable_gallery( $value, 0 );

            case 'repeater':
                if ( ! is_array( $value ) ) {
                    return $value;
                }
                $subfields = isset( $field['subfields'] ) ? $field['subfields'] : array();
                $resolved  = array();
                foreach ( $value as $row ) {
                    if ( ! is_array( $row ) ) {
                        $resolved[] = $row;
                        continue;
                    }
                    $resolved_row = array();
                    foreach ( $row as $sub_key => $sub_value ) {
                        $sub_field = isset( $subfields[ $sub_key ] ) ? $subfields[ $sub_key ] : array( 'type' => 'text' );
                        $resolved_row[ $sub_key ] = $this->resolve_media_for_import( $sub_value, $sub_field );
                    }
                    $resolved[] = $resolved_row;
                }
                return $resolved;

            default:
                return $value;
        }
    }

    /**
     * Resolve a portable image payload back to a local attachment ID.
     *
     * @param mixed $value   Portable payload or raw value.
     * @param int   $post_id Post to attach sideloaded image to.
     * @return int Attachment ID or 0.
     */
    private function resolve_portable_image( $value, $post_id ) {
        if ( ! is_array( $value ) || empty( $value['_attachment_url'] ) ) {
            return is_numeric( $value ) ? absint( $value ) : 0;
        }

        $url = $value['_attachment_url'];
        $alt = isset( $value['_alt'] ) ? $value['_alt'] : '';

        return $this->sideload_image( $url, $post_id, $alt );
    }

    /**
     * Resolve a portable gallery payload back to local attachment IDs.
     *
     * @param mixed $value   Portable gallery payload or raw value.
     * @param int   $post_id Post to attach sideloaded images to.
     * @return string Comma-separated attachment IDs.
     */
    private function resolve_portable_gallery( $value, $post_id ) {
        if ( ! is_array( $value ) || empty( $value['_attachment_urls'] ) ) {
            // Raw value — could be comma-separated IDs.
            if ( is_string( $value ) ) {
                return $value;
            }
            if ( is_array( $value ) ) {
                return implode( ',', array_map( 'absint', $value ) );
            }
            return '';
        }

        $ids = array();
        foreach ( $value['_attachment_urls'] as $portable ) {
            $id = $this->resolve_portable_image( $portable, $post_id );
            if ( $id ) {
                $ids[] = $id;
            }
        }

        return implode( ',', $ids );
    }

    /**
     * Sideload an image from URL, with caching and SSRF protection.
     *
     * @param string $url     Image URL.
     * @param int    $post_id Post to attach to.
     * @param string $alt     Alt text to restore.
     * @return int Attachment ID or 0.
     */
    private function sideload_image( $url, $post_id, $alt = '' ) {
        if ( empty( $url ) ) {
            return 0;
        }

        // Check cache.
        if ( isset( $this->sideload_cache[ $url ] ) ) {
            return $this->sideload_cache[ $url ];
        }

        // SSRF protection: only http/https.
        $scheme = wp_parse_url( $url, PHP_URL_SCHEME );
        if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
            $this->warnings[] = sprintf(
                /* translators: %s: URL */
                __( 'Skipped image with invalid scheme: %s', 'bookings_and_flights-content-manager' ),
                $url
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        if ( ! wp_http_validate_url( $url ) ) {
            $this->warnings[] = sprintf(
                /* translators: %s: URL */
                __( 'Skipped image that failed URL validation: %s', 'bookings_and_flights-content-manager' ),
                $url
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        // Local URL detection: skip sideload if already on this site.
        $local_id = attachment_url_to_postid( $url );
        if ( $local_id ) {
            $this->sideload_cache[ $url ] = $local_id;
            // Still update alt text if provided.
            if ( '' !== $alt ) {
                update_post_meta( $local_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
            }
            return $local_id;
        }

        // Download to temp file.
        $tmp = download_url( $url );
        if ( is_wp_error( $tmp ) ) {
            $this->warnings[] = sprintf(
                /* translators: %1$s: URL, %2$s: error */
                __( 'Failed to download image "%1$s": %2$s', 'bookings_and_flights-content-manager' ),
                $url,
                $tmp->get_error_message()
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        $file_array = array(
            'name'     => basename( wp_parse_url( $url, PHP_URL_PATH ) ),
            'tmp_name' => $tmp,
        );

        $attachment_id = media_handle_sideload( $file_array, $post_id );

        // Clean up temp file if sideload failed.
        if ( is_wp_error( $attachment_id ) ) {
            if ( file_exists( $tmp ) ) {
                unlink( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
            }
            $this->warnings[] = sprintf(
                /* translators: %1$s: URL, %2$s: error */
                __( 'Failed to sideload image "%1$s": %2$s', 'bookings_and_flights-content-manager' ),
                $url,
                $attachment_id->get_error_message()
            );
            $this->sideload_cache[ $url ] = 0;
            return 0;
        }

        // Restore alt text.
        if ( '' !== $alt ) {
            update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
        }

        $this->sideload_cache[ $url ] = $attachment_id;
        return $attachment_id;
    }

    /**
     * Resolve CPT meta for export — handles image_keys, gallery_keys, and plain meta.
     *
     * @param int   $post_id    Post ID.
     * @param array $definition CPT definition.
     * @return array Exported meta.
     */
    private function resolve_cpt_meta_for_export( $post_id, $definition ) {
        $meta_fields  = isset( $definition['meta_fields'] ) ? $definition['meta_fields'] : array();
        $image_keys   = isset( $definition['image_keys'] ) ? $definition['image_keys'] : array();
        $gallery_keys = isset( $definition['gallery_keys'] ) ? $definition['gallery_keys'] : array();
        $meta_prefix  = isset( $definition['meta_prefix'] ) ? $definition['meta_prefix'] : '';

        $exported = array();

        // Export declared meta_fields.
        foreach ( $meta_fields as $key => $type ) {
            $value = get_post_meta( $post_id, $key, true );
            $value = maybe_unserialize( $value );
            $exported[ $key ] = $value;
        }

        // Export image keys as portable payloads.
        foreach ( $image_keys as $key ) {
            $value = get_post_meta( $post_id, $key, true );
            $exported[ $key ] = $this->attachment_to_portable( absint( $value ) );
        }

        // Export gallery keys as portable payloads.
        foreach ( $gallery_keys as $key ) {
            $value = get_post_meta( $post_id, $key, true );
            $value = maybe_unserialize( $value );
            $ids   = $value;
            if ( is_string( $ids ) && ! empty( $ids ) ) {
                $ids = array_map( 'absint', explode( ',', $ids ) );
            }
            if ( is_array( $ids ) ) {
                $portable = array();
                foreach ( $ids as $id ) {
                    $portable[] = $this->attachment_to_portable( absint( $id ) );
                }
                $exported[ $key ] = array(
                    '_attachment_urls' => $portable,
                    'ids'             => array_map( 'absint', $ids ),
                );
            } else {
                $exported[ $key ] = $value;
            }
        }

        return $exported;
    }

    // =========================================================================
    // LOOKUP HELPERS
    // =========================================================================

    /**
     * Find a page by template and slug.
     *
     * @param string $template  Page template filename.
     * @param string $post_name Post slug.
     * @return int|false Post ID or false.
     */
    private function find_page_by_template_and_slug( $template, $post_name ) {
        $args = array(
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'post_status'    => 'any',
            'no_found_rows'  => true,
            'meta_key'       => '_wp_page_template',
            'meta_value'     => $template,
            'fields'         => 'ids',
        );

        if ( ! empty( $post_name ) ) {
            $args['name'] = $post_name;
        }

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            return $query->posts[0];
        }

        return false;
    }

    /**
     * Find a CPT post by cascade: source_id meta → slug → title.
     *
     * @param string $post_type CPT slug.
     * @param int    $source_id Source post ID from export.
     * @param string $slug      Post slug.
     * @param string $title     Post title.
     * @return int|false Post ID or false.
     */
    private function find_cpt_post( $post_type, $source_id, $slug, $title ) {
        // 1. Match by _source_post_id meta.
        if ( $source_id ) {
            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'no_found_rows'  => true,
                'meta_key'       => '_source_post_id',
                'meta_value'     => $source_id,
                'fields'         => 'ids',
            ) );

            if ( $query->have_posts() ) {
                return $query->posts[0];
            }
        }

        // 2. Match by slug.
        if ( ! empty( $slug ) ) {
            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'no_found_rows'  => true,
                'name'           => $slug,
                'fields'         => 'ids',
            ) );

            if ( $query->have_posts() ) {
                return $query->posts[0];
            }
        }

        // 3. Match by title (last resort).
        if ( ! empty( $title ) ) {
            $query = new WP_Query( array(
                'post_type'      => $post_type,
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'no_found_rows'  => true,
                'title'          => $title,
                'fields'         => 'ids',
            ) );

            if ( $query->have_posts() ) {
                return $query->posts[0];
            }
        }

        return false;
    }

    // =========================================================================
    // URL REWRITING
    // =========================================================================

    /**
     * Rewrite URLs in a value — recursive for arrays, string-only.
     *
     * Only runs if source and target hosts differ.
     *
     * @param mixed  $value      Value to rewrite.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     * @return mixed Rewritten value.
     */
    private function rewrite_urls( $value, $source_url, $target_url ) {
        if ( empty( $source_url ) || empty( $target_url ) ) {
            return $value;
        }

        $source_host = wp_parse_url( $source_url, PHP_URL_HOST );
        $target_host = wp_parse_url( $target_url, PHP_URL_HOST );

        if ( $source_host === $target_host ) {
            return $value;
        }

        return $this->rewrite_urls_recursive( $value, $source_url, $target_url );
    }

    /**
     * Recursively rewrite URLs in a value.
     *
     * @param mixed  $value      Value to process.
     * @param string $source_url Full source origin (scheme + host).
     * @param string $target_url Full target origin (scheme + host).
     * @return mixed
     */
    private function rewrite_urls_recursive( $value, $source_url, $target_url ) {
        if ( is_string( $value ) ) {
            // Replace full origin.
            $source_origin = $this->get_origin( $source_url );
            $target_origin = $this->get_origin( $target_url );

            if ( $source_origin && $target_origin ) {
                $value = str_replace( $source_origin, $target_origin, $value );
            }

            return $value;
        }

        if ( is_array( $value ) ) {
            foreach ( $value as $k => $v ) {
                $value[ $k ] = $this->rewrite_urls_recursive( $v, $source_url, $target_url );
            }
            return $value;
        }

        // Integers, booleans, null — untouched.
        return $value;
    }

    /**
     * Extract origin (scheme + host) from a URL.
     *
     * @param string $url Full URL.
     * @return string|false Origin string or false.
     */
    private function get_origin( $url ) {
        $parts = wp_parse_url( $url );
        if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
            return false;
        }

        $origin = $parts['scheme'] . '://' . $parts['host'];
        if ( ! empty( $parts['port'] ) ) {
            $origin .= ':' . $parts['port'];
        }

        return $origin;
    }

    /**
     * Rewrite URLs only for text-like field types.
     *
     * @param mixed  $value      Field value.
     * @param string $type       Field type.
     * @param string $source_url Source site URL.
     * @param string $target_url Target site URL.
     * @return mixed
     */
    private function rewrite_urls_for_field( $value, $type, $source_url, $target_url ) {
        $rewritable_types = array( 'text', 'textarea', 'wysiwyg', 'url' );

        if ( in_array( $type, $rewritable_types, true ) ) {
            return $this->rewrite_urls( $value, $source_url, $target_url );
        }

        // Link field: rewrite only the 'url' subfield.
        if ( 'link' === $type && is_array( $value ) && isset( $value['url'] ) ) {
            $value['url'] = $this->rewrite_urls( $value['url'], $source_url, $target_url );
            return $value;
        }

        return $value;
    }

    // =========================================================================
    // ADMIN NOTICES
    // =========================================================================

    /**
     * Store a notice in a transient for display after redirect.
     *
     * @param string $type    Notice type: success, error, warning.
     * @param string $message Notice message.
     */
    private function set_transient_notice( $type, $message ) {
        set_transient(
            'bookings_and_flights_export_import_notice',
            array(
                'type'    => $type,
                'message' => $message,
            ),
            60
        );
    }

    /**
     * Display admin notices from transient.
     */
    public function display_admin_notices() {
        $notice = get_transient( 'bookings_and_flights_export_import_notice' );

        if ( ! $notice ) {
            return;
        }

        delete_transient( 'bookings_and_flights_export_import_notice' );

        $type    = isset( $notice['type'] ) ? $notice['type'] : 'info';
        $message = isset( $notice['message'] ) ? $notice['message'] : '';

        if ( empty( $message ) ) {
            return;
        }

        $class = 'notice notice-' . esc_attr( $type ) . ' is-dismissible';

        // Support multi-line messages (warnings appended).
        $lines = explode( "\n", $message );
        $html  = '<p><strong>' . esc_html( array_shift( $lines ) ) . '</strong></p>';

        if ( ! empty( $lines ) ) {
            $html .= '<ul style="list-style:disc;margin-left:1.5em;">';
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( '' !== $line ) {
                    $html .= '<li>' . esc_html( $line ) . '</li>';
                }
            }
            $html .= '</ul>';
        }

        printf( '<div class="%s">%s</div>', esc_attr( $class ), $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Redirect back to the tools page (PRG pattern).
     */
    private function redirect_back() {
        wp_safe_redirect( admin_url( 'tools.php?page=bookings_and_flights-export-import' ) );
        exit;
    }
}
