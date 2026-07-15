# ssh521/laravel-admin

Laravel 13 프로젝트에 관리자 기반을 빠르게 붙이기 위한 재사용 패키지다.

`ssh521/laravel-admin`은 Laravel starter kit 위에 얹어 쓰는 admin foundation이다. Filament, Nova, Backpack 같은 범용 CRUD 빌더를 대체하기보다, 관리자 인증, 권한, 메뉴, 설치 흐름, 기능 패키지 등록 계약을 제공하는 데 집중한다.

```text
Laravel starter kit
└── ssh521/laravel-admin
    ├── ssh521/laravel-admin-ui
    ├── ssh521/laravel-file
    ├── ssh521/laravel-bbs
    └── ssh521/laravel-blog
```

자세한 제품 방향은 [프로젝트 방향과 개발 로드맵](docs/project-direction-roadmap.md)을 참고한다.

## 핵심 책임

이 패키지가 직접 소유하는 영역은 아래와 같다.

- 관리자 전용 guard/provider/login/logout
- 패키지 소유 `admin_users` 관리자 계정
- 호스트 앱 `User` 모델 기반 일반 회원 관리
- 관리자 role/permission 관리
- menu/menu category 기반 관리자 메뉴 시스템
- `AdminPackageRegistrar` 기반 기능 패키지 메뉴와 권한 등록 계약
- `laravel-admin:install`, `laravel-admin:update`, `laravel-admin:make-super-admin` command
- `ssh521/laravel-admin-ui` layout, component, asset 연결

일반 회원 관리는 starter kit 기반 프로젝트의 기본 관리자 기능으로 보고 core에 포함한다. 게시판, 블로그, 파일 관리처럼 사이트 도메인에 가까운 기능은 `ssh521/laravel-file`, `ssh521/laravel-bbs`, `ssh521/laravel-blog` 같은 기능 패키지로 분리한다.

현재는 초기 개발 단계이므로 이전 설치본 호환성보다 구조 정리와 책임 분리를 우선한다. 공개 사용자나 외부 프로젝트 사용자가 생기면 별도의 하위 호환성 정책을 적용한다.

## 요구 사항

- PHP `^8.3`
- Laravel/Illuminate `^13.0`
- Livewire `^3.0` 또는 `^4.0`
- Spatie Laravel Permission `^6.0`
- `ssh521/laravel-admin-ui ^1.0`

관리자 CSS/JS는 Laravel Vite 빌드에 포함해서 사용한다.

## Starter Kit 선택

`ssh521/laravel-admin`은 Laravel 13 기반 프로젝트를 전제로 한다. 2026년 7월 현재 `laravel/livewire-starter-kit`의 stable 태그(`v1.0.x`)는 Laravel 12를 설치하므로, Laravel 13 프로젝트를 새로 만들 때는 `dev-main`을 명시해야 한다.

```bash
composer create-project laravel/livewire-starter-kit:dev-main workbench-laravel13
```

stable starter kit으로 생성한 뒤 `laravel-admin`을 설치하면 `illuminate/* ^13.0` 의존성 충돌이 발생한다. 이미 Laravel 12 starter kit으로 만든 프로젝트라면 먼저 Laravel 13 / Livewire 4 계열로 올린 뒤 설치한다.

## 로컬 개발 폴더 구조

로컬에서 여러 `ssh521/*` 패키지를 함께 개발할 때는 workbench Laravel 앱과 패키지 repo를 분리해서 둔다.

```text
~/Projects/Packagist/
├── workbench-laravel13/
└── packages/
    └── ssh521/
        ├── laravel-admin/
        ├── laravel-admin-ui/
        ├── laravel-file/
        ├── laravel-bbs/
        └── laravel-blog/
```

`workbench-laravel13`은 Laravel 13 starter kit 설치 후 패키지를 path repository로 연결해 실제 설치, 화면, migrate/seed, Vite build를 검증하는 앱이다. 각 패키지는 독립 GitHub repo로 관리하고, workbench에서는 symlink 기반 path repository로 연결한다.

workbench `composer.json` 예시는 아래와 같다.

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

이 구조를 쓰면 `laravel-admin`, `laravel-admin-ui`, 기능 패키지를 각각 독립 repo로 커밋하면서도 workbench에서 실제 Laravel 앱 기준으로 바로 검증할 수 있다.

## 빠른 설치

일반 프로젝트에서는 Composer로 패키지를 설치한다.

```bash
composer create-project laravel/livewire-starter-kit:dev-main my-site
cd my-site
composer require ssh521/laravel-admin
php artisan laravel-admin:install
npm install
npm run build
```

로컬 패키지 개발 환경에서는 path repository를 연결해서 설치한다.

```bash
composer create-project laravel/livewire-starter-kit:dev-main workbench-laravel13
cd workbench-laravel13
composer config repositories.ssh521-laravel-admin path ../packages/ssh521/laravel-admin
composer config repositories.ssh521-laravel-admin-ui '{"type":"path","url":"../packages/ssh521/laravel-admin-ui","options":{"symlink":true,"versions":{"ssh521/laravel-admin-ui":"1.0.0"}}}'
composer require ssh521/laravel-admin:^1.0
php artisan laravel-admin:install --force
npm install
npm run build
```

로컬 path repository에서 `laravel-admin-ui`는 Git tag가 있어도 branch checkout 상태에서는 Composer가 `dev-main`으로 해석할 수 있다. `laravel-admin`은 `ssh521/laravel-admin-ui ^1.0`을 요구하므로, 위 예시처럼 `options.versions`에 `ssh521/laravel-admin-ui`를 `1.0.0`으로 지정해야 한다. 이 alias가 없으면 `found ssh521/laravel-admin-ui[dev-main] but it does not match the constraint` 의존성 오류가 날 수 있다.

기본 설치 커맨드는 아래 작업을 수행한다.

- migration과 관리자 UI asset을 퍼블리시한다.
- 관리자 테이블을 migrate한다.
- 기본 role/permission과 menu를 seed한다. 관리자 계정은 명시적으로 활성화한 경우에만 seed한다.
- `vite.config.js`에 관리자 CSS/JS input을 추가한다.
- `--with=...` 옵션이 있으면 기능 패키지 installer를 이어서 실행한다.

설치 후 화면이나 테스트를 확인하기 전에 `npm install`과 `npm run build`를 실행한다. 관리자 CSS/JS가 Vite manifest에 포함되어야 `/admin/login`과 관리자 화면이 정상 렌더링된다.

```bash
npm install
npm run build
```

빌드 후 `/admin/login`으로 접속한다. 공개 패키지는 예측 가능한 기본 관리자 계정을 만들지 않으므로, 설치 전에 안전한 계정 정보를 명시적으로 설정하거나 설치 후 `laravel-admin:make-super-admin`을 사용한다.

```dotenv
LARAVEL_ADMIN_SEED_ADMIN_ENABLED=true
LARAVEL_ADMIN_SEED_ADMIN_EMAIL=owner@example.com
LARAVEL_ADMIN_SEED_ADMIN_PASSWORD=replace-with-a-long-random-password
LARAVEL_ADMIN_SEED_ADMIN_NAME="Super Admin"
LARAVEL_ADMIN_SEED_ADMIN_VERIFIED=true
```

`LARAVEL_ADMIN_SEED_ADMIN_ENABLED`의 기본값은 `false`이며, 새 계정을 만들 때 비밀번호는 최소 12자여야 한다.

## 설치 옵션

`laravel-admin:install`에서 자주 쓰는 옵션은 아래와 같다.

```bash
php artisan laravel-admin:install --force
php artisan laravel-admin:install --skip-migrate
php artisan laravel-admin:install --skip-seed
php artisan laravel-admin:install --skip-admin-user
php artisan laravel-admin:install --skip-vite
php artisan laravel-admin:install --publish-config
php artisan laravel-admin:install --publish-views
php artisan laravel-admin:install --publish-seeders
```

기능 패키지를 함께 설치할 수도 있다.

```bash
php artisan laravel-admin:install --with=file
php artisan laravel-admin:install --with=bbs,blog --with-test-data
```

회원 관리는 core 기본 기능이므로 별도 `users` 키를 사용하지 않는다. 현재 core installer가 알고 있는 기능 패키지 키는 `bbs`, `blog`, `file`이다.

실시간 관리자 알림이 필요하면 추천 확장 패키지를 추가로 설치한다. `laravel-admin`은 알림 패키지 없이도 동작하며, 패키지가 설치되어 있고 `broadcast_notifications.enabled`가 켜져 있을 때만 선택적으로 알림을 발송한다.

```bash
composer require ssh521/laravel-broadcast-notification
php artisan laravel-broadcast-notification:install
```

알림 패키지를 설치하면 관리자 계정 생성/수정/삭제, 관리자 권한 변경, 역할/권한/메뉴/메뉴 카테고리 변경, 관리자 로그인 이벤트를 관리자들에게 broadcast notification으로 보낼 수 있다.

## 설정

기본 설정 파일은 `config/laravel-admin.php`로 퍼블리시할 수 있다.

```bash
php artisan vendor:publish --tag=laravel-admin-config --no-interaction
```

주요 설정은 아래와 같다.

```php
return [
    'route_prefix' => 'admin',
    'route_name_prefix' => 'admin.',
    'middleware' => [
        'web',
        Ssh521\LaravelAdmin\Http\Middleware\AuthenticateAdmin::class,
        'verified',
    ],

    'auth' => [
        'guard' => 'laravel_admin',
        'provider' => 'laravel_admin_users',
        'admin_login' => [
            'enabled' => true,
            'middleware' => ['web'],
            'redirect_to' => '/admin',
        ],
        'session' => [
            'enabled' => true,
            'cookie' => 'laravel-admin-session',
        ],
    ],

    'admin_user_model' => Ssh521\LaravelAdmin\Models\AdminUser::class,
    'admin_users_table' => 'admin_users',
    'user_model' => App\Models\User::class,
    'super_admin_role' => 'Super Admin',
    'broadcast_notifications' => [
        'enabled' => true,
        'login_events_enabled' => true,
        'facade' => 'Ssh521\\LaravelBroadcastNotification\\Facades\\BroadcastNotification',
    ],
];
```

서비스 프로바이더는 설정된 guard/provider를 런타임에 등록한다. 새 Laravel 프로젝트에서 기본 `laravel_admin` guard를 그대로 쓴다면 `config/auth.php`를 직접 수정하지 않아도 된다.

일반 사용자와 관리자 session guard는 기본적으로 서로 다른 쿠키를 사용한다. 관리자 route·path와 같은 호스트의 관리자 Livewire 요청에는 `LARAVEL_ADMIN_SESSION_COOKIE`가 적용되므로 한쪽 로그아웃이 다른 쪽 세션을 무효화하지 않는다.

```dotenv
SESSION_COOKIE=application-session
LARAVEL_ADMIN_SEPARATE_SESSION=true
LARAVEL_ADMIN_SESSION_COOKIE=application-admin-session
```

기존 단일 세션 쿠키 동작이 필요한 호스트 앱은 `LARAVEL_ADMIN_SEPARATE_SESSION=false`로 비활성화할 수 있다. 분리 설정을 처음 배포하면 기존 관리자 쿠키 대신 새 관리자 쿠키를 사용하므로 관리자는 한 번 다시 로그인해야 한다.

`ssh521/laravel-broadcast-notification`은 선택 의존성이다. 설치되어 있지 않거나 configured facade class가 없으면 알림 발송은 조용히 건너뛰고 관리자 기능은 계속 동작한다.

전체 연동은 `LARAVEL_ADMIN_BROADCAST_NOTIFICATIONS_ENABLED=false` 또는 `broadcast_notifications.enabled=false`로 끈다. 로그인 알림만 끄려면 `LARAVEL_ADMIN_BROADCAST_LOGIN_NOTIFICATIONS_ENABLED=false` 또는 `broadcast_notifications.login_events_enabled=false`를 사용한다. 커스텀 알림 facade를 쓸 때는 `broadcast_notifications.facade`에 `to()` 메서드를 제공하는 class를 지정한다.

## 관리자 인증

이 패키지는 호스트 앱의 `/login`, `/register`, Fortify view를 덮어쓰지 않는다. Breeze, Jetstream, 기존 커스텀 인증 화면과 route/view 충돌을 피하기 위해 관리자 로그인은 별도 경로인 `/admin/login`을 사용한다.

`auth.admin_login.enabled`가 `true`이면 인증되지 않은 관리자 요청은 `/admin/login`으로 이동한다. 비활성화하면 호스트 앱의 `login` route로 fallback한다.

관리자 계정은 일반 회원 `users` 테이블이 아니라 패키지 소유 `admin_users` 테이블을 사용한다. 일반 회원 관리는 `user_model`에 지정한 호스트 앱 모델을 기준으로 동작한다.

관리자 role/permission은 `admin_users` 모델과 관리자 guard 전용이다. 일반 회원 `user_model`에는 Spatie `HasRoles` trait을 요구하지 않으며, 일반 회원 관리 화면은 이름, 이메일, 비밀번호, 이메일 인증 상태 같은 회원 계정 정보만 다룬다.

## Super Admin

설치 seeder는 기본 관리자 계정을 만들고 `Super Admin` 역할을 부여한다. 별도 계정을 만들거나 기존 관리자 계정을 승격하려면 아래 command를 사용한다.

```bash
php artisan laravel-admin:make-super-admin admin@example.com --verified
```

비대화형 설치에서는 비밀번호 옵션을 넘길 수 있다.

```bash
php artisan laravel-admin:make-super-admin admin@example.com --password=secret --verified
```

`Super Admin` 역할은 `Gate::before`를 통해 관리자 권한 검사를 통과한다.

## Seeder

패키지 seeder는 퍼블리시하지 않고도 네임스페이스로 실행할 수 있다.

```bash
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\RolePermissionSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\MenuSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\AdminUserSeeder"
```

호스트 앱에서 역할, 권한, 메뉴, 관리자 계정 seeder를 커스터마이징해야 한다면 퍼블리시한다.

```bash
php artisan vendor:publish --tag=laravel-admin-seeders --no-interaction
```

퍼블리시 위치는 아래와 같다.

```text
database/seeders/vendor/laravel-admin
```

기본 `MenuSeeder`는 관리자 계정, 역할, 권한 관리를 위한 `관리자 전용` 메뉴 폴더를 만든다. 이 폴더는 기본 `Super Admin`, `Admin` 역할에 연결된다. 메뉴 폴더 노출은 role 연결로 판단하고, 실제 route/action 접근은 permission으로 다시 판단한다.

## 권한과 메뉴 계약

권한 엔진은 `spatie/laravel-permission`을 사용한다. 모든 관리자 role/permission은 `config('laravel-admin.auth.guard')` 기준 guard로 생성한다.

권한 구조는 아래 기준을 따른다.

- `Super Admin`은 모든 관리자 권한을 통과한다.
- role/permission은 관리자 계정과 관리자 화면 접근 제어에만 사용한다.
- role은 메뉴 카테고리 노출을 제어한다.
- permission은 화면과 기능 단위 접근을 제어한다.
- `Admin` 역할명 자체로는 Policy를 통과하지 않는다. `Admin`도 DB에 등록되고 부여된 permission으로 접근한다.
- 기능 패키지는 `AdminPackageRegistrar`로 메뉴와 permission을 등록한다.
- 관리자 화면용 API는 route-level `can:*` 미들웨어와 Policy/Gate를 사용한다.

자세한 기준은 [관리자 권한 계약](docs/admin-authorization-contract.md)을 참고한다.

## 기능 패키지

기능 패키지는 `laravel-admin` 위에 올라가는 독립 패키지다. core는 기능 자체를 직접 품지 않고 기능 패키지가 관리자 메뉴, 권한, route, installer를 등록할 수 있는 계약을 제공한다.

새 기능 패키지를 만들 때는 [새 기능 패키지 만드는 법](docs/feature-package-template.md)을 기준으로 삼는다.

기능 패키지에서 지켜야 할 기본 규칙은 아래와 같다.

- `ssh521/laravel-admin`에 의존한다.
- `AdminPackageRegistrar`로 메뉴와 permission을 등록한다.
- 관리자 route는 `config('laravel-admin.middleware')`와 admin guard 계약을 따른다.
- package-local 디자인 시스템을 만들지 않고 `ssh521/laravel-admin-ui` 컴포넌트를 사용한다.
- migration, seeder, installer는 반복 실행해도 안전해야 한다.

일반 회원 관리는 core가 기본 제공한다. 호스트 앱이 기본 `App\Models\User`가 아닌 다른 회원 모델을 사용하면 `config('laravel-admin.user_model')`만 교체한다.

## UI와 Blade 컴포넌트

관리자 화면의 source of truth는 `ssh521/laravel-admin-ui`다. 새 관리자 화면이나 기능 패키지 UI를 만들 때는 아래 문서를 먼저 확인한다.

- `../laravel-admin-ui/docs/admin-design-rules.md`
- `../laravel-admin-ui/docs/admin-ui-design-contract.md`

패키지 소유 관리자 컴포넌트는 `laravel-admin` 네임스페이스로 등록된다. 패키지 view 안에서는 prefix 있는 컴포넌트를 사용한다.

```blade
<x-laravel-admin::admin.layouts.admin>
<x-laravel-admin::admin.primary-button>
<x-laravel-admin::admin.input>
```

`<x-button>`, `<x-input>`, `<x-layouts.admin>`처럼 prefix 없는 컴포넌트는 호스트 앱의 소유일 수 있으므로 패키지 view에서 사용하지 않는다.

### 동적 Livewire 모달

동적 Livewire child component를 모달 안에 띄울 때는 `admin.modal-stack`을 페이지에 한 번 렌더링한다.

```blade
<livewire:admin.modal-stack />
```

부모 컴포넌트는 `admin:modal-stack:open` 또는 `admin:modal-stack:push` 이벤트로 모달을 연다. `open`은 기존 stack을 교체하고, `push`는 다중 모달을 쌓는다.

```php
$this->dispatch('admin:modal-stack:push', [
    'component' => 'admin.users.user-edit-modal',
    'params' => ['userId' => $user->id],
    'title' => '사용자 수정',
    'size' => 'md',
]);
```

배열 payload와 named 인자를 모두 지원한다. 배열 payload를 쓰면서 `width`, `height`, `minHeight`, `draggable`, `resizable`, `closeOnBackdrop` 같은 named 옵션을 함께 넘기면 named 옵션이 payload 값보다 우선한다.

```php
$this->dispatch(
    'admin:modal-stack:push',
    ['component' => 'admin.users.user-show-modal', 'params' => ['userId' => $user->id]],
    width: 680,
    height: 480,
    minHeight: 420,
);
```

ModalStack은 component name, modal id, params hash, version으로 고유 key를 만들고, 닫힌 모달을 stack에서 제거한다. 같은 edit component를 여러 번 열어도 Livewire 인스턴스와 validation error가 섞이지 않는다.

자식 컴포넌트는 저장 후 이벤트를 발생시키고 stack entry를 닫을 수 있다.

```php
$this->dispatch('user-saved', userId: $this->userId);
$this->dispatch('admin:modal-stack:close', id: $this->modalStackId);
```

컴포넌트만 퍼블리시하려면 아래 명령을 사용한다.

```bash
php artisan vendor:publish --tag=laravel-admin-ui-components --no-interaction
```

전체 view를 커스터마이징하려면 명시적으로 퍼블리시한다.

```bash
php artisan vendor:publish --tag=laravel-admin-ui-views --no-interaction
```

## 에셋과 Vite

관리자 CSS/JS는 `ssh521/laravel-admin-ui`가 제공하고, `laravel-admin:install`이 `resources/vendor/laravel-admin`으로 퍼블리시한다.

설치 커맨드는 `vite.config.js`의 Laravel plugin input에 아래 값을 추가한다.

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

패키지 업데이트 후에는 asset을 다시 퍼블리시하고 의존성을 설치한 뒤 빌드한다.

```bash
composer update ssh521/laravel-admin ssh521/laravel-admin-ui
php artisan laravel-admin:update
npm install
npm run build
```

`laravel-admin:update`는 기본적으로 migration과 UI asset만 업데이트에 안전한 방식으로 갱신한다. config, view, seeder는 호스트 앱에서 수정했을 수 있으므로 명시 옵션이 있을 때만 퍼블리시한다.

```bash
php artisan laravel-admin:update --publish-config
php artisan laravel-admin:update --publish-views
php artisan laravel-admin:update --publish-seeders
```

덮어쓰기가 필요할 때만 `--force-config`, `--force-views`, `--force-seeders`를 사용한다.

## AI 개발 문맥

호스트 프로젝트에서 Codex, Cursor, Claude, GitHub Copilot 같은 AI 도구가 `laravel-admin`의 책임 경계와 UI/권한 계약을 참조하게 하려면 AI 문맥 파일을 설치한다.

```bash
php artisan laravel-admin:install-ai-context
```

기본 출력 위치는 `.agents/laravel-admin.md`다. 기존 파일은 덮어쓰지 않는다. 갱신하려면 `--force`를 사용한다.

```bash
php artisan laravel-admin:install-ai-context --force
```

Codex용 skill 템플릿도 함께 설치할 수 있다.

```bash
php artisan laravel-admin:install-ai-context --with-codex-skill
```

기본 출력 위치는 `.codex/skills/laravel-admin-package`다.

## 개발 문서

- [프로젝트 방향과 개발 로드맵](docs/project-direction-roadmap.md)
- [관리자 권한 계약](docs/admin-authorization-contract.md)
- [새 기능 패키지 만드는 법](docs/feature-package-template.md)
- [AI 개발 가이드](docs/ai-development-guide.md)

관리자 UI 변경은 `ssh521/laravel-admin-ui` 문서와 현재 `Admin Users` 화면을 기준으로 맞춘다.

## 로컬 검증

패키지 자체 테스트는 아래 명령을 기준으로 한다.

```bash
vendor/bin/phpunit --configuration phpunit.xml.dist
```

로컬 패키지 checkout에 `vendor/bin/phpunit`이 없으면 workbench Laravel 앱에서 path repository로 설치한 뒤 검증한다. 패키지 변경 후 최소한 아래 순서로 확인한다.

- `php artisan laravel-admin:install --force`
- `npm install`
- `npm run build`
- `php artisan route:list --name=admin`
- `php artisan view:cache`
- `/admin/login`, `/admin`, 관리자 계정, 역할, 권한, 메뉴 화면 렌더링

문서만 수정한 경우에는 Markdown diff와 공백 오류를 확인한다.

```bash
git diff --check
```
