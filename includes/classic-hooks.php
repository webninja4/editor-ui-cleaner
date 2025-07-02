<?php
/**
 * Contains the logic for hiding elements in the Classic Editor.
 *
 * @package Editor_UI_Cleaner
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Remove classic editor meta boxes.
 *
 * @param string $post_type The current post type.
 * @param array  $elements_to_hide An array of element IDs to hide.
 */
function euc_remove_classic_meta_boxes( $post_type, $elements_to_hide ) {
    error_log( 'EUC Debug: euc_remove_classic_meta_boxes called for post type: ' . $post_type );
    error_log( 'EUC Debug: Elements to hide (meta boxes): ' . print_r( $elements_to_hide, true ) );
    global $wp_meta_boxes;
    $all_elements = euc_get_configurable_ui_elements();

    foreach ( $elements_to_hide as $element_id ) {
        if ( isset( $all_elements[$element_id]['classic'] ) ) {
            $meta_box_ids = array_map( 'trim', explode( ',', $all_elements[$element_id]['classic'] ) );

            foreach ( $meta_box_ids as $meta_box_id ) {
                $meta_box_id = trim( $meta_box_id, '#' ); // Ensure no leading #
                // Iterate over all contexts and priorities to remove the meta box
                // This is a more robust way to ensure removal regardless of where it's registered.
                foreach ( ['normal', 'advanced', 'side'] as $context ) {
                    foreach ( ['high', 'core', 'default', 'low'] as $priority ) {
                        if ( isset( $wp_meta_boxes[$post_type][$context][$priority][$meta_box_id] ) ) {
                            remove_meta_box( $meta_box_id, $post_type, $context );
                        }
                    }
                }
            }
        }
    }
}

/**
 * Hide classic editor elements using CSS.
 *
 * @param array $elements_to_hide An array of element IDs to hide.
 */
function euc_hide_classic_elements_with_css( $elements_to_hide ) {
    error_log( 'EUC Debug: euc_hide_classic_elements_with_css called.' );
    error_log( 'EUC Debug: Elements to hide (CSS): ' . print_r( $elements_to_hide, true ) );
    $all_elements = euc_get_configurable_ui_elements();
    $css_selectors = [];

    foreach ( $elements_to_hide as $element_id ) {
        // Check if the element is configured to be hidden in the classic editor
        // and if it has a 'classic' CSS selector defined.
        if ( isset( $all_elements[$element_id]['classic'] ) ) {
            // Add the classic selector(s) to the list.
            // Split by comma to handle multiple selectors for one element (e.g., discussion).
            $selectors = explode( ',', $all_elements[$element_id]['classic'] );
            foreach ($selectors as $selector) {
                $css_selectors[] = trim($selector); // Trim whitespace
            }
        }
    }

    if ( ! empty( $css_selectors ) ) {
        $css = implode( ', ', $css_selectors ) . ' { display: none !important; }';
        wp_add_inline_style( 'wp-admin', $css );
    }
}

/**
 * Apply custom CSS rules for classic editor.
 *
 * @param string $custom_css The custom CSS rules.
 */
function euc_apply_classic_custom_css( $custom_css ) {
    error_log( 'EUC Debug: euc_apply_classic_custom_css called.' );
    error_log( 'EUC Debug: Custom CSS: ' . $custom_css );
    if ( ! empty( $custom_css ) ) {
        $selectors = array_map( 'trim', explode( "\n", $custom_css ) );
        $selectors = array_filter( $selectors ); // Remove empty lines.
        if ( ! empty( $selectors ) ) {
            $css = implode( ', ', $selectors ) . ' { display: none !important; }';
            wp_add_inline_style( 'wp-admin', $css );
        }
    }
}