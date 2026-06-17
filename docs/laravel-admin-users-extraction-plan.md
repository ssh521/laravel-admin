# laravel-admin-users 패키지 분리 준비

이 문서는 `ssh521/laravel-admin` core에 남아 있는 일반 `User` 관리 기능을 `ssh521/laravel-admin-users` 기능 패키지로 분리하기 위한 작업 목록이다.

현재 패키지는 개발 초기이고 사용자는 패키지 작성자 1명이므로 이전 설치본 호환성보다 구조 정리를 우선한다. 따라서 core에서 일반 `User` 관련 route, policy, permission, menu, UI 연결은 deprecated 처리 없이 제거한다.

## 분리 목표

`laravel-admin` core는 관리자 foundation에 집중한다.

- 관리자 인증과 `AdminUser`
- 관리자 role/permission
- 관리자 menu/menu category
- `AdminPackageRegistrar`
- install/update command
- `laravel-admin-ui` 연결

`laravel-admin-users`는 호스트 앱의 일반 `User` 관리를 담당한다.

- `App\Models\User` 목록, 상세, 등록, 수정, 삭제
- 이메일 인증 상태 변경
- user search/detail/form Livewire modal
- user 관련 admin route
- user 관련 API route와 resource
- user 관련 permission과 menu 등록

## 새 패키지 기본 정보

```text
Composer package: ssh521/laravel-admin-users
Namespace: Ssh521\LaravelAdminUsers
Config key: laravel-admin-users
View namespace: laravel-admin-users
Command: laravel-admin-users:install
Admin route name prefix: admin-users.admin.
Permission prefix: user-
```

`admin-users`라는 이름은 관리자 계정 resource인 `admin.admin-users.*`와 혼동될 수 있다. 그래서 route name prefix는 최종 결정 전에 다시 검토한다.

대안:

```text
Package: ssh521/laravel-user-admin
Namespace: Ssh521\LaravelUserAdmin
Route prefix: user-admin.admin.
Command: laravel-user-admin:install
```

현재 대화에서는 `ssh521/laravel-admin-users`를 우선 이름으로 사용한다.

## 새 패키지 권장 구조

```text
laravel-admin-users/
├── composer.json
├── config/
│   └── laravel-admin-users.php
├── database/
│   └── seeders/
│       └── LaravelAdminUsersSeeder.php
├── resources/
│   └── views/
│       ├── admin/
│       │   └── users/
│       └── livewire/
│           └── admin/
│               └── users/
├── routes/
│   ├── admin.php
│   └── api.php
├── src/
│   ├── Actions/User/
│   ├── Console/Commands/InstallCommand.php
│   ├── DTOs/User/
│   ├── Http/Controllers/Admin/UserController.php
│   ├── Http/Controllers/Api/UserController.php
│   ├── Http/Resources/UserResource.php
│   ├── Livewire/Admin/Users/
│   ├── Models/User.php
│   ├── Policies/UserPolicy.php
│   ├── Services/UserService.php
│   ├── Support/ResolvesUserModel.php
│   ├── Validators/User/
│   └── LaravelAdminUsersServiceProvider.php
├── tests/
└── phpunit.xml.dist
```

## User model 처리

현재 core의 `src/Models/User.php`는 단순히 `App\Models\User`를 상속한다.

```php
class User extends BaseUser
{
}
```

새 패키지에서는 호스트 앱 user model을 config로 받는다.

```php
return [
    'user_model' => App\Models\User::class,
];
```

Action, service, policy는 직접 `App\Models\User`를 참조하지 않고 resolver를 사용한다.

```php
final class ResolvesUserModel
{
    public function class(): string
    {
        return config('laravel-admin-users.user_model', \App\Models\User::class);
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return ($this->class())::query();
    }
}
```

## core에서 이동할 파일

### PHP 코드

| 현재 파일 | 새 패키지 위치 | 처리 |
| --- | --- | --- |
| `src/Models/User.php` | `src/Models/User.php` 또는 `Support/ResolvesUserModel.php` | config 기반 resolver로 재작성 |
| `src/Services/UserService.php` | `src/Services/UserService.php` | namespace 변경 후 이동 |
| `src/Actions/User/*` | `src/Actions/User/*` | namespace 변경 후 이동 |
| `src/DTOs/User/*` | `src/DTOs/User/*` | namespace 변경 후 이동 |
| `src/Validators/User/*` | `src/Validators/User/*` | namespace 변경 후 이동 |
| `src/Policies/UserPolicy.php` | `src/Policies/UserPolicy.php` | admin guard와 token ability 기준 유지 |
| `src/Http/Controllers/Admin/UserController.php` | `src/Http/Controllers/Admin/UserController.php` | namespace와 view namespace 변경 |
| `src/Http/Controllers/Api/UserController.php` | `src/Http/Controllers/Api/UserController.php` | namespace와 route 이동 |
| `src/Http/Resources/UserResource.php` | `src/Http/Resources/UserResource.php` | namespace 변경 후 이동 |
| `src/Livewire/Admin/Users/*` | `src/Livewire/Admin/Users/*` | component name 충돌 검토 후 이동 |
| `tests/Feature/UserPolicyTest.php` | `tests/Feature/UserPolicyTest.php` | 새 패키지 테스트로 이동 |

### laravel-admin-ui view

`laravel-admin-ui`에서 아래 view를 새 패키지 소유로 이동하거나 publish source를 조정한다.

| 현재 파일 | 새 패키지 위치 |
| --- | --- |
| `resources/views/admin/users/index.blade.php` | `laravel-admin-users/resources/views/admin/users/index.blade.php` |
| `resources/views/admin/users/create.blade.php` | `laravel-admin-users/resources/views/admin/users/create.blade.php` |
| `resources/views/admin/users/edit.blade.php` | `laravel-admin-users/resources/views/admin/users/edit.blade.php` |
| `resources/views/admin/users/show.blade.php` | `laravel-admin-users/resources/views/admin/users/show.blade.php` |
| `resources/views/admin/users/select-user.blade.php` | `laravel-admin-users/resources/views/admin/users/select-user.blade.php` |
| `resources/views/admin/users/partials/form.blade.php` | `laravel-admin-users/resources/views/admin/users/partials/form.blade.php` |
| `resources/views/livewire/admin/users/user-table.blade.php` | `laravel-admin-users/resources/views/livewire/admin/users/user-table.blade.php` |
| `resources/views/livewire/admin/users/user-form.blade.php` | `laravel-admin-users/resources/views/livewire/admin/users/user-form.blade.php` |
| `resources/views/livewire/admin/users/user-detail-modal.blade.php` | `laravel-admin-users/resources/views/livewire/admin/users/user-detail-modal.blade.php` |
| `resources/views/livewire/admin/users/user-search-modal.blade.php` | `laravel-admin-users/resources/views/livewire/admin/users/user-search-modal.blade.php` |

새 패키지 view는 `laravel-admin-users::...` 네임스페이스를 사용하되, 공통 layout과 component는 계속 `x-laravel-admin::...`를 사용한다.

## core에서 제거할 연결 지점

### ServiceProvider

`src/LaravelAdminServiceProvider.php`에서 제거:

- `use Ssh521\LaravelAdmin\Models\User`
- `use Ssh521\LaravelAdmin\Policies\UserPolicy`
- `use Ssh521\LaravelAdmin\Livewire\Admin\Users\UserDetailModal`
- `use Ssh521\LaravelAdmin\Livewire\Admin\Users\UserForm`
- `use Ssh521\LaravelAdmin\Livewire\Admin\Users\UserSearchModal`
- `use Ssh521\LaravelAdmin\Livewire\Admin\Users\UserTable`
- `Livewire::component('admin.users.*', ...)`
- `Gate::policy(User::class, UserPolicy::class)`

새 패키지 ServiceProvider에서 등록:

- user policy
- user Livewire components
- admin routes
- API routes
- config/views/publishes
- install command

### Web routes

`routes/web.php`에서 제거:

- `use Ssh521\LaravelAdmin\Http\Controllers\Admin\UserController`
- `use Ssh521\LaravelAdmin\Livewire\Admin\Users\UserTable`
- `/admin/users` route group 전체

새 패키지 `routes/admin.php`에서 등록한다.

### API routes

`routes/api.php`에서 user 관련 API를 core에서 제거한다.

- `AuthController::login`
- `/user`
- `/logout`
- `Route::apiResource('users', UserController::class)`

현재 `src/Http/Controllers/Api/AuthController.php`는 `Ssh521\LaravelAdmin\Services\AuthService`를 참조하지만 해당 class가 core에 없다. 이 API 인증 영역은 `laravel-admin-users`로 이동하면서 다음 중 하나로 정리한다.

1. `laravel-admin-users`에서 `AuthService`를 새로 구현한다.
2. API 인증을 범위에서 제외하고 user CRUD API만 옮긴다.
3. API 기능을 별도 패키지로 미룬다.

초기 분리에서는 2번을 추천한다. web admin user CRUD 분리를 먼저 끝내고 API는 별도 단계에서 정리한다.

### Seeder

`database/seeders/MenuSeeder.php`에서 제거:

- `member` category
- `회원` menu
- `memberCategoryId`
- Super Admin/Admin에 member category 연결
- User role에 general category 연결 여부 재검토

`database/seeders/RolePermissionSeeder.php`에서 제거:

- `super-admin-users`
- `admin-manage-users`
- `user-list`
- `user-view`
- `user-create`
- `user-edit`
- `user-delete`
- `user-view-auth-list`
- `User` role
- `Guest` role
- user 관련 obsolete permission

새 패키지 seeder에서 `AdminPackageRegistrar`로 등록:

```php
app(AdminPackageRegistrar::class)->register('laravel-admin-users', [
    'category' => [
        'name' => '회원 관리',
        'sort_order' => 200,
    ],
    'menus' => [
        [
            'name' => '회원',
            'route_name' => 'admin.users.index',
            'url' => '/admin/users',
            'icon' => 'fas fa-user',
            'description' => '사용자 목록',
        ],
    ],
    'permissions' => [
        ['name' => 'user-list'],
        ['name' => 'user-view'],
        ['name' => 'user-create'],
        ['name' => 'user-edit'],
        ['name' => 'user-delete'],
        ['name' => 'user-view-auth-list'],
    ],
]);
```

Route name은 최종 package naming 결정 후 확정한다.

### Tests

core에서 수정:

- `tests/Feature/UiResourceOwnershipTest.php`에서 `laravel-admin::admin.users.index`와 `laravel-admin::livewire.admin.users.user-table` 기대 제거
- core에 user route가 없다는 테스트 추가 가능
- core seeder에 user permission이 없다는 테스트 추가 가능

새 패키지로 이동:

- `tests/Feature/UserPolicyTest.php`
- user route authorization tests
- user menu/permission registration test
- user views exist test

## Livewire event naming

현재 user Livewire event는 `admin-users:user-*` prefix를 사용한다.

예:

- `admin-users:user-form:open`
- `admin-users:user-detail-modal:open`
- `admin-users:user:saved`

이 이름은 관리자 계정 resource인 `admin-users`와 혼동된다. 새 패키지에서는 아래 중 하나로 변경한다.

추천:

```text
admin-user-management:user-form:open
admin-user-management:user-detail-modal:open
admin-user-management:user:saved
```

더 짧은 대안:

```text
laravel-admin-users:user-form:open
laravel-admin-users:user-detail-modal:open
laravel-admin-users:user:saved
```

호환성을 고려하지 않으므로 새 패키지로 옮길 때 event name도 같이 정리한다.

## composer 의존성 검토

core `composer.json` 의존성 처리 결과:

- `laravel/sanctum`: 일반 user API 제거와 함께 core 필수 의존성에서 제거
- `laravel/jetstream`: core 기능에 직접 필요하지 않아 제거
- `livewire/livewire`: core left menu/header/modal이 Livewire를 쓰므로 core 유지 가능
- `spatie/laravel-permission`: core role/permission 때문에 유지
- `ssh521/laravel-admin-ui`: core UI 연결 때문에 유지

`laravel-admin-users` 의존성 후보:

- `ssh521/laravel-admin`
- `livewire/livewire`
- `laravel/sanctum`은 API 기능을 포함할 때만 추가

## 작업 순서

1. `ssh521/laravel-admin-users` 패키지 디렉터리와 기본 composer/service provider를 생성한다.
2. user web admin 기능만 먼저 이동한다.
3. 새 패키지에서 `AdminPackageRegistrar` seeder로 회원 메뉴와 permission을 등록한다.
4. core에서 user web route, Livewire 등록, policy 등록, menu/permission seeder 항목을 제거한다.
5. workbench에서 `laravel-admin + laravel-admin-users` 조합을 path repository로 연결한다.
6. user UI view를 `laravel-admin-ui`에서 새 패키지로 이동한다.
7. user policy와 route authorization 테스트를 새 패키지에서 통과시킨다.
8. API user/auth 기능은 web admin 분리 후 별도 작업으로 결정한다.

## 완료 기준

- `laravel-admin` core에 일반 `User` CRUD 코드가 없다.
- `laravel-admin` core seeder에 `회원 관리` category와 `user-*` permission이 없다.
- `laravel-admin` core route에 `/admin/users`와 `/api/admin/users`가 없다.
- `laravel-admin-users` 설치 후 회원 관리 메뉴와 화면이 등록된다.
- `laravel-admin-users`는 `App\Models\User` 대신 config 기반 user model을 사용한다.
- core 테스트와 `laravel-admin-users` 테스트가 각각 통과한다.
