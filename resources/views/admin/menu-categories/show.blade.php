<x-laravel-admin::admin.layouts.admin title="메뉴 카테고리 상세">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menu-categories.index') }}">메뉴 카테고리 관리</a>
            </x-slot>
            <x-slot name="description">{{ __('Menu Category Detail') }}</x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900">
        <div class="min-h-[600px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Menu Category Information') }}</h1>
            </div>

            <x-laravel-admin::admin.session-messages />

            <div class="border border-dashed border-[#bdbdbd] bg-[#f7f7f7] px-6 py-8 text-base font-bold text-[#222222] sm:px-12 lg:px-16 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <div class="mx-auto max-w-[800px]">
                    <div class="space-y-3">
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('ID') }} :</div>
                            <div>{{ $menuCategory->id }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('카테고리명') }} :</div>
                            <div>{{ $menuCategory->name }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('정렬 순서') }} :</div>
                            <div>{{ $menuCategory->sort_order }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('상태') }} :</div>
                            <div>{{ $menuCategory->is_active ? __('활성') : __('비활성') }}</div>
                        </div>
                    </div>

                    <div class="my-6 border-t border-[#8d8d8d]"></div>

                    <div class="space-y-3">
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('허용 역할') }} :</div>
                            <div>
                                @forelse($menuCategory->roles as $role)
                                    <span>{{ $role->name }}</span>@if (! $loop->last)<span>, </span>@endif
                                @empty
                                    {{ __('이 카테고리에 허용된 역할이 없습니다.') }}
                                @endforelse
                            </div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('메뉴 개수') }} :</div>
                            <div>{{ $menuCategory->menus->count() }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('등록일') }} :</div>
                            <div>{{ $menuCategory->created_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="flex min-h-[24px] flex-col sm:flex-row">
                            <div class="w-[120px] shrink-0 text-right sm:pr-3">{{ __('수정일') }} :</div>
                            <div>{{ $menuCategory->updated_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($menuCategory->menus->count() > 0)
                <div class="mt-8 overflow-x-auto">
                    <table class="min-w-full border-collapse text-sm text-[#111111] dark:text-gray-100">
                        <thead>
                            <tr class="border-y border-[#cfcfcf] bg-[#dedede] text-[#555555] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <th class="h-12 px-2 text-left font-bold">{{ __('Menu Name') }}</th>
                                <th class="h-12 px-2 text-left font-bold">{{ __('Link') }}</th>
                                <th class="h-12 px-2 text-left font-bold">{{ __('Status') }}</th>
                                <th class="h-12 px-2 text-right font-bold">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menuCategory->menus as $menu)
                                <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] dark:border-gray-700 dark:bg-gray-800">
                                    <td class="h-12 whitespace-nowrap px-4 font-bold">{{ $menu->name }}</td>
                                    <td class="h-12 whitespace-nowrap px-4">{{ $menu->route_name ?: ($menu->url ?: '-') }}</td>
                                    <td class="h-12 whitespace-nowrap px-4">{{ $menu->is_active ? __('활성') : __('비활성') }}</td>
                                    <td class="h-12 whitespace-nowrap px-4 text-right">
                                        <a href="{{ route('admin.menus.show', $menu) }}">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-8 h-12 border-b border-[#e6e6e6] bg-[#fbfbfb] px-4 text-center text-sm leading-[48px] text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    {{ __('이 카테고리에 연결된 메뉴가 없습니다.') }}
                </div>
            @endif

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <a href="{{ route('admin.menu-categories.index') }}" class="inline-flex items-center justify-center min-w-[104px] px-5 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('목록보기') }}
                </a>
                @can('update', $menuCategory)
                    <a href="{{ route('admin.menu-categories.edit', $menuCategory) }}" class="inline-flex items-center justify-center min-w-[120px] px-5 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest !text-white dark:!text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('수정하기') }}
                    </a>
                @endcan
                <a href="{{ route('admin.menu-categories.roles', $menuCategory) }}" class="inline-flex items-center justify-center min-w-[120px] px-5 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest !text-white dark:!text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('권한 관리') }}
                </a>
            </div>
        </div>
    </div>

    @can('delete', $menuCategory)
        <form action="{{ route('admin.menu-categories.destroy', $menuCategory) }}" method="POST" onsubmit="return confirm('{{ __('정말 삭제하시겠습니까?') }}')" class="mx-auto mt-3 flex w-full max-w-5xl justify-start px-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                {{ __('삭제하기') }}
            </button>
        </form>
    @endcan
</x-laravel-admin::admin.layouts.admin>
