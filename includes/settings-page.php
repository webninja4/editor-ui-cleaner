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
                        foreach ( $post_types as $post_type_id => $post_type ) {
                            ?>
                            <div class="euc-accordion-item">
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
                                    echo '<div class="euc-custom-css-container">';
                                    echo '<p><strong>' . esc_html__( 'Custom CSS Selectors (one per line):', 'editor-ui-cleaner' ) . '</strong></p>';
                                    echo '<details class="euc-css-helper">';
                                    echo '<summary>' . esc_html__( 'How to find CSS selectors', 'editor-ui-cleaner' ) . '</summary>';
                                    echo '<div class="euc-css-helper-content">';
                                    echo '<p>' . esc_html__( 'Use your browser\u0027s developer tools to inspect the editor and find the ID or class of the element you want to hide. Right-click the element and choose \"Inspect\".', 'editor-ui-cleaner' ) . '</p>';
                                    echo '<ul>';
                                    echo '<li><strong>' . esc_html__( 'Classic Editor:', 'editor-ui-cleaner' ) . '</strong> ' . esc_html__( 'Metaboxes usually have an ID, e.g.,', 'editor-ui-cleaner' ) . ' <code>#my_plugin_metabox</code>.</li>';
                                    echo '<li><strong>' . esc_html__( 'Block Editor (Gutenberg):', 'editor-ui-cleaner' ) . '</strong> ' . esc_html__( 'Elements are often targeted with classes. Look for a unique class on the panel or button, e.g.,', 'editor-ui-cleaner' ) . ' <code>.my-plugin-sidebar-panel</code>.</li>';
                                    echo '<li>' . esc_html__( 'Prefix IDs with a hash (#) and classes with a period (.).', 'editor-ui-cleaner' ) . '</li>';
                                    echo '</ul>';
                                    echo '</div>';
                                    echo '</details>';
                                    echo '<textarea name="euc_settings[' . esc_attr( $role_id ) . '][' . esc_attr( $post_type_id ) . '][custom_css]" rows="5" cols="50" class="large-text code">' . esc_textarea( $custom_css ) . '</textarea>';
                                    echo '</div>';
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