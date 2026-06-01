<x-layouts.admin title="공지사항 관리">

    <x-slot name="header">

        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Notice List') }}
            </x-slot>
        </x-admin-header>

    </x-slot>

    <livewire:admin.notices.notice-table />

</x-layouts.admin>

