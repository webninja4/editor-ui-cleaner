<?php
/**
 * Renders the admin settings page.
 *
 * @package Editor_UI_Cleaner
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Renders the settings page form.
 */
function euc_render_settings_page_content() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p class="description"><?php esc_html_e( 'Check the boxes next to the UI elements you wish to HIDE for the selected roles and post types.', 'editor-ui-cleaner' ); ?></p>
        <form action="options.php" method="post">
            <?php
            // Output security fields for the registered setting "euc_settings"
            settings_fields( 'euc_settings' );
            // Output setting sections and fields
            do_settings_sections( 'editor-ui-cleaner' );
            // Output save settings button
            submit_button( __( 'Save Settings', 'editor-ui-cleaner' ) );
            ?>
        </form>
    </div>
    <?php
}
