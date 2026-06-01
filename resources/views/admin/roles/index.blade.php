<x-layouts.admin title="역할 관리">

    <x-slot name="header">

        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Role List') }}
            </x-slot>
        </x-admin-header>

    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="text-2xl text-left mt-1 font-bold dark:text-white">
                {{ __('역할 리스트') }}
                @if(request('search'))
                    <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                        - "{{ request('search') }}" 검색 결과
                    </span>
                @endif
            </div>

            <div class="flex justify-between items-center pb-4">

                <div>
                    @can('viewAny', \Spatie\Permission\Models\Role::class)
                    <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">목록보기</a> 
                    <span class="mx-1">|</span>
                    @endcan

                    @can('create', \Spatie\Permission\Models\Role::class)
                    <a href="{{ route('admin.roles.create') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">등록하기</a>
                    @endcan
                    
                    <span class="mx-1">|</span>
                    
                    {{-- 메뉴 카테고리 선택 버튼 --}}
                    <button type="button" 
                            id="open-menu-category-selector"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        메뉴 카테고리 관리
                    </button>
                </div>

                <div>
                    <form class="flex items-center ml-1 space-x-2" action="{{ route('admin.roles.index') }}" method="GET">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="w-full relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 dark:focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 block w-40 pl-2 pr-8 p-1.5"
                                placeholder="역할 검색">
                            @if(request('search'))
                                <a href="{{ route('admin.roles.index') }}" 
                                   class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>

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

            <div
                class="relative overflow-x-auto my-2 border-gray-200 dark:border-gray-700 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">
                <table class="w-full min-w-[300px] text-sm text-left rtl:text-right">
            <thead
                class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-200 dark:bg-gray-700 border-b border-t border-gray-300 dark:border-gray-600">
                <tr>
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">
                        {{ __('Role Name') }}
                    </th>
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">
                        {{ __('Assigned Permissions') }}
                    </th>
                    <th scope="col" class="px-2 py-3 whitespace-nowrap">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $role)
                <tr
                    class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <th scope="row"
                                        class="px-2 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        @can('view', $role)
                                        <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('admin-roles:role-detail-modal:show', { detail: {{ $role->id }} }))"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-left">
                                            {{ $role->name }}
                                        </button>
                                        @else
                                        {{ $role->name }}
                                        @endcan
                                    </th>
                                    <td class="px-2 py-2">
                                        @if($role->permissions->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($role->permissions as $permission)
                                                <span
                                                    class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                                    {{ $permission->name }}
                                                </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-xs">권한 없음</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-right whitespace-nowrap">

                                        @can('update', $role)
                                        <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('admin-roles:role-detail-modal:show', { detail: {{ $role->id }} }))"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            상세보기
                                        </button>
                                        @endcan

                                    </td>
                                </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-2 py-4 text-center">
                            @if(request('search'))
                                "{{ request('search') }}"에 대한 검색 결과가 없습니다.
                            @else
                                등록된 역할이 없습니다.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="my-3">
        {!! $data->appends(request()->query())->links() !!}
    </div>
    </div>

    {{-- 메뉴 카테고리 선택 모달 --}}
    <x-draggable-modal 
        id="menu-category-selector-modal"
        title="메뉴 카테고리 관리"
        width="600"
        height="500"
        :close-on-backdrop-click="true"
    >
        <div class="p-4">
            {{-- 검색 영역 --}}
            <div class="mb-4">
                <div class="relative">
                    <input type="text" 
                           id="menu-category-search"
                           placeholder="메뉴 카테고리 검색..."
                           class="w-full px-3 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- 메뉴 카테고리 목록 --}}
            <div class="max-h-80 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md">
                <div id="menu-category-list" class="divide-y divide-gray-200 dark:divide-gray-600">
                    @php
                        $menuCategories = \Ssh521\LaravelAdmin\Models\MenuCategory::withCount('menus')->with('roles')->ordered()->get();
                    @endphp
                    @foreach($menuCategories as $category)
                    <div class="menu-category-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150"
                         data-category-id="{{ $category->id }}"
                         data-category-name="{{ $category->name }}"
                         data-category-menu-count="{{ $category->menu_count ?? 0 }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $category->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    메뉴 {{ $category->menu_count ?? 0 }}개
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    허용된 역할: 
                                    @if($category->roles->count() > 0)
                                        @foreach($category->roles as $role)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-red-500">없음</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        활성
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        비활성
                                    </span>
                                @endif
                                <button type="button" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                        onclick="manageCategoryRoles({{ $category->id }}, '{{ $category->name }}')">
                                    역할 관리
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                {{-- 빈 상태 메시지 --}}
                <div id="no-menu-categories-message" class="p-6 text-center text-gray-500 dark:text-gray-400 hidden">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">메뉴 카테고리가 없습니다</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">등록된 메뉴 카테고리가 없습니다.</p>
                </div>
            </div>
            
            {{-- 모달 푸터 --}}
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" 
                        id="close-menu-category-selector"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors duration-200">
                    닫기
                </button>
            </div>
        </div>
    </x-draggable-modal>

    {{-- 역할 관리 모달 --}}
    <x-draggable-modal 
        id="role-management-modal"
        title="카테고리 역할 관리"
        width="500"
        height="400"
        :close-on-backdrop-click="true"
    >
        <div class="p-4">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="category-name-display"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">이 카테고리에 접근할 수 있는 역할을 선택하세요.</p>
            </div>
            
            <div class="max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md p-3">
                <div id="role-list" class="space-y-2">
                    @php
                        $allRoles = \Spatie\Permission\Models\Role::all();
                    @endphp
                    @foreach($allRoles as $role)
                    <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
                        <input type="checkbox" 
                               class="role-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               value="{{ $role->id }}"
                               data-role-name="{{ $role->name }}">
                        <span class="text-sm text-gray-900 dark:text-white">{{ $role->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" 
                        id="cancel-role-management"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors duration-200">
                    취소
                </button>
                <button type="button" 
                        id="save-role-management"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors duration-200">
                    저장
                </button>
            </div>
        </div>
    </x-draggable-modal>


    
    {{-- JavaScript --}}
    <script>
    // 전역 변수 선언
    let currentCategoryId = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        const openMenuCategorySelector = document.getElementById('open-menu-category-selector');
        const closeMenuCategorySelector = document.getElementById('close-menu-category-selector');
        const menuCategorySearch = document.getElementById('menu-category-search');
        const menuCategoryList = document.getElementById('menu-category-list');
        const noMenuCategoriesMessage = document.getElementById('no-menu-categories-message');
        const cancelRoleManagement = document.getElementById('cancel-role-management');
        const saveRoleManagement = document.getElementById('save-role-management');
        let allMenuCategories = [];
        let filteredMenuCategories = [];
        
        // 초기 메뉴 카테고리 목록 설정
        allMenuCategories = Array.from(document.querySelectorAll('.menu-category-item')).map(item => ({
            id: item.dataset.categoryId,
            name: item.dataset.categoryName,
            menuCount: item.dataset.categoryMenuCount,
            element: item
        }));
        
        filteredMenuCategories = [...allMenuCategories];
        
        // 메뉴 카테고리 선택 모달 열기
        openMenuCategorySelector.addEventListener('click', function() {
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'menu-category-selector-modal', action: 'open' } 
            }));
        });
        
        // 메뉴 카테고리 선택 모달 닫기
        closeMenuCategorySelector.addEventListener('click', function() {
            window.dispatchEvent(new CustomEvent('close-modal', { 
                detail: { modalId: 'menu-category-selector-modal' } 
            }));
        });
        
        // 메뉴 카테고리 검색
        menuCategorySearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                filteredMenuCategories = [...allMenuCategories];
            } else {
                filteredMenuCategories = allMenuCategories.filter(category => 
                    category.name.toLowerCase().includes(searchTerm)
                );
            }
            
            renderMenuCategoryList();
        });
        
        // 메뉴 카테고리 목록 렌더링
        function renderMenuCategoryList() {
            allMenuCategories.forEach(category => {
                category.element.style.display = 'none';
            });
            
            if (filteredMenuCategories.length === 0) {
                noMenuCategoriesMessage.classList.remove('hidden');
                menuCategoryList.classList.add('hidden');
            } else {
                noMenuCategoriesMessage.classList.add('hidden');
                menuCategoryList.classList.remove('hidden');
                
                filteredMenuCategories.forEach(category => {
                    category.element.style.display = 'block';
                });
            }
        }
        
        // 역할 관리 취소
        cancelRoleManagement.addEventListener('click', function() {
            window.dispatchEvent(new CustomEvent('close-modal', { 
                detail: { modalId: 'role-management-modal' } 
            }));
        });
        
        // 역할 관리 저장
        saveRoleManagement.addEventListener('click', function() {
            console.log('저장 버튼 클릭됨, currentCategoryId:', currentCategoryId);
            
            if (!currentCategoryId) {
                console.error('currentCategoryId가 설정되지 않음');
                showNotification('카테고리 ID가 설정되지 않았습니다.', 'error');
                return;
            }
            
            const selectedRoles = Array.from(document.querySelectorAll('.role-checkbox:checked'))
                .map(checkbox => checkbox.value);
            
            console.log('선택된 역할들:', selectedRoles);
            
            // AJAX 요청으로 역할 저장
            fetch(`/admin/menu-categories/${currentCategoryId}/roles`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    roles: selectedRoles
                })
            })
            .then(response => {
                console.log('응답 상태:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('응답 데이터:', data);
                if (data.success) {
                    // 성공 메시지 표시
                    showNotification('역할이 성공적으로 저장되었습니다.', 'success');
                    
                    // 모달 닫기
                    window.dispatchEvent(new CustomEvent('close-modal', { 
                        detail: { modalId: 'role-management-modal' } 
                    }));
                    
                    // 페이지 새로고침하여 업데이트된 정보 표시
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || '역할 저장 중 오류가 발생했습니다.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('역할 저장 중 오류가 발생했습니다: ' + error.message, 'error');
            });
        });
        
        // 모달이 열릴 때 검색어 초기화
        document.addEventListener('open-modal', function(event) {
            if (event.detail.modalId === 'menu-category-selector-modal') {
                menuCategorySearch.value = '';
                filteredMenuCategories = [...allMenuCategories];
                renderMenuCategoryList();
            }
        });
        
    });
    
    // 전역 알림 표시 함수
    function showNotification(message, type = 'info') {
        // 간단한 알림 구현 (실제 프로젝트에서는 더 정교한 알림 시스템 사용)
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // 전역 함수: 카테고리 역할 관리
    function manageCategoryRoles(categoryId, categoryName) {
        console.log('manageCategoryRoles 호출됨:', categoryId, categoryName);
        currentCategoryId = categoryId;
        console.log('currentCategoryId 설정됨:', currentCategoryId);
        
        // 카테고리 이름 표시
        document.getElementById('category-name-display').textContent = categoryName;
        
        // 현재 카테고리의 역할 정보 가져오기
        fetch(`/admin/menu-categories/${categoryId}/roles`)
            .then(response => {
                console.log('역할 정보 요청 응답:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('역할 정보 데이터:', data);
                // 모든 체크박스 해제
                document.querySelectorAll('.role-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                // 현재 카테고리에 허용된 역할 체크
                if (data.roles) {
                    data.roles.forEach(roleId => {
                        const checkbox = document.querySelector(`.role-checkbox[value="${roleId}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                }
                
                // 역할 관리 모달 열기
                window.dispatchEvent(new CustomEvent('open-modal', { 
                    detail: { modalId: 'role-management-modal', action: 'open' } 
                }));
            })
            .catch(error => {
                console.error('역할 정보 가져오기 오류:', error);
                // 오류 발생 시에도 모달은 열기
                window.dispatchEvent(new CustomEvent('open-modal', { 
                    detail: { modalId: 'role-management-modal', action: 'open' } 
                }));
            });
    }
    </script>

    {{-- 역할 상세 모달 --}}
    <livewire:admin.roles.role-detail-modal />

</x-layouts.admin>