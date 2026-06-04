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

    <div class="mx-auto w-full max-w-7xl bg-white px-2 py-2 dark:bg-gray-900">
        <div class="min-h-[720px] bg-white px-8 py-10 sm:px-16 lg:px-24 dark:bg-gray-800">
            <div class="mb-10">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Menu Category Information') }}</h1>
            </div>

            <x-laravel-admin::admin.session-messages />

            <div class="border border-dashed border-[#bdbdbd] bg-[#f7f7f7] px-8 py-10 text-[15px] font-bold text-[#222222] sm:px-16 lg:px-24 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <div class="mx-auto max-w-[980px]">
                    <div class="space-y-4">
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('ID') }} :</div>
                            <div>{{ $menuCategory->id }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('카테고리명') }} :</div>
                            <div>{{ $menuCategory->name }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('정렬 순서') }} :</div>
                            <div>{{ $menuCategory->sort_order }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('상태') }} :</div>
                            <div>{{ $menuCategory->is_active ? __('활성') : __('비활성') }}</div>
                        </div>
                    </div>

                    <div class="my-8 border-t border-[#8d8d8d]"></div>

                    <div class="space-y-4">
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('허용 역할') }} :</div>
                            <div>
                                @forelse($menuCategory->roles as $role)
                                    <span>{{ $role->name }}</span>@if (! $loop->last)<span>, </span>@endif
                                @empty
                                    {{ __('이 카테고리에 허용된 역할이 없습니다.') }}
                                @endforelse
                            </div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('메뉴 개수') }} :</div>
                            <div>{{ $menuCategory->menus->count() }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('등록일') }} :</div>
                            <div>{{ $menuCategory->created_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                        <div class="flex min-h-[28px] flex-col sm:flex-row">
                            <div class="w-[150px] shrink-0 text-right sm:pr-4">{{ __('수정일') }} :</div>
                            <div>{{ $menuCategory->updated_at?->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($menuCategory->menus->count() > 0)
                <div class="mt-8 overflow-x-auto">
                    <table class="min-w-full border-collapse text-[14px] text-[#111111] dark:text-gray-100">
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
                <div class="mt-8 h-12 border-b border-[#e6e6e6] bg-[#fbfbfb] px-4 text-center text-[14px] leading-[48px] text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    {{ __('이 카테고리에 연결된 메뉴가 없습니다.') }}
                </div>
            @endif

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <a href="{{ route('admin.menu-categories.index') }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('목록보기') }}
                </a>
                @can('update', $menuCategory)
                    <a href="{{ route('admin.menu-categories.edit', $menuCategory) }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                        {{ __('수정하기') }}
                    </a>
                @endcan
                <a href="{{ route('admin.menu-categories.roles', $menuCategory) }}" class="inline-flex h-[34px] min-w-[120px] items-center justify-center border border-[#777777] bg-[#eeeeee] px-6 text-[15px] font-bold text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('권한 관리') }}
                </a>
            </div>
        </div>
    </div>

    @can('delete', $menuCategory)
        <form action="{{ route('admin.menu-categories.destroy', $menuCategory) }}" method="POST" onsubmit="return confirm('{{ __('정말 삭제하시겠습니까?') }}')" class="mx-auto mt-3 flex w-full max-w-7xl justify-start px-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                {{ __('삭제하기') }}
            </button>
        </form>
    @endcan
</x-laravel-admin::admin.layouts.admin>
