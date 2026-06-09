{{-- 메뉴 관리 페이지 - 메뉴 목록 조회, 검색, 정렬, 순서 변경 기능 --}}
<x-laravel-admin::admin.layouts.admin title="메뉴 관리">
    {{-- 페이지 헤더 --}}
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Menu List') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    {{-- 메인 컨텐츠 영역 --}}
    <div class="w-full bg-white border border-[#d8d8d0] px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[560px] border border-[#d9d9d9] bg-white px-6 py-7 sm:px-10 sm:py-10 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-2">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">
                    {{ __('메뉴 리스트') }}
                    @if(request('search'))
                        <span class="text-[16px] font-normal text-gray-600 dark:text-gray-400">
                            - "{{ request('search') }}" 검색 결과
                        </span>
                    @endif
                </h1>

                <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-base font-semibold">
                    @can('viewAny', Ssh521\LaravelAdmin\Models\Menu::class)
                        <a href="{{ route('admin.menus.index') }}">{{ __('목록보기') }}</a>
                        <span class="text-[#222222] dark:text-gray-400">|</span>
                    @endcan

                    @can('create', Ssh521\LaravelAdmin\Models\Menu::class)
                        <a href="{{ route('admin.menus.create') }}">{{ __('등록하기') }}</a>
                    @endcan
                </div>
            </div>

            <x-laravel-admin::admin.session-messages />

            <form class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end" action="{{ route('admin.menus.index') }}" method="GET">
                <label for="menu-search" class="sr-only">Search</label>
                <input
                    id="menu-search"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="admin-focus-border h-[28px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 text-base text-[#111111] outline-none sm:w-[260px] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="{{ __('메뉴 검색') }}"
                    onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';"
                    onblur="this.style.borderColor='#7d7d7d'; this.style.boxShadow='none';"
                >

                @if(request('search'))
                    <a href="{{ route('admin.menus.index') }}" class="inline-flex h-[28px] items-center rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-3 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                        {{ __('초기화') }}
                    </a>
                @endif

                <button type="submit" class="h-[28px] cursor-pointer rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-4 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('검색') }}
                </button>
            </form>

            {{-- 체크박스 제어 컴포넌트 --}}
            <div class="mb-1 flex flex-wrap items-center gap-x-1 text-[12px] font-semibold">
                <x-laravel-admin::admin.checkbox-controls />
                <span class="text-[#222222] dark:text-gray-400">|</span>
                <a href="javascript:void(0)" onclick="openCategorySelectionModal()">{{ __('분류선택') }}</a>
            </div>

            {{-- 메뉴 목록 테이블 --}}
            <div class="overflow-x-auto">
                        @php
                            $currentSort = $sort;
                            $currentDirection = $direction;

                            $getNextDirection = fn($f) => ($currentSort === $f && $currentDirection === 'asc') ? 'desc' : 'asc';

                            $renderSortIcon = function($f) use ($currentSort, $currentDirection) {
                                if ($currentSort !== $f) return '';
                                return $currentDirection === 'asc'
                                    ? '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-label="오름차순"><path fill-rule="evenodd" d="M3.293 12.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L10 7.414l-5.293 5.293a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>'
                                    : '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor" aria-label="내림차순"><path fill-rule="evenodd" d="M16.707 7.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6A1 1 0 014.707 7.293L10 12.586l5.293-5.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
                            };
                        @endphp

            <table class="min-w-full border-collapse text-base text-[#111111] dark:text-gray-100">
            <thead>
                <tr class="border-y border-[#cfcfcf] bg-[#dedede] text-[#555555] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    {{-- ID 컬럼 --}}
                    <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">ID</th>

                    {{-- 메뉴명 컬럼 (정렬 가능) --}}
                    <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">
                        <a href="{{ route('admin.menus.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $getNextDirection('name')])) }}"
                           class="inline-flex items-center gap-1">
                            메뉴명
                            {!! $renderSortIcon('name') !!}
                        </a>
                    </th>

                    {{-- 카테고리 컬럼 (정렬 가능) --}}
                    <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">
                        @if(empty(request('search')))
                        <a href="{{ route('admin.menus.index', array_merge(request()->query(), ['sort' => 'category', 'direction' => $getNextDirection('category')])) }}"
                           class="inline-flex items-center gap-1">
                            카테고리
                            {!! $renderSortIcon('category') !!}
                        </a>
                        @else
                        <span class="inline-flex items-center gap-1">
                            카테고리
                        </span>
                        @endif
                    </th>

                    {{-- 라우트/URL 컬럼 --}}
                    <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">라우트/URL</th>

                    {{-- 상태 컬럼 --}}
                    <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">상태</th>

                    {{-- 액션 컬럼 --}}
                    <th scope="col" class="h-10 px-2 text-right font-bold whitespace-nowrap">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- 메뉴 목록 반복 출력 --}}
                @forelse ($menus as $key => $menu)
                <tr
                    class="border-b border-[#e6e6e6] bg-[#fbfbfb] transition-colors hover:bg-white dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">

                    {{-- 메뉴 ID --}}
                    <th scope="row" class="h-10 px-4 font-medium">
                        <x-laravel-admin::admin.checkbox :value="$menu->id" />
                    </th>

                    {{-- 메뉴명 (링크 가능) --}}
                    <th scope="row" class="h-10 whitespace-nowrap px-4 text-left font-bold">
                        @can('view', $menu)
                        <a href="{{ route('admin.menus.show', $menu) }}">
                            @if($menu->icon)
                                <i class="{{ $menu->icon }} mr-2 text-blue-500"></i>
                            @endif
                            {{ $menu->name }}
                        </a>
                        @else
                        {{ $menu->name }}
                        @endcan
                    </th>

                    {{-- 카테고리 (순서 변경 모달 트리거) --}}
                    <td class="h-10 whitespace-nowrap px-4">
                        @if($menu->category)
                            <x-laravel-admin::admin.modal-trigger
                                text="{{ $menu->category->name }}"
                                modal-id="menu-order-modal"
                                variant="primary"
                                type="link"
                                data-category-id="{{ $menu->category->id }}"
                                data-category-name="{{ $menu->category->name }}"
                                modal-type="single"
                            />
                        @else
                            -
                        @endif
                    </td>

                    {{-- 라우트/URL 정보 --}}
                    <td class="h-10 whitespace-nowrap px-4">
                        @if($menu->route_name)
                            {{-- 라우트명이 있는 경우 --}}
                            <a href="{{ $menu->url }}"
                               target="{{ $menu->target ?? '_self' }}"
                               class="font-medium">
                                {{ $menu->route_name }}
                            </a>
                            @if($menu->route_parameters)
                                <br><small class="text-gray-400 dark:text-gray-500">{{ $menu->route_parameters }}</small>
                            @endif
                        @else
                            {{-- 직접 URL인 경우 --}}
                            @if($menu->url && $menu->url !== '#')
                                <a href="{{ $menu->url }}"
                                   target="{{ $menu->target ?? '_self' }}"
                                   class="">
                                    {{ $menu->url }}
                                </a>
                            @else
                                <span class="text-gray-600 dark:text-gray-400">{{ $menu->url ?: '-' }}</span>
                            @endif
                        @endif
                        {{-- 외부 링크 표시 --}}
                        @if($menu->is_external)
                            <span class="ml-1 text-[12px] text-red-700 dark:text-red-300">외부</span>
                        @endif
                    </td>

                    {{-- 활성/비활성 상태 --}}
                    <td class="h-10 whitespace-nowrap px-4">
                        <span class="{{ $menu->is_active ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                            {{ $menu->is_active ? __('활성') : __('비활성') }}
                        </span>
                    </td>

                    {{-- 액션 버튼 --}}
                    <td class="h-10 whitespace-nowrap px-4 text-right">
                        @can('update', $menu)
                        <a href="{{ route('admin.menus.edit', $menu) }}">{{ __('수정') }}</a>
                        @endcan
                    </td>
                </tr>
                @empty
                    {{-- 메뉴가 없을 때 표시 --}}
                    <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] dark:border-gray-700 dark:bg-gray-800">
                        <td colspan="6" class="h-10 px-4 text-center text-gray-500 dark:text-gray-400">
                            @if(request('search'))
                                {{ __('":keyword"에 대한 검색 결과가 없습니다.', ['keyword' => request('search')]) }}
                            @else
                                {{ __('등록된 메뉴가 없습니다.') }}
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-sm">
        {!! $menus->appends(request()->query())->links() !!}
    </div>
        </div>
    </div>

    {{-- 카테고리 선택 모달 (draggable + Livewire) --}}
    <x-laravel-admin::admin.draggable-modal
        id="category-selection-modal"
        title="메뉴 카테고리 변경"
        width="500"
        height="700"
        :close-on-backdrop-click="false"
    >
        <livewire:admin.menus.change-category-modal />
    </x-laravel-admin::admin.draggable-modal>

    {{-- 메뉴 순서 변경 모달 (Livewire 컴포넌트) --}}
    <x-laravel-admin::admin.draggable-modal
        id="menu-order-modal"
        title="메뉴 순서 변경"
        width="600"
        height="500"
        :close-on-backdrop-click="false"
    >
        <livewire:admin.menus.menu-order-modal />
    </x-laravel-admin::admin.draggable-modal>

    {{-- 메뉴 순서 변경 및 카테고리 변경 관련 JavaScript --}}
    <script>
        // ==========================================
        // 카테고리 선택 관련 기능
        // ==========================================

        /**
         * 카테고리 선택 모달 열기
         */
        function openCategorySelectionModal() {
            const selectedIds = getSelectedCheckboxValues();
            if (selectedIds.length === 0) {
                alert('{{ __('카테고리를 변경할 메뉴를 먼저 선택해주세요.') }}');
                return;
            }
            // Livewire로 선택 목록 전달 (객체로 래핑)
            Livewire.dispatch('admin-menus:change-category-modal:set-selected', { ids: selectedIds });

            // 드래그 모달 열기
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: { modalId: 'category-selection-modal' }
            }));
        }


        // ==========================================
        // 메뉴 순서 변경 관련 기능 (Livewire 컴포넌트로 이동됨)
        // ==========================================
        // DOM 로드 완료 후 이벤트 리스너 등록
        document.addEventListener('DOMContentLoaded', function() {
            // 모든 카테고리 링크에 클릭 이벤트 리스너 추가
            document.querySelectorAll('[data-category-id]').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-category-id');
                    const categoryName = this.getAttribute('data-category-name');
                    if (categoryId && categoryName) {
                        // 모달 열기
                        window.dispatchEvent(new CustomEvent('open-modal', {
                            detail: { modalId: 'menu-order-modal' }
                        }));
                        // Livewire 컴포넌트에 모달 열기 이벤트 전달
                        Livewire.dispatch('admin-menus:menu-order-modal:open', {
                            data: {
                                categoryId: parseInt(categoryId),
                                categoryName: categoryName
                            }
                        });
                    }
                });
            });

            // 페이지 새로고침 이벤트 처리
            Livewire.on('menu-order-modal:refresh-page', () => {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });

            // 알림 이벤트 처리
            Livewire.on('admin-menus:menu-order-modal:notification', (data) => {
                alert(data.message);
            });
        });
    </script>

</x-laravel-admin::admin.layouts.admin>
