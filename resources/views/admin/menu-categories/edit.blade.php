<x-laravel-admin::admin.layouts.admin title="메뉴 카테고리 수정">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menu-categories.index') }}">메뉴 카테고리 관리</a>
            </x-slot>
            <x-slot name="description">{{ __('Edit Menu Category') }}</x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    @php
        $inputClass = 'admin-focus-border rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-2 py-1 text-lg font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
        $labelClass = 'w-[120px] shrink-0 pr-3 text-right text-base leading-[28px] text-[#111111] dark:text-gray-100';
        $rowClass = 'flex gap-1 flex-row sm:items-start';
        $focusIn = "this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';";
        $focusOut = "this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';";
    @endphp

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[600px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Menu Category Information') }}</h1>
            </div>

            <x-laravel-admin::admin.session-messages />

            <form action="{{ route('admin.menu-categories.update', $menuCategory) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mx-auto max-w-[860px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('카테고리명') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="name" name="name" type="text" value="{{ old('name', $menuCategory->name) }}" autocomplete="name" placeholder="Enter category name" required class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('name')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="sort_order" class="{{ $labelClass }}">{{ __('정렬 순서') }} :</label>
                                <div class="w-full sm:w-[160px]">
                                    <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $menuCategory->sort_order) }}" min="0" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('sort_order')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <span class="{{ $labelClass }}">{{ __('상태') }} :</span>
                                <label for="is_active" class="flex min-h-[24px] items-center gap-2 text-base font-bold">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $menuCategory->is_active)) class="size-4 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                    <span>{{ __('활성화') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-8" style="border-top-color: #aaaaaa;">
                        <div class="flex flex-wrap justify-center gap-2">
                            <x-laravel-admin::admin.primary-button class="min-w-[120px] justify-center px-5">
                                {{ __('수정하기') }}
                            </x-laravel-admin::admin.primary-button>
                            <a href="{{ route('admin.menu-categories.roles', $menuCategory) }}" class="inline-flex h-[30px] min-w-[104px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-5 text-base font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                                {{ __('권한 관리') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @can('delete', $menuCategory)
        <form action="{{ route('admin.menu-categories.destroy', $menuCategory) }}" method="POST" onsubmit="return confirm('정말 삭제하시겠습니까?')" class="mx-auto mt-3 flex w-full max-w-5xl justify-start px-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                {{ __('삭제하기') }}
            </button>
        </form>
    @endcan
</x-laravel-admin::admin.layouts.admin>
