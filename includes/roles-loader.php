<?php
/**
 * Handles fetching of user roles and post types.
 *
 * @package Editor_UI_Cleaner
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Get all editable roles.
 *
 * @return array An array of role objects.
 */
function euc_get_all_roles() {
    if ( ! function_exists( 'get_editable_roles' ) ) {
        require_once ABSPATH . 'wp-admin/includes/user.php';
    }
    return get_editable_roles();
}

/**
 * Get all public post types.
 *
 * @return array An array of post type names.
 */
function euc_get_all_post_types() {
    $args = array(
        'public'   => true,
    );
    $post_types = get_post_types( $args, 'objects' );

    // Remove attachments from the list as it's not a typical post type to configure.
    unset( $post_types['attachment'] );

    return $post_types;
}
