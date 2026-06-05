<header x-data="{
        isVisible: true,
        lastScrollY: 0,
        scrollThreshold: 100,
        scrollTimeout: null,
        init() {
            this.lastScrollY = window.scrollY;

            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;

                // 스크롤 방향 감지
                if (currentScrollY > this.lastScrollY && currentScrollY > this.scrollThreshold) {
                    // 아래로 스크롤하고 임계값 이상 스크롤된 경우 헤더 숨김
                    this.isVisible = false;
                } else if (currentScrollY < this.lastScrollY) {
                    // 위로 스크롤하는 경우 헤더 표시
                    this.isVisible = true;
                }

                // 스크롤이 멈춘 후 일정 시간 후에 헤더 표시 (선택사항)
                clearTimeout(this.scrollTimeout);
                this.scrollTimeout = setTimeout(() => {
                    if (currentScrollY > this.scrollThreshold) {
                        this.isVisible = true;
                    }
                }, 2000);

                this.lastScrollY = currentScrollY;
            });
        }
    }" x-show="isVisible" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform -translate-y-full" x-transition:enter-end="transform translate-y-0"
    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="transform translate-y-0"
    x-transition:leave-end="transform -translate-y-full"
    class="fixed top-0 left-0 w-full z-50 bg-white dark:bg-[#000000] border-b border-gray-400 dark:border-gray-800 shadow-lg flex items-center h-16 px-4">

    <!-- 햄버거 버튼 -->
    <button class="block lg:hidden mr-0 p-2 rounded-full hover:bg-purple-100 dark:hover:bg-purple-800 transition"
        @click="isMobileMenuOpen = !isMobileMenuOpen;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-700 dark:text-purple-300" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- 햄버거 버튼 -->
    <button
        class="hidden lg:block mr-2 p-2 rounded-full hover:bg-purple-100 dark:hover:bg-purple-800 transition relative group"
        @click="isDesktop = !isDesktop; localStorage.setItem('adminSidebarOpen', isDesktop)" title="사이드바 토글 (Ctrl+B)">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-700 dark:text-purple-300" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>

        <!-- 단축키 안내 툴팁 -->
        <div
            class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-50">
            Ctrl+B
            <div
                class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-900">
            </div>
        </div>
    </button>

    <!-- 로고 -->
    <a href="{{ route('admin.index') }}" class="flex items-center gap-2" style="text-decoration: none;">
        <x-laravel-admin::admin.site-logo class="w-[22px] -mb-1 dark:text-purple-200" />
        <span class="text-md lg:text-xl font-bold text-purple-700 dark:text-purple-200 tracking-tight">관리자 메뉴</span>
    </a>

    <x-laravel-admin::admin.dark-mode-toggle class="ml-0" />

    <div class="flex-1">

        <div class="w-full flex" x-data="{
                searchOpen: false,
                searchQuery: '',
                searchResults: [],
                selectedIndex: -1,
                isLoading: false,
                searchTimeout: null,

                // 검색 함수
                async performSearch(query) {
                    console.log('검색 시작:', query);
                    if (query.length < 2) {
                        this.searchResults = [];
                        this.selectedIndex = -1;
                        return;
                    }

                    this.isLoading = true;
                    try {
                        // 검색어 전처리: 앞뒤 공백 제거
                        const cleanQuery = query.trim();
                        const url = `/admin/menus/search?q=${encodeURIComponent(cleanQuery)}`;
                        console.log('API 호출:', url);
                        const response = await fetch(url);
                        console.log('응답 상태:', response.status);
                        const data = await response.json();
                        console.log('검색 결과:', data);
                        this.searchResults = data;

                        // 검색 결과가 있으면 첫 번째 항목에 포커스
                        if (data.length > 0) {
                            this.selectedIndex = 0;
                        } else {
                            this.selectedIndex = -1;
                        }
                    } catch (error) {
                        console.error('검색 오류:', error);
                        this.searchResults = [];
                        this.selectedIndex = -1;
                    } finally {
                        this.isLoading = false;
                    }
                }
             }" x-init="
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        // 작은 화면에서는 드롭다운 토글, 큰 화면에서는 검색창에 포커스
                        if (window.innerWidth < 640) {
                            searchOpen = !searchOpen;
                        } else {
                            $refs.desktopSearch.focus();
                        }
                    }
                    if (e.key === 'Escape' && searchOpen) {
                        searchOpen = false;
                    }
                });
             ">

            <div class="flex w-full justify-center relative">
                <!-- 데스크탑: 검색창 -->
                <div class="w-1/2 hidden sm:block relative">
                    <x-laravel-admin::admin.input x-ref="desktopSearch" x-model="searchQuery" @input="
                            // 입력 시 앞뒤 공백 제거
                            searchQuery = searchQuery.trim();
                            clearTimeout(searchTimeout);
                            searchTimeout = setTimeout(() => performSearch(searchQuery), 300);
                        " @keydown="
                            if (event.key === 'ArrowDown') {
                                event.preventDefault();
                                if (searchResults.length > 0) {
                                    selectedIndex = Math.min(selectedIndex + 1, searchResults.length - 1);
                                }
                            } else if (event.key === 'ArrowUp') {
                                event.preventDefault();
                                if (searchResults.length > 0) {
                                    selectedIndex = Math.max(selectedIndex - 1, 0);
                                }
                            } else if (event.key === 'Enter' && selectedIndex >= 0 && searchResults[selectedIndex]) {
                                event.preventDefault();
                                const result = searchResults[selectedIndex];
                                if (result.target === '_blank') {
                                    window.open(result.url, '_blank');
                                } else {
                                    window.location.href = result.url;
                                }
                            } else if (event.key === 'Escape') {
                                searchResults = [];
                                selectedIndex = -1;
                            }
                        " @focus="if (searchQuery.length >= 2) performSearch(searchQuery)"
                        @blur="setTimeout(() => { searchResults = []; selectedIndex = -1; }, 200)" class="w-full"
                        placeholder="메뉴 검색 (Ctrl+K)" />

                    <!-- 검색 결과 드롭다운 -->
                    <div x-show="searchResults.length > 0" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">

                        <div x-show="isLoading" x-cloak class="p-4 text-center text-gray-500">
                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            검색 중...
                        </div>

                        <template x-for="(result, index) in searchResults" :key="result.id">
                            <a :href="result.url" :target="result.target"
                                @click="searchResults = []; selectedIndex = -1; searchQuery = ''" :class="{
                                   'bg-purple-50 dark:bg-purple-900': index === selectedIndex,
                                   'hover:bg-gray-50 dark:hover:bg-gray-700': index !== selectedIndex
                               }"
                                class="block p-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                                            <i x-show="result.icon" :class="result.icon"
                                                class="text-purple-600 dark:text-purple-300 text-sm"></i>
                                            <svg x-show="!result.icon"
                                                class="w-4 h-4 text-purple-600 dark:text-purple-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="result.name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="result.category">
                                        </div>
                                        <div x-show="result.description"
                                            :class="result.descriptionMatch ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-400 dark:text-gray-500'"
                                            class="text-xs mt-1" x-text="result.description"></div>
                                    </div>
                                </div>
                            </a>
                        </template>

                        <div x-show="searchResults.length === 0 && searchQuery.length >= 2 && !isLoading"
                            x-cloak class="p-4 text-center text-gray-500">
                            검색 결과가 없습니다.
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full sm:hidden flex justify-end">
                <!-- 모바일: 검색 아이콘 -->
                <button class="p-2 rounded hover:bg-purple-100 dark:hover:bg-purple-800 transition"
                    @click="searchOpen = !searchOpen">
                    <svg class="h-5 w-5 text-purple-700 dark:text-purple-300" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4-4m0 0A7 7 0 104 4a7 7 0 0013 13z" />
                    </svg>
                </button>
            </div>

            <!-- 모바일: 검색 입력창 드롭다운 -->

            <div x-cloak x-show="searchOpen" x-transition
                class="fixed inset-0 z-50 flex items-start justify-center bg-black bg-opacity-30 sm:hidden"
                @click.away="searchOpen = false">
                <div class="mt-20 w-11/12 max-w-md bg-white dark:bg-gray-900 rounded shadow-lg p-4 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <x-laravel-admin::admin.input x-model="searchQuery" @input="
                                // 입력 시 앞뒤 공백 제거
                                searchQuery = searchQuery.trim();
                                clearTimeout(searchTimeout);
                                searchTimeout = setTimeout(() => performSearch(searchQuery), 300);
                            " @keydown="
                                if (event.key === 'ArrowDown') {
                                    event.preventDefault();
                                    if (searchResults.length > 0) {
                                        selectedIndex = Math.min(selectedIndex + 1, searchResults.length - 1);
                                    }
                                } else if (event.key === 'ArrowUp') {
                                    event.preventDefault();
                                    if (searchResults.length > 0) {
                                        selectedIndex = Math.max(selectedIndex - 1, 0);
                                    }
                                } else if (event.key === 'Enter' && selectedIndex >= 0 && searchResults[selectedIndex]) {
                                    event.preventDefault();
                                    const result = searchResults[selectedIndex];
                                    if (result.target === '_blank') {
                                        window.open(result.url, '_blank');
                                    } else {
                                        window.location.href = result.url;
                                    }
                                    searchOpen = false;
                                } else if (event.key === 'Escape') {
                                    searchResults = [];
                                    selectedIndex = -1;
                                    searchOpen = false;
                                }
                            " @focus="if (searchQuery.length >= 2) performSearch(searchQuery)" class="flex-1"
                            placeholder="메뉴 검색" autofocus />
                        <button class="ml-2 p-2 rounded hover:bg-purple-100 dark:hover:bg-purple-800 transition"
                            @click="searchOpen = false">
                            <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- 모바일 검색 결과 -->
                    <div x-show="searchResults.length > 0" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-96 overflow-y-auto">

                        <div x-show="isLoading" x-cloak class="p-4 text-center text-gray-500">
                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            검색 중...
                        </div>

                        <template x-for="(result, index) in searchResults" :key="result.id">
                            <a :href="result.url" :target="result.target"
                                @click="searchResults = []; selectedIndex = -1; searchQuery = ''; searchOpen = false"
                                :class="{
                                   'bg-purple-50 dark:bg-purple-900': index === selectedIndex,
                                   'hover:bg-gray-50 dark:hover:bg-gray-700': index !== selectedIndex
                               }"
                                class="block p-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                                            <i x-show="result.icon" :class="result.icon"
                                                class="text-purple-600 dark:text-purple-300 text-sm"></i>
                                            <svg x-show="!result.icon"
                                                class="w-4 h-4 text-purple-600 dark:text-purple-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="result.name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="result.category">
                                        </div>
                                        <div x-show="result.description"
                                            :class="result.descriptionMatch ? 'text-purple-600 dark:text-purple-400 font-medium' : 'text-gray-400 dark:text-gray-500'"
                                            class="text-xs mt-1" x-text="result.description"></div>
                                    </div>
                                </div>
                            </a>
                        </template>

                        <div x-show="searchResults.length === 0 && searchQuery.length >= 2 && !isLoading"
                            x-cloak class="p-4 text-center text-gray-500">
                            검색 결과가 없습니다.
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- 우측: 드롭다운 메뉴 -->
    <div class="flex items-center gap-4">

        <!-- Teams Dropdown -->
        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <div class="ms-3 relative">
            <x-laravel-admin::admin.dropdown align="right" width="60">
                <x-slot name="trigger">
                    <span class="inline-flex rounded-md">
                        <button type="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-purple-700 dark:text-purple-300 bg-white dark:bg-gray-900 hover:text-purple-900 dark:hover:text-purple-200 focus:outline-none focus:bg-purple-50 dark:focus:bg-purple-900 active:bg-purple-100 dark:active:bg-purple-800 transition">
                            {{ Auth::user()->currentTeam->name }}
                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                            </svg>
                        </button>
                    </span>
                </x-slot>
                <x-slot name="content">
                    <div class="w-60">
                        <!-- Team Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Team') }}
                        </div>
                        <!-- Team Settings -->
                        <x-laravel-admin::admin.dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                            {{ __('Team Settings') }}
                        </x-laravel-admin::admin.dropdown-link>
                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-laravel-admin::admin.dropdown-link href="{{ route('teams.create') }}">
                            {{ __('Create New Team') }}
                        </x-laravel-admin::admin.dropdown-link>
                        @endcan
                        <!-- Team Switcher -->
                        @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200 dark:border-gray-600"></div>
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>
                        @foreach (Auth::user()->allTeams() as $team)
                        <x-laravel-admin::admin.switchable-team :team="$team" />
                        @endforeach
                        @endif
                    </div>
                </x-slot>
            </x-laravel-admin::admin.dropdown>
        </div>
        @endif

        <!-- Settings Dropdown -->
        <div class="ms-3 relative">
            <x-laravel-admin::admin.dropdown align="right" width="48">
                <x-slot name="trigger">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <button
                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-purple-300 transition">
                        <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />
                    </button>
                    @else
                    <span class="inline-flex rounded-md">
                        <button type="button"
                            class="inline-flex items-center px-1 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-purple-700 dark:text-purple-300 bg-white dark:bg-gray-900 hover:text-purple-900 dark:hover:text-purple-200 focus:outline-none focus:bg-purple-50 dark:focus:bg-purple-900 active:bg-purple-100 dark:active:bg-purple-800 transition">
                            {{ Auth::user()->name }}
                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </span>
                    @endif
                </x-slot>

                <x-slot name="content">

                    <!-- Admin Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Admin Management') }}
                    </div>


                    @if(auth()->user())
                    <x-laravel-admin::admin.dropdown-link href="{{ route('admin.index') }}" :active="request()->routeIs('admin.index')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-4 h-4 mr-2 inline -mt-1">
                            <circle cx="12" cy="12" r="3" />
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09A1.65 1.65 0 0 0 11 3.09V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                        </svg>
                        {{ __('관리자 홈') }}
                    </x-laravel-admin::admin.dropdown-link>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-600"></div>

                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Account') }}
                    </div>

                    @php
                        $adminProfileRoute = config('laravel-admin.route_name_prefix', 'admin.').'profile.show';
                        $profileRoute = Route::has($adminProfileRoute) ? $adminProfileRoute : 'profile.show';
                    @endphp

                    <x-laravel-admin::admin.dropdown-link href="{{ route($profileRoute) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-4 h-4 mr-2 inline -mt-1">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>{{ __('Profile') }}
                    </x-laravel-admin::admin.dropdown-link>
                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-laravel-admin::admin.dropdown-link href="{{ route('api-tokens.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-4 h-4 mr-2  inline -mt-1">
                            <path d="M16 4h2v16H4V6h2M15 3H9v20"></path>
                        </svg>{{ __('API Tokens') }}
                    </x-laravel-admin::admin.dropdown-link>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-600"></div>

                    <!-- Authentication -->
                    @php
                        $adminLogoutRoute = config('laravel-admin.route_name_prefix', 'admin.').'logout';
                        $logoutRoute = Route::has($adminLogoutRoute) ? $adminLogoutRoute : 'logout';
                    @endphp

                    <form method="POST" action="{{ route($logoutRoute) }}" x-data>
                        @csrf
                        <x-laravel-admin::admin.dropdown-link href="{{ route($logoutRoute) }}" @click.prevent="$root.submit();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="w-4 h-4 mr-2 inline -mt-1">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16,17 21,12 16,7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            {{ __('Log Out') }}
                        </x-laravel-admin::admin.dropdown-link>
                    </form>


                </x-slot>
            </x-laravel-admin::admin.dropdown>
        </div>
    </div>
</header>
