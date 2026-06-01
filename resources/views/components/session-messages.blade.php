{{-- 성공 메시지 --}}
@if ($message = Session::get('success'))
    <x-alert type="success" :message="$message" dismissible />
@endif

{{-- 에러 메시지 --}}
@if ($message = Session::get('error'))
    <x-alert type="error" :message="$message" dismissible />
@endif

{{-- 경고 메시지 --}}
@if ($message = Session::get('warning'))
    <x-alert type="warning" :message="$message" dismissible />
@endif

{{-- 정보 메시지 --}}
@if ($message = Session::get('info'))
    <x-alert type="info" :message="$message" dismissible />
@endif




