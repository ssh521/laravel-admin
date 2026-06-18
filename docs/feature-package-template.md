# 새 기능 패키지 만드는 법

이 문서는 `ssh521/laravel-admin`을 기반으로 `ssh521/laravel-bbs`, `ssh521/laravel-blog`, `ssh521/laravel-file` 같은 기능 패키지를 새로 만들 때 따르는 템플릿이다.

목표는 다음과 같다.

- 기능 패키지는 `ssh521/laravel-admin`에 의존한다.
- 관리자 메뉴와 권한은 `AdminPackageRegistrar` 계약으로 등록한다.
- 설치 커맨드는 공통 `PackageInstaller` helper를 사용한다.
- publish 정책은 install/update 성격을 분리한다.
- Testbench 테스트로 메뉴, 권한, 라우트 권한 미들웨어를 고정한다.

## 1. 패키지 이름 정하기

예시:

```text
ssh521/laravel-shop
Ssh521\LaravelShop
laravel-shop
shop
```

권장 네이밍:

- Composer package: `ssh521/laravel-{feature}`
- Namespace: `Ssh521\Laravel{Feature}`
- Config key: `laravel-{feature}`
- Publish tag prefix: `laravel-{feature}`
- Artisan command: `laravel-{feature}:install`
- Admin route name prefix: `{feature}.admin.`
- Permission prefix: `{feature}-`

## 2. 기본 디렉터리 구조

```text
laravel-{feature}/
├── composer.json
├── config/
│   └── laravel-{feature}.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── Laravel{Feature}AdminMenuSeeder.php
├── resources/
│   └── views/
├── routes/
│   ├── web.php
│   └── admin.php
├── src/
│   ├── Console/
│   │   └── Commands/
│   │       └── InstallCommand.php
│   └── Laravel{Feature}ServiceProvider.php
├── tests/
│   ├── Feature/
│   │   └── Laravel{Feature}AdminRegistrationTest.php
│   ├── TestCase.php
│   └── bootstrap.php
└── phpunit.xml.dist
```

기능 패키지에 DB 테이블이 없으면 `database/migrations`는 생략해도 된다. 관리자 화면이 없으면 `routes/admin.php`와 admin menu seeder도 생략할 수 있지만, 보통 laravel-admin 기능 패키지는 관리자 메뉴를 가진다.

## 3. composer.json 템플릿

```json
{
    "name": "ssh521/laravel-{feature}",
    "description": "Laravel {Feature} package for laravel-admin.",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.3",
        "illuminate/support": "^13.0",
        "livewire/livewire": "^3.0|^4.0",
        "ssh521/laravel-admin": "^1.0"
    },
    "require-dev": {
        "orchestra/testbench": "^11.0"
    },
    "autoload": {
        "psr-4": {
            "Ssh521\\Laravel{Feature}\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Ssh521\\Laravel{Feature}\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "vendor/bin/phpunit --configuration phpunit.xml.dist"
    },
    "extra": {
        "laravel": {
            "providers": [
                "Ssh521\\Laravel{Feature}\\Laravel{Feature}ServiceProvider"
            ]
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

패키지를 Packagist에 공개할 계획이면 `version` 필드는 composer 경고가 나므로 빼는 것이 좋다. 내부 path repository로만 쓸 때는 있어도 동작에는 문제 없다.

## 4. ServiceProvider 템플릿

```php
<?php

namespace Ssh521\Laravel{Feature};

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Ssh521\Laravel{Feature}\Console\Commands\InstallCommand;

class Laravel{Feature}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-{feature}.php', 'laravel-{feature}');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-{feature}');

        $this->registerRoutes();
        $this->registerCommands();
        $this->registerPublishables();
    }

    private function registerRoutes(): void
    {
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        });
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
        ]);
    }

    private function registerPublishables(): void
    {
        $this->publishes([
            __DIR__.'/../config/laravel-{feature}.php' => config_path('laravel-{feature}.php'),
        ], 'laravel-{feature}-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-{feature}-migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-{feature}'),
        ], 'laravel-{feature}-views');

        $this->publishes([
            __DIR__.'/../database/seeders' => database_path('seeders/vendor/laravel-{feature}'),
        ], 'laravel-{feature}-seeders');
    }
}
```

주의할 점:

- package view namespace는 `laravel-{feature}`로 고정한다.
- host app component와 충돌하지 않도록 공용 Blade component 이름을 함부로 등록하지 않는다.
- admin route는 host의 `laravel-admin.middleware` 기본값을 따르게 만든다.

## 5. config 템플릿

```php
<?php

return [
    'prefix' => [
        'admin' => env('LARAVEL_{FEATURE}_ADMIN_PREFIX', '{feature}'),
    ],

    'admin' => [
        'middleware' => null,
    ],
];
```

`admin.middleware`가 `null`이면 기능 패키지는 `config('laravel-admin.middleware', ['web', 'auth', 'verified'])`를 사용한다. 패키지별로 별도 미들웨어가 필요할 때만 override한다.

## 6. 관리자 라우트 템플릿

기본 권장 방식은 `laravel-admin` 본체와 같은 controller + Blade resource route다. Livewire는 정렬, 모달, 실시간 검색처럼 화면 안 상호작용이 많은 일부 UI에 선택적으로 사용한다.

```php
<?php

use Illuminate\Support\Facades\Route;
use Ssh521\Laravel{Feature}\Http\Controllers\Admin\DashboardController;
use Ssh521\Laravel{Feature}\Http\Controllers\Admin\{Resource}Controller;

$prefix = trim(config('laravel-admin.route_prefix', 'admin'), '/').'/'.trim(config('laravel-{feature}.prefix.admin', '{feature}'), '/');
$middleware = config('laravel-{feature}.admin.middleware') ?: config('laravel-admin.middleware', ['web', 'auth', 'verified']);

Route::prefix($prefix)->name('{feature}.admin.')->middleware($middleware)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('can:{feature}-dashboard-access')
        ->name('dashboard');

    Route::get('/{resources}', [{Resource}Controller::class, 'index'])
        ->middleware('can:{feature}-{resources}-view')
        ->name('{resources}.index');
    Route::get('/{resources}/create', [{Resource}Controller::class, 'create'])
        ->middleware('can:{feature}-{resources}-create')
        ->name('{resources}.create');
    Route::post('/{resources}', [{Resource}Controller::class, 'store'])
        ->middleware('can:{feature}-{resources}-create')
        ->name('{resources}.store');
    Route::get('/{resources}/{{resource}}', [{Resource}Controller::class, 'show'])
        ->middleware('can:{feature}-{resources}-view')
        ->name('{resources}.show');
    Route::get('/{resources}/{{resource}}/edit', [{Resource}Controller::class, 'edit'])
        ->middleware('can:{feature}-{resources}-update')
        ->name('{resources}.edit');
    Route::put('/{resources}/{{resource}}', [{Resource}Controller::class, 'update'])
        ->middleware('can:{feature}-{resources}-update')
        ->name('{resources}.update');
    Route::delete('/{resources}/{{resource}}', [{Resource}Controller::class, 'destroy'])
        ->middleware('can:{feature}-{resources}-delete')
        ->name('{resources}.destroy');
});
```

원칙:

- 메뉴에 노출되는 관리자 라우트는 반드시 permission을 하나 이상 가진다.
- 라우트 권한은 Spatie permission name과 동일하게 둔다.
- 권한 이름은 기능 단위로 읽히게 만든다. 예: `{feature}-manage-orders`, `{feature}-manage-settings`.
- CRUD 권한 이름은 기본적으로 `{feature}-{resources}-view`, `{feature}-{resources}-create`, `{feature}-{resources}-update`, `{feature}-{resources}-delete` 형식을 사용한다.
- route middleware permission, `AdminPackageRegistrar` seeder permission, 테스트 fixture permission은 같은 문자열을 사용한다.
- index/show/search/read-only 보조 API는 view 권한을, create/store는 create 권한을, edit/update/reorder/toggle/status 변경은 update 권한을, destroy는 delete 권한을 사용한다.
- `Super Admin` bypass는 core `Gate::before`에 맡기고, 기능 패키지는 route-level permission과 action-level permission을 일관되게 선언한다.

## 7. Controller + Blade CRUD 계약

기능 패키지의 관리자 CRUD는 아래 계약을 기본으로 한다. 패키지 특성상 Livewire 중심 구현이 더 적합한 경우에도 route name, permission, view publish 정책은 이 계약과 최대한 맞춘다.

### 7.1 디렉터리

```text
laravel-{feature}/
├── routes/
│   └── admin.php
├── src/
│   └── Http/
│       ├── Controllers/
│       │   └── Admin/
│       │       ├── DashboardController.php
│       │       └── {Resource}Controller.php
│       └── Requests/
│           └── Admin/
│               ├── Store{Resource}Request.php
│               └── Update{Resource}Request.php
└── resources/
    └── views/
        └── admin/
            ├── dashboard.blade.php
            └── {resources}/
                ├── index.blade.php
                ├── create.blade.php
                ├── edit.blade.php
                ├── show.blade.php
                └── partials/
                    └── form.blade.php
```

### 7.2 Route Names

기본 route name은 `{feature}.admin.` prefix를 사용한다.

```text
{feature}.admin.dashboard
{feature}.admin.{resources}.index
{feature}.admin.{resources}.create
{feature}.admin.{resources}.store
{feature}.admin.{resources}.show
{feature}.admin.{resources}.edit
{feature}.admin.{resources}.update
{feature}.admin.{resources}.destroy
```

route parameter는 단수 camel case를 사용한다.

```php
Route::resource('pages', PageController::class)
    ->parameters(['pages' => 'page']);
```

### 7.3 Controllers

Controller는 `Ssh521\Laravel{Feature}\Http\Controllers\Admin` namespace 하위에 둔다.

기본 action:

```text
index(Request $request)
create()
store(Store{Resource}Request $request)
show({Resource} ${resource})
edit({Resource} ${resource})
update(Update{Resource}Request $request, {Resource} ${resource})
destroy({Resource} ${resource})
```

원칙:

- list query, sorting, filtering은 `index`에서 명시적으로 처리한다.
- 저장 validation은 FormRequest로 분리한다.
- 생성/수정 성공 후 index 또는 show로 redirect하고 session flash message를 남긴다.
- 삭제는 가능하면 soft delete를 우선 고려한다.
- 복잡한 도메인 작업은 Controller 안에 두지 말고 Action 또는 Service로 분리한다.

### 7.4 Views

package view namespace는 `laravel-{feature}`로 고정한다.

```php
return view('laravel-{feature}::admin.{resources}.index', compact('{resources}'));
```

관리자 view는 `resources/views/admin` 하위에 둔다.

화면별 계약:

- `index`: 검색/필터 bar, table 또는 card grid, pagination, row actions를 제공한다.
- `create`: sectioned form과 저장/취소 action을 제공한다.
- `edit`: sectioned form, 좌측 destructive action, 우측 저장/취소 action을 제공한다.
- `show`: semantic `dl`, `dt`, `dd` 기반 detail view와 action footer를 제공한다.
- `partials/form`: create/edit에서 공유하며 field name을 route/action 변경 없이 유지한다.

UI는 `ssh521/laravel-admin-ui/docs/admin-ui-design-contract.md`를 따른다.

### 7.5 Permissions

CRUD 권한은 route middleware와 seeder permission name이 일치해야 한다. 아래 네 가지 permission은 route, seeder, 테스트에서 같은 문자열로 반복 사용한다.

```text
{feature}-{resources}-view
{feature}-{resources}-create
{feature}-{resources}-update
{feature}-{resources}-delete
```

추가 상태 전환이나 publish 기능은 명시적 권한을 둔다.

```text
{feature}-{resources}-publish
{feature}-{resources}-restore
{feature}-{resources}-force-delete
```

web admin route는 route-level `can:*` 미들웨어를 기본 권한 경계로 둔다. Controller action 내부에서는 record 상태가 필요한 검사만 Policy나 service action으로 보강한다. route-level permission은 사용자에게 기능 화면이 열리는지 결정하고, Policy는 owner, publish 상태, 삭제 가능 여부처럼 record 상태에 따른 추가 제약만 담당한다.

### 7.6 Livewire 사용 기준

Livewire는 기본 CRUD 구현 방식이 아니라 보조 UI로 사용한다.

적합한 경우:

- drag/drop 정렬
- 검색 모달
- 파일 선택 모달
- 짧은 quick edit modal
- 상태 토글
- 실시간 미리보기

피해야 하는 경우:

- 단순 index/create/edit/show CRUD 전체를 Livewire로만 구현하는 것
- route, field name, validation behavior를 Livewire 내부 상태에만 숨기는 것
- view publish 후 커스터마이징하기 어려운 대형 component로 화면 전체를 묶는 것

권한이 필요한 Livewire action은 admin route와 같은 guard/middleware 위에서 동작한다는 전제에 더해 action 시작 지점에서 `Gate::authorize()` 또는 `$this->authorize()`로 검사한다. route 밖에서 실행될 수 있는 정렬, 카테고리 변경, 상태 토글, quick edit 저장 action은 route middleware에만 의존하지 않는다.

예:

```php
public function updateOrder(array $order): void
{
    Gate::authorize('{feature}-{resources}-update');

    // reorder...
}
```

### 7.7 API 권한 기준

core `laravel-admin`은 기본 API surface를 비워두고, 기능 패키지가 필요한 API route를 직접 등록한다.

API route를 추가할 때는 먼저 API 성격을 정한다.

- 관리자 화면 보조 API: admin 화면의 AJAX, fetch, modal, reorder, search를 지원한다.
- 외부 token API: 외부 클라이언트나 자동화가 token으로 호출한다.
- 관리자 기능 token API: token으로 호출하지만 관리자 데이터 변경이나 관리자 기능 실행까지 수행한다.
- 공개 API: 관리자 권한 계약 밖의 공개 surface다.

관리자 화면에서 AJAX나 fetch로 호출하는 API는 web admin route와 같은 admin session middleware와 Spatie permission 이름을 사용한다. 외부 token API는 기능 패키지가 명시적으로 opt-in하고, Sanctum 같은 token 인증과 ability를 사용한다. Sanctum 의존성과 설정 권장안은 기능 패키지 README에 문서화한다.

권장 조합:

- 관리자 화면 보조 API: `config('laravel-admin.middleware')` + `can:{permission-name}` 또는 Policy
- 외부 token API: `auth:sanctum` + token ability
- 관리자 기능 token API: `auth:sanctum` + token ability + `can:{permission-name}` 또는 Policy
- 공개 API: 패키지 도메인 정책에 따로 정의하고 관리자 메뉴/permission seeder에 묶지 않는다.

관리자 화면 보조 API 예시:

```php
$middleware = config('laravel-{feature}.admin.middleware') ?: config('laravel-admin.middleware', ['web', 'auth', 'verified']);

Route::prefix($prefix.'/api')
    ->name('{feature}.admin.api.')
    ->middleware($middleware)
    ->group(function () {
        Route::get('/{resources}/search', [{Resource}Controller::class, 'search'])
            ->middleware('can:{feature}-{resources}-view')
            ->name('{resources}.search');

        Route::post('/{resources}/reorder', [{Resource}Controller::class, 'reorder'])
            ->middleware('can:{feature}-{resources}-update')
            ->name('{resources}.reorder');
    });
```

외부 token API 예시:

```php
Route::prefix('api/{feature}')
    ->name('{feature}.api.')
    ->middleware(['api', 'auth:sanctum', 'ability:{feature}:read'])
    ->group(function () {
        Route::get('/{resources}', [Api\{Resource}Controller::class, 'index'])
            ->name('{resources}.index');
    });
```

API 테스트에는 미인증, ability 없는 token, permission 없는 관리자, 허용 관리자, Super Admin bypass 케이스를 포함한다.

## 8. 관리자 메뉴/권한 seeder 템플릿

```php
<?php

namespace Ssh521\Laravel{Feature}\Database\Seeders;

use Illuminate\Database\Seeder;
use Ssh521\LaravelAdmin\Support\AdminPackageRegistrar;

class Laravel{Feature}AdminMenuSeeder extends Seeder
{
    public function run(): void
    {
        $result = app(AdminPackageRegistrar::class)->register('laravel-{feature}', [
            'category' => [
                'name' => '{기능명} 관리',
                'is_active' => true,
                'sort_order' => 700,
            ],
            'menus' => [
                [
                    'name' => '{기능명} 홈',
                    'route_name' => '{feature}.admin.dashboard',
                    'url' => '/admin/{feature}',
                    'sort_order' => 0,
                    'icon' => 'fas fa-cog',
                    'description' => '{기능명} 관리 홈',
                ],
            ],
            'permissions' => [
                ['name' => '{feature}-dashboard-access', 'description' => '{기능명} 관리자 대시보드 접근', 'sort_order' => 700],
                ['name' => '{feature}-{resources}-view', 'description' => '{기능명} 목록/상세 조회', 'sort_order' => 710],
                ['name' => '{feature}-{resources}-create', 'description' => '{기능명} 등록', 'sort_order' => 711],
                ['name' => '{feature}-{resources}-update', 'description' => '{기능명} 수정/정렬/상태 변경', 'sort_order' => 712],
                ['name' => '{feature}-{resources}-delete', 'description' => '{기능명} 삭제', 'sort_order' => 713],
            ],
        ]);

        $this->warnMissingRoles(array_unique([
            ...$result['missing_menu_roles'],
            ...$result['missing_permission_roles'],
        ]));
    }

    /**
     * @param  array<int, string>  $roleNames
     */
    private function warnMissingRoles(array $roleNames): void
    {
        if ($roleNames !== []) {
            $this->command?->warn('laravel-admin RolePermissionSeeder를 먼저 실행하면 {Feature} 메뉴와 권한이 기본 역할에 할당됩니다.');
        }
    }
}
```

`AdminPackageRegistrar` 기본 역할은 `Super Admin`, `Admin`이다. `roles`는 메뉴 폴더 노출 역할이고, `permission_roles`는 세부 권한을 받을 역할이다. 패키지별로 다르게 주고 싶으면 definition에 `roles`, `permission_roles`를 넘긴다.

```php
'roles' => ['Super Admin'],
'permission_roles' => ['Super Admin', 'Admin'],
```

## 9. install command 템플릿

```php
<?php

namespace Ssh521\Laravel{Feature}\Console\Commands;

use Illuminate\Console\Command;
use Ssh521\LaravelAdmin\Support\PackageInstaller;
use Ssh521\Laravel{Feature}\Database\Seeders\Laravel{Feature}AdminMenuSeeder;

class InstallCommand extends Command
{
    protected $signature = 'laravel-{feature}:install
        {--force : Force database commands and overwrite update-safe published files}
        {--skip-migrate : Publish migrations without running them}
        {--skip-seed : Do not seed Laravel {Feature} admin menus}
        {--publish-config : Publish Laravel {Feature} config}
        {--publish-migrations : Publish Laravel {Feature} migrations}
        {--publish-views : Publish Laravel {Feature} views}
        {--publish-seeders : Publish Laravel {Feature} seeders}
        {--force-config : Overwrite published Laravel {Feature} config}
        {--force-views : Overwrite published Laravel {Feature} views}
        {--force-seeders : Overwrite published Laravel {Feature} seeders}
        {--with-test-data : Seed Laravel {Feature} sample data}';

    protected $description = 'Publish, migrate, and seed Laravel {Feature} resources.';

    public function handle(): int
    {
        $this->info('Installing Laravel {Feature}...');

        $this->publishResources();

        if (! $this->option('skip-migrate')) {
            $this->installer()->migrate($this, (bool) $this->option('force'));
        }

        if (! $this->option('skip-seed') && $this->canSeedAdminMenus()) {
            $this->seed(Laravel{Feature}AdminMenuSeeder::class);
        }

        $this->newLine();
        $this->info('Laravel {Feature} install complete.');

        return self::SUCCESS;
    }

    private function publishResources(): void
    {
        if ($this->option('publish-config') || $this->option('force-config')) {
            $this->installer()->publishTags($this, ['laravel-{feature}-config'], (bool) $this->option('force-config'));
        }

        if ($this->option('publish-migrations')) {
            $this->installer()->publishTags($this, ['laravel-{feature}-migrations']);
        }

        if ($this->option('publish-views') || $this->option('force-views')) {
            $this->installer()->publishTags($this, ['laravel-{feature}-views'], (bool) $this->option('force-views'));
        }

        if ($this->option('publish-seeders') || $this->option('force-seeders')) {
            $this->installer()->publishTags($this, ['laravel-{feature}-seeders'], (bool) $this->option('force-seeders'));
        }
    }

    private function seed(string $class): void
    {
        $this->installer()->seed($this, $class, (bool) $this->option('force'));
    }

    private function canSeedAdminMenus(): bool
    {
        return $this->installer()->canSeedAdminMenus($this, '{Feature}');
    }

    private function installer(): PackageInstaller
    {
        return app(PackageInstaller::class);
    }
}
```

## 10. publish 정책

install 기본 동작:

- package DB가 있으면 migrate 실행
- admin tables가 있으면 admin menu/permission seeding 실행
- config/views/seeders는 기본으로 publish하지 않는다
- host customization이 가능한 파일은 명시 옵션으로만 publish한다

권장 옵션:

```bash
php artisan laravel-{feature}:install
php artisan laravel-{feature}:install --publish-config
php artisan laravel-{feature}:install --publish-views
php artisan laravel-{feature}:install --publish-seeders
php artisan laravel-{feature}:install --force-views
```

반복 업데이트에서 config/views/seeders를 무조건 덮어쓰지 않는다. 패키지 기본 asset처럼 항상 최신이 필요한 파일만 update-safe 대상으로 둔다.

## 11. laravel-admin install 연동

`laravel-admin:install --with={feature}`로 함께 설치하려면 `laravel-admin`의 install command installer map에 추가한다.

```php
$this->installer()->installFeaturePackages(
    $this,
    $this->installer()->parsePackageList($this->option('with')),
    [
        'bbs' => 'laravel-bbs:install',
        'blog' => 'laravel-blog:install',
        'file' => 'laravel-file:install',
        '{feature}' => 'laravel-{feature}:install',
    ],
    [
        '--force' => (bool) $this->option('force'),
        '--skip-migrate' => (bool) $this->option('skip-migrate'),
        '--skip-seed' => (bool) $this->option('skip-seed'),
        '--with-test-data' => (bool) $this->option('with-test-data'),
    ]
);
```

기능 패키지 install command는 admin installer와 옵션 이름을 맞추면 조합 설치가 단순해진다.

## 12. Testbench 테스트 템플릿

테스트는 최소 세 가지를 확인한다.

- seeder가 메뉴 카테고리, 메뉴, 권한을 만든다.
- `Admin`, `Super Admin` 역할에 메뉴 카테고리와 권한이 연결된다.
- admin route에 기대한 `can:*` 미들웨어가 붙는다.

`tests/TestCase.php` 예시:

```php
<?php

namespace Ssh521\Laravel{Feature}\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;
use Ssh521\LaravelAdmin\Database\Seeders\RolePermissionSeeder;
use Ssh521\LaravelAdmin\LaravelAdminServiceProvider;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\Laravel{Feature}\Laravel{Feature}ServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--path' => $this->adminPackagePath('database/migrations'),
            '--realpath' => true,
        ])->run();

        $this->seed(RolePermissionSeeder::class);
    }

    protected function getPackageProviders($app): array
    {
        return [
            PermissionServiceProvider::class,
            LivewireServiceProvider::class,
            LaravelAdminServiceProvider::class,
            Laravel{Feature}ServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('permission.cache.store', 'array');
        $app['config']->set('permission.models.permission', Permission::class);
    }

    protected function adminPackagePath(string $path = ''): string
    {
        $basePath = dirname(__DIR__, 2).'/laravel-admin';

        return $path === '' ? $basePath : $basePath.'/'.$path;
    }
}
```

route middleware assertion 예시:

```php
public function test_admin_routes_use_seeded_route_level_permissions(): void
{
    $expected = [
        '{feature}.admin.dashboard' => '{feature}-dashboard-access',
        '{feature}.admin.{resources}.index' => '{feature}-{resources}-view',
        '{feature}.admin.{resources}.create' => '{feature}-{resources}-create',
        '{feature}.admin.{resources}.store' => '{feature}-{resources}-create',
        '{feature}.admin.{resources}.show' => '{feature}-{resources}-view',
        '{feature}.admin.{resources}.edit' => '{feature}-{resources}-update',
        '{feature}.admin.{resources}.update' => '{feature}-{resources}-update',
        '{feature}.admin.{resources}.destroy' => '{feature}-{resources}-delete',
    ];

    foreach ($expected as $routeName => $permission) {
        $this->assertRouteHasMiddleware($routeName, "can:{$permission}");
        $this->assertDatabaseHas('permissions', [
            'name' => $permission,
            'guard_name' => config('laravel-admin.auth.guard', 'laravel_admin'),
        ]);
    }
}

private function assertRouteHasMiddleware(string $routeName, string $middleware): void
{
    $route = Route::getRoutes()->getByName($routeName);

    $this->assertNotNull($route, "Route [{$routeName}] was not registered.");
    $this->assertContains($middleware, $route->gatherMiddleware());
}
```

## 13. 개발 순서 체크리스트

1. composer package skeleton 생성
2. config 기본값 작성
3. ServiceProvider 작성
4. routes/admin.php 작성
5. 관리자 Controller + Blade CRUD 화면 작성
6. admin menu/permission seeder 작성
7. install command 작성
8. `laravel-admin:install --with={feature}` 연동
9. Testbench 테스트 작성
10. `composer test`, `pint`, `composer validate` 실행
11. test app에서 path repository로 install 검증
12. publish 정책 문서화

## 14. 검증 명령

패키지 단독:

```bash
composer install
composer test
vendor/bin/pint --test
composer validate --no-check-publish
```

test app에서:

```bash
composer require ssh521/laravel-{feature}:dev-main
php artisan package:discover
php artisan laravel-admin:install --with={feature}
php artisan route:list --path=admin/{feature} -v
```

## 15. 완료 기준

새 기능 패키지는 아래 조건을 만족해야 한다.

- `php artisan laravel-{feature}:install`이 반복 실행 가능하다.
- 관리자 메뉴와 권한 등록이 idempotent하다.
- `Admin`, `Super Admin`에 기본 메뉴/권한이 연결된다.
- route-level `can:*` 권한이 존재하고 seeder permission name과 일치한다.
- 권한이 필요한 Livewire action은 action 시작 지점에서 route-level permission과 같은 이름으로 검사한다.
- config/views/seeders publish가 명시 옵션으로 분리되어 있다.
- Testbench 테스트가 통과한다.
- host app의 auth, view, component 이름을 침범하지 않는다.

## 16. 기존 기능 패키지 정비 과제

이 문서의 Controller + Blade CRUD 계약은 신규 패키지부터 우선 적용한다. 이미 운영 중인 기능 패키지는 호환성을 유지하면서 점진적으로 맞춘다.

대상 패키지:

- `ssh521/laravel-bbs`
- `ssh521/laravel-blog`

정비 방향:

- 단순 설정/마스터 데이터 CRUD는 Controller + Blade resource 구조로 전환한다.
- 상호작용이 많은 목록, 정렬, 모달, 빠른 상태 변경은 Livewire 보조 UI로 유지할 수 있다.
- 기존 route name, permission name, view publish path는 바로 깨지지 않게 alias 또는 migration guide를 제공한다.
- 관리자 화면은 `laravel-admin-ui/docs/admin-ui-design-contract.md`의 `index`, `create`, `edit`, `show`, `modal` resource pattern을 따른다.
- route-level `can:*` 미들웨어와 admin menu/permission seeder를 공통 계약 이름 체계에 맞춘다.
- 전환 작업은 기능 변경과 UI/구조 변경을 분리해서 진행한다.

패키지별 우선 후보:

- `laravel-bbs`: board/group 설정 CRUD, admin dashboard, 권한/스킨 설정 화면
- `laravel-blog`: category/tag/settings CRUD, admin dashboard, 글 상태/분류 관리 화면
