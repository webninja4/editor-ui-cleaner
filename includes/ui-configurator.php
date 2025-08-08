<?php
/**
 * Reads saved settings and applies them to the editor screens.
 *
 * @package Editor_UI_Cleaner
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'classic-hooks.php';
require_once plugin_dir_path( __FILE__ ) . 'block-hooks.php';

/**
 * The main controller function.
 */
function euc_apply_ui_rules() {
    if ( ! is_admin() ) {
        return;
    }
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'EUC Debug: euc_apply_ui_rules called.' );
    }

    $current_user = wp_get_current_user();
    $user_roles = (array) $current_user->roles;
    $settings = get_option( 'euc_settings', [] );
    $screen = get_current_screen();

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'EUC Debug: Current User Roles: ' . print_r( $user_roles, true ) );
        error_log( 'EUC Debug: Current Screen: ' . print_r( $screen, true ) );
        error_log( 'EUC Debug: All Settings: ' . print_r( $settings, true ) );
    }

    // Don't apply rules to the built-in administrator role.
    if ( in_array( 'administrator', (array) $current_user->roles ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'EUC Debug: User is administrator, returning.' );
        }
        return;
    }

    if ( ! $screen || ! isset( $screen->post_type ) || empty( $screen->post_type ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'EUC Debug: Not a post editing screen or post type not set, returning.' );
        }
        return;
    }

    $post_type = $screen->post_type;

    foreach ( $user_roles as $role_id ) {
        if ( isset( $settings[$role_id][$post_type] ) ) {
            $elements_to_hide = $settings[$role_id][$post_type];
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'EUC Debug: Elements to hide for role ' . $role_id . ' and post type ' . $post_type . ': ' . print_r( $elements_to_hide, true ) );
            }
            if ( ! empty( $elements_to_hide ) ) {
                // Always enqueue admin.js and pass all necessary data.
                add_action( 'admin_enqueue_scripts', function() use ( $elements_to_hide, $settings, $role_id, $post_type, $screen ) {
                    $all_configurable_elements = euc_get_configurable_ui_elements();
                    $classic_css_selectors = [];
                    $block_panel_names = [];
                    $custom_css_string = '';

                    foreach ( $elements_to_hide as $element_id ) {
                        if ( isset( $all_configurable_elements[$element_id]['classic'] ) ) {
                            $selectors = explode( ',', $all_configurable_elements[$element_id]['classic'] );
                            foreach ($selectors as $selector) {
                                $classic_css_selectors[] = trim($selector);
                            }
                        }
                        if ( isset( $all_configurable_elements[$element_id]['block_panel'] ) ) {
                            $block_panel_names[] = $all_configurable_elements[$element_id]['block_panel'];
                        }
                    }

                    if ( isset( $settings[$role_id][$post_type]['custom_css'] ) ) {
                        $custom_css_string = $settings[$role_id][$post_type]['custom_css'];
                    }

                    wp_enqueue_script(
                        'euc-admin-script',
                        plugin_dir_url( __FILE__ ) . '../assets/admin.js',
                        [ 'wp-dom-ready', 'wp-edit-post', 'wp-data' ],
                        '1.0.0',
                        true
                    );

                    wp_localize_script(
                        'euc-admin-script',
                        'eucSettings',
                        [
                            'isBlockEditorScreen' => (bool) $screen->is_block_editor,
                            'classicCssSelectors' => $classic_css_selectors,
                            'blockPanelNames'     => $block_panel_names,
                            'customCss'           => $custom_css_string,
                        ]
                    );
                });

                // Apply Classic Editor PHP hooks if the Classic Editor is likely active.
                // This is a heuristic check, as is_block_editor can be true even if Classic Editor is rendered.
                $is_classic_editor_html_heuristic = false;
                if ( function_exists( 'use_block_editor_for_post_type' ) && ! use_block_editor_for_post_type( $post_type ) ) {
                    $is_classic_editor_html_heuristic = true;
                } elseif ( ! empty( $_GET['classic-editor'] ) || ! empty( $_GET['classic-editor__forget'] ) ) {
                    $is_classic_editor_html_heuristic = true;
                } elseif (
                    class_exists( 'Classic_Editor' )
                    && method_exists( 'Classic_Editor', 'is_classic_editor_plugin_active_for_this_post_type' )
                    && call_user_func( [ 'Classic_Editor', 'is_classic_editor_plugin_active_for_this_post_type' ], $post_type )
                ) {
                    $is_classic_editor_html_heuristic = true;
                }

                if ( $is_classic_editor_html_heuristic ) {
                    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                        error_log( 'EUC Debug: Applying Classic Editor PHP hooks based on heuristic.' );
                    }
                    add_action( 'add_meta_boxes', function() use ( $post_type, $elements_to_hide ) {
                        euc_remove_classic_meta_boxes( $post_type, $elements_to_hide );
                    }, 100 );
                    add_action( 'admin_head', function() use ( $elements_to_hide, $settings, $role_id, $post_type ) {
                        euc_hide_classic_elements_with_css( $elements_to_hide );

                        if ( isset( $settings[$role_id][$post_type]['custom_css'] ) && ! empty( $settings[$role_id][$post_type]['custom_css'] ) ) {
                            $custom_css = $settings[$role_id][$post_type]['custom_css'];
                            euc_apply_classic_custom_css( $custom_css );
                        }
                    } );
                }
            }
        }
    }
}
add_action( 'current_screen', 'euc_apply_ui_rules' );