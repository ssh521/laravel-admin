<x-laravel-admin::admin.layouts.admin title="메뉴 등록">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menus.index') }}">메뉴 관리</a>
            </x-slot>
            <x-slot name="description">{{ __('Create New Menu') }}</x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    @php
        $inputClass = 'admin-focus-border rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-2 py-1 text-lg font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
        $textareaClass = 'admin-focus-border rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-2 py-2 text-lg font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
        $labelClass = 'w-[120px] shrink-0 pr-3 text-right text-base leading-[28px] text-[#111111] dark:text-gray-100';
        $rowClass = 'flex gap-1 flex-row sm:items-start';
        $focusIn = "this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';";
        $focusOut = "this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';";
    @endphp

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[600px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Menu Information') }}</h1>
            </div>

            <x-laravel-admin::admin.session-messages />

            <form action="{{ route('admin.menus.store') }}" method="POST">
                @csrf

                <div class="mx-auto max-w-[860px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('메뉴명') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" placeholder="Enter menu name" required class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('name')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="category_id" class="{{ $labelClass }}">{{ __('카테고리') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <select id="category_id" name="category_id" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                        <option value="">{{ __('선택하세요') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('category_id')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="parent_id" class="{{ $labelClass }}">{{ __('상위 메뉴') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <select id="parent_id" name="parent_id" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                        <option value="">{{ __('선택하세요') }}</option>
                                        @foreach($parentMenus as $parentMenu)
                                            <option value="{{ $parentMenu->id }}" @selected(old('parent_id') == $parentMenu->id)>{{ $parentMenu->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('parent_id')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="route_name" class="{{ $labelClass }}">{{ __('Route Name') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="route_name" name="route_name" type="text" value="{{ old('route_name') }}" placeholder="예: users.index" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('route_name')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="route_parameters" class="{{ $labelClass }}">{{ __('Route Params') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="route_parameters" name="route_parameters" type="text" value="{{ old('route_parameters') }}" placeholder='예: {"id": 1}' class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('route_parameters')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="url" class="{{ $labelClass }}">{{ __('Direct URL') }} :</label>
                                <div class="w-full sm:w-[420px]">
                                    <input id="url" name="url" type="text" value="{{ old('url') }}" placeholder="예: /admin/users" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('url')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="target" class="{{ $labelClass }}">{{ __('Target') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <select id="target" name="target" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                        <option value="">{{ __('선택하세요') }}</option>
                                        <option value="_self" @selected(old('target') == '_self')>{{ __('현재 창') }}</option>
                                        <option value="_blank" @selected(old('target') == '_blank')>{{ __('새 창') }}</option>
                                        <option value="_parent" @selected(old('target') == '_parent')>{{ __('부모 창') }}</option>
                                        <option value="_top" @selected(old('target') == '_top')>{{ __('최상위 창') }}</option>
                                    </select>
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('target')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-6" style="border-top-color: #aaaaaa;">
                        <div class="space-y-3">
                            <div class="{{ $rowClass }}">
                                <label for="sort_order" class="{{ $labelClass }}">{{ __('정렬 순서') }} :</label>
                                <div class="w-full sm:w-[160px]">
                                    <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" min="0" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('sort_order')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="icon" class="{{ $labelClass }}">{{ __('Icon') }} :</label>
                                <div class="w-full sm:w-[260px]">
                                    <input id="icon" name="icon" type="text" value="{{ old('icon') }}" placeholder="fas fa-home" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('icon')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <span class="{{ $labelClass }}">{{ __('상태') }} :</span>
                                <label for="is_active" class="flex min-h-[24px] items-center gap-2 text-base font-bold">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true)) class="size-4 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                    <span>{{ __('활성화') }}</span>
                                </label>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="description" class="{{ $labelClass }}">{{ __('설명') }} :</label>
                                <div class="w-full sm:w-[620px]">
                                    <textarea id="description" name="description" rows="3" class="{{ $textareaClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">{{ old('description') }}</textarea>
                                    <x-laravel-admin::admin.input-error-message class="mt-2 text-[13px]" :messages="$errors->get('description')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-dashed pt-8" style="border-top-color: #aaaaaa;">
                        <div class="flex justify-center">
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
