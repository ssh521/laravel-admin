<div class="w-full lg:max-w-7xl mx-auto">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">

        <div class="text-2xl text-left mt-1 font-bold dark:text-white">
            {{ __('회원 리스트') }}
            @if($search || $role)
            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                @if($search && $role)
                - "{{ $search }}" 검색 결과 ({{ $role }} Role)
                @elseif($search)
                - "{{ $search }}" 검색 결과
                @elseif($role)
                - {{ $role }} Role 필터
                @endif
            </span>
            @endif
        </div>

        <div class="flex justify-between items-center pb-4">
            <div>
                @can('viewAny', Ssh521\LaravelAdmin\Models\User::class)
                <a href="{{ route('admin.users.index') }}"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">목록보기</a>
                <span class="mx-1">|</span>
                @endcan

                @can('create', Ssh521\LaravelAdmin\Models\User::class)
                <a href="{{ route('admin.users.create') }}"">등록하기</a>
                    @endcan
                </div>

                <div>
                    <form class=" flex items-center ml-1 space-x-2" x-data
                    x-init="$nextTick(() => $refs.searchInput?.focus())">
                    <!-- Role 필터 -->
                    <div class="relative">
                        <select wire:model.live="role"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 dark:focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 block w-32 pl-2 pr-8 p-1.5">
                            <option value="">전체 Role</option>
                            @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->name }}" {{ $role==$roleOption->name ? 'selected' : ''
                                }}>
                                {{ $roleOption->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 검색 입력 -->
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="w-full relative">
                        <x-laravel-admin::admin.input type="text" wire:model.live.debounce.300ms="search" x-ref="searchInput"
                            class="w-40 pl-2 pr-8" placeholder="사용자 검색" />
                        @if($search)
                        <button wire:click="$set('search', '')"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @endif
                    </div>

                    <button type="button"
                        class="p-1 text-sm font-medium text-white bg-gray-500 rounded-lg border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 30 30"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-2-2m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>

                    <!-- 필터 초기화 버튼 -->
                    @if($search || $role)
                    <button wire:click="$set('search', ''); $set('role', '')" type="button"
                        class="p-1 text-sm font-medium text-white bg-red-500 rounded-lg border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300"
                        title="필터 초기화">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Reset</span>
                    </button>
                    @endif
                    </form>
            </div>
        </div>

        <x-laravel-admin::session-messages />

        <div
            class="relative overflow-x-auto my-2 border-gray-200 dark:border-gray-700 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">

            <table class="w-full min-w-[300px] text-sm text-left rtl:text-right">
                <thead
                    class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-200 dark:bg-gray-700 border-b border-t border-gray-300 dark:border-gray-600">
                    <tr>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            Name
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            E-mail
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap hidden sm:table-cell">
                            이메일 인증
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            Role
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap hidden sm:table-cell">
                            Permission (Direct)
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap hidden sm:table-cell">
                            가입일
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $key => $user)
                    <tr
                        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <th scope="row" class="px-2 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            @can('view', $user)
                            <button
                                type="button"
                                wire:click="showUserDetailModal({{ $user->id }})"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-left">
                                {{ $user->name }}
                            </button>
                        @else
                            {{ $user->name }}
                            @endcan
                        </th>
                        <td class="px-2 py-2 whitespace-nowrap dark:text-white">
                            @can('view', $user)
                            <a href="{{ route('admin.users.show', $user->id) }}"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $user->email }}
                            </a>
                            @else
                            {{ $user->email }}
                            @endcan
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell">
                                @if($user->email_verified_at)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                        인증됨
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                        미인증
                                    </span>
                                @endif
                        </td>
                        <td class="px-2 py-2">
                            @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                            <span
                                class="px-2 py-1 mx-0.5 text-xs {{ $user->hasRole('Super Admin') ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }} rounded-full">
                                {{ $v }}
                            </span>
                            @endforeach
                            @endif
                        </td>
                        <td class="px-2 py-2 hidden sm:table-cell">
                            @if(!empty($user->getDirectPermissions()))
                            @foreach($user->getDirectPermissions() as $permission)
                            <span
                                class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full mr-1 mb-1 inline-block">
                                {{ $permission->name }}
                            </span>
                            @endforeach
                            @else
                            <span class="text-xs">직접 권한 없음</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell dark:text-white">
                            {{ $user->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-2 py-2 text-right whitespace-nowrap">
                            @can('update', $user)
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ __('Edit') }}
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-2 py-4 text-center">
                            @if($search && $role)
                            "{{ $search }}" 검색 결과 중 {{ $role }} Role 사용자가 없습니다.
                            @elseif($search)
                            "{{ $search }}"에 대한 검색 결과가 없습니다.
                            @elseif($role)
                            {{ $role }} Role 사용자가 없습니다.
                            @else
                            등록된 사용자가 없습니다.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="my-3">
            {!! $users->appends(request()->query())->links() !!}
        </div>

    </div>

    <livewire:admin.users.user-detail-modal />

</div>