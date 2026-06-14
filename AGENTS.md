# Laravel Admin Package Guidelines

## UI Baseline

Use the current `Admin Users` screens as the default UI reference for new admin models and package screens.

- List pages should use a wide white content area, thin understated borders, compact table spacing, small search controls, slight control radius, and row hover feedback where hovered data rows turn white then return to their original subtle gray background.
- Create and edit forms should use the same wide white layout, section divider lines, left label column, underline-style inputs, compact field height, and blue focus border/ring for active inputs.
- Detail/view pages should place record details inside a dashed box with a very light gray background; action buttons should stay outside the dashed detail box.
- Delete actions should live on edit pages, outside the main form box at the lower-left, styled as a small text link instead of a large button.
- Avoid heavy card styling, shadows, large rounded corners, decorative UI, or oversized typography. Keep the admin interface quiet, dense, and work-focused.

## Reference Files

- `../laravel-admin-ui/resources/views/admin/admin-users/index.blade.php`
- `../laravel-admin-ui/resources/views/admin/admin-users/create.blade.php`
- `../laravel-admin-ui/resources/views/admin/admin-users/edit.blade.php`
- `../laravel-admin-ui/resources/views/admin/admin-users/show.blade.php`
- `../laravel-admin-ui/resources/views/admin/admin-users/partials/form.blade.php`
