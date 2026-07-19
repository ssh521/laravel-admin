# ssh521/laravel-admin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ssh521/laravel-admin.svg?style=flat-square)](https://packagist.org/packages/ssh521/laravel-admin)
[![Total Downloads](https://img.shields.io/packagist/dt/ssh521/laravel-admin.svg?style=flat-square)](https://packagist.org/packages/ssh521/laravel-admin)
[![License](https://img.shields.io/packagist/l/ssh521/laravel-admin.svg?style=flat-square)](LICENSE)

Laravel 13 애플리케이션에 독립된 관리자 인증, 계정, 역할·권한, 메뉴와 확장 패키지 계약을 추가하는 관리자 기반 패키지입니다.

Filament, Nova, Backpack과 같은 범용 CRUD 빌더를 대체하지 않습니다. 호스트 애플리케이션이 자체 관리자 기능을 일관된 인증·권한·UI 계약 위에서 구현할 수 있도록 공통 기반을 제공합니다.

## 주요 기능

- 일반 사용자와 분리된 관리자 guard, provider, 로그인과 세션 쿠키
- 패키지 소유 `admin_users` 관리자 계정
- Spatie Laravel Permission 기반 관리자 역할과 권한
- 역할별 관리자 메뉴와 메뉴 카테고리
- 호스트 애플리케이션의 일반 사용자 관리
- 반복 실행 가능한 설치·업데이트·Super Admin 명령
- `ssh521/laravel-admin-ui` 기반 Blade 화면과 자산
- 기능 패키지가 메뉴·권한·설치 과정을 등록하는 확장 계약

## 요구 사항

- PHP `^8.3`
- Laravel/Illuminate `^13.0`
- Livewire `^3.0` 또는 `^4.0`
- Spatie Laravel Permission `^6.0`
- Laravel Vite가 구성된 호스트 애플리케이션

이 패키지는 Laravel 13 전용입니다. Laravel 12 프로젝트라면 먼저 Laravel 13으로 업그레이드해야 하며, 패키지가 호스트 애플리케이션의 프레임워크 버전을 자동으로 변경하지는 않습니다.

## 설치

Laravel 13 애플리케이션에서 Composer 패키지와 관리자 리소스를 설치합니다.

```bash
composer require ssh521/laravel-admin
php artisan laravel-admin:install
npm install
npm run build
```

설치 명령은 다음 작업을 수행합니다.

- migration과 관리자 UI 자산을 퍼블리시합니다.
- 관리자 테이블을 migrate합니다.
- 기본 역할·권한·메뉴를 등록합니다.
- `vite.config.js`에 관리자 CSS와 JavaScript input을 추가합니다.

설치가 끝나면 `/admin/login`에서 관리자 로그인을 사용할 수 있습니다.

## 첫 관리자 계정

공개 패키지는 예측 가능한 기본 관리자 계정을 자동으로 만들지 않습니다. 대화형 명령으로 새 관리자를 만들거나 기존 관리자를 `Super Admin`으로 승격합니다.

```bash
php artisan laravel-admin:make-super-admin admin@example.com --verified
```

새 계정을 만드는 경우 명령이 비밀번호를 안전하게 입력받습니다. 셸 기록에 비밀번호가 남을 수 있으므로 `--password` 옵션은 자동화가 꼭 필요한 환경에서만 사용하십시오.

비대화형 배포에서 seeder로 관리자를 생성하려면 환경변수를 명시적으로 설정합니다.

```dotenv
LARAVEL_ADMIN_SEED_ADMIN_ENABLED=true
LARAVEL_ADMIN_SEED_ADMIN_EMAIL=owner@example.com
LARAVEL_ADMIN_SEED_ADMIN_PASSWORD=replace-with-a-long-random-password
LARAVEL_ADMIN_SEED_ADMIN_NAME="Super Admin"
LARAVEL_ADMIN_SEED_ADMIN_VERIFIED=true
```

`LARAVEL_ADMIN_SEED_ADMIN_ENABLED`의 기본값은 `false`입니다. 새 계정을 seed할 때는 유효한 이메일과 12자 이상의 비밀번호가 필요합니다.

## 설치 옵션

기존 파일을 덮어쓰거나 일부 설치 단계를 건너뛸 때만 필요한 옵션을 사용합니다.

```bash
php artisan laravel-admin:install --skip-migrate
php artisan laravel-admin:install --skip-seed
php artisan laravel-admin:install --skip-admin-user
php artisan laravel-admin:install --skip-vite
php artisan laravel-admin:install --publish-config
php artisan laravel-admin:install --publish-views
php artisan laravel-admin:install --publish-seeders
```

`--force`, `--force-config`, `--force-views`, `--force-seeders`는 기존 파일 또는 데이터베이스 작업에 영향을 줄 수 있으므로 변경 내용을 확인한 뒤 사용하십시오.

## 설정

기본 설정을 변경해야 할 때만 설정 파일을 퍼블리시합니다.

```bash
php artisan vendor:publish --tag=laravel-admin-config --no-interaction
```

주요 설정은 `config/laravel-admin.php`에서 관리합니다.

```php
return [
    'route_prefix' => 'admin',
    'route_name_prefix' => 'admin.',

    'auth' => [
        'guard' => 'laravel_admin',
        'provider' => 'laravel_admin_users',
        'session' => [
            'enabled' => true,
            'cookie' => 'laravel-admin-session',
        ],
    ],

    'admin_user_model' => Ssh521\LaravelAdmin\Models\AdminUser::class,
    'user_model' => App\Models\User::class,
    'super_admin_role' => 'Super Admin',
];
```

일반 사용자와 관리자 session guard는 기본적으로 서로 다른 쿠키를 사용합니다.

```dotenv
SESSION_COOKIE=application-session
LARAVEL_ADMIN_SEPARATE_SESSION=true
LARAVEL_ADMIN_SESSION_COOKIE=application-admin-session
```

분리 설정을 처음 적용하면 관리자는 새 관리자 쿠키로 한 번 다시 로그인해야 합니다. 기존 단일 쿠키 동작이 필요하면 `LARAVEL_ADMIN_SEPARATE_SESSION=false`로 비활성화할 수 있습니다.

## 인증과 권한

관리자 로그인은 호스트 애플리케이션의 `/login`, `/register` 또는 Fortify 인증 화면을 덮어쓰지 않고 `/admin/login`에서 독립적으로 동작합니다.

관리자 역할과 권한은 `config('laravel-admin.auth.guard')`에 설정된 관리자 guard를 사용합니다.

- `Super Admin`은 `Gate::before`를 통해 관리자 권한 검사를 통과합니다.
- 역할은 메뉴 카테고리 노출을 제어합니다.
- 권한은 화면과 기능 단위 접근을 제어합니다.
- 일반 사용자 모델에는 Spatie `HasRoles` trait이 필요하지 않습니다.
- 기능 패키지는 `AdminPackageRegistrar`로 메뉴와 권한을 등록합니다.

자세한 규칙은 [관리자 권한 계약](docs/admin-authorization-contract.md)을 참고하십시오.

## UI와 자산

관리자 화면의 Blade 뷰, 컴포넌트, CSS와 JavaScript는 [`ssh521/laravel-admin-ui`](https://packagist.org/packages/ssh521/laravel-admin-ui)가 제공합니다.

패키지 뷰에서는 호스트 애플리케이션의 컴포넌트와 충돌하지 않도록 `x-laravel-admin::admin.*` 네임스페이스를 사용합니다.

```blade
<x-laravel-admin::admin.action-button variant="primary">
    저장하기
</x-laravel-admin::admin.action-button>
```

전체 뷰를 커스터마이징해야 할 때만 명시적으로 퍼블리시합니다.

```bash
php artisan vendor:publish --tag=laravel-admin-ui-views --no-interaction
php artisan vendor:publish --tag=laravel-admin-ui-components --no-interaction
```

퍼블리시된 호스트 뷰는 패키지 업데이트를 자동으로 따라가지 않으므로 변경 범위를 작게 유지하는 것이 좋습니다.

## 업데이트

패키지를 업데이트한 뒤 migration과 UI 자산을 안전한 기본 범위로 갱신합니다.

```bash
composer update ssh521/laravel-admin ssh521/laravel-admin-ui
php artisan laravel-admin:update
npm run build
```

설정, 뷰, seeder는 호스트 애플리케이션에서 수정했을 수 있으므로 업데이트 명령이 기본적으로 덮어쓰지 않습니다.

## 확장 패키지 개발

`laravel-admin`은 기능 패키지가 관리자 메뉴, 권한, route와 installer를 등록할 수 있는 계약을 제공합니다. 새 확장 패키지를 만들 때는 [기능 패키지 가이드](docs/feature-package-template.md)를 기준으로 구현하십시오.

현재 설치 가능한 공개 패키지는 [Packagist의 `ssh521` 검색 결과](https://packagist.org/search/?query=ssh521)에서 확인할 수 있습니다.

## AI 개발 문맥

호스트 프로젝트의 AI 개발 도구가 관리자 패키지 계약을 참조하도록 문맥 파일을 설치할 수 있습니다.

```bash
php artisan laravel-admin:install-ai-context
php artisan laravel-admin:install-ai-context --with-codex-skill
```

자세한 내용은 [AI 개발 가이드](docs/ai-development-guide.md)를 참고하십시오.

## 문서

- [프로젝트 방향과 로드맵](docs/project-direction-roadmap.md)
- [관리자 권한 계약](docs/admin-authorization-contract.md)
- [기능 패키지 가이드](docs/feature-package-template.md)
- [AI 개발 가이드](docs/ai-development-guide.md)
- [관리자 UI 패키지](https://github.com/ssh521/laravel-admin-ui)

## 개발과 테스트

```bash
composer test
git diff --check
```

통합 화면 변경은 Laravel 13 호스트 애플리케이션에서 설치, migration, Vite build와 `/admin/login` 렌더링까지 확인해야 합니다.

## 지원과 보안

버그와 기능 요청은 [GitHub Issues](https://github.com/ssh521/laravel-admin/issues)에 등록해 주십시오. 보안 취약점은 공개 이슈 대신 [GitHub Security Advisory](https://github.com/ssh521/laravel-admin/security/advisories/new)로 제보해 주십시오.

## 라이선스

이 패키지는 [MIT License](LICENSE)로 배포됩니다.
