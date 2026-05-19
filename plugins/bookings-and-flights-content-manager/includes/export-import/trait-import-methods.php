<?php
/**
 * Export/import method group.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined( 'ABSPATH' ) || exit;

trait Bookings_And_Flights_Export_Import_Import_Methods {
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
        $existing  = get_option( Bookings_And_Flights_Options::OPTION_NAME, array() );
        $sanitized = is_array( $existing ) ? $existing : array();

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
}
