{{-- 메뉴 관리 페이지 - 메뉴 목록 조회, 검색, 정렬, 순서 변경 기능 --}}
<x-layouts.admin title="메뉴 관리">
    {{-- 페이지 헤더 --}}
    <x-slot name="header">
        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Menu List') }}
            </x-slot>
        </x-admin-header>
    </x-slot>

    {{-- 메인 컨텐츠 영역 --}}
    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">

            {{-- 페이지 제목 --}}
            <div class="text-2xl text-left mt-1 font-bold dark:text-white">
                {{ __('메뉴 리스트') }}
                @if(request('search'))
                    <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                        - "{{ request('search') }}" 검색 결과
                    </span>
                @endif
            </div>

            {{-- 액션 버튼 및 검색 영역 --}}
            <div class="flex justify-between items-center pb-4">
                {{-- 네비게이션 링크 --}}
                <div>
                    @can('viewAny', Ssh521\LaravelAdmin\Models\Menu::class)
                    <a href="{{ route('admin.menus.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">목록보기</a>
                    <span class="mx-1">|</span>
                    @endcan

                    @can('create', Ssh521\LaravelAdmin\Models\Menu::class)
                    <a href="{{ route('admin.menus.create') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">등록하기</a>
                    @endcan
                </div>

            <div>
                <form class="flex items-center ml-1 space-x-2" action="{{ route('admin.menus.index') }}" method="GET">
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="w-full relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 dark:focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 block w-40 pl-2 pr-8 p-1.5"
                            placeholder="메뉴 검색">
                        {{-- 검색어 초기화 버튼 --}}
                        @if(request('search'))
                            <a href="{{ route('admin.menus.index') }}" 
                               class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>

                    {{-- 검색 버튼 --}}
                    <button type="submit"
                        class="p-1 text-sm font-medium text-white bg-gray-500 rounded-lg border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 30 30"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-2-2m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>
                </form>
            </div>
        </div>

        <x-session-messages />

        {{-- 체크박스 제어 컴포넌트 --}}
        <div class="pb-2">
            <x-checkbox-controls /> | 
            <a href="javascript:void(0)" onclick="openCategorySelectionModal()" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">분류선택</a>
        </div>

        {{-- 메뉴 목록 테이블 --}}
        <div
            class="relative overflow-x-auto my-2 border-gray-200 dark:border-gray-700 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">
                        @php
                            // 정렬 관련 변수들을 한 번에 정의
                            $currentSort = request('sort', 'id');
                            $currentDirection = request('direction', 'desc');
                            
                            // 정렬 방향 결정 함수
                            $getNextDirection = function($sortField) use ($currentSort, $currentDirection) {
                                return ($currentSort === $sortField && $currentDirection === 'asc') ? 'desc' : 'asc';
                            };
                            
                            // 정렬 아이콘 렌더링 함수
                            $renderSortIcon = function($sortField) use ($currentSort, $currentDirection) {
                                if ($currentSort === $sortField) {
                                    if ($currentDirection === 'asc') {
                                        return '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3.293 12.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L10 7.414l-5.293 5.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>';
                                    } else {
                                        return '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 7.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6A1 1 0 014.707 7.293L10 12.586l5.293-5.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>';
                                    }
                                }
                                return '';
                            };
                        @endphp

            <table class="w-full min-w-[300px] text-sm text-left rtl:text-right">
            <thead
                class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-200 dark:bg-gray-700 border-b border-t border-gray-300 dark:border-gray-600">
                <tr>
                    {{-- ID 컬럼 --}}
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">ID</th>
                    
                    {{-- 메뉴명 컬럼 (정렬 가능) --}}
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">
                        <a href="{{ route('admin.menus.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $getNextDirection('name')])) }}"
                           class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400">
                            메뉴명
                            {!! $renderSortIcon('name') !!}
                        </a>
                    </th>
                    
                    {{-- 카테고리 컬럼 (정렬 가능) --}}
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">
                        @if(empty(request('search')))
                        <a href="{{ route('admin.menus.index', array_merge(request()->query(), ['sort' => 'category', 'direction' => $getNextDirection('category')])) }}"
                           class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400">
                            카테고리
                            {!! $renderSortIcon('category') !!}
                        </a>
                        @else
                        <span class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300">
                            카테고리
                        </span>
                        @endif
                    </th>
                    
                    {{-- 라우트/URL 컬럼 --}}
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">라우트/URL</th>
                    
                    {{-- 상태 컬럼 --}}
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">상태</th>
                    
                    {{-- 액션 컬럼 --}}
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- 메뉴 목록 반복 출력 --}}
                @forelse ($menus as $key => $menu)
                <tr
                    class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    
                    {{-- 메뉴 ID --}}
                    <th scope="row" class="px-2 py-2 font-medium text-gray-900 dark:text-white">
                        <x-checkbox :value="$menu->id" />
                    </th>
                    
                    {{-- 메뉴명 (링크 가능) --}}
                    <th scope="row" class="px-2 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                        @can('view', $menu)
                        <a href="{{ route('admin.menus.show', $menu) }}"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-left">
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
                    <td class="px-2 py-2">
                        @if($menu->category)
                            <x-modal-trigger 
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
                    <td class="px-2 py-2 whitespace-nowrap dark:text-white">
                        @if($menu->route_name)
                            {{-- 라우트명이 있는 경우 --}}
                            <a href="{{ $menu->url }}" 
                               target="{{ $menu->target ?? '_self' }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
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
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $menu->url }}
                                </a>
                            @else
                                <span class="text-gray-600 dark:text-gray-400">{{ $menu->url ?: '-' }}</span>
                            @endif
                        @endif
                        {{-- 외부 링크 표시 --}}
                        @if($menu->is_external)
                            <span class="ml-1 text-xs bg-red-100 text-red-800 px-1 rounded">외부</span>
                        @endif
                    </td>
                    
                    {{-- 활성/비활성 상태 --}}
                    <td class="px-2 py-2">
                        <span class="px-2 py-1 text-xs {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full">
                            {{ $menu->is_active ? '활성' : '비활성' }}
                        </span>
                    </td>
                    
                    {{-- 액션 버튼 --}}
                    <td class="px-2 py-2 text-right whitespace-nowrap">
                        @can('update', $menu)
                        <a class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            href="{{ route('admin.menus.edit', $menu) }}">수정</a>
                        @endcan
                    </td>
                </tr>
                @empty
                    {{-- 메뉴가 없을 때 표시 --}}
                    <tr>
                        <td colspan="6" class="px-2 py-4 text-center">
                            @if(request('search'))
                                "{{ request('search') }}"에 대한 검색 결과가 없습니다.
                            @else
                                등록된 메뉴가 없습니다.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="my-3">
        {!! $menus->appends(request()->query())->links() !!}
    </div>

    </div>

    {{-- 카테고리 선택 모달 (draggable + Livewire) --}}
    <x-draggable-modal
        id="category-selection-modal"
        title="메뉴 카테고리 변경"
        width="500"
        height="700"
        :close-on-backdrop-click="false"
    >
        <livewire:menus.change-category-modal />
    </x-draggable-modal>

    {{-- 메뉴 순서 변경 모달 (Livewire 컴포넌트) --}}
    <x-draggable-modal 
        id="menu-order-modal"
        title="메뉴 순서 변경"
        width="600"
        height="500"
        :close-on-backdrop-click="false"
    >
        <livewire:admin.menus.menu-order-modal />
    </x-draggable-modal>

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
                alert('카테고리를 변경할 메뉴를 먼저 선택해주세요.');
                return;
            }
            // Livewire로 선택 목록 전달 (객체로 래핑)
            Livewire.dispatch('menus:change-category-modal:set-selected', { ids: selectedIds });

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

</x-layouts.admin> 