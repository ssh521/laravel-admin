<x-laravel-admin::layouts.admin title="권한 관리">

    <x-slot name="header">

        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">{{ __('Admin Home') }}</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Permission List') }}
            </x-slot>
        </x-laravel-admin::admin-header>

    </x-slot>


    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="text-2xl text-left mt-1 font-bold dark:text-white">
                {{ __('Permission List') }}
                @if(request('search'))
                    <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                        - "{{ request('search') }}" {{ __('search results') }}
                    </span>
                @endif
            </div>

            <x-laravel-admin::session-messages />

            <div class="flex justify-between items-center pb-4">

                <div>
                    @can('viewAny', Ssh521\LaravelAdmin\Models\Menu::class)
                    <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ __('View List') }}</a>
                    <span class="mx-1">|</span>
                    @endcan

                    @can('create', arguments: Ssh521\LaravelAdmin\Models\Menu::class)
                    <a href="{{ route('admin.permissions.create') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ __('Create New') }}</a>
                    @endcan
                </div>

                <div>
                    <form class="flex items-center ml-1 space-x-2" action="{{ route('admin.permissions.index') }}" method="GET">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="w-full relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 dark:focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 block w-40 pl-2 pr-8 p-1.5"
                                placeholder="{{ __('Search permissions') }}">
                            @if(request('search'))
                                <a href="{{ route('admin.permissions.index') }}"
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

            <div
                class="relative overflow-x-auto my-2 border-gray-200 dark:border-gray-700 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800"
                x-data="permissionSortable()"
                x-init="initSortable()">

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <button @click="toggleSortMode()"
                                        :class="sortMode === 'drag' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded-md text-sm font-medium transition-colors">
                                    {{ __('Drag Sort') }}
                                </button>
                                <button @click="toggleSortMode()"
                                        :class="sortMode === 'click' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="px-3 py-1 rounded-md text-sm font-medium transition-colors">
                                    {{ __('Click Sort') }}
                                </button>
                            </div>
                            <div x-show="sortMode === 'drag'" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Drag rows to reorder') }}
                            </div>
                        </div>

            <table class="w-full min-w-[300px] text-sm text-left rtl:text-right">
                <thead
                    class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-200 dark:bg-gray-700 border-b border-t border-gray-300 dark:border-gray-600">
                    <tr>
                        <th scope="col" class="px-2 py-3 w-8 whitespace-nowrap">
                            <span x-show="sortMode === 'drag'" class="text-gray-400">⋮⋮</span>
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                                        <button x-show="sortMode === 'click'"
                                                @click="sortBy('name')"
                                                class="flex items-center space-x-1 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
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
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                                        <button x-show="sortMode === 'click'"
                                                @click="sortBy('description')"
                                                class="flex items-center space-x-1 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
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
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody id="sortable-permissions">
                    @forelse ($data as $permission)
                    <tr data-id="{{ $permission->id }}"
                        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-move"
                        :class="sortMode === 'drag' ? 'cursor-move' : 'cursor-default'">
                                    <td class="px-2 py-2 w-8">
                                        <span x-show="sortMode === 'drag'" class="text-gray-400 cursor-move">⋮⋮</span>
                                    </td>
                                    <th scope="row"
                                        class="px-2 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $permission->name }}
                                    </th>
                                    <td class="px-2 py-2 text-right">
                                        {{ $permission->description }}
                                    </td>
                                    <td class="px-2 py-2 text-right">

                                        @can('update', $permission)
                                        <a class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            href="{{ route('admin.permissions.edit', $permission) }}">{{ __('Edit') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 py-4 text-center">
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

        <div class="my-3">
            {!! $data->appends(request()->query())->links() !!}
        </div>
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

                toggleSortMode() {
                    this.sortMode = this.sortMode === 'click' ? 'drag' : 'click';
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
                    const newOrder = Array.from(evt.to.children).map((row, index) => ({
                        id: row.dataset.id,
                        order: index + 1
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

</x-laravel-admin::layouts.admin>