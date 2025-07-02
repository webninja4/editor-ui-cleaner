<?php
/**
 * Contains the logic for hiding elements in the Block Editor.
 *
 * @package Editor_UI_Cleaner
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Enqueue scripts and styles to hide block editor elements.
 *
 * @param array $elements_to_hide An array of element IDs to hide.
 * @param string $custom_css Custom CSS selectors to hide.
 */
function euc_enqueue_block_editor_assets( $elements_to_hide, $custom_css = '' ) {
    $all_elements = euc_get_configurable_ui_elements();
    $panels_to_hide = [];

    foreach ( $elements_to_hide as $element_id ) {
        if ( isset( $all_elements[$element_id]['block_panel'] ) ) {
            $panels_to_hide[] = $all_elements[$element_id]['block_panel'];
        }
    }

    if ( ! empty( $panels_to_hide ) || ! empty( $custom_css ) ) {
        wp_enqueue_style(
            'euc-admin-styles',
            plugin_dir_url( __FILE__ ) . '../assets/admin.css',
            [],
            '1.0.0'
        );

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
                'panelsToHide' => $panels_to_hide,
                'customCss'    => $custom_css,
            ]
        );
    }
}