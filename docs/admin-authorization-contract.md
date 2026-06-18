# 관리자 권한 계약

`ssh521/laravel-admin`은 관리자 권한을 두 계층으로 나누어 다룬다.

## 메뉴 폴더 노출

메뉴 폴더 노출은 역할 기반이다.

- `menu_categories`와 `menu_category_role`은 특정 역할이 어떤 메뉴 폴더를 볼 수 있는지 결정한다.
- 관리자 계정에 역할을 부여하는 것은 기본적으로 좌측 메뉴 폴더 노출을 결정하는 일이다.
- `MenuSeeder`는 `Super Admin` 역할에 기본 메뉴 폴더 전체를 연결한다.
- `MenuSeeder`는 `Admin` 역할에 기본 일반, 메뉴 관리 폴더를 연결하지만 `관리자 전용` 폴더는 연결하지 않는다.
- 기능 패키지는 `AdminPackageRegistrar`를 통해 자기 메뉴 카테고리를 `Super Admin`, `Admin`, 또는 패키지 전용 역할에 연결할 수 있다.

## 세부 권한

세부 기능 접근은 permission 기반이다.

- 라우트 미들웨어, Policy, action 단위 권한 검사는 Spatie permission name을 사용한다.
- `RolePermissionSeeder`는 laravel-admin의 기본 permission을 생성한다.
- 기본 설치 기준으로 `Super Admin`과 `Admin`은 설정된 admin guard의 모든 permission을 받는다.
- 기능 패키지의 관리자 seeder는 `AdminPackageRegistrar`로 permission을 등록한다. 기본값은 패키지 permission을 `Super Admin`, `Admin` 양쪽에 연결한다.
- 기능 패키지가 더 좁은 기본 권한을 원하면 `permission_roles`를 명시한다.

## 권한 적용 위치

관리자 web route는 인증 미들웨어와 `can:*` 미들웨어를 함께 사용한다.

- route group은 `config('laravel-admin.middleware', ['web', 'auth', 'verified'])`를 따른다.
- 기능 접근은 route-level `can:{permission-name}` 미들웨어를 우선 사용한다.
- controller action 내부에서 추가 상태 전환, owner 확인, 삭제 가능 여부처럼 record 상태가 필요한 검사를 Policy나 service action에서 보강한다.
- permission 이름은 route middleware, seeder, 테스트에서 같은 문자열을 사용한다.

Livewire는 기본 CRUD 소유자가 아니라 보조 UI다.

- 정렬, 검색 모달, 상태 토글, 짧은 quick edit처럼 화면 안 상호작용이 필요한 경우에 사용한다.
- Livewire component도 admin route와 같은 guard/middleware 위에서 동작해야 한다.
- 권한이 필요한 Livewire action은 action 시작 지점에서 `Gate::authorize()` 또는 `$this->authorize()`로 검사한다.
- 단순 index/create/edit/show 전체를 Livewire 내부 상태에만 숨기지 않는다. 공개 route name, form field, validation behavior는 controller/resource contract에 남긴다.

API route는 core가 기본 surface를 제공하지 않고 기능 패키지가 직접 등록한다.

- core `routes/api.php`는 비워둔다.
- 기능 패키지가 API를 제공할 때는 API 성격을 먼저 관리자 화면 보조 API, 외부 token API, 공개 API 중 하나로 분류한다.
- 관리자 화면에서 AJAX나 fetch로 호출하는 보조 API는 web admin route와 같은 admin session guard를 사용하고, Spatie permission 기반 `can:*` 또는 Policy 검사를 기본으로 한다. 이 API는 사람이 관리자 화면에서 조작하는 기능이므로 route permission 이름도 web admin route와 같은 문자열을 사용한다.
- 외부 클라이언트나 token 기반 연동 API는 기능 패키지가 명시적으로 opt-in하고, Sanctum 같은 token 인증과 ability를 사용한다. Sanctum 의존성과 설정 권장안은 해당 기능 패키지 책임이다.
- token API가 관리자 데이터를 변경하거나 관리자 기능을 실행한다면 Sanctum ability와 Spatie permission/Policy를 둘 다 요구한다.
- 공개 API는 관리자 권한 계약 밖의 surface로 보고, 관리자 메뉴/permission seeder에 묶지 않는다.
- API 테스트에는 API 성격에 따라 미인증, ability 없는 token, permission 없는 관리자, 허용 관리자, Super Admin bypass 케이스를 포함한다.

권장 조합:

```text
관리자 화면 보조 API  = admin session middleware + can:{permission-name}
외부 token API        = auth:sanctum + ability
관리자 기능 token API = auth:sanctum + ability + can:{permission-name}/Policy
공개 API              = 패키지 도메인 정책에 따로 정의
```

## 패키지 Seeder 계약

기능 패키지의 관리자 seeder는 아래 형태를 따른다.

```php
app(AdminPackageRegistrar::class)->register('laravel-example', [
    'category' => [
        'name' => 'Example 관리',
        'sort_order' => 700,
    ],
    'menus' => [
        [
            'name' => 'Example',
            'route_name' => 'example.admin.dashboard',
            'url' => '/admin/example',
        ],
    ],
    'permissions' => [
        ['name' => 'example-dashboard-access'],
        ['name' => 'example-manage-settings'],
    ],
]);
```

`roles`는 메뉴 폴더 노출 대상을 제어하고, `permission_roles`는 permission을 받을 역할을 제어한다.

```php
'roles' => ['Super Admin'],
'permission_roles' => ['Super Admin', 'Admin'],
```

이 계약을 따르면 기본 `Admin` 역할은 설치된 관리자 기능을 바로 사용할 수 있고, 패키지별로 필요할 때는 메뉴 폴더를 숨기거나 권한 연결 대상을 명시적으로 좁힐 수 있다.
