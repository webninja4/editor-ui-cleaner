=== Editor UI Cleaner ===
Plugin Name: Editor UI Cleaner
Plugin URI: https://github.com/your-repo/editor-ui-cleaner
Contributors: Paul Steele, Gemini
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Allows administrators to hide specific admin screen UI elements based on user role in both Classic and Block Editors.

== Description ==

Editor UI Cleaner is a lightweight WordPress plugin that provides granular control over the administrative user interface. It allows site administrators to hide various UI elements (meta boxes, panels, etc.) from specific user roles, ensuring a cleaner and more focused editing experience.

Key Features:

*   **Role-Based Control:** Configure UI visibility based on WordPress user roles.
*   **Classic Editor Support:** Hide meta boxes and other elements in the Classic Editor.
*   **Block Editor Support:** Hide panels and elements in the Block Editor.
*   **Dynamic Editor Detection:** Intelligently applies rules based on whether the Classic or Block Editor HTML is being rendered.
*   **Custom CSS:** Option to add custom CSS selectors for advanced hiding needs.

== Installation ==

1.  Upload the `editor-ui-cleaner` folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Navigate to `Settings > Editor UI Cleaner` to configure which UI elements to hide for each user role and post type.

== Changelog ==

= 1.0.0 =
*   Initial release.
*   Implemented core UI hiding logic for Classic and Block Editors.
*   Added dynamic editor detection.
*   Introduced role-based settings and custom CSS option.