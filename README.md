# ssh521/laravel-admin

Laravel 13용 재사용 관리자 패널 패키지다.

## 로컬 설치

```bash
composer config repositories.ssh521-laravel-admin path ../packages/laravel-admin
composer require ssh521/laravel-admin:dev-main
php artisan vendor:publish --tag=laravel-admin-config --no-interaction
php artisan vendor:publish --tag=laravel-admin-migrations --no-interaction
php artisan vendor:publish --tag=laravel-admin-seeders --no-interaction
php artisan laravel-admin:install --force --no-interaction
npm install @fortawesome/fontawesome-free sortablejs axios
npm run build
php artisan migrate
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\RolePermissionSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\AdminUserSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\MenuSeeder"
```

관리자 인증은 설정 가능한 `laravel_admin` guard와 패키지 소유 `admin_users` 테이블을 사용한다. 회원 관리 화면은 여전히 호스트 앱의 `App\Models\User`를 대상으로 한다. 해당 화면에서 호스트 사용자에게 역할을 부여하려면 호스트 `User` 모델이 `Spatie\Permission\Traits\HasRoles`를 사용해야 한다.

`AdminUser`는 `Ssh521\LaravelAdmin\Services\AdminUserService` 아래 별도 서비스 계층을 가진다. 관련 DTO, action, validator도 `AdminUser` 네임스페이스 아래에 있다. 패키지 소유 관리자 계정은 이 계층을 사용하고, 회원 관리 화면에 표시되는 호스트 앱 사용자는 `UserService`를 사용한다.

패키지의 `AdminUserSeeder`는 초기 관리자 계정을 생성하거나 갱신하고 설정된 관리자 역할을 부여한다. seeding 전에 호스트 앱 `.env`에서 아래 값을 바꿀 수 있다.

```dotenv
LARAVEL_ADMIN_SEED_ADMIN_EMAIL=admin@example.com
LARAVEL_ADMIN_SEED_ADMIN_PASSWORD=change-this-password
LARAVEL_ADMIN_SEED_ADMIN_NAME="Super Admin"
LARAVEL_ADMIN_SEED_ADMIN_VERIFIED=true
```

관리자 스타일시트는 Tailwind CSS v4 기준으로 작성되어 있으며 Laravel 13 스타터 킷의 Vite 빌드에 포함할 수 있다. 설치 커맨드는 관리자 에셋을 퍼블리시하고 `vite.config.js`에 아래 input을 추가한다.

```js
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ],
})
```

## 인증

이 패키지는 호스트 앱의 `/login`, `/register`, Fortify view를 덮어쓰지 않는다. Breeze, Jetstream, 기존 커스텀 인증 화면과 route/view 충돌을 피하기 위해서다.

별도 관리자 로그인 화면을 `/admin/login`에서 사용하려면 아래처럼 설정한다.

```php
'auth' => [
    'guard' => 'laravel_admin',
    'provider' => 'laravel_admin_users',
    'admin_login' => [
        'enabled' => true,
        'middleware' => ['web'],
        'redirect_to' => '/admin',
    ],
],
'admin_user_model' => Ssh521\LaravelAdmin\Models\AdminUser::class,
'admin_users_table' => 'admin_users',
```

관리자 route는 기본적으로 패키지의 `AuthenticateAdmin` 미들웨어를 사용한다. 관리자 로그인이 활성화되어 있으면 인증되지 않은 `/admin` 요청은 `/admin/login`으로 이동한다. 비활성 상태에서는 호스트 앱의 `login` route로 fallback한다. 패키지는 설정된 guard/provider를 런타임에 등록하므로, 새 Laravel 프로젝트는 커스텀 guard 이름이나 모델을 쓰려는 경우가 아니라면 `config/auth.php`를 직접 수정하지 않아도 된다.

## 에셋 업데이트

퍼블리시된 CSS/JS 파일은 `ssh521/laravel-admin-ui`에서 제공한다. 패키지 업데이트 후 호스트 Vite 빌드가 최신 관리자 에셋을 사용하도록 아래 명령으로 덮어쓴다.

```bash
composer update ssh521/laravel-admin ssh521/laravel-admin-ui
php artisan laravel-admin:install --force --no-interaction
npm install @fortawesome/fontawesome-free sortablejs axios
npm run build
```

일반 업데이트 중에는 config, migration, seeder, view를 강제로 퍼블리시하지 않는다. 해당 파일들은 호스트 앱에서 커스터마이징했을 수 있다.

## Seeder 퍼블리시

호스트 앱에서 초기 역할, 권한, 메뉴, 관리자 계정을 커스터마이징하려면 패키지 seeder를 퍼블리시할 수 있다.

```bash
php artisan vendor:publish --tag=laravel-admin-seeders --no-interaction
```

퍼블리시된 seeder는 아래 위치로 복사된다.

```text
database/seeders/vendor/laravel-admin
```

패키지 seeder는 퍼블리시하지 않고도 패키지 네임스페이스로 실행할 수 있다.

```bash
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\RolePermissionSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\AdminUserSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\MenuSeeder"
```

기본적으로 `MenuSeeder`는 관리자 계정, 역할, 권한 관리를 위한 `관리자 전용` 메뉴 폴더를 만든다. 이 폴더는 `Super Admin` 역할에만 연결된다. 일반 `Admin` 역할은 일반, 회원 관리, 메뉴 관리 폴더를 받지만 `관리자 전용` 폴더는 받지 않는다.

역할 부여는 관리자 메뉴 폴더 노출을 제어하고, Spatie permission은 세부 기능 권한을 제어한다. 기본 설치 기준으로 `RolePermissionSeeder`는 `Super Admin`과 `Admin` 모두에게 설정된 admin guard의 모든 permission을 부여한다. 기능 패키지는 `AdminPackageRegistrar`로 메뉴와 permission을 등록해야 한다. 자세한 기준은 [관리자 권한 계약](docs/admin-authorization-contract.md)을 참고한다.

## 기능 패키지 템플릿

`laravel-bbs`, `laravel-blog`, `laravel-file` 같은 패키지를 만들 때는 [새 기능 패키지 만드는 법](docs/feature-package-template.md)을 기준으로 삼는다.

이 템플릿은 패키지 네이밍, ServiceProvider 구조, 설치 커맨드, 퍼블리시 정책, `AdminPackageRegistrar` 메뉴/권한 등록, route-level `can:*` 미들웨어, `laravel-admin:install --with=...` 연동, Testbench 테스트 기준을 다룬다.

## Blade 컴포넌트 규칙

패키지 소유 관리자 컴포넌트는 `ssh521/laravel-admin-ui`의 `resources/views/components/admin` 아래에 있으며 `laravel-admin` 네임스페이스로 등록된다. 패키지 view 안에서는 `<x-laravel-admin::admin.primary-button>`, `<x-laravel-admin::admin.layouts.admin>`, `<x-laravel-admin::admin.input>`처럼 사용한다.

`<x-button>`, `<x-input>`, `<x-layouts.admin>`처럼 prefix 없는 패키지 컴포넌트를 추가하거나 사용하지 않는다. 이런 이름은 호스트 Laravel 앱의 소유이며 Breeze, Jetstream, Flux, 커스텀 앱 컴포넌트가 이미 제공하고 있을 수 있다.

퍼블리시된 패키지 view는 `resources/views/vendor/laravel-admin` 아래에 두고, 컴포넌트는 `resources/views/vendor/laravel-admin/components`에 분리한다. 컴포넌트만 필요하면 아래 명령만 실행한다.

```bash
php artisan vendor:publish --tag=laravel-admin-ui-components --no-interaction
```

호스트 앱이 커스터마이징을 위해 패키지 view를 퍼블리시하더라도 패키지 컴포넌트에는 `x-laravel-admin::` prefix를 유지한다. View가 의도적으로 호스트 앱 컴포넌트를 호출하려는 경우에만 prefix 없는 `<x-...>` 태그로 바꾼다.

분리된 `admin_users` 테이블이 생기기 전 설치본을 업데이트한다면 새 migration만 퍼블리시할 수 있다.

```bash
php artisan vendor:publish --tag=laravel-admin-admin-users-migration --no-interaction
php artisan migrate
```

## 초기 Super Admin 설정

Migration과 역할/권한 seeding 후 패키지 seeder를 실행한다.

```bash
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\AdminUserSeeder"
```

특정 관리자 계정을 직접 생성하거나 `Super Admin`으로 승격할 수도 있다.

```bash
php artisan laravel-admin:make-super-admin admin@example.com --verified
```

관리자 사용자가 이미 있으면 이 커맨드는 `Super Admin` 역할을 부여한다. 사용자가 없으면 `admin_users`에 계정을 만들고 비밀번호를 입력받는다. 비대화형 설치에서는 `--password=...` 옵션을 넘긴다.

## 포함 기능

- `ssh521/laravel-admin-ui`를 통해 admin-yaver에서 가져온 관리자 layout, 좌측 메뉴, header, dark mode, modal UI
- 회원, 역할, 권한, 메뉴 카테고리, 메뉴, API 관리 화면
- `MenuCategory`와 `menu_category_role` 기반 역할별 메뉴 폴더 접근 제어
- 기존 `admin-users:user-form:open` 스타일 이벤트를 따르는 Livewire modal 컴포넌트
- 이 패키지에서 제공하는 퍼블리시 가능 config, migration, seeder
- `ssh521/laravel-admin-ui`에서 제공하는 퍼블리시 가능 view와 Vite 에셋

## 추가 개발 노트

아래 항목은 추출된 관리자 패널을 다른 Laravel 프로젝트에 안전하게 설치 가능한 패키지로 다듬기 위해 계속 점검해야 하는 영역이다.

### 패키지 안정화

- 복사된 모든 view/component에서 앱 고유 브랜딩, 문구, route, asset이 남아 있는지 검토하고 필요한 값은 `config/laravel-admin.php`로 이동한다.
- 관리자 route prefix, middleware, 기본 guard, dashboard route, API route prefix, 활성화 모듈에 대한 config 옵션을 추가한다.
- 모든 패키지 모델이 패키지 네임스페이스를 일관되게 사용하면서도 호스트 프로젝트가 user model을 override할 수 있는지 확인한다.
- 남아 있는 호스트 앱 가정을 설정값이나 패키지 기본값으로 대체한다.
- config, asset, migration, seeder, view에 대해 업데이트에 안전한 퍼블리시 tag를 추가한다.

### 인증과 권한

- 관리자 인증은 호스트 `users` 테이블과 분리하되, 회원 관리 화면의 호스트 `User` 모델은 설정 가능하게 유지한다.
- 온보딩 중 `php artisan laravel-admin:make-super-admin`을 통해 Super Admin 설정을 명시적으로 안내한다.
- 모든 Policy가 Super Admin bypass와 역할/메뉴 permission 검사를 일관되게 유지하는지 확인한다.
- 역할 기반 좌측 메뉴 폴더 노출 테스트를 추가한다. 특히 `MenuCategory` 접근이 역할에 따라 달라지는지 확인한다.
- API 접근 제어를 Sanctum token ability, Spatie permission, 또는 둘 모두로 처리할지 결정한다.

### UI와 에셋

- 현재 관리자 UI 형태인 좌측 메뉴 폴더, header, dark mode, modal 기반 CRUD, Livewire 이벤트 네이밍을 유지한다.
- 회원 관리를 향후 CRUD 화면의 기준 패턴으로 삼는다.
- 새 관리자 모듈을 추가한 뒤 modal 크기, 모바일 동작, dark mode를 확인한다.
- 퍼블리시된 관리자 CSS가 Laravel 13 Tailwind CSS v4 Vite 플러그인과 호환되는지 유지한다.
- 퍼블리시된 에셋을 호스트 `resources/vendor/laravel-admin`에 복사할지, production에서 패키지 리소스로 제공할지 검토한다.

### API 관리

- API 관리 화면을 token 표시 수준에서 token 생성, 폐기, ability 부여, 사용 안내까지 확장한다.
- 새 패키지 API에는 API Resource를 추가하고, API surface가 커지면 route versioning을 유지한다.
- 호스트 프로젝트를 위한 Sanctum 설정 권장안을 문서화한다.
- API endpoint에 대해 미인증, ability 없는 인증 사용자, Super Admin 케이스를 포함한 authorization 테스트를 추가한다.

### 테스트 앱

- `testapp`은 Composer path repository 기반 로컬 설치 검증 프로젝트로 유지한다.
- 패키지 코드를 변경하기 전 `testapp`에서 설치 시점 문제를 재현한다.
- `composer install`, `npm run build`, `php artisan migrate --seed`, Super Admin 생성, register/login view, 기본 관리자 페이지 렌더링을 계속 확인한다.
- `/register`, `/login`, `/admin`, 메뉴 화면, 회원 화면, API token 화면에 대한 작은 smoke test suite를 `testapp`에 추가한다.

### 릴리스 체크리스트

- 퍼블리시 전에 패키지 단위 Pest 테스트 또는 orchestra/testbench 구성을 추가한다.
- Migration과 seeder가 반복 로컬 설치에도 충분히 idempotent한지 확인한다.
- 첫 공개 version tag와 지원 Laravel/PHP 버전을 결정한다.
- 공개 릴리스 전에 Packagist metadata, license, contribution note를 준비한다.
