<x-laravel-admin::admin.layouts.admin title="메뉴 카테고리 관리">

    <x-slot name="header">

        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Menu Categories') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>

    </x-slot>

    <!-- 드래그 앤 드롭 순서 변경을 위한 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.querySelector('#categories-tbody');
            if (tbody) {
                new Sortable(tbody, {
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
        });

        function updateCategoryOrder(newOrder) {
            console.log('순서 변경 요청:', newOrder);

            fetch('{{ route("admin.menu-categories.update-order-multiple", [], false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ categories: newOrder })
            })
            .then(response => {
                console.log('서버 응답 상태:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        try {
                            const data = JSON.parse(text);
                            if (data && data.message) {
                                throw new Error(data.message);
                            }
                        } catch (e) {
                            // ignore JSON parse errors
                        }

                        throw new Error(text || `HTTP error! status: ${response.status}`);
                    });
                }

                // Laravel이 항상 JSON을 주지 않는 경우(빈 응답/파싱 실패)에도
                // 서버 처리는 성공(HTTP 2xx)으로 보고 후속 UI 갱신을 진행한다.
                return response.text().then(text => {
                    if (!text) {
                        return { success: true };
                    }

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.warn('응답 JSON 파싱 실패. success로 간주합니다.', text);
                        return { success: true };
                    }
                });
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
                } else {
                    console.error('서버 에러:', data.message);
                    showNotification(data.message || '순서 변경 중 오류가 발생했습니다.', 'error');
                }
            })
            .catch(error => {
                console.error('네트워크 에러:', error);
                showNotification(error?.message || '순서 변경 중 네트워크 오류가 발생했습니다.', 'error');
            });
        }

        function refreshLeftMenu() {
            console.log('refreshLeftMenu 함수 실행됨');

            // 성공 메시지를 먼저 표시
            //showNotification('왼쪽 메뉴를 업데이트하는 중...', 'info');

            // Livewire left-menu 갱신 이벤트만 dispatch (페이지 새로고침 금지)
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
            window.dispatchEvent(new CustomEvent('notification:show', {
                detail: {
                    message: message,
                    type: type ?? 'info',
                },
            }));
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

    <div class="w-full bg-white border border-[#d8d8d0] px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[560px] border border-[#d9d9d9] bg-white px-6 py-7 sm:px-10 sm:py-10 dark:bg-gray-800 dark:border-gray-700">
                    <div class="mb-2">
                        <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">
                            {{ __('Menu Categories List') }}
                            @if(request('search'))
                            <span class="text-[16px] font-normal text-gray-600 dark:text-gray-400">
                                - "{{ request('search') }}" 검색 결과
                            </span>
                            @endif
                        </h1>

                        <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-base font-semibold">
                            @can('viewAny', Ssh521\LaravelAdmin\Models\MenuCategory::class)
                            <a href="{{ route('admin.menu-categories.index') }}">{{ __('목록보기') }}</a>
                            <span class="text-[#222222] dark:text-gray-400">|</span>
                            @endcan

                            @can('create', Ssh521\LaravelAdmin\Models\MenuCategory::class)
                            <a href="{{ route('admin.menu-categories.create') }}">{{ __('등록하기') }}</a>
                            @endcan
                        </div>
                    </div>

                    <x-laravel-admin::admin.session-messages />

                    <form class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end" action="{{ route('admin.menu-categories.index') }}" method="GET">
                        <label for="category-search" class="sr-only">Search</label>

                        <input
                            id="category-search"
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="admin-focus-border h-[28px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 text-base text-[#111111] outline-none sm:w-[260px] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="{{ __('카테고리 검색') }}"
                            onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';"
                            onblur="this.style.borderColor='#7d7d7d'; this.style.boxShadow='none';"
                        >

                        @if(request('search'))
                        <a href="{{ route('admin.menu-categories.index') }}" class="inline-flex h-[28px] items-center rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-3 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                            {{ __('초기화') }}
                        </a>
                        @endif

                        <button type="submit" class="h-[28px] cursor-pointer rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-4 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                            {{ __('검색') }}
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-base text-[#111111] dark:text-gray-100">
                            <thead>
                                <tr class="border-y border-[#cfcfcf] bg-[#dedede] text-[#555555] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                    <th scope="col" class="h-10 w-8 px-2 text-left font-bold">
                                        <span class="sr-only">순서</span>
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-left font-bold">
                                        ID
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-left font-bold">
                                        카테고리명
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-left font-bold">
                                        메뉴 개수
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-left font-bold">
                                        정렬 순서
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-left font-bold">
                                        상태
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-left font-bold">
                                        허용 역할
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-left font-bold">
                                        생성일
                                    </th>
                                    <th scope="col" class="h-10 px-2 text-right font-bold">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="categories-tbody">
                                @forelse($categories as $category)
                                <tr data-category-id="{{ $category->id }}"
                                    class="border-b border-[#e6e6e6] bg-[#fbfbfb] transition-colors hover:bg-white dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">

                                    <td class="h-10 px-4">
                                        <div class="drag-handle flex items-center justify-center">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 8h16M4 16h16"></path>
                                            </svg>
                                        </div>
                                    </td>

                                    <td class="h-10 whitespace-nowrap px-4">
                                        {{ $category->id }}
                                    </td>
                                    <th scope="row"
                                        class="h-10 whitespace-nowrap px-4 text-left font-bold">

                                        @can('view', $category)
                                        <a href="{{ route('admin.menu-categories.show', $category) }}">{{ $category->name
                                            }}</a>
                                        @else
                                        {{ $category->name }}
                                        @endcan

                                    </th>
                                    <td class="h-10 whitespace-nowrap px-4">
                                        {{ $category->menus_count }}
                                    </td>
                                    <td class="h-10 whitespace-nowrap px-4 sort-order-cell">
                                        {{ $category->sort_order }}
                                    </td>
                                    <td class="h-10 whitespace-nowrap px-4">
                                        <span class="{{ $category->is_active ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                                            {{ $category->is_active ? __('활성') : __('비활성') }}
                                        </span>
                                    </td>
                                    <td class="h-10 px-4" data-roles-cell="{{ $category->id }}">
                                        @if($category->roles->count() > 0)
                                        <div class="flex flex-wrap gap-x-2 gap-y-1">
                                            @foreach($category->roles as $role)
                                            <span>{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                        @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('없음') }}</span>
                                        @endif
                                    </td>
                                    <td class="h-10 whitespace-nowrap px-4">
                                        {{ $category->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="h-10 whitespace-nowrap px-4 text-right">

                                        @can('update', $category)
                                        <x-laravel-admin::admin.modal-trigger
                                            text="권한"
                                            modal-id="roles-modal"
                                            type="link"
                                            variant="primary"
                                            modal-type="single"
                                            class="cursor-pointer"
                                            data-category-id="{{ $category->id }}"
                                            data-category-name="{{ $category->name }}" />
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] dark:border-gray-700 dark:bg-gray-800">
                                    <td colspan="9" class="h-10 px-4 text-center text-gray-500 dark:text-gray-400">
                                        @if(request('search'))
                                        {{ __('":keyword"에 대한 검색 결과가 없습니다.', ['keyword' => request('search')]) }}
                                        @else
                                        {{ __('등록된 카테고리가 없습니다.') }}
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 text-sm">
                        {!! $categories->appends(request()->query())->links() !!}
                    </div>
        </div>
    </div>

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
                        @click="$dispatch('close-modal', { modalId: 'roles-modal', action: 'close' })"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        {{ __('취소') }}
                    </button>
                    <button id="roles-submit-btn" type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('저장') }}
                    </button>
                </div>
            </form>
        </div>
    </x-laravel-admin::admin.draggable-modal>

    <script>
        let currentCategoryId = null;

        // draggable-modal 이벤트 리스너 추가
        document.addEventListener('open-modal', function(event) {
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

        const getRolesModalUrlBase = '{{ route('admin.menu-categories.get-roles-modal', ['menuCategory' => '__ID__']) }}';

        function loadCategoryRoles(categoryId) {
            fetch(getRolesModalUrlBase.replace('__ID__', categoryId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(response.status);
                const ct = response.headers.get('content-type');
                if (!ct || !ct.includes('application/json')) throw new Error('Unexpected response type');
                return response.json();
            })
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
            const submitBtn = document.getElementById('roles-submit-btn');
            container.innerHTML = '';

            if (availableRoles.length === 0) {
                container.innerHTML = '<p class="text-gray-500 dark:text-gray-400">{{ __('사용 가능한 역할이 없습니다.') }}</p>';
                submitBtn.disabled = true;
                return;
            }
            submitBtn.disabled = false;

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

        const updateRolesUrlBase = '{{ route('admin.menu-categories.update-roles-api', ['menuCategory' => '__ID__']) }}';

            // API로 권한 저장
            fetch(updateRolesUrlBase.replace('__ID__', currentCategoryId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ roles: selectedRoles })
            })
            .then(response => {
                if (!response.ok) throw new Error(response.status);
                const ct = response.headers.get('content-type');
                if (!ct || !ct.includes('application/json')) throw new Error('Unexpected response type');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification(data.message || '권한이 성공적으로 저장되었습니다.', 'success');
                    // 테이블 역할 셀 동적 업데이트
                    const rolesCell = document.querySelector(`[data-roles-cell="${currentCategoryId}"]`);
                    if (rolesCell && data.roles !== undefined) {
                        if (data.roles.length > 0) {
                            rolesCell.innerHTML = `<div class="flex flex-wrap gap-x-2 gap-y-1">${data.roles.map(r => `<span>${r.name}</span>`).join('')}</div>`;
                        } else {
                            rolesCell.innerHTML = '<span class="text-gray-500 dark:text-gray-400">없음</span>';
                        }
                    }
                    // 모달 닫기
                    window.dispatchEvent(new CustomEvent('draggable-modal-close', {
                        detail: { modalId: 'roles-modal' }
                    }));
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
</x-laravel-admin::admin.layouts.admin>
