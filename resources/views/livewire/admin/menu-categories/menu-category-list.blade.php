<div class="py-0">
    <div class="w-full mx-auto sm:px-0 lg:px-0">
        <div class="bg-white dark:bg-gray-800 overflow-hidden">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="text-2xl text-left mt-1 font-bold dark:text-white">
                    {{ __('Menu Categories List') }}
                    @if($search)
                    <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                        - "{{ $search }}" 검색 결과
                    </span>
                    @endif
                </div>

                <x-laravel-admin::admin.session-messages />

                <div class="flex justify-between items-center pb-4">
                    <div>
                        @if($canCreate)
                        <a href="{{ route('menu-categories.create') }}"
                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">등록하기</a>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- 순서 변경 안내 -->
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                </svg>
                                드래그하여 순서 변경
                            </span>
                        </div>

                        <div class="flex items-center ml-1">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="w-full relative">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="search"
                                    class="bg-gray-50 border w-40 z-0 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block max-w-xs pl-2 pr-8 p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="카테고리 검색">

                                @if($search)
                                <button
                                    wire:click="$set('search', '')"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-x-auto my-2">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead
                            class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-200 dark:bg-gray-700 border-b border-t border-gray-300 dark:border-gray-600">
                            <tr>
                                <th scope="col" class="px-2 py-3 w-8">
                                    <span class="sr-only">순서</span>
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    ID
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    카테고리명
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    메뉴 개수
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    정렬 순서
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    상태
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    허용 역할
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    생성일
                                </th>
                                <th scope="col" class="px-2 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="categories-tbody">
                            @forelse($categories as $category)
                            <tr data-category-id="{{ $category->id }}"
                                class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">

                                <td class="px-2 py-2">
                                    <div class="drag-handle flex items-center justify-center">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                </td>

                                <td class="px-2 py-2">
                                    {{ $category->id }}
                                </td>
                                <th scope="row"
                                    class="px-2 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    @if($canView($category))
                                    <a class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        href="{{ route('menu-categories.show', $category) }}">{{ $category->name }}</a>
                                    @else
                                    {{ $category->name }}
                                    @endif
                                </th>
                                <td class="px-2 py-2">
                                    {{ $category->menus_count }}
                                </td>
                                <td class="px-2 py-2 sort-order-cell">
                                    {{ $category->sort_order }}
                                </td>
                                <td class="px-2 py-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                        {{ $category->is_active ? '활성' : '비활성' }}
                                    </span>
                                </td>
                                <td class="px-2 py-2">
                                    @if($category->roles->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($category->roles as $role)
                                        <span
                                            class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                            {{ $role->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @else
                                    <span class="text-gray-400 dark:text-gray-500 text-xs">없음</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    {{ $category->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-2 py-2 text-right">
                                    @if($canUpdate($category))
                                    <x-laravel-admin::admin.modal-trigger
                                        text="권한"
                                        modal-id="roles-modal"
                                        type="link"
                                        variant="primary"
                                        modal-type="single"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer"
                                        data-category-id="{{ $category->id }}"
                                        data-category-name="{{ $category->name }}" />
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <td colspan="9" class="px-2 py-4 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                    "{{ $search }}"에 대한 검색 결과가 없습니다.
                                    @else
                                    등록된 카테고리가 없습니다.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 드래그 앤 드롭 순서 변경을 위한 JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initSortable();
    });

    // Livewire 컴포넌트가 업데이트된 후 다시 초기화
    document.addEventListener('livewire:init', function() {
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (el.querySelector('#categories-tbody')) {
                initSortable();
            }
        });
    });

    function initSortable() {
        const tbody = document.querySelector('#categories-tbody');
        if (tbody && !tbody.sortableInstance) {
            tbody.sortableInstance = new Sortable(tbody, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    const rows = Array.from(tbody.querySelectorAll('tr[data-category-id]'));
                    const newOrder = rows.map((row, index) => ({
                        id: row.dataset.categoryId,
                        sort_order: index + 1
                    }));

                    // 순서 변경 API 호출
                    updateCategoryOrder(newOrder);
                }
            });
        }
    }

    function updateCategoryOrder(newOrder) {
        console.log('순서 변경 요청:', newOrder);

        fetch('{{ route("menu-categories.update-order-multiple") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ categories: newOrder })
        })
        .then(response => {
            console.log('서버 응답 상태:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('서버 응답 데이터:', data);
            if (data.success) {
                // 성공 메시지 표시
                showNotification('카테고리 순서가 성공적으로 변경되었습니다.', 'success');
                // 페이지 새로고침 없이 순서 번호 업데이트
                updateSortOrderNumbers();

                // Livewire left-menu 컴포넌트 refresh
                refreshLeftMenu();

                // Livewire 이벤트 dispatch
                if (window.Livewire) {
                    window.Livewire.dispatch('admin-menu-categories:menu-categories:reordered');
                }
            } else {
                console.error('서버 에러:', data.message);
                showNotification(data.message || '순서 변경 중 오류가 발생했습니다.', 'error');
            }
        })
        .catch(error => {
            console.error('네트워크 에러:', error);
            showNotification('순서 변경 중 네트워크 오류가 발생했습니다.', 'error');
        });
    }

    function refreshLeftMenu() {
        console.log('refreshLeftMenu 함수 실행됨');

        // Livewire 이벤트 시도
        if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
            try {
                console.log('Livewire 이벤트 dispatch 시도');
                window.Livewire.dispatch('admin-menu-categories:menu-categories:reordered');
            } catch (error) {
                console.error('Livewire 이벤트 dispatch 실패:', error);
            }
        }
    }

    function updateSortOrderNumbers() {
        const rows = document.querySelectorAll('#categories-tbody tr[data-category-id]');
        rows.forEach((row, index) => {
            const sortOrderCell = row.querySelector('.sort-order-cell');
            if (sortOrderCell) {
                sortOrderCell.textContent = index + 1;
            }
        });
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        let bgColor = 'bg-red-500 text-white';

        if (type === 'success') {
            bgColor = 'bg-green-500 text-white top-4';
        } else if (type === 'info') {
            bgColor = 'bg-blue-500 text-white top-20';
        }

        notification.className = `fixed right-4 p-4 rounded-lg shadow-lg z-50 ${bgColor}`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>

<!-- 드래그 앤 드롭 스타일 -->
<style>
    .drag-handle {
        cursor: grab;
        color: #6b7280;
        transition: color 0.2s;
    }

    .drag-handle:hover {
        color: #374151;
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    .sortable-ghost {
        opacity: 0.5;
        background-color: #f3f4f6 !important;
    }

    .sortable-chosen {
        background-color: #dbeafe !important;
    }

    .sortable-drag {
        background-color: #ffffff !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .drag-handle svg {
        width: 16px;
        height: 16px;
    }
</style>

<!-- 권한 관리 모달 -->
<x-laravel-admin::admin.draggable-modal id="roles-modal" title="권한 관리" :width="600" :height="500">
    <div class="p-6">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-category-name">
                카테고리 권한 관리
            </h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                메뉴 카테고리에 허용할 역할을 선택합니다.
            </p>
        </div>

        <form id="roles-form" class="space-y-4">
            @csrf
            <div id="roles-container" class="space-y-3">
                <!-- 역할 목록이 여기에 동적으로 로드됩니다 -->
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button"
                    @click="$dispatch('draggable-modal-close', { modalId: 'roles-modal', action: 'close' })"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    취소
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    저장
                </button>
            </div>
        </form>
    </div>
</x-laravel-admin::admin.draggable-modal>

<script>
    let currentCategoryId = null;

    // draggable-modal 이벤트 리스너 추가
    document.addEventListener('draggable-modal', function(event) {
        if (event.detail.modalId === 'roles-modal') {
            // 클릭된 요소에서 카테고리 정보 가져오기
            const trigger = event.target.closest('[data-category-id]');
            if (trigger) {
                const categoryId = trigger.getAttribute('data-category-id');
                const categoryName = trigger.getAttribute('data-category-name');

                currentCategoryId = categoryId;
                document.getElementById('modal-category-name').textContent = `[${categoryName}] 권한 관리`;

                // 권한 데이터 로드
                loadCategoryRoles(categoryId);
            }
        }
    });

    function loadCategoryRoles(categoryId) {
        fetch(`/admin/menu-categories/${categoryId}/roles/modal`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRoles(data.availableRoles, data.selectedRoles);
            } else {
                showNotification('권한 데이터를 불러오는 중 오류가 발생했습니다.', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading roles:', error);
            showNotification('권한 데이터를 불러오는 중 네트워크 오류가 발생했습니다.', 'error');
        });
    }

    function renderRoles(availableRoles, selectedRoles) {
        const container = document.getElementById('roles-container');
        container.innerHTML = '';

        if (availableRoles.length === 0) {
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400">사용 가능한 역할이 없습니다.</p>';
            return;
        }

        availableRoles.forEach(role => {
            const isSelected = selectedRoles.includes(role.id);
            const roleElement = document.createElement('div');
            roleElement.className = 'flex items-center';
            roleElement.innerHTML = `
                <input type="checkbox"
                       id="role-${role.id}"
                       name="roles[]"
                       value="${role.id}"
                       ${isSelected ? 'checked' : ''}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                <label for="role-${role.id}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    ${role.name}
                </label>
            `;
            container.appendChild(roleElement);
        });
    }

    // 폼 제출 처리
    document.getElementById('roles-form').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!currentCategoryId) {
            showNotification('카테고리 정보가 없습니다.', 'error');
            return;
        }

        const formData = new FormData(this);
        const selectedRoles = Array.from(this.querySelectorAll('input[name="roles[]"]:checked'))
            .map(input => input.value);

        // API로 권한 저장
        fetch(`/admin/menu-categories/${currentCategoryId}/roles`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ roles: selectedRoles })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || '권한이 성공적으로 저장되었습니다.', 'success');
                // 모달 닫기
                window.dispatchEvent(new CustomEvent('draggable-modal-close', {
                    detail: { modalId: 'roles-modal' }
                }));
                // Livewire 컴포넌트 새로고침
                if (window.Livewire) {
                    window.Livewire.dispatch('admin-menu-categories:menu-categories:reordered');
                }
            } else {
                showNotification(data.message || '권한 저장 중 오류가 발생했습니다.', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving roles:', error);
            showNotification('권한 저장 중 네트워크 오류가 발생했습니다.', 'error');
        });
    });
</script>

