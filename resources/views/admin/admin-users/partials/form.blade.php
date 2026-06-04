@php
    $inputClass = 'admin-focus-border h-[32px] rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-3 text-[14px] font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
    $labelClass = 'w-[116px] shrink-0 pr-4 text-right text-[15px] font-bold leading-[32px] text-[#111111] dark:text-gray-100';
    $rowClass = 'flex flex-col gap-1 sm:flex-row sm:items-start';
@endphp

<div class="mx-auto max-w-[1060px] text-[#111111] dark:text-gray-100">
    <div class="border-t border-[#8d8d8d] pt-7">
        <div class="space-y-4">
            <div class="{{ $rowClass }}">
                <label for="name" class="{{ $labelClass }}">{{ __('이름') }} :</label>
                <div class="w-full sm:w-[320px]">
                    <input id="name" name="name" type="text" value="{{ old('name', $adminUser?->name) }}" autocomplete="name" class="{{ $inputClass }} w-full" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('name')" />
                </div>
            </div>

            <div class="{{ $rowClass }}">
                <label for="email" class="{{ $labelClass }}">{{ __('E-mail') }} :</label>
                <div class="w-full sm:w-[520px]">
                    <input id="email" name="email" type="email" value="{{ old('email', $adminUser?->email) }}" autocomplete="email" class="{{ $inputClass }} w-full" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('email')" />
                </div>
            </div>
        </div>
    </div>

    <div class="mt-7 border-t border-[#8d8d8d] pt-7">
        <div class="space-y-4">
            <div class="{{ $rowClass }}">
                <label for="password" class="{{ $labelClass }}">{{ __('비밀번호') }} :</label>
                <div class="w-full sm:w-[320px]">
                    <input id="password" name="password" type="password" autocomplete="new-password" class="{{ $inputClass }} w-full" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('password')" />
                </div>
            </div>

            <div class="{{ $rowClass }}">
                <label for="confirm-password" class="{{ $labelClass }}">{{ __('확인') }} :</label>
                <div class="w-full sm:w-[320px]">
                    <input id="confirm-password" name="confirm-password" type="password" autocomplete="new-password" class="{{ $inputClass }} w-full" onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';" onblur="this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';">
                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('confirm-password')" />
                </div>
            </div>
        </div>
    </div>

    <div class="mt-7 border-t border-[#8d8d8d] pt-7">
        <div class="space-y-4">
            <div class="{{ $rowClass }}">
                <span class="{{ $labelClass }}">{{ __('인증') }} :</span>
                <label for="email_verified" class="flex min-h-[32px] items-center gap-2 text-[14px] font-bold">
                    <input id="email_verified" name="email_verified" type="checkbox" value="1" @checked(old('email_verified', $adminUser?->email_verified_at ? '1' : null) == '1') class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]">
                    <span>{{ __('Email Verified') }}</span>
                </label>
            </div>

            <div class="{{ $rowClass }}">
                <span class="{{ $labelClass }}">{{ __('역할') }} :</span>
                <div class="grid min-h-[32px] w-full max-w-[720px] grid-cols-1 gap-x-6 gap-y-3 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($roles as $role)
                        <label for="role_{{ $role->id }}" class="flex items-center gap-2 text-[14px] font-bold">
                            <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}" @checked(in_array($role->id, old('roles', $adminUser?->roles->pluck('id')->all() ?? []))) class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]">
                            <span>{{ $role->name }}</span>
                        </label>
                    @endforeach
                    <x-laravel-admin::admin.input-error-message class="col-span-full mt-1 text-[13px]" :messages="$errors->get('roles')" />
                </div>
            </div>
        </div>
    </div>

    <div class="mt-7 border-t border-[#8d8d8d] pt-10">
        <div class="flex justify-center">
            <button type="submit" class="h-[34px] min-w-[140px] border border-[#777777] bg-[#eeeeee] px-8 text-[15px] font-bold leading-none text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                {{ $submitLabel }}
            </button>
        </div>
    </div>
</div>
