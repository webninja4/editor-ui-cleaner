# Project: Editor UI Cleaner

This document outlines the development plan and key decisions for the Editor UI Cleaner WordPress plugin.

## Development Plan

### Phase 1: Foundation and Scaffolding (Complete)

- [X] Create plugin directory structure.
- [X] Create empty plugin files.
- [X] Add standard plugin header to `plugin.php`.
- [X] Create `GEMINI.md` project context file.
- [X] Initialize Git repository and push to GitHub.

### Phase 2: Admin Settings UI (Complete)

- [X] Implement `roles-loader.php` to fetch roles and post types.
- [X] Register the `Settings > Editor UI Cleaner` admin page.
- [X] Build the settings form in `templates/settings-form.php`.
- [X] Implement Settings API for saving data.

### Phase 3: Core Hiding Logic (Complete)

- [X] Define the master list of controllable UI elements.
- [X] Implement Classic Editor hiding logic in `classic-hooks.php`.
- [X] Implement Block Editor hiding logic in `block-hooks.php`.
- [X] Develop the main controller to apply rules based on user/screen.

### Phase 4: Finalization and Repository Push (Complete)

- [X] Implement security best practices (nonces, sanitization, escaping).
- [X] Create `uninstall.php` script.
- [X] Populate `readme.txt`.
- [X] Push final, stable code to GitHub.

### Phase 5: Settings UI Enhancement

- [ ] Implement a tabbed interface for user roles.
- [ ] Create collapsible sections for post types.
- [ ] Set the default state to show the first role tab and expand the first post type.
- [ ] Add a "Check/Uncheck All" toggle for each post type section.
- [ ] Enqueue dedicated CSS and JavaScript for the new UI.
- [ ] Apply styling to match the WordPress admin theme.

## Key Decisions

*   **Framework:** Standard WordPress functions and APIs. No external frameworks to keep it lightweight.
*   **Settings Storage:** A single option in the `wp_options` table, storing a multidimensional array.
*   **UI Hiding:** A combination of `remove_meta_box()` for classic metaboxes and injected CSS (`display: none !important;`) for other elements, especially in the Block Editor. Dynamic detection of editor type (Classic vs. Block) based on rendered HTML for accurate rule application.

## Changelog

### 1.1.0 (In Progress)
*   Started work on a major UI overhaul for the settings page.
*   fix: Collapsed all post types by default in the settings UI.
*   fix: Corrected apostrophe rendering in custom CSS helper text.

### 1.0.0 - 2025-07-02
*   feat: Complete Phase 4 development tasks, including security enhancements and the addition of an `uninstall.php` script.
*   Initial release.