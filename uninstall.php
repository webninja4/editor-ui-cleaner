<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Editor_UI_Cleaner
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'euc_settings' );
