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

    <div class="mx-auto w-full max-w-7xl bg-white px-2 py-2 dark:bg-gray-900">
        <div class="min-h-[720px] bg-white px-8 py-10 sm:px-16 lg:px-24 dark:bg-gray-800">
            <div class="mb-10">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Admin User Information') }}</h1>
            </div>

            <div class="border border-dashed border-[#bdbdbd] bg-[#f7f7f7] px-8 py-10 text-[15px] font-bold text-[#222222] sm:px-16 lg:px-24 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <div class="mx-auto max-w-[980px]">
                    <div class="space-y-4">
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('이름') }} :</div>
                            <div>{{ $adminUser->name }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('E-mail') }} :</div>
                            <div>{{ $adminUser->email }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('인증상태') }} :</div>
                            <div>
                                @if ($adminUser->email_verified_at)
                                    {{ __('Verified') }} : {{ $adminUser->email_verified_at->format('Y-m-d H:i:s') }}
                                @else
                                    {{ __('Not Verified') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="my-8 border-t border-[#8d8d8d]"></div>

                    <div class="space-y-4">
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('역할') }} :</div>
                            <div>
                                @forelse ($adminUser->getRoleNames() as $role)
                                    <span>{{ $role }}</span>@if (! $loop->last)<span>, </span>@endif
                                @empty
                                    {{ __('No roles assigned') }}
                                @endforelse
                            </div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('등록일') }} :</div>
                            <div>{{ $adminUser->created_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('수정일') }} :</div>
                            <div>{{ $adminUser->updated_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <a href="{{ route('admin.admin-users.index') }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('목록보기') }}
                </a>
                <a href="{{ route('admin.admin-users.edit', $adminUser->getKey()) }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('수정하기') }}
                </a>
            </div>
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>
