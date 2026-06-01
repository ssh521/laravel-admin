<x-laravel-admin::layouts.admin title="사용자 관리">

    <x-slot name="header">

        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.users.select-user') }}">사용자 선택(모달폼 예제)</a>
            </x-slot>
            <x-slot name="description">
                {{ __('User List') }}
            </x-slot>
        </x-laravel-admin::admin-header>

    </x-slot>

    <livewire:admin.users.user-table />

</x-laravel-admin::layouts.admin>