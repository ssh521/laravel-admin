<x-laravel-admin::admin.layouts.admin title="사용자 수정">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                @can('viewAny', Ssh521\LaravelAdmin\Models\User::class)
                    - <a href="{{ route('admin.users.index') }}">회원 리스트</a>
                @endcan
            </x-slot>
            <x-slot name="description">
                {{ __('Edit User') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    @php
        $inputClass = 'admin-focus-border rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-2 py-1 text-lg font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
        $labelClass = 'w-[96px] shrink-0 pr-3 text-right text-base leading-[28px] text-[#111111] dark:text-gray-100';
        $rowClass = 'flex gap-1 flex-row sm:items-start';
        $focusIn = "this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';";
        $focusOut = "this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';";
    @endphp

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[600px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('User Information') }}</h1>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mx-auto max-w-[860px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('이름') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" placeholder="Enter user name" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('name')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="email" class="{{ $labelClass }}">{{ __('E-mail') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" placeholder="Enter email address" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('email')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="password" class="{{ $labelClass }}">{{ __('비밀번호') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Enter new password (leave blank to keep current)" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('password')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="confirm-password" class="{{ $labelClass }}">{{ __('확인') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <input id="confirm-password" name="confirm-password" type="password" autocomplete="new-password" placeholder="Confirm new password" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('confirm-password')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <span class="{{ $labelClass }}">{{ __('인증') }} :</span>
                                <div class="min-h-[24px]">
                                    <label for="email_verified" class="flex min-h-[24px] items-center gap-2 text-base font-bold">
                                        <input id="email_verified" name="email_verified" type="checkbox" value="1" @checked(old('email_verified', $user->email_verified_at ? '1' : null) == '1') class="size-4 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                        <span>{{ __('Email Verified') }}</span>
                                    </label>
                                    <div class="mt-1 text-[13px] font-bold text-gray-600 dark:text-gray-400">
                                        @if ($user->email_verified_at)
                                            {{ __('Verified on') }}: {{ $user->email_verified_at->format('Y-m-d H:i:s') }}
                                        @else
                                            {{ __('Not Verified') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-8" style="border-top-color: #aaaaaa;">
                        <div class="flex justify-center">
                            <x-laravel-admin::admin.primary-button class="min-w-[120px] justify-center px-5">
                                {{ __('수정하기') }}
                            </x-laravel-admin::admin.primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($user->id != auth()->user()->id)
        @can('delete', $user)
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('정말 삭제하시겠습니까?') }}')" class="mx-auto mt-3 flex w-full max-w-5xl justify-start px-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                    {{ __('삭제하기') }}
                </button>
            </form>
        @endcan
    @endif

</x-laravel-admin::admin.layouts.admin>
