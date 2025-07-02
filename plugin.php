<?php
/**
 * Plugin Name:       Editor UI Cleaner
 * Plugin URI:        https://github.com/your-repo/editor-ui-cleaner
 * Description:       Allows administrators to hide specific admin screen UI elements based on user role.
 * Version:           1.0.0
 * Author:            Paul Steele and Gemini
 * Author URI:        https://projecta.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       editor-ui-cleaner
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Include the roles loader.
require_once plugin_dir_path( __FILE__ ) . 'includes/roles-loader.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ui-configurator.php';

// Add the settings page.
function euc_add_settings_page() {
    add_options_page(
        __( 'Editor UI Cleaner', 'editor-ui-cleaner' ),
        __( 'Editor UI Cleaner', 'editor-ui-cleaner' ),
        'manage_options',
        'editor-ui-cleaner',
        'euc_render_settings_page'
    );
}
add_action( 'admin_menu', 'euc_add_settings_page' );

// Render the settings page.
function euc_render_settings_page() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/settings-page.php';
    euc_render_settings_page_content();
}

/**
 * Get the master list of configurable UI elements.
 *
 * @return array
 */
function euc_get_configurable_ui_elements() {
    return [
        'revisions' => [
            'label' => __( 'Revisions', 'editor-ui-cleaner' ),
            'classic' => '#revisionsdiv',
            'block_panel' => 'Revisions',
        ],
        'page_template' => [
            'label' => __( 'Page Attributes (Template, Order)', 'editor-ui-cleaner' ),
            'classic' => '#pageparentdiv',
            'block_panel' => 'Template',
        ],
        'parent' => [
            'label' => __( 'Page Attributes (Parent)', 'editor-ui-cleaner' ),
            'classic' => '#pageparentdiv',
            'block_panel' => 'Parent',
        ],
        'discussion' => [
            'label' => __( 'Discussion (Comments, Trackbacks)', 'editor-ui-cleaner' ),
            'classic' => '#commentstatusdiv, #trackbacksdiv',
            'block_panel' => 'Discussion',
        ],
        'author' => [
            'label' => __( 'Author', 'editor-ui-cleaner' ),
            'classic' => '#authordiv',
            'block_panel' => 'Author',
        ],
        'featured_image' => [
            'label' => __( 'Featured Image', 'editor-ui-cleaner' ),
            'classic' => '#postimagediv',
            'block_panel' => '.editor-post-featured-image',
        ],
        'excerpt' => [
            'label' => __( 'Excerpt', 'editor-ui-cleaner' ),
            'classic' => '#postexcerpt',
            'block_panel' => 'Excerpt',
        ],
        'custom_fields' => [
            'label' => __( 'Custom Fields', 'editor-ui-cleaner' ),
            'classic' => '#postcustom',
            'block_panel' => 'Custom Fields',
        ],
        'slug' => [
            'label' => __( 'Slug (Permalink)', 'editor-ui-cleaner' ),
            'classic' => '#slugdiv',
            'block_panel' => 'Slug',
        ],
        'categories' => [
            'label' => __( 'Categories', 'editor-ui-cleaner' ),
            'classic' => '#categorydiv',
            'block_panel' => 'Categories',
        ],
        'tags' => [
            'label' => __( 'Tags', 'editor-ui-cleaner' ),
            'classic' => '#tagsdiv-post_tag',
            'block_panel' => 'Tags',
        ],
        'screen_options' => [
            'label' => __( 'Screen Options Tab', 'editor-ui-cleaner' ),
            'classic' => '#screen-options-link-wrap',
            'block_panel' => '',
        ],
        'help_tab' => [
            'label' => __( 'Help Tab', 'editor-ui-cleaner' ),
            'classic' => '#contextual-help-link-wrap',
            'block_panel' => '',
        ],
    ];
}

/**
 * Check if the block editor is used for a given post type.
 *
 * @param string $post_type The post type slug.
 * @return bool True if the block editor is used, false otherwise.
 */
function euc_use_block_editor_for_post_type( $post_type ) {
    if ( function_exists( 'use_block_editor_for_post_type' ) ) {
        return use_block_editor_for_post_type( $post_type );
    }
    // Fallback for older WordPress versions or if Gutenberg is not active.
    return false;
}

/**
 * Register settings, sections, and fields.
 */
function euc_register_settings() {
    // Register the main setting.
    register_setting( 'euc_settings', 'euc_settings', 'euc_sanitize_settings' );

    // Get roles and post types.
    $roles = euc_get_all_roles();
    $post_types = euc_get_all_post_types();

    foreach ( $roles as $role_id => $role ) {
        // Don't add settings for administrators.
        if ( 'administrator' === $role_id ) {
            continue;
        }

        add_settings_section(
            'euc_section_' . $role_id,
            sprintf( __( 'Settings for %s', 'editor-ui-cleaner' ), $role['name'] ),
            'euc_render_section_callback',
            'editor-ui-cleaner'
        );

        foreach ( $post_types as $post_type_id => $post_type ) {
            add_settings_field(
                'euc_field_' . $role_id . '_' . $post_type_id,
                $post_type->label,
                'euc_render_field_callback',
                'editor-ui-cleaner',
                'euc_section_' . $role_id,
                [
                    'role_id' => $role_id,
                    'post_type_id' => $post_type_id,
                ]
            );
        }
    }
}
add_action( 'admin_init', 'euc_register_settings' );

/**
 * Render a settings section.
 *
 * @param array $args
 */
function euc_render_section_callback( $args ) {
    // You can add a description here if you want.
}

/**
 * Render a settings field (the checkboxes).
 *
 * @param array $args
 */
function euc_render_field_callback( $args ) {
    $options = get_option( 'euc_settings' );
    $role_id = $args['role_id'];
    $post_type_id = $args['post_type_id'];
    $elements = euc_get_configurable_ui_elements();

    foreach ( $elements as $element_id => $element ) {
        $checked = isset( $options[$role_id][$post_type_id] ) && in_array( $element_id, $options[$role_id][$post_type_id] );
        echo '<label style="display: block; margin-bottom: 5px;">';
        echo '<input type="checkbox" name="euc_settings[' . esc_attr( $role_id ) . '][' . esc_attr( $post_type_id ) . '][]" value="' . esc_attr( $element_id ) . '" ' . checked( $checked, true, false ) . ' /> ';
        echo esc_html( $element['label'] );
        echo '</label>';
    }

    // Add a field for custom CSS selectors.
    $custom_css = isset( $options[$role_id][$post_type_id]['custom_css'] ) ? $options[$role_id][$post_type_id]['custom_css'] : '';
    echo '<p><strong>' . esc_html__( 'Custom CSS Selectors (one per line):', 'editor-ui-cleaner' ) . '</strong></p>';
    echo '<textarea name="euc_settings[' . esc_attr( $role_id ) . '][' . esc_attr( $post_type_id ) . '][custom_css]" rows="5" cols="50" class="large-text code">' . esc_textarea( $custom_css ) . '</textarea>';
}

/**
 * Sanitize the settings array before saving.
 *
 * @param array $input
 * @return array
 */
function euc_sanitize_settings( $input ) {
    $new_input = [];
    $roles = array_keys( euc_get_all_roles() );
    $post_types = array_keys( euc_get_all_post_types() );
    $elements = array_keys( euc_get_configurable_ui_elements() );

    foreach ( $input as $role_id => $post_types_data ) {
        if ( in_array( $role_id, $roles ) ) {
            foreach ( $post_types_data as $post_type_id => $data ) {
                if ( in_array( $post_type_id, $post_types ) ) {
                    // Sanitize checkboxes.
                    if ( isset( $data ) && is_array( $data ) ) {
                        $new_input[$role_id][$post_type_id] = array_intersect( (array) $data, $elements );
                    } else {
                        $new_input[$role_id][$post_type_id] = [];
                    }

                    // Sanitize custom CSS.
                    if ( isset( $data['custom_css'] ) ) {
                        $new_input[$role_id][$post_type_id]['custom_css'] = sanitize_textarea_field( $data['custom_css'] );
                    }
                }
            }
        }
    }

    return $new_input;
}
