# 관리자 기능 전달 계약

이 문서는 관리자 화면 하나를 코드 작성에서 운영 노출까지 완성할 때 지켜야 하는 공통 계약이다. 권한의 세부 의미는 [관리자 권한 계약](admin-authorization-contract.md), 패키지 구조와 구현 예시는 [기능 패키지 가이드](feature-package-template.md), 화면 구성과 접근성은 `ssh521/laravel-admin-ui`의 [관리자 UI 디자인 계약](https://github.com/ssh521/laravel-admin-ui/blob/main/docs/admin-ui-design-contract.md)을 따른다.

## 계약 범위

관리자 기능은 다음 항목이 모두 준비되어야 완료된 것으로 본다.

1. 이름이 고정된 route와 route-level permission
2. 반복 실행 가능한 메뉴·권한 seeder
3. 메뉴 카테고리 노출 역할과 permission 부여 역할
4. 인증·인가 결과와 데이터 상태를 구분하는 화면
5. 운영 DB에 registration을 반영하는 배포 절차
6. 위 항목의 문자열과 관계를 고정하는 자동 테스트

화면 파일이 존재하거나 관리자 URL이 HTTP 200을 반환하는 것만으로는 완료가 아니다. 메뉴가 보여도 permission이 없으면 접근할 수 없고, 코드에 permission이 있어도 운영 DB에 seed되지 않으면 사용할 수 없다.

## 등록 계약

기능은 `AdminPackageRegistrar`에 안정적인 package key와 category, menu, permission을 등록한다.

- package key, route name, permission name은 배포 후 임의로 바꾸지 않는다.
- menu의 `route_name`은 실제 등록된 관리자 route를 가리켜야 한다.
- menu URL은 route의 실제 경로와 일치해야 한다.
- route의 `can:{permission}`과 seeder permission 이름은 정확히 같아야 한다.
- `roles`는 메뉴 카테고리를 볼 역할, `permission_roles`는 기능에 접근할 permission을 받을 역할이다.
- 기본 역할을 사용하더라도 보안 범위가 중요한 기능은 `roles`와 `permission_roles`를 명시한다.
- 같은 seeder를 반복 실행해도 category, menu, permission 또는 pivot이 중복되지 않아야 한다.
- `missing_menu_roles`와 `missing_permission_roles`를 무시하지 않는다. 설치 명령에서는 경고하고 자동 테스트와 배포 검증에서는 실패로 취급한다.

호스트 애플리케이션은 기능별 seeder를 작게 유지하고, 배포용 aggregate seeder가 이를 호출하는 구성을 권장한다. 개발용 사용자나 샘플 데이터를 만드는 루트 `DatabaseSeeder`를 운영 registration 용도로 사용하지 않는다.

## 권한 계약

관리자 기능 접근은 역할명이 아니라 permission으로 결정한다.

- web 관리자 route는 관리자 인증 middleware와 `can:{permission}`을 함께 사용한다.
- `Admin` 역할명 자체를 controller, request, Policy에서 우회 조건으로 사용하지 않는다.
- `Super Admin` 우회는 core `Gate::before`에만 둔다.
- FormRequest, Policy, Livewire action이 같은 기능을 보호한다면 route와 같은 permission 이름을 사용한다.
- 외부 token API를 호출하는 관리자 화면은 관리자 permission과 외부 token ability를 서로 다른 권한층으로 검증한다.
- 메뉴 폴더 노출은 접근 허가 증거가 아니며, route permission 통과를 별도로 확인한다.

읽기 전용 기능은 하나의 `*-view` permission으로 index, show, search를 보호할 수 있다. 생성, 수정, 삭제, 실행, 재처리처럼 상태를 바꾸는 기능은 별도 permission을 둔다.

## 최소 화면 상태 계약

세부 레이아웃은 `laravel-admin-ui`가 소유한다. 이 문서는 관리자 기능이 의미상 구분해야 할 최소 상태만 정의한다.

- 접근 권한 없음: route에서 403을 반환하며 화면을 부분 렌더링하지 않는다.
- 설정 누락 또는 외부 서비스 장애: 데이터가 0건인 것처럼 표시하지 않고 별도 경고 상태를 보여준다.
- 원본 데이터 없음: 연결은 정상이지만 아직 생성·동기화·import된 데이터가 없음을 표시한다.
- 검색 결과 없음: 전체 데이터 없음과 구분하고 현재 필터를 초기화할 방법을 제공한다.
- 정상 결과: 기준 시각, 데이터 출처, 전체 건수와 페이지 정보를 표시한다.
- 처리 중·부분 완료·실패: 도메인이 상태를 제공하면 정상 완료와 구분한다.

관리자 화면에는 token, 비밀번호, 원본 예외, 전체 외부 응답, 내부 파일 경로를 표시하지 않는다. 운영자에게 필요한 상태 코드는 안전한 내부 코드로 정규화하고 상세 진단은 비밀값이 제거된 로그나 감사 기록으로 남긴다.

## UI 최소 기준

- `x-laravel-admin::admin.*` 공용 component를 우선 사용한다.
- 목록은 공용 page/list/filter/table 흐름과 query-preserving pagination을 따른다.
- 성공·정보 메시지는 `role="status"`, 오류·경고는 `role="alert"`를 사용한다.
- 빈 결과와 동적 갱신 영역은 보조 기술이 상태 변화를 인식할 수 있어야 한다.
- 모바일 390x844에서 가로 overflow와 핵심 action 접근성을 확인한다.
- 다크 모드와 키보드 focus 상태를 확인한다.

추가적인 시각 요구는 `laravel-admin-ui` 계약에 누적하며, 이 문서에 스타일 세부 구현을 복제하지 않는다.

## Seeder와 배포 계약

새 DB의 기본 설치 순서는 다음과 같다.

1. migration
2. core `RolePermissionSeeder`
3. core `MenuSeeder`
4. 필요한 기능·호스트 seeder
5. 관리자 계정 생성 또는 역할 부여
6. permission cache reset

기존 DB라도 새 menu 또는 permission definition이 추가된 배포에서는 해당 기능 seeder를 다시 실행한다. registration seeder는 additive하고 idempotent해야 하며, 운영 배포에서 루트 `php artisan db:seed`를 관성적으로 실행하지 않는다.

배포 후에는 다음을 실제 DB와 route에서 확인한다.

- menu와 permission이 각각 한 건 존재한다.
- 대상 role과 category·permission pivot이 존재한다.
- route middleware의 permission 문자열이 DB permission과 같다.
- permission 없는 관리자는 403, permission 있는 관리자는 200을 받는다.
- 외부 연동 화면은 필요한 token ability로 정상 응답을 받는다.

## 필수 테스트

기능별 테스트는 최소한 다음을 고정한다.

1. seeder를 두 번 실행해도 registration이 한 건씩 유지된다.
2. route의 `can:*` 문자열과 seeded permission이 일치한다.
3. menu route name과 URL이 실제 route와 일치한다.
4. 의도한 role에 category와 permission이 연결된다.
5. permission 없는 관리자 403, permission 있는 관리자 성공을 확인한다.
6. `Super Admin` bypass를 사용하는 경우 직접 permission 없이 접근 가능함을 확인한다.
7. 외부 API 화면은 미설정, 연결 실패, 401/403, 잘못된 응답과 정상 빈 결과를 구분한다.
8. 화면과 응답에 token이나 내부 예외가 노출되지 않는다.
9. view compile과 관련 feature test가 통과한다.

## 완료 기준

다음 조건이 모두 충족되어야 관리자 기능이 전달 완료 상태다.

- route, controller/request, seeder, 테스트의 permission 문자열이 일치한다.
- menu, permission, role 연결이 운영 DB에 반영되었다.
- 인증, 권한 없음, 장애, 데이터 없음, 정상 데이터 상태가 혼동되지 않는다.
- 공용 UI 계약과 기본 접근성 검증을 통과한다.
- 설치·업데이트·롤백 시 실행할 명시적 seeder와 검증 명령이 문서화되어 있다.
- 비밀값과 내부 진단 정보가 화면, 로그, 테스트 fixture에 남지 않는다.
