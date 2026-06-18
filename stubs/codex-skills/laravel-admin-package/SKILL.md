---
name: laravel-admin-package
description: Work on ssh521 Laravel Admin package projects. Use when changing laravel-admin core, laravel-admin-ui backed admin screens, feature packages such as laravel-admin-users/bbs/blog/file/page, admin route/menu/permission registration, package installers, package documentation, or host-project integration for ssh521/laravel-admin.
---

# Laravel Admin Package

## Overview

Use this skill to keep `ssh521/laravel-admin` package work aligned with the package boundary, admin UI contract, permission model, and workbench verification flow.

## Context First

Before editing, identify the owner of the behavior.

- Core admin guard, admin users, roles, permissions, menus, installers, and `AdminPackageRegistrar` belong in `ssh521/laravel-admin`.
- Admin Blade resources and shared components usually come from `ssh521/laravel-admin-ui` through the `laravel-admin::` namespace.
- General user management belongs in `ssh521/laravel-admin-users`, not in core.
- Site-domain features belong in separate feature packages such as `laravel-bbs`, `laravel-blog`, `laravel-file`, or `laravel-page`.

Read these files when they exist in the current install or path repository:

- `vendor/ssh521/laravel-admin/docs/ai-development-guide.md`
- `vendor/ssh521/laravel-admin/docs/admin-authorization-contract.md`
- `vendor/ssh521/laravel-admin/docs/feature-package-template.md`
- `vendor/ssh521/laravel-admin/README.md`
- `vendor/ssh521/laravel-admin-ui/docs/admin-design-rules.md`
- `vendor/ssh521/laravel-admin-ui/docs/admin-ui-design-contract.md`

If the project uses path repositories, use the actual package paths instead of `vendor/`.

## Workflow

1. Inspect the real route, service provider, config, view namespace, seeder, and test shape before deciding where to change code.
2. For UI work, read the `laravel-admin-ui` design contract and mirror current Admin Users screens where relevant.
3. For permission bugs, inspect route middleware, seeder permission names, and DB-backed role/menu state before rewriting policies.
4. For feature packages, follow `feature-package-template.md` for install command, publish tags, route-level `can:*`, menu registration, and Testbench coverage.
5. Keep host app auth screens and general `User` CRUD out of core unless the user explicitly changes the package boundary.

## Verification

Prefer package-local PHPUnit when available. If package-local dependencies are missing, use the host workbench PHPUnit or the strongest lighter checks available: `php -l`, `composer validate --strict`, `git diff --check`, and `php artisan view:cache` for Blade/view namespace changes.
