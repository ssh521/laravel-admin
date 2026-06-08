<x-laravel-admin::admin.layouts.admin title="관리자 계정 정보">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.admin-users.index') }}">관리자 계정</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Admin User Information') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900">
        <div class="min-h-[450px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Admin User Information') }}</h1>
            </div>

            <div class="border border-dashed border-[#bdbdbd] bg-[#f7f7f7] px-6 py-8 text-base font-bold text-[#222222] sm:px-12 lg:px-16 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <div class="mx-auto max-w-[800px]">
                    <div class="space-y-3">
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('이름') }} :</div>
                            <div>{{ $adminUser->name }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('E-mail') }} :</div>
                            <div>{{ $adminUser->email }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('인증상태') }} :</div>
                            <div>
                                @if ($adminUser->email_verified_at)
                                    {{ __('Verified') }} : {{ $adminUser->email_verified_at->format('Y-m-d H:i:s') }}
                                @else
                                    {{ __('Not Verified') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="my-6 border-t border-[#8d8d8d]"></div>

                    <div class="space-y-3">
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('역할') }} :</div>
                            <div>
                                @forelse ($adminUser->getRoleNames() as $role)
                                    <span>{{ $role }}</span>@if (! $loop->last)<span>, </span>@endif
                                @empty
                                    {{ __('No roles assigned') }}
                                @endforelse
                            </div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('등록일') }} :</div>
                            <div>{{ $adminUser->created_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('수정일') }} :</div>
                            <div>{{ $adminUser->updated_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <a href="{{ route('admin.admin-users.index') }}" class="inline-flex items-center justify-center min-w-[104px] px-5 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('목록보기') }}
                </a>
                <a href="{{ route('admin.admin-users.edit', $adminUser->getKey()) }}" class="inline-flex items-center justify-center min-w-[120px] px-5 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest !text-white dark:!text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('수정하기') }}
                </a>
            </div>
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>
