<x-laravel-admin::admin.layouts.admin title="권한 관리">

    <x-slot name="header">

        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">{{ __('Admin Home') }}</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Permission List') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>

    </x-slot>


    <div class="w-full bg-white border border-[#d8d8d0] px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[560px] border border-[#d9d9d9] bg-white px-6 py-7 sm:px-10 sm:py-10 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-2">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Permissions') }}</h1>

                <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-base font-semibold">
                    @can('viewAny', Ssh521\LaravelAdmin\Models\Menu::class)
                    <a href="{{ route('admin.permissions.index') }}">{{ __('목록보기') }}</a>
                    <span class="text-[#222222] dark:text-gray-400">|</span>
                    @endcan

                    @can('create', arguments: Ssh521\LaravelAdmin\Models\Menu::class)
                    <a href="{{ route('admin.permissions.create') }}">{{ __('등록하기') }}</a>
                    @endcan
                </div>

                @if(request('search'))
                    <p class="mt-3 text-[13px] font-semibold text-[#555555] dark:text-gray-400">"{{ request('search') }}" 검색 결과</p>
                @endif
            </div>

            <x-laravel-admin::admin.session-messages />

            <form class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end" action="{{ route('admin.permissions.index') }}" method="GET">
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if(request('direction'))
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                    @endif
                    <label for="permission-search" class="sr-only">권한 검색</label>
                    <div class="relative w-full sm:w-[260px]">
                        <input id="permission-search" type="text" name="search" value="{{ request('search') }}"
                            class="admin-focus-border h-[28px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 pr-8 text-base text-[#111111] outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';"
                            onblur="this.style.borderColor='#7d7d7d'; this.style.boxShadow='none';"
                            placeholder="권한 이름 또는 설명 검색">
                        @if(request('search'))
                            <a href="{{ route('admin.permissions.index') }}"
                               class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="h-[28px] cursor-pointer rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-4 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                        {{ __('검색') }}
                    </button>
                </form>

            <div
                class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800"
                x-data="permissionSortable()"
                x-init="initSortable()">

                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2 text-[13px] font-semibold">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span class="text-[#555555] dark:text-gray-300">{{ __('정렬 방식') }} :</span>
                                <button @click="setSortMode('drag')"
                                        :class="sortMode === 'drag' ? 'underline' : ''"
                                        class="cursor-pointer font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                                    {{ __('Drag Sort') }}
                                </button>
                                <span class="text-[#222222] dark:text-gray-400">|</span>
                                <button @click="setSortMode('click')"
                                        :class="sortMode === 'click' ? 'underline' : ''"
                                        class="cursor-pointer font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                                    {{ __('Click Sort') }}
                                </button>
                            </div>
                            <div x-show="sortMode === 'drag'" class="text-[12px] text-gray-600 dark:text-gray-400">
                                {{ __('Drag rows to reorder') }}
                            </div>
                        </div>

            <table class="min-w-full border-collapse text-base text-[#111111] dark:text-gray-100">
                <thead
                    class="border-y border-[#cfcfcf] bg-[#dedede] text-[#555555] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                    <tr>
                        <th scope="col" class="h-10 px-2 text-left font-bold w-8 whitespace-nowrap">
                            <span x-show="sortMode === 'drag'" class="text-gray-400">⋮⋮</span>
                        </th>
                        <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">
                                        <button x-show="sortMode === 'click'"
                                                @click="sortBy('name')"
                                                class="flex items-center space-x-1 text-[#555555] hover:text-[#111111] dark:text-gray-200 dark:hover:text-white">
                                            <span>{{ __('Permission Name') }}</span>
                                            <svg x-show="sortField === 'name' && sortDirection === 'asc'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <svg x-show="sortField === 'name' && sortDirection === 'desc'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <svg x-show="sortField !== 'name'" class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                            <span x-show="sortMode === 'drag'" class="text-gray-700 dark:text-gray-300">{{ __('Permission Name') }}</span>
                        </th>
                        <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">
                                        <button x-show="sortMode === 'click'"
                                                @click="sortBy('description')"
                                                class="flex items-center space-x-1 text-[#555555] hover:text-[#111111] dark:text-gray-200 dark:hover:text-white">
                                            <span>{{ __('Permission Description') }}</span>
                                            <svg x-show="sortField === 'description' && sortDirection === 'asc'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <svg x-show="sortField === 'description' && sortDirection === 'desc'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <svg x-show="sortField !== 'description'" class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                            <span x-show="sortMode === 'drag'" class="text-gray-700 dark:text-gray-300">{{ __('Permission Description') }}</span>
                        </th>
                        <th scope="col" class="h-10 px-2 text-right font-bold whitespace-nowrap">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody id="sortable-permissions">
                    @forelse ($data as $permission)
                    <tr data-id="{{ $permission->id }}"
                        class="border-b border-[#e6e6e6] bg-[#fbfbfb] transition-colors hover:bg-white dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 cursor-move"
                        :class="sortMode === 'drag' ? 'cursor-move' : 'cursor-default'">
                                    <td class="h-10 px-2 w-8">
                                        <span x-show="sortMode === 'drag'" class="text-gray-400 cursor-move">⋮⋮</span>
                                    </td>
                                    <th scope="row"
                                        class="h-10 px-4 text-left font-bold whitespace-nowrap">
                                        {{ $permission->name }}
                                    </th>
                                    <td class="h-10 px-4 text-left">
                                        {{ $permission->description ?: '설명 없음' }}
                                    </td>
                                    <td class="h-10 px-4 text-right">

                                        @can('update', $permission)
                                        <a class="font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]"
                                            href="{{ route('admin.permissions.edit', $permission) }}">{{ __('Edit') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="h-10 px-4 text-center text-gray-500 dark:text-gray-400">
                                @if(request('search'))
                                    {{ __('No search results found for') }} "{{ request('search') }}".
                                @else
                                    {{ __('No permissions registered.') }}
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($data->hasPages())
        <div class="mt-6 text-sm">
            {!! $data->withQueryString()->links() !!}
        </div>
        @endif
    </div>

    <script>
        function permissionSortable() {
            return {
                sortMode: 'click', // 'click' or 'drag'
                sortField: '{{ request('sort', 'name') }}',
                sortDirection: '{{ request('direction', 'asc') }}',
                sortableInstance: null,
                permissions: @json($data->items()),

                initSortable() {
                    this.$nextTick(() => {
                        this.initSortableInstance();
                    });
                },

                initSortableInstance() {
                    if (this.sortMode === 'drag') {
                        const tbody = document.getElementById('sortable-permissions');
                        if (tbody && !this.sortableInstance) {
                            this.sortableInstance = new Sortable(tbody, {
                                animation: 150,
                                ghostClass: 'sortable-ghost',
                                chosenClass: 'sortable-chosen',
                                dragClass: 'sortable-drag',
                                handle: '.cursor-move',
                                onEnd: (evt) => {
                                    this.updateOrder(evt);
                                }
                            });
                        }
                    } else {
                        if (this.sortableInstance) {
                            this.sortableInstance.destroy();
                            this.sortableInstance = null;
                        }
                    }
                },

                setSortMode(mode) {
                    this.sortMode = mode;
                    this.initSortableInstance();
                },

                sortBy(field) {
                    if (this.sortField === field) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortField = field;
                        this.sortDirection = 'asc';
                    }
                    this.performSort();
                },

                performSort() {
                    const url = new URL(window.location);
                    url.searchParams.set('sort', this.sortField);
                    url.searchParams.set('direction', this.sortDirection);
                    window.location.href = url.toString();
                },

                updateOrder(evt) {
                    const pageOffset = ({{ $data->currentPage() }} - 1) * {{ $data->perPage() }};
                    const newOrder = Array.from(evt.to.children).map((row, index) => ({
                        id: row.dataset.id,
                        order: pageOffset + index + 1
                    }));

                    // 서버에 새로운 순서 저장
                    fetch('{{ route("admin.permissions.update-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ order: newOrder })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // 성공 메시지 표시
                            this.showNotification('{{ __("Order updated successfully") }}', 'success');
                        } else {
                            // 오류 메시지 표시
                            this.showNotification('{{ __("Failed to update order") }}', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showNotification('{{ __("An error occurred") }}', 'error');
                    });
                },

                showNotification(message, type) {
                    // 간단한 알림 표시 (실제 프로젝트에서는 더 정교한 알림 시스템 사용)
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 p-4 rounded-md text-white z-50 ${
                        type === 'success' ? 'bg-green-500' : 'bg-red-500'
                    }`;
                    notification.textContent = message;
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                }
            }
        }
    </script>

    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #f3f4f6;
        }
        .sortable-chosen {
            background: #e5e7eb;
        }
        .sortable-drag {
            background: #d1d5db;
        }
    </style>

</x-laravel-admin::admin.layouts.admin>
