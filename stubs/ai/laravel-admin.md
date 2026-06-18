# Laravel Admin AI Context

이 프로젝트는 `ssh521/laravel-admin` 기반 관리자 기능을 사용한다. AI 도구가 관리자 기능을 만들거나 수정할 때는 아래 기준을 먼저 따른다.

## Source of Truth

먼저 읽을 문서:

- `vendor/ssh521/laravel-admin/docs/ai-development-guide.md`
- `vendor/ssh521/laravel-admin/docs/admin-authorization-contract.md`
- `vendor/ssh521/laravel-admin/docs/feature-package-template.md`
- `vendor/ssh521/laravel-admin/README.md`
- `vendor/ssh521/laravel-admin-ui/docs/admin-design-rules.md`
- `vendor/ssh521/laravel-admin-ui/docs/admin-ui-design-contract.md`

Path repository 또는 monorepo 개발 중이면 `vendor/` 대신 실제 패키지 경로의 같은 문서를 읽는다.

## Package Boundaries

- `laravel-admin` core는 관리자 guard/provider/login/logout, `admin_users`, role/permission, menu/menu category, installer, `laravel-admin-ui` 연결을 소유한다.
- 일반 회원 관리와 사이트 도메인 기능은 core에 넣지 않고 기능 패키지로 분리한다.
- 일반 회원 관리는 `ssh521/laravel-admin-users` 패키지를 기준으로 삼는다.

## Feature Package Rules

- 새 관리자 기능은 가능하면 `ssh521/laravel-{feature}` 패키지로 만든다.
- 메뉴와 permission은 `Ssh521\LaravelAdmin\Support\AdminPackageRegistrar`로 등록한다.
- Route middleware의 `can:*` permission과 seeder permission 이름은 반드시 일치시킨다.
- CRUD permission 기본 이름은 `{feature}-{resources}-view`, `{feature}-{resources}-create`, `{feature}-{resources}-update`, `{feature}-{resources}-delete`다.
- 설치 흐름은 기능 패키지 install command와 `laravel-admin:install --with={feature}` 연동을 따른다.

## Admin UI Rules

- 관리자 UI는 `ssh521/laravel-admin-ui`의 design contract를 따른다.
- 목록, 생성, 수정, 상세, modal 패턴은 `admin-ui-design-contract.md`를 기준으로 구현한다.
- 패키지 view에서는 호스트 앱 컴포넌트와 충돌하지 않도록 `x-laravel-admin::` prefix를 사용한다.
- prefix 없는 `<x-button>`, `<x-input>`, `<x-layouts.admin>` 같은 컴포넌트를 새로 추가하지 않는다.

## Debugging Checklist

- 권한 문제는 route middleware, seeder, DB의 role/permission/menu 상태를 함께 확인한다.
- `laravel-admin::` Blade view 문제는 `ssh521/laravel-admin-ui` 패키지의 view namespace와 component registration을 확인한다.
- 패키지 로컬 PHPUnit이 없으면 설치 프로젝트 또는 workbench의 PHPUnit, `php artisan view:cache`, `git diff --check`, `php -l`로 검증한다.
