# Laravel Admin 프로젝트 방향과 개발 로드맵

이 문서는 `ssh521/laravel-admin`을 Laravel starter kit 위에 얹어 사이트를 빠르게 구축하기 위한 관리자 기반 패키지로 발전시키기 위한 방향과 우선순위를 정리한다.

## 목표

`ssh521/laravel-admin`의 목표는 Filament, Nova, Backpack 같은 범용 CRUD 빌더를 그대로 대체하는 것이 아니다.

목표는 Laravel starter kit 설치 후 관리자 기반을 빠르게 붙이고, 필요한 사이트 기능을 패키지 단위로 조립할 수 있게 만드는 것이다.

```text
Laravel starter kit
└── ssh521/laravel-admin
    ├── ssh521/laravel-admin-users
    ├── ssh521/laravel-file
    ├── ssh521/laravel-bbs
    ├── ssh521/laravel-blog
    └── other feature packages
```

`laravel-admin`은 관리자 기능 자체를 모두 품는 패키지가 아니라, 관리자 기능 패키지들이 올라가는 foundation 역할을 한다.

## 개발 단계 정책

현재 사용자는 패키지 작성자 1명이고 공개 사용자를 고려하지 않는다.

따라서 당분간은 이전 설치본 호환성, deprecation window, 기존 사용자 migration path보다 구조 정리와 책임 분리를 우선한다. 잘못 잡힌 경계가 있으면 하위 호환성을 유지하려고 우회 코드를 늘리지 않고 직접 정리한다.

공개 배포 또는 외부 프로젝트 사용자가 생기는 시점부터 semantic versioning, migration guide, deprecation policy를 별도로 적용한다.

## 패키지 책임 범위

### laravel-admin core에 남길 것

- 관리자 전용 guard, provider, login/logout
- `admin_users` 관리자 계정
- 관리자 role/permission 관리
- 관리자 menu/menu category 관리
- `AdminPackageRegistrar` 기반 메뉴와 권한 등록 계약
- install/update/make-super-admin command
- 관리자 dashboard shell
- `laravel-admin-ui` view/component/layout 연결
- 기능 패키지 설치 hook

### laravel-admin core에서 뺄 것

- 호스트 앱 `App\Models\User` CRUD
- 일반 회원 API
- user search/detail/form Livewire
- 사이트별 도메인 기능
- 특정 프로젝트 브랜딩, favicon, home route 같은 호스트 앱 가정

### AdminUser와 User 구분

`AdminUser`는 관리자 인증 주체이므로 core에 남긴다.

일반 `User`는 사이트 도메인 모델이다. 모든 사이트가 같은 회원 구조를 갖지 않기 때문에 `ssh521/laravel-admin-users` 같은 별도 기능 패키지로 분리한다.

## 역할과 권한 방향

역할/권한 엔진은 `spatie/laravel-permission`을 계속 사용한다.

직접 구현하지 않는 이유:

- guard, cache, Gate/Policy, middleware, seeder, 테스트, 성능 이슈를 직접 떠안지 않아도 된다.
- Laravel 생태계에서 익숙한 방식이라 기능 패키지 작성자가 이해하기 쉽다.
- `laravel-admin`은 권한 엔진보다 관리자 패키지 생태계 계약에 집중할 수 있다.

직접 개발할 영역은 권한 엔진이 아니라 관리자 운영 규칙이다.

- 관리자 guard 기준 permission naming
- role 기반 메뉴 카테고리 노출
- permission 기반 기능 접근 제어
- Super Admin bypass
- 기능 패키지별 메뉴/권한 등록 계약
- 관리자 UI에서 role/permission을 관리하는 UX

모든 관리자 role/permission은 `config('laravel-admin.auth.guard', 'laravel_admin')` 기준 guard로 생성한다.

## UI 방향

`ssh521/laravel-admin-ui`를 관리자 presentation source of truth로 유지한다.

`laravel-admin`은 controller, service, policy, registrar, command를 담당하고, 화면 구현과 공통 component/layout은 `laravel-admin-ui`가 담당한다.

UI 원칙:

- 조용하고 업무 중심적인 관리자 UI를 유지한다.
- `admin-users` 화면을 기준 구현으로 삼는다.
- list, form, detail, modal 패턴을 `laravel-admin-ui` contract로 고정한다.
- 기능 패키지는 package-local 디자인 시스템을 만들지 않고 `laravel-admin-ui` component를 사용한다.
- dark mode, mobile layout, long label 처리를 기본 요구사항으로 둔다.

보완해야 할 UI 구조:

- admin shell의 legacy 색상, favicon, `route('home')` 같은 호스트 앱 가정을 config화하거나 제거한다. 완료: `laravel-admin-ui` packaged view에서 host `home` route와 favicon/manifest hardcode를 제거했다.
- Livewire layout/view namespace를 `laravel-admin::` 계열로 통일한다. 완료: core controller/view 참조와 UI package view namespace는 `laravel-admin::` 기준으로 정리했다.
- `laravel-admin-ui` ServiceProvider가 view namespace를 책임지게 하고, core의 path repository 의존 fallback은 줄인다. 완료: core provider는 UI package provider 등록에 의존하고 sibling path fallback은 제거했다.
- `회원 리스트` 같은 표현은 contract에 맞춰 `회원 목록`으로 통일한다.
- index 화면에서 자기 자신으로 가는 `목록보기` 버튼을 제거한다.

## 추천 개발 환경

Mac mini를 항상 켜져 있는 메인 개발 서버로 두고, Mac Air는 원격 클라이언트처럼 사용하는 방식을 권장한다.

```text
GitHub      = 소스 기준
Mac mini    = 메인 개발, Herd, DB, workbench, 테스트
Mac Air     = 원격 편집, 리뷰, 가벼운 작업
Codex       = 구조 변경, 반복 작업, 테스트 작성
Cursor      = 설계 검토, 세밀한 편집, UI 확인
Herd        = 로컬 Laravel 실행 환경
```

가능하면 Mac Air에서 별도 clone으로 동시에 개발하기보다 Cursor Remote SSH 또는 SSH 기반 작업으로 Mac mini의 같은 workspace를 연다.

Mac Air에 직접 clone해서 작업해야 한다면 작업 시작 전 `git pull`, 작업 종료 전 `git push`를 강제하고, DB/storage/.env에 의존하는 작업은 Mac mini에서만 한다.

## 추천 workspace 구조

패키지는 workbench Laravel 앱 밖에 독립 repo로 둔다.

```text
~/Projects/Packagist/
├── workbench-laravel13/
└── packages/
    └── ssh521/
        ├── laravel-admin/
        ├── laravel-admin-ui/
        ├── laravel-admin-users/
        ├── laravel-file/
        ├── laravel-bbs/
        └── laravel-blog/
```

`workbench-laravel13`은 Laravel starter kit 설치 후 패키지를 path repository로 연결해 실제 설치와 화면을 검증하는 앱이다.

각 패키지는 독립 GitHub repo로 관리한다.

```text
ssh521/laravel-admin
ssh521/laravel-admin-ui
ssh521/laravel-admin-users
ssh521/laravel-file
ssh521/laravel-bbs
ssh521/laravel-blog
ssh521/laravel-admin-workbench
```

workbench `composer.json`은 path repository symlink를 사용한다.

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../packages/ssh521/laravel-admin",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "path",
            "url": "../packages/ssh521/laravel-admin-ui",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

## 패키지 개발 원칙

- 패키지 하나당 GitHub repo 하나를 둔다.
- 패키지 하나당 README, LICENSE, tests, phpunit 설정을 둔다.
- 기능 패키지는 `ssh521/laravel-admin`에 의존한다.
- 기능 패키지는 `AdminPackageRegistrar`로 메뉴와 permission을 등록한다.
- migration은 반복 실행과 fresh install을 우선 고려한다.
- seeder는 여러 번 실행해도 안전하게 만든다.
- UI 변경과 동작 변경은 커밋을 분리한다.
- `laravel-admin-ui` contract 변경은 별도 PR로 관리한다.
- core 패키지는 feature 기능을 직접 늘리지 않는다.
- 기능 패키지 route는 core의 `laravel-admin.middleware`와 admin guard 계약을 따른다.

## 설치 경험 목표

기본 설치는 짧고 예측 가능해야 한다.

```bash
composer require ssh521/laravel-admin
php artisan laravel-admin:install
php artisan laravel-admin:make-super-admin
```

기능 패키지는 선택 설치한다.

```bash
composer require ssh521/laravel-admin-users
composer require ssh521/laravel-file

php artisan laravel-admin:install --with=users,file
```

또는 각 기능 패키지 installer를 직접 실행할 수 있게 한다.

```bash
php artisan laravel-admin-users:install
php artisan laravel-file:install
```

## 개발 우선순위 로드맵

### 1단계: Core 경계 확정

목표: `laravel-admin`이 관리자 foundation이라는 정체성을 코드로 고정한다.

- `AdminUser`와 일반 `User` 책임을 문서와 코드에서 분리한다.
- core에 남길 기능과 feature package로 뺄 기능을 README에 명시한다.
- `User` 관련 controller, service, action, DTO, validator, policy, Livewire, view 목록을 분리 대상으로 표시한다.
- `composer.json` 의존성 중 core 필수와 optional 성격을 구분한다.
- core에서 일반 user API와 starter kit 인증 보조 기능을 제거하고 Jetstream/Sanctum 의존성을 기능 패키지 책임으로 분리한다.

완료 기준:

- core 책임 범위가 README와 docs에 반영되어 있다.
- `User` 관련 기능의 이동 대상이 명확하다.
- 새 기능을 core에 추가하지 않는 기준이 정리되어 있다.

### 2단계: laravel-admin-users 패키지 분리

목표: 호스트 앱 `User` 관리 기능을 첫 번째 feature package로 분리한다.

상태: 완료. 세부 분리 계획 문서는 제거하고, 현재 책임 경계는 README와 실제 패키지 구조를 기준으로 판단한다.

- `ssh521/laravel-admin-users` repo를 만든다.
- 기존 `UserController`, `UserService`, `Actions/User`, `DTOs/User`, `Validators/User`, `UserPolicy`를 이동한다.
- user 관련 Livewire component와 Blade view를 이동한다.
- user menu/permission seeder를 `AdminPackageRegistrar` 기반으로 작성한다.
- `laravel-admin-users:install` command를 만든다.
- core의 user route/menu/permission 기본 등록을 제거한다.

완료 기준:

- `laravel-admin` 없이 일반 user CRUD 코드가 core에 남지 않는다.
- `laravel-admin-users` 설치 후 기존 회원 관리 화면이 동작한다.
- user feature package 테스트가 통과한다.

### 3단계: UI shell 안정화

목표: `laravel-admin-ui`가 모든 관리자 화면의 안정적인 presentation layer가 되게 한다.

상태: 완료. `laravel-admin-ui`가 admin shell view namespace와 공통 layout/component를 책임지고, core는 UI package provider를 등록하는 구조로 정리했다. Admin shell과 packaged resource view에서 host `home` route, root favicon/manifest, Jetstream/profile/logout route 직접 가정을 제거하거나 조건부 route fallback으로 바꿨다.

- admin layout의 legacy 색상과 호스트 앱 favicon/home route 가정을 제거하거나 config화한다.
- `laravel-admin-ui` ServiceProvider가 view namespace와 component namespace를 책임지게 정리한다.
- core의 path repository 전용 view fallback을 줄인다.
- Livewire layout namespace를 패키지 namespace 기준으로 통일한다.
- `admin-users` 화면을 기준으로 roles, permissions, menus, menu-categories 화면을 contract에 맞춘다.
- list/form/detail/modal에 대한 screenshot 또는 render smoke test를 추가한다.

완료 기준:

- 새 Laravel starter kit 앱에 설치해도 admin layout이 호스트 앱 가정 없이 렌더링된다.
- 주요 admin resource 화면의 용어, 버튼 배치, dark mode가 contract와 맞는다.

검증 기준:

- `laravel-admin` core의 `UiResourceOwnershipTest`가 core resource directory 제거, UI publish tag 분리, `laravel-admin::` namespace view resolution, sibling path fallback 미사용을 확인한다.
- `laravel-admin-ui`의 `LaravelAdminUiServiceProviderTest`가 packaged views에서 `route('home')`, favicon, manifest hardcode가 재도입되지 않는지 확인한다.
- admin header account dropdown은 gray/indigo 중심의 조용한 shell tone으로 정리하고 logout만 red action으로 분리한다.

### 4단계: 권한 계약 강화

목표: 메뉴 노출과 기능 접근 제어를 일관된 관리자 권한 모델로 고정한다.

상태: 진행 중. web admin route에 route-level `can:*` middleware를 적용하기 시작했고, route middleware permission 이름이 seeder permission과 일치하는지 테스트로 고정했다. 메뉴 카테고리 변경과 메뉴 정렬 Livewire action도 admin guard 기준 `menu-edit` permission을 직접 확인하게 보강했다.

- 모든 role/permission seeder가 admin guard 기준으로 생성되는지 확인한다.
- `AdminPackageRegistrar`의 `roles`와 `permission_roles` 계약을 테스트로 고정한다.
- Super Admin bypass 테스트를 추가한다. 진행: route-level `can:*` middleware에서도 Super Admin bypass가 동작하는지 확인한다.
- menu category role 노출 테스트를 추가한다. 진행: LeftMenu가 Admin, Super Admin, custom role별로 허용된 active menu category만 노출하는지 확인한다.
- route-level `can:*` middleware 적용 기준을 기능 패키지 템플릿에 반영한다. 완료: route middleware, seeder permission, 테스트 fixture가 같은 permission 문자열을 쓰도록 템플릿과 테스트 예시를 보강했다.
- 권한이 필요한 Livewire action은 action 시작 지점에서 검사한다. 진행: menu category 변경과 menu order 변경 action은 `menu-edit` permission을 직접 확인한다.
- 기능 패키지 API 권한을 Sanctum ability, Spatie permission, policy 중 어떤 조합으로 쓸지 결정한다.

완료 기준:

- web admin route, Livewire action, API route의 권한 방식이 문서화되어 있다.
- 기본 Admin과 Super Admin의 차이가 테스트로 확인된다.

### 5단계: Installer와 update 흐름 정리

목표: starter kit 이후 설치 경험을 짧고 재현 가능하게 만든다.

- `laravel-admin:install`을 fresh install 기준으로 정리한다.
- `laravel-admin:update`는 config/view/migration/seeder 강제 덮어쓰기를 피한다.
- `--with=users,file,bbs,blog` 기능 패키지 설치 흐름을 확정한다.
- feature package installer discovery 또는 명시 mapping 방식을 결정한다.
- `make-super-admin` command를 설치 문서의 표준 절차로 만든다.
- workbench에서 fresh install smoke test를 만든다.

완료 기준:

- 새 Laravel starter kit 앱에서 문서의 명령만으로 `/admin` 접속까지 가능하다.
- 기능 패키지를 선택 설치하고 메뉴/권한이 등록된다.

### 6단계: Workbench와 CI 구축

목표: Mac mini와 GitHub에서 같은 검증을 반복할 수 있게 한다.

- `laravel-admin-workbench`를 공식 테스트 앱으로 고정한다.
- path repository 기반 로컬 설치 문서를 작성한다.
- GitHub Actions로 패키지 test, Pint, static check를 실행한다.
- workbench smoke test에 migrate, seed, admin login, dashboard render를 포함한다.
- Mac mini Herd domain과 로컬 env 예시를 문서화한다.

완료 기준:

- PR마다 최소 패키지 테스트가 자동 실행된다.
- Mac mini에서 fresh install 검증 절차가 반복 가능하다.

### 7단계: 기능 패키지 생태계 확장

목표: admin foundation 위에 실제 사이트 기능을 빠르게 붙인다.

- `laravel-file`을 feature package 기준에 맞춘다.
- `laravel-bbs`를 feature package 기준에 맞춘다.
- `laravel-blog`를 feature package 기준에 맞춘다.
- 각 패키지는 admin menu, permission, install command, tests를 갖는다.
- 공통 반복 작업이 많아지면 `laravel-admin:make-package` 또는 stub generator를 검토한다.

완료 기준:

- starter kit + laravel-admin + 선택 기능 패키지 조합으로 사이트 기본 골격을 빠르게 만들 수 있다.
- 기능 패키지 추가 방식이 문서와 코드에서 일관된다.

### 8단계: 릴리스 준비

목표: 공개 배포 또는 장기 개인 사용 기준의 안정성을 확보한다.

- Composer package metadata를 정리한다.
- 공개 사용자가 생긴 이후의 하위 호환성 정책을 정한다.
- `composer.json`의 `version` 필드 제거 여부를 결정한다.
- CHANGELOG를 작성한다.
- semantic versioning 기준을 정한다.
- migration 변경 정책을 정한다.
- Packagist 공개 범위와 license를 점검한다.

완료 기준:

- `1.0.0` 기준 core 책임 범위와 BC 정책이 정리되어 있다.
- 새 프로젝트에 설치 가능한 최소 문서와 테스트가 준비되어 있다.

## 단기 실행 순서

현재 1-3단계는 완료 상태로 보고, 다음 작업은 아래 순서가 좋다.

1. `laravel-admin` core의 web admin route에 route-level `can:*` middleware 계약을 적용한다.
2. seeder permission name, route middleware name, 테스트 fixture의 permission string이 같은 이름 체계를 쓰는지 맞춘다.
3. Admin과 Super Admin의 차이를 route 접근 테스트로 고정한다.
4. 권한이 필요한 Livewire action에서 `Gate::authorize()` 또는 `$this->authorize()` 누락이 없는지 확인한다.
5. installer와 `--with=users,file,bbs,blog` 기능 패키지 설치 흐름을 정리한다.
6. workbench fresh install smoke test를 만든다.

이 순서로 가면 UI shell 안정화 이후 권한 계약을 먼저 고정하고, 그 위에 installer/workbench와 기능 패키지 생태계를 안정적으로 확장할 수 있다.
