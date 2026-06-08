@php
    $isProfile = $isProfile ?? false;
    $inputClass = 'admin-focus-border rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-2 py-1 text-lg font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
    $lockedInputClass = $inputClass.' cursor-not-allowed opacity-70';
    $labelClass = 'w-[96px] shrink-0 pr-3 text-right text-base leading-[28px] text-[#111111] dark:text-gray-100';
    $rowClass = 'flex gap-1 flex-row sm:items-start';
    $roleIds = $adminUser?->roles->pluck('id')->all() ?? [];
@endphp

<div class="mx-auto max-w-[860px] text-[#111111] dark:text-gray-100">

    <div class="border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
        <div class="space-y-3">
            <div class="{{ $rowClass }}">
                <label for="name" class="{{ $labelClass }}">{{ __('이름') }} :</label>
                <div class="w-full sm:w-[260px]">
                    <input id="name" name="name" type="text" value="{{ old('name', $adminUser?->name) }}" autocomplete="name" class="{{ $inputClass }} w-full" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-xs" :messages="$errors->get('name')" />
                </div>
            </div>

            <div class="{{ $rowClass }}">
                <label for="email" class="{{ $labelClass }}">{{ __('E-mail') }} :</label>
                <div class="w-full sm:w-[420px]">
                    <input id="email" name="email" type="email" value="{{ $isProfile ? $adminUser?->email : old('email', $adminUser?->email) }}" autocomplete="email" class="{{ ($isProfile ? $lockedInputClass : $inputClass) }} w-full" @disabled($isProfile) onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-xs" :messages="$errors->get('email')" />
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
        <div class="space-y-3">
            <div class="{{ $rowClass }}">
                <label for="password" class="{{ $labelClass }}">{{ __('비밀번호') }} :</label>
                <div class="w-full sm:w-[260px]">
                    <input id="password" name="password" type="password" autocomplete="new-password" class="{{ $inputClass }} w-full" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-xs" :messages="$errors->get('password')" />
                </div>
            </div>

            <div class="{{ $rowClass }}">
                <label for="confirm-password" class="{{ $labelClass }}">{{ __('확인') }} :</label>
                <div class="w-full sm:w-[260px]">
                    <input id="confirm-password" name="confirm-password" type="password" autocomplete="new-password" class="{{ $inputClass }} w-full" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-xs" :messages="$errors->get('confirm-password')" />
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
        <div class="space-y-3">
            <div class="{{ $rowClass }}">
                <span class="{{ $labelClass }}">{{ __('인증') }} :</span>
                <label for="email_verified" class="flex min-h-[24px] items-center gap-2 text-base font-bold @if($isProfile) cursor-not-allowed opacity-70 @endif">
                    <input id="email_verified" name="email_verified" type="checkbox" value="1" @checked($isProfile ? (bool) $adminUser?->email_verified_at : old('email_verified', $adminUser?->email_verified_at ? '1' : null) == '1') @disabled($isProfile) class="size-4 border-[#777777] text-[#663601] focus:ring-[#663601]">
                    <span>{{ __('Email Verified') }}</span>
                </label>
            </div>

            <div class="{{ $rowClass }}">
                <span class="{{ $labelClass }}">{{ __('역할') }} :</span>
                <div class="grid min-h-[24px] w-full max-w-[580px] grid-cols-1 gap-x-5 gap-y-2 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($roles as $role)
                        <label for="role_{{ $role->id }}" class="flex items-center gap-2 text-base font-bold @if($isProfile) cursor-not-allowed opacity-70 @endif">
                            <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}" @checked(in_array($role->id, $isProfile ? $roleIds : old('roles', $roleIds))) @disabled($isProfile) class="size-4 border-[#777777] text-[#663601] focus:ring-[#663601]">
                            <span>{{ $role->name }}</span>
                        </label>
                    @endforeach
                    <x-laravel-admin::admin.input-error-message class="col-span-full mt-1 text-xs" :messages="$errors->get('roles')" />
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 border-t border-dashed pt-8" style="border-top-color: #aaaaaa;">
        <div class="flex justify-center">
            <x-laravel-admin::admin.primary-button class="min-w-[120px] justify-center px-5">
                {{ $submitLabel }}
            </x-laravel-admin::admin.primary-button>
        </div>
    </div>
</div>
