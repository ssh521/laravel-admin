<x-laravel-admin::layouts.admin title="사용자 정보">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                @can('viewAny', Ssh521\LaravelAdmin\Models\User::class)
                    - <a href="{{ route('admin.users.index') }}">회원 리스트</a>
                @endcan
            </x-slot>
            <x-slot name="description">
                {{ __('User Information') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    <div class="mx-auto w-full max-w-7xl bg-white px-2 py-2 dark:bg-gray-900">
        <div class="min-h-[720px] bg-white px-8 py-10 sm:px-16 lg:px-24 dark:bg-gray-800">
            <div class="mb-10">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('User Information') }}</h1>
            </div>

            <div class="border border-dashed border-[#bdbdbd] bg-[#f7f7f7] px-8 py-10 text-[15px] font-bold text-[#222222] sm:px-16 lg:px-24 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <div class="mx-auto max-w-[980px]">
                    <div class="space-y-4">
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('이름') }} :</div>
                            <div>{{ $user->name }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('E-mail') }} :</div>
                            <div>{{ $user->email }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('인증상태') }} :</div>
                            <div>
                                @if ($user->email_verified_at)
                                    {{ __('Verified') }} : {{ $user->email_verified_at->format('Y-m-d H:i:s') }}
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
                                @forelse ($user->getRoleNames() as $role)
                                    <span>{{ $role }}</span>@if (! $loop->last)<span>, </span>@endif
                                @empty
                                    {{ __('No roles assigned') }}
                                @endforelse
                            </div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('권한') }} :</div>
                            <div>
                                @forelse ($user->getPermissionNames() as $permission)
                                    <span>{{ $permission }}</span>@if (! $loop->last)<span>, </span>@endif
                                @empty
                                    {{ __('No permissions assigned') }}
                                @endforelse
                            </div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('등록일') }} :</div>
                            <div>{{ $user->created_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('수정일') }} :</div>
                            <div>{{ $user->updated_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <a href="{{ route('admin.users.index') }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('목록보기') }}
                </a>
                @can('update', $user)
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                        {{ __('수정하기') }}
                    </a>
                @endcan
            </div>
        </div>
    </div>

    @if($user->id != auth()->user()->id)
        @can('delete', $user)
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}');" class="mx-auto mt-3 flex w-full max-w-7xl justify-start px-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                    {{ __('삭제하기') }}
                </button>
            </form>
        @endcan
    @endif
</x-laravel-admin::layouts.admin>
