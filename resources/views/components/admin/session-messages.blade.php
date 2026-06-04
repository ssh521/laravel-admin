{{-- 성공 메시지 --}}
@if ($message = Session::get('success'))
    <x-laravel-admin::admin.alert type="success" :message="$message" dismissible />
@endif

{{-- 에러 메시지 --}}
@if ($message = Session::get('error'))
    <x-laravel-admin::admin.alert type="error" :message="$message" dismissible />
@endif

{{-- 경고 메시지 --}}
@if ($message = Session::get('warning'))
    <x-laravel-admin::admin.alert type="warning" :message="$message" dismissible />
@endif

{{-- 정보 메시지 --}}
@if ($message = Session::get('info'))
    <x-laravel-admin::admin.alert type="info" :message="$message" dismissible />
@endif




