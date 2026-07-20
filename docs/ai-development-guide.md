# Laravel Admin AI 개발 가이드

이 문서는 `ssh521/laravel-admin`을 설치한 호스트 프로젝트에서 AI 도구가 관리자 기능을 만들거나 수정할 때 참조해야 할 기준이다.

## 기본 방향

`ssh521/laravel-admin`은 Laravel starter kit 위에 관리자 기반을 빠르게 붙이기 위한 admin foundation이다. 범용 CRUD 빌더가 아니라 관리자 인증, 역할/권한, 메뉴 시스템, 기능 패키지 연결 계약을 제공한다.

Core 패키지가 직접 소유하는 책임:

- 관리자 전용 guard, provider, login/logout
- 패키지 소유 `admin_users` 관리자 계정
- 호스트 앱 `User` 모델 기반 일반 회원 관리
- 관리자 role/permission 관리
- menu/menu category 기반 관리자 메뉴 시스템
- `AdminPackageRegistrar` 기반 기능 패키지 메뉴와 권한 등록 계약
- install/update/make-super-admin command
- `ssh521/laravel-admin-ui` layout, component, asset 연결

Core에 넣지 않는 책임:

- 사이트 도메인별 게시판, 블로그, 파일, 페이지 기능
- 호스트 앱의 `/login`, `/register`, starter kit 인증 화면 덮어쓰기

일반 회원 관리는 core가 기본 제공한다. 호스트 앱이 기본 `App\Models\User`가 아닌 다른 회원 모델을 사용하면 `config('laravel-admin.user_model')`만 교체한다.

## 호스트 애플리케이션 기본 계약

- `config/app.php`의 기본 시간대는 `env('APP_TIMEZONE', 'Asia/Seoul')`로 둔다.
- 사용자·관리자 화면과 Scheduler에서 별도 요구가 없으면 한국 표준시를 사용한다.
- 외부 API가 UTC를 요구하면 해당 API 경계에서 명시적으로 변환하고 시간대 또는 offset을 함께 전달한다.
- 다른 시간대를 사용하는 호스트 앱은 `APP_TIMEZONE` 또는 `laravel-admin:install --skip-timezone`으로 명시적으로 재정의한다.

## AI가 먼저 읽을 문서

호스트 프로젝트에서 관리자 기능을 개발하기 전에 아래 문서를 우선 확인한다.

- `vendor/ssh521/laravel-admin/README.md`
- `vendor/ssh521/laravel-admin/docs/admin-authorization-contract.md`
- `vendor/ssh521/laravel-admin/docs/feature-package-template.md`
- `vendor/ssh521/laravel-admin/docs/project-direction-roadmap.md`
- `vendor/ssh521/laravel-admin-ui/docs/admin-design-rules.md`
- `vendor/ssh521/laravel-admin-ui/docs/admin-ui-design-contract.md`

Path repository로 개발 중이면 `vendor/` 대신 실제 패키지 경로의 같은 파일을 참조한다.

## 기능 패키지 개발 기준

새 관리자 기능은 가능하면 별도 `ssh521/laravel-{feature}` 패키지로 만든다.

- `docs/feature-package-template.md`의 네이밍, ServiceProvider, install command, publish tag 구조를 따른다.
- 관리자 메뉴와 permission은 `AdminPackageRegistrar`로 등록한다.
- Route middleware의 `can:*` permission 이름과 seeder의 permission 이름은 동일해야 한다.
- `laravel-admin:install --with={feature}` 연동이 필요한 기능은 core install command의 installer map에 추가한다.
- 기능 패키지마다 README, config, migration, seeder, Testbench 테스트를 둔다.

기본 CRUD permission 이름:

```text
{feature}-{resources}-view
{feature}-{resources}-create
{feature}-{resources}-update
{feature}-{resources}-delete
```

상태 전환이나 복구 기능이 있으면 별도 permission을 둔다.

```text
{feature}-{resources}-publish
{feature}-{resources}-restore
{feature}-{resources}-force-delete
```

## UI 개발 기준

관리자 UI는 `ssh521/laravel-admin-ui`가 source of truth다.

- 새 화면을 만들기 전에 `admin-design-rules.md`와 `admin-ui-design-contract.md`를 읽는다.
- 목록 화면은 검색/필터 bar, table 또는 card grid, pagination, row actions를 제공한다.
- 생성 화면은 sectioned form과 저장/취소 action을 제공한다.
- 수정 화면은 sectioned form, 좌측 destructive action, 우측 저장/취소 action을 제공한다.
- 상세 화면은 semantic `dl`, `dt`, `dd` 기반 detail view와 action footer를 제공한다.
- create/edit 공유 폼은 `partials/form`으로 분리하고 field name을 route/action 변경 없이 유지한다.

패키지 view에서는 prefix 없는 호스트 앱 컴포넌트 대신 `x-laravel-admin::` 컴포넌트를 사용한다.

```blade
<x-laravel-admin::admin.layouts.admin>
<x-laravel-admin::admin.primary-button>
<x-laravel-admin::admin.input>
```

## 인증과 권한 기준

- 관리자 route는 설정된 admin guard와 `AuthenticateAdmin` middleware를 기준으로 한다.
- `Super Admin`은 모든 Gate를 통과할 수 있다.
- 메뉴 폴더 노출은 role과 `menu_category_role`이 제어한다.
- 세부 기능 접근은 Spatie permission과 route-level `can:*` middleware가 제어한다.
- `Admin` 역할명 자체로 Policy를 통과시키지 않는다. `Admin`도 DB에 등록되고 부여된 permission으로 접근한다.
- 새 permission 문자열을 route, Policy, Livewire action에서 쓰면 seeder 또는 `AdminPackageRegistrar`에도 같은 문자열을 등록한다.
- 권한 문제를 수정할 때는 코드만 보지 말고 route middleware, seeder, 실제 DB role/permission/menu 상태를 함께 확인한다.

## 검증 기준

가능하면 패키지 루트에서 PHPUnit을 실행한다.

```bash
vendor/bin/phpunit --configuration phpunit.xml.dist
```

패키지 로컬 의존성이 없으면 호스트 workbench 또는 설치 프로젝트의 PHPUnit으로 검증한다. 최소한 아래 정적 검사를 실행한다.

```bash
composer validate --strict
git diff --check
php -l path/to/changed.php
```

UI나 view namespace 변경은 호스트 프로젝트에서 `php artisan view:cache` 또는 실제 페이지 렌더링으로 확인한다.
