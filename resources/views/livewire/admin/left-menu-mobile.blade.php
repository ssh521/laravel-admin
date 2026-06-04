<div class="lg:hidden">
    <!-- 오버레이: 메뉴가 열릴 때만 화면 전체를 덮음 -->
    <div x-show="isMobileMenuOpen" x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-60 z-40"
        @click="isMobileMenuOpen = false"></div>

    <!-- 좌측 메뉴: YouTube 스타일, 트랜지션 포함 -->
    <aside x-show="isMobileMenuOpen"
        x-transition:enter="transition-transform duration-300"
        x-transition:enter-start="-translate-x-64"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-64"
        @transitionend="
            // 트랜지션이 완료되고 메뉴가 열려있을 때 폴더 상태 복원
            if (isMobileMenuOpen && typeof window.restoreAllDtreeNodes === 'function') {
                window.restoreAllDtreeNodes();
            }
        "
        class="fixed top-0 left-0 h-screen w-64
            shadow-lg border-r border-gray-200 dark:border-gray-700
           z-50 bg-gray-50 dark:bg-gray-900 transform" style="display: none;">

        <!-- 상단: 햄버거+로고 (헤더와 동일) -->
        <div
            class="flex items-center h-16 px-4 border-b border-indigo-200 dark:border-indigo-700 bg-white dark:bg-gray-900">
            <button class="mr-0 p-2 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-800 transition"
                @click="isMobileMenuOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-700 dark:text-purple-300" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            </button>
            <!-- 로고 -->
            <a href="{{ route('admin.index') }}" class="flex items-center gap-2" style="text-decoration: none;">
                <x-laravel-admin::admin.site-logo class="w-[22px] -mb-1 dark:text-purple-200" />
                <span class="text-md lg:text-xl font-bold text-purple-700 dark:text-purple-200 tracking-tight">관리자 메뉴</span>
            </a>
        </div>

        <!-- 메뉴 본문: 아이콘+텍스트, 섹션 구분, 스크롤 -->
        <nav x-data="sidebarBackground" :style="backgroundStyle" class="overflow-y-auto h-[calc(100vh-64px)] py-0 px-0"
             @click.self="isMobileMenuOpen = false">
            <div @click="if ($event.target.closest('a')) { isMobileMenuOpen = false; }">
                @livewire('admin.left-menu')
            </div>
        </nav>
    </aside>
</div>
