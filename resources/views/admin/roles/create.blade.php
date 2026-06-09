<x-laravel-admin::admin.layouts.admin title="역할 등록">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">Admin Home</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Create New Role') }}
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
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Role Information') }}</h1>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="mx-auto max-w-[860px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('Role Name') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" placeholder="{{ __('Enter role name') }}" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    @if ($errors->has('name'))
                                        <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="['Please enter a role name!']" />
                                    @endif
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="description" class="{{ $labelClass }}">{{ __('Description') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="description" name="description" type="text" value="{{ old('description') }}" placeholder="{{ __('Enter role description') }}" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('description')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="{{ $rowClass }}">
                            <span class="{{ $labelClass }}">{{ __('권한') }} :</span>
                            <div class="grid min-h-[24px] w-full max-w-[620px] grid-cols-1 gap-x-5 gap-y-2 pt-1 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($permissions as $permission)
                                    <label for="permission_{{ $permission->id }}" class="flex items-center gap-2 text-base font-bold">
                                        <input id="permission_{{ $permission->id }}" name="permissions[]" type="checkbox"
                                               value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                               class="size-4 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                        <span>{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                                @if ($errors->has('permissions'))
                                    <x-laravel-admin::admin.input-error-message class="col-span-full mt-1 text-[13px]" :messages="['Please select at least one permission!']" />
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-8" style="border-top-color: #aaaaaa;">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="inline-flex h-[30px] min-w-[104px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-5 text-base font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                                {{ __('목록보기') }}
                            </a>
                            <x-laravel-admin::admin.primary-button class="min-w-[120px] justify-center px-5">
                                {{ __('등록하기') }}
                            </x-laravel-admin::admin.primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>
