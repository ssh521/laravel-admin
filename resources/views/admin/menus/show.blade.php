<x-laravel-admin::admin.layouts.admin title="메뉴 상세">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menus.index') }}">메뉴 관리</a>
            </x-slot>
            <x-slot name="description">{{ __('Menu Detail') }}</x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900">
        <div class="min-h-[600px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Menu Information') }}</h1>
            </div>

            <x-laravel-admin::admin.session-messages />

            <div class="border border-dashed border-[#bdbdbd] bg-[#f7f7f7] px-6 py-8 text-base font-bold text-[#222222] sm:px-12 lg:px-16 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <div class="mx-auto max-w-[800px]">
                    <div class="space-y-3">
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('ID') }} :</div>
                            <div>{{ $menu->id }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('메뉴명') }} :</div>
                            <div>
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }} mr-2"></i>
                                @endif
                                {{ $menu->name }}
                            </div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('카테고리') }} :</div>
                            <div>{{ $menu->category ? $menu->category->name : '-' }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('상위 메뉴') }} :</div>
                            <div>{{ $menu->parent ? $menu->parent->name : '-' }}</div>
                        </div>
                    </div>

                    <div class="my-6 border-t border-[#8d8d8d]"></div>

                    <div class="space-y-3">
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('Route Name') }} :</div>
                            <div>{{ $menu->route_name ?: '-' }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('Route Params') }} :</div>
                            <div>{{ $menu->route_parameters ?: '-' }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('Direct URL') }} :</div>
                            <div>{{ $menu->url ?: '-' }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('Target') }} :</div>
                            <div>{{ $menu->target ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="my-6 border-t border-[#8d8d8d]"></div>

                    <div class="space-y-3">
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('정렬 순서') }} :</div>
                            <div>{{ $menu->sort_order }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('상태') }} :</div>
                            <div>{{ $menu->is_active ? __('활성') : __('비활성') }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('외부 링크') }} :</div>
                            <div>{{ $menu->is_external ? __('예') : __('아니오') }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('등록일') }} :</div>
                            <div>{{ $menu->created_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('수정일') }} :</div>
                            <div>{{ $menu->updated_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        @if($menu->description)
                            <div class="flex min-h-[24px] flex-col sm:flex-row">
                                <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('설명') }} :</div>
                                <div>{{ $menu->description }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($menu->children->count() > 0)
                <div class="mt-8 overflow-x-auto">
                    <table class="min-w-full border-collapse text-sm text-[#111111] dark:text-gray-100">
                        <thead>
                            <tr class="border-y border-[#cfcfcf] bg-[#dedede] text-[#555555] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <th class="h-12 px-2 text-left font-bold">{{ __('Child Menu') }}</th>
                                <th class="h-12 px-2 text-left font-bold">{{ __('Route/URL') }}</th>
                                <th class="h-12 px-2 text-left font-bold">{{ __('Status') }}</th>
                                <th class="h-12 px-2 text-right font-bold">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menu->children as $childMenu)
                                <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] dark:border-gray-700 dark:bg-gray-800">
                                    <td class="h-12 whitespace-nowrap px-4 font-bold">{{ $childMenu->name }}</td>
                                    <td class="h-12 whitespace-nowrap px-4">{{ $childMenu->route_name ?: ($childMenu->url ?: '-') }}</td>
                                    <td class="h-12 whitespace-nowrap px-4">{{ $childMenu->is_active ? __('활성') : __('비활성') }}</td>
                                    <td class="h-12 whitespace-nowrap px-4 text-right">
                                        <a href="{{ route('admin.menus.show', $childMenu) }}">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <a href="{{ route('admin.menus.index') }}" class="inline-flex items-center justify-center min-w-[104px] px-5 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('목록보기') }}
                </a>
                @can('update', $menu)
                    <a href="{{ route('admin.menus.edit', $menu) }}" class="inline-flex items-center justify-center min-w-[120px] px-5 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest !text-white dark:!text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('수정하기') }}
                    </a>
                @endcan
            </div>
        </div>
    </div>

    @can('delete', $menu)
        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('{{ __('정말 삭제하시겠습니까?') }}')" class="mx-auto mt-3 flex w-full max-w-5xl justify-start px-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                {{ __('삭제하기') }}
            </button>
        </form>
    @endcan
</x-laravel-admin::admin.layouts.admin>
