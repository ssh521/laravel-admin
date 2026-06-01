<div class="flex h-full min-h-0 flex-col pt-4 lg:pt-0" x-on:click="if ($event.target.closest('a')) { sidebarOpen = false }">

    {{-- Brand --}}
    {{-- <div class="flex items-center gap-3 py-2" x-bind:class="desktopSidebarCollapsed ? 'px-3 justify-center' : 'px-6'">
    </div> --}}

    @include('partials.sidebar-nav')

    <div class="px-2.5 text-xs mb-10 font-semibold text-gray-500 dark:text-gray-400" x-bind:class="desktopSidebarCollapsed ? 'hidden' : ''">
    </div>
</div>

