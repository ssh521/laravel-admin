<x-laravel-admin::admin.layouts.admin title="관리자 계정 수정">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.admin-users.index') }}">관리자 계정</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Edit Admin User') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[450px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Admin User Information') }}</h1>
            </div>

            @php
                $isProfile = $isProfile ?? false;
                $formRoute = $isProfile
                    ? route(config('laravel-admin.route_name_prefix', 'admin.').'profile.update')
                    : route('admin.admin-users.update', $adminUser->getKey());
            @endphp

            <form action="{{ $formRoute }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.admin-users.partials.form', [
                    'adminUser' => $adminUser,
                    'roles' => $roles,
                    'isProfile' => $isProfile,
                    'submitLabel' => __('수정하기'),
                ])
            </form>

        </div>
    </div>

    @unless ($isProfile)
        <form action="{{ route('admin.admin-users.destroy', $adminUser->getKey()) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this admin user?') }}');" class="mx-auto mt-3 flex w-full max-w-5xl justify-start px-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                {{ __('삭제하기') }}
            </button>
        </form>
    @endunless

    @if ($isProfile && session('success'))
        <script>
            window.addEventListener('load', () => {
                window.dispatchEvent(new CustomEvent('notification:show', {
                    detail: {
                        message: @js(session('success')),
                        type: 'success',
                    },
                }));
            });
        </script>
    @endif
</x-laravel-admin::admin.layouts.admin>
