<x-laravel-admin::layouts.admin title="권한 수정">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">Admin Home</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Edit Permission') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    @php
        $inputClass = 'admin-focus-border h-[32px] rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-3 text-[14px] font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
        $labelClass = 'w-[116px] shrink-0 pr-4 text-right text-[15px] font-bold leading-[32px] text-[#111111] dark:text-gray-100';
        $rowClass = 'flex flex-col gap-1 sm:flex-row sm:items-start';
        $focusIn = "this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';";
        $focusOut = "this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';";
    @endphp

    <div class="mx-auto w-full max-w-7xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[720px] bg-white px-8 py-10 sm:px-16 lg:px-24 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-10">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Edit Permission Information') }}</h1>
            </div>

            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mx-auto max-w-[1060px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-[#8d8d8d] pt-7">
                        <div class="space-y-4">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('Permission Name') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="name" name="name" type="text" value="{{ old('name', $permission->name) }}" autocomplete="name" placeholder="{{ __('Enter permission name') }}" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    @if ($errors->has('name'))
                                        <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="['Please enter a permission name!']" />
                                    @endif
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="description" class="{{ $labelClass }}">{{ __('Description') }} :</label>
                                <div class="w-full sm:w-[520px]">
                                    <input id="description" name="description" type="text" value="{{ old('description', $permission->description ?? '') }}" placeholder="{{ __('Enter permission description') }}" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('description')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 border-t border-[#8d8d8d] pt-10">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.permissions.index') }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                                {{ __('목록보기') }}
                            </a>
                            <button type="submit" class="h-[34px] min-w-[140px] border border-[#777777] bg-[#eeeeee] px-8 text-[15px] font-bold leading-none text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                                {{ __('수정하기') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            @can('delete', $permission)
            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="mx-auto mt-3 flex w-full max-w-[1060px] justify-start">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]"
                    onclick="return confirm('{{ __('정말 삭제하시겠습니까?') }}')">{{ __('Delete') }}</button>
            </form>
            @endcan
        </div>
    </div>
</x-laravel-admin::layouts.admin>
