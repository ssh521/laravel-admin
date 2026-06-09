<x-laravel-admin::admin.layouts.admin title="권한 수정">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">Admin Home</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Edit Permission') }}
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
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Edit Permission Information') }}</h1>
            </div>

            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mx-auto max-w-[860px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('Permission Name') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="name" name="name" type="text" value="{{ old('name', $permission->name) }}" autocomplete="name" placeholder="{{ __('Enter permission name') }}" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    @if ($errors->has('name'))
                                        <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="['Please enter a permission name!']" />
                                    @endif
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="description" class="{{ $labelClass }}">{{ __('Description') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="description" name="description" type="text" value="{{ old('description', $permission->description ?? '') }}" placeholder="{{ __('Enter permission description') }}" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('description')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-8" style="border-top-color: #aaaaaa;">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.permissions.index') }}" class="inline-flex h-[30px] min-w-[104px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-5 text-base font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                                {{ __('목록보기') }}
                            </a>
                            <x-laravel-admin::admin.primary-button class="min-w-[120px] justify-center px-5">
                                {{ __('수정하기') }}
                            </x-laravel-admin::admin.primary-button>
                        </div>
                    </div>
                </div>
            </form>

            @can('delete', $permission)
            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="mx-auto mt-3 flex w-full max-w-[860px] justify-start">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]"
                    onclick="return confirm('{{ __('정말 삭제하시겠습니까?') }}')">{{ __('Delete') }}</button>
            </form>
            @endcan
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>
