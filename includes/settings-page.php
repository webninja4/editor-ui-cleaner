<?php
/**
 * Renders the admin settings page with a tabbed and collapsible UI.
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
    $roles = euc_get_all_roles();
    $post_types = euc_get_all_post_types();
    $options = get_option( 'euc_settings' );
    $elements = euc_get_configurable_ui_elements();
    ?>
    <div class="wrap euc-settings-wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p class="description"><?php esc_html_e( 'Check the boxes next to the UI elements you wish to HIDE for the selected roles and post types.', 'editor-ui-cleaner' ); ?></p>

        <form action="options.php" method="post">
            <?php settings_fields( 'euc_settings' ); ?>

            <div class="euc-tabs-container">
                <h2 class="nav-tab-wrapper">
                    <?php
                    $first_role = true;
                    foreach ( $roles as $role_id => $role ) {
                        if ( 'administrator' === $role_id ) continue;
                        $active_class = $first_role ? 'nav-tab-active' : '';
                        echo '<a href="#euc-tab-' . esc_attr( $role_id ) . '" class="nav-tab ' . esc_attr( $active_class ) . '">' . esc_html( $role['name'] ) . '</a>';
                        $first_role = false;
                    }
                    ?>
                </h2>

                <?php
                $first_role = true;
                foreach ( $roles as $role_id => $role ) {
                    if ( 'administrator' === $role_id ) continue;
                    $active_class = $first_role ? 'euc-tab-content-active' : '';
                    $first_role = false;
                    ?>
                    <div id="euc-tab-<?php echo esc_attr( $role_id ); ?>" class="euc-tab-content <?php echo esc_attr( $active_class ); ?>">
                        <?php
                        $first_post_type = true;
                        foreach ( $post_types as $post_type_id => $post_type ) {
                            $is_open = $first_post_type ? 'euc-accordion-open' : '';
                            $first_post_type = false;
                            ?>
                            <div class="euc-accordion-item <?php echo esc_attr( $is_open ); ?>">
                                <h3 class="euc-accordion-title">
                                    <?php echo esc_html( $post_type->label ); ?>
                                    <span class="euc-accordion-icon dashicons dashicons-arrow-down"></span>
                                </h3>
                                <div class="euc-accordion-content">
                                    <label class="euc-toggle-all-label">
                                        <input type="checkbox" class="euc-toggle-all" />
                                        <strong><?php esc_html_e( 'Check / Uncheck All', 'editor-ui-cleaner' ); ?></strong>
                                    </label>
                                    <hr>
                                    <?php
                                    foreach ( $elements as $element_id => $element ) {
                                        $checked = isset( $options[$role_id][$post_type_id] ) && in_array( $element_id, $options[$role_id][$post_type_id] );
                                        echo '<label class="euc-checkbox-label"><input type="checkbox" name="euc_settings[' . esc_attr( $role_id ) . '][' . esc_attr( $post_type_id ) . '][]" value="' . esc_attr( $element_id ) . '" ' . checked( $checked, true, false ) . ' /> ' . esc_html( $element['label'] ) . '</label>';
                                    }
                                    $custom_css = isset( $options[$role_id][$post_type_id]['custom_css'] ) ? $options[$role_id][$post_type_id]['custom_css'] : '';
                                    echo '<p><strong>' . esc_html__( 'Custom CSS Selectors (one per line):', 'editor-ui-cleaner' ) . '</strong></p>';
                                    echo '<textarea name="euc_settings[' . esc_attr( $role_id ) . '][' . esc_attr( $post_type_id ) . '][custom_css]" rows="5" cols="50" class="large-text code">' . esc_textarea( $custom_css ) . '</textarea>';
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>

            <?php submit_button( __( 'Save Settings', 'editor-ui-cleaner' ) ); ?>
        </form>
    </div>
    <?php
}