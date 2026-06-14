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
use Livewire\Livewire;
use Ssh521\Laravel{Feature}\Console\Commands\InstallCommand;
use Ssh521\Laravel{Feature}\Http\Livewire\Admin\Dashboard;

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
        $this->registerLivewireComponents();
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

    private function registerLivewireComponents(): void
    {
        Livewire::component('laravel-{feature}.admin.dashboard', Dashboard::class);
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

```php
<?php

use Illuminate\Support\Facades\Route;
use Ssh521\Laravel{Feature}\Http\Livewire\Admin\Dashboard;

$prefix = trim(config('laravel-admin.route_prefix', 'admin'), '/').'/'.trim(config('laravel-{feature}.prefix.admin', '{feature}'), '/');
$middleware = config('laravel-{feature}.admin.middleware') ?: config('laravel-admin.middleware', ['web', 'auth', 'verified']);

Route::prefix($prefix)->name('{feature}.admin.')->middleware($middleware)->group(function () {
    Route::get('/', Dashboard::class)
        ->middleware('can:{feature}-dashboard-access')
        ->name('dashboard');
});
```

원칙:

- 메뉴에 노출되는 관리자 라우트는 반드시 permission을 하나 이상 가진다.
- 라우트 권한은 Spatie permission name과 동일하게 둔다.
- 권한 이름은 기능 단위로 읽히게 만든다. 예: `{feature}-manage-orders`, `{feature}-manage-settings`.

## 7. 관리자 메뉴/권한 seeder 템플릿

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
                ['name' => '{feature}-manage-settings', 'description' => '{기능명} 설정 관리', 'sort_order' => 710],
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

`AdminPackageRegistrar` 기본 역할은 `Super Admin`, `Admin`이다. 패키지별로 다르게 주고 싶으면 definition에 `roles`, `permission_roles`를 넘긴다.

```php
'roles' => ['Super Admin'],
'permission_roles' => ['Super Admin', 'Admin'],
```

## 8. install command 템플릿

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

## 9. publish 정책

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

## 10. laravel-admin install 연동

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

## 11. Testbench 테스트 템플릿

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
private function assertRouteHasMiddleware(string $routeName, string $middleware): void
{
    $route = Route::getRoutes()->getByName($routeName);

    $this->assertNotNull($route, "Route [{$routeName}] was not registered.");
    $this->assertContains($middleware, $route->gatherMiddleware());
}
```

## 12. 개발 순서 체크리스트

1. composer package skeleton 생성
2. config 기본값 작성
3. ServiceProvider 작성
4. routes/admin.php 작성
5. 관리자 Livewire 화면 최소 1개 작성
6. admin menu/permission seeder 작성
7. install command 작성
8. `laravel-admin:install --with={feature}` 연동
9. Testbench 테스트 작성
10. `composer test`, `pint`, `composer validate` 실행
11. test app에서 path repository로 install 검증
12. publish 정책 문서화

## 13. 검증 명령

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

## 14. 완료 기준

새 기능 패키지는 아래 조건을 만족해야 한다.

- `php artisan laravel-{feature}:install`이 반복 실행 가능하다.
- 관리자 메뉴와 권한 등록이 idempotent하다.
- `Admin`, `Super Admin`에 기본 메뉴/권한이 연결된다.
- route-level `can:*` 권한이 존재한다.
- config/views/seeders publish가 명시 옵션으로 분리되어 있다.
- Testbench 테스트가 통과한다.
- host app의 auth, view, component 이름을 침범하지 않는다.
