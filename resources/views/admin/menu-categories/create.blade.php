<x-laravel-admin::layouts.admin title="메뉴 카테고리 등록">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menu-categories.index') }}">메뉴 카테고리 관리</a>
            </x-slot>
            <x-slot name="description">{{ __('Create New Menu Category') }}</x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    @php
        $inputClass = 'admin-focus-border h-[32px] rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-3 text-[14px] font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
        $labelClass = 'w-[150px] shrink-0 pr-4 text-right text-[15px] font-bold leading-[32px] text-[#111111] dark:text-gray-100';
        $rowClass = 'flex flex-col gap-1 sm:flex-row sm:items-start';
        $focusIn = "this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';";
        $focusOut = "this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';";
    @endphp

    <div class="mx-auto w-full max-w-7xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[720px] bg-white px-8 py-10 sm:px-16 lg:px-24 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-10">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Menu Category Information') }}</h1>
            </div>

            <form action="{{ route('admin.menu-categories.store') }}" method="POST">
                @csrf

                <div class="mx-auto max-w-[1060px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-[#8d8d8d] pt-7">
                        <div class="space-y-4">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('카테고리명') }} :</label>
                                <div class="w-full sm:w-[520px]">
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" placeholder="Enter category name" required class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('name')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="sort_order" class="{{ $labelClass }}">{{ __('정렬 순서') }} :</label>
                                <div class="w-full sm:w-[160px]">
                                    <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" min="0" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('sort_order')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <span class="{{ $labelClass }}">{{ __('상태') }} :</span>
                                <label for="is_active" class="flex min-h-[32px] items-center gap-2 text-[14px] font-bold">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true)) class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                    <span>{{ __('활성화') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 border-t border-[#8d8d8d] pt-10">
                        <div class="flex justify-center">
                            <button type="submit" class="h-[34px] min-w-[140px] border border-[#777777] bg-[#eeeeee] px-8 text-[15px] font-bold leading-none text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                                {{ __('등록하기') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-laravel-admin::layouts.admin>
