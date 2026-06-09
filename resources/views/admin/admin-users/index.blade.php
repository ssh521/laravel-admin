<x-laravel-admin::admin.layouts.admin title="관리자 계정 관리">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Admin User List') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="w-full bg-white border border-[#d8d8d0] px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[560px] border border-[#d9d9d9] bg-white px-6 py-7 sm:px-10 sm:py-10 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-2">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Admin Users') }}</h1>

                <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-base font-semibold">
                    <a href="{{ route('admin.admin-users.index') }}">{{ __('목록보기') }}</a>
                    <span class="text-[#222222] dark:text-gray-400">|</span>
                    <a href="{{ route('admin.admin-users.create') }}">{{ __('등록하기') }}</a>
                </div>
            </div>

            <x-laravel-admin::admin.session-messages />

            <form method="GET" action="{{ route('admin.admin-users.index') }}" class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                <select name="role" class="admin-focus-border h-[28px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 text-base text-[#111111] outline-none sm:w-[150px] dark:bg-gray-700 dark:border-gray-600 dark:text-white" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='#7d7d7d'; this.style.boxShadow='none';">
                    <option value="">{{ __('All Roles') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <input
                    name="search"
                    type="text"
                    value="{{ request('search') }}"
                    placeholder="{{ __('Search name or email') }}"
                    class="admin-focus-border h-[28px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 text-base text-[#111111] outline-none sm:w-[260px] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';"
                    onblur="this.style.borderColor='#7d7d7d'; this.style.boxShadow='none';"
                >
                <button type="submit" class="h-[28px] cursor-pointer rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-4 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('검색') }}
                </button>
            </form>

            <div class="mb-1 flex flex-wrap items-center gap-x-1 text-[12px] font-semibold">
                <a href="#">{{ __('전체') }}</a>
                <span class="text-[#222222] dark:text-gray-400">|</span>
                <a href="#">{{ __('해지') }}</a>
                <span class="text-[#222222] dark:text-gray-400">|</span>
                <a href="#">{{ __('반전') }}</a>
            </div>


            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-base text-[#111111] dark:text-gray-100">
                    <thead>
                        <tr class="border-y border-[#cfcfcf] bg-[#dedede] text-[#555555] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            <th class="h-10 px-2 text-left font-bold">{{ __('Name') }}</th>
                            <th class="h-10 px-2 text-left font-bold">{{ __('Email') }}</th>
                            <th class="h-10 px-2 text-left font-bold">{{ __('Roles') }}</th>
                            <th class="h-10 px-2 text-left font-bold">{{ __('Verified') }}</th>
                            <th class="h-10 px-2 text-right font-bold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($adminUsers as $adminUser)
                            <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] transition-colors hover:bg-white dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                                <td class="h-10 whitespace-nowrap px-4 font-bold">{{ $adminUser->name }}</td>
                                <td class="h-10 whitespace-nowrap px-4">{{ $adminUser->email }}</td>
                                <td class="h-10 px-4">
                                    <div class="flex flex-wrap gap-x-2 gap-y-1">
                                        @forelse ($adminUser->getRoleNames() as $role)
                                            <span>{{ $role }}</span>
                                        @empty
                                            <span class="text-gray-500 dark:text-gray-400">{{ __('No roles') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="h-10 whitespace-nowrap px-4">
                                    @if ($adminUser->email_verified_at)
                                        <span>{{ __('Verified') }}</span>
                                    @else
                                        <span>{{ __('Not Verified') }}</span>
                                    @endif
                                </td>
                                <td class="h-10 whitespace-nowrap px-4 text-right">
                                    <a href="{{ route('admin.admin-users.show', $adminUser->getKey()) }}">{{ __('View') }}</a>
                                    <span class="mx-1 text-gray-400">|</span>
                                    <a href="{{ route('admin.admin-users.edit', $adminUser->getKey()) }}">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] dark:border-gray-700 dark:bg-gray-800">
                                <td colspan="5" class="h-10 px-4 text-center text-gray-500 dark:text-gray-400">{{ __('No admin users found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-sm">
                {{ $adminUsers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>
