@props([
    'modalId' => '',
    'title' => '제목 없음',
    'showCloseButton' => true,
    'showResizeHandles' => true,
    'single' => false,
    'backdrop' => false,
])

{{-- 
    다이나믹 멀티 모달 컴포넌트
    - 4방향 리사이즈 (SE, SW, NE, NW)
    - 드래그 가능
    - 클릭 시 맨 앞으로
    - 무한대 생성 가능
--}}

<div 
    x-data="dynamicModal({
        modalId: '{{ $modalId }}',
        single: {{ $single ? 'true' : 'false' }}
    })"
    class="select-none"
    {{ $attributes }}
    x-effect="single && setTimeout(() => Alpine.store('dynamicModals').disableOtherModals('{{ $modalId }}'), 10)"
    data-modal-id="{{ $modalId }}"
>
    <!-- 모달 박스 -->
    <div 
        :style="style"
        @mousedown.self="if (!single) bringToFront()"
        @touchstart.self="if (!single) bringToFront()"
        @mousedown.capture="bringToFront()"
        class="flex flex-col bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-300 dark:border-gray-600 overflow-hidden"
        style="width: 100%; height: 100%; position: relative;"
    >
        <!-- 헤더 (드래그 핸들) -->
        <div 
            @mousedown.stop="startDrag($event)"
            @touchstart.stop="startDrag($event)"
            class="flex justify-between items-center bg-gradient-to-r from-purple-400 to-pink-400 dark:from-purple-600 dark:to-pink-600 px-4 py-2 cursor-move select-none touch-none"
        >
            <span class="text-sm font-semibold text-white drop-shadow-sm truncate">
                {{ $title }}
            </span>
            
            <div class="flex items-center gap-1 ml-2">
                @if(!$single)
                    <!-- 최소화/복원 버튼 (싱글 모달에서는 숨김) -->
                    <button 
                        @click.stop="isMinimized ? restore() : minimize()" 
                        @touchstart.stop="isMinimized ? restore() : minimize()"
                        class="flex-shrink-0 hover:bg-white/20 rounded-full p-1 transition-colors duration-200 active:bg-white/30"
                        type="button"
                        :title="isMinimized ? '복원' : '최소화'"
                    >
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13H5v-2h14v2z"/>
                        </svg>
                    </button>
                @endif

                <!-- 최대화/복원 버튼 -->
                <button 
                    @click.stop="toggleMaximize()" 
                    @touchstart.stop="toggleMaximize()"
                    class="flex-shrink-0 hover:bg-white/20 rounded-full p-1 transition-colors duration-200 active:bg-white/30"
                    type="button"
                    :title="isMaximized ? '복원' : '최대화'"
                >
                    <!-- 최대화 아이콘 -->
                    <svg x-show="!isMaximized" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                    <!-- 복원 아이콘 -->
                    <svg x-show="isMaximized" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                    </svg>
                </button>

                @if($showCloseButton)
                <!-- 닫기 버튼 -->
                <button 
                    @click.stop="close()" 
                    @touchstart.stop="close()"
                    class="flex-shrink-0 hover:bg-white/20 rounded-full p-1 transition-colors duration-200 active:bg-white/30"
                    type="button"
                    title="닫기"
                >
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    </svg>
                </button>
                @endif
            </div>
        </div>

        <!-- 모달 콘텐츠 -->
        <div x-show="!isMinimized" class="flex-1 overflow-auto p-2 text-gray-900 dark:text-gray-100">
            {{ $slot }}
        </div>

        @if($showResizeHandles)
        <!-- 4방향 리사이즈 핸들 -->
        
        <!-- SE (오른쪽 아래) -->
        <div 
            x-show="!isMaximized && !isMinimized"
            @mousedown.stop="startResize($event, 'se')"
            @touchstart.stop="startResize($event, 'se')"
            class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize group touch-none"
            title="크기 조정"
        >
            <div class="absolute bottom-0 right-0 w-3 h-3 border-r-2 border-b-2 border-gray-400 dark:border-gray-500 group-hover:border-purple-500 transition-colors"></div>
        </div>

        <!-- SW (왼쪽 아래) -->
        <div 
            x-show="!isMaximized && !isMinimized"
            @mousedown.stop="startResize($event, 'sw')"
            @touchstart.stop="startResize($event, 'sw')"
            class="absolute bottom-0 left-0 w-4 h-4 cursor-sw-resize group touch-none"
            title="크기 조정"
        >
            <div class="absolute bottom-0 left-0 w-3 h-3 border-l-2 border-b-2 border-gray-400 dark:border-gray-500 group-hover:border-purple-500 transition-colors"></div>
        </div>

        <!-- NE (오른쪽 위) -->
        <div 
            x-show="!isMaximized && !isMinimized"
            @mousedown.stop="startResize($event, 'ne')"
            @touchstart.stop="startResize($event, 'ne')"
            class="absolute top-10 right-0 w-4 h-4 cursor-ne-resize group touch-none"
            title="크기 조정"
        >
            <div class="absolute top-0 right-0 w-3 h-3 border-r-2 border-t-2 border-gray-400 dark:border-gray-500 group-hover:border-purple-500 transition-colors"></div>
        </div>

        <!-- NW (왼쪽 위) -->
        <div 
            x-show="!isMaximized && !isMinimized"
            @mousedown.stop="startResize($event, 'nw')"
            @touchstart.stop="startResize($event, 'nw')"
            class="absolute top-10 left-0 w-4 h-4 cursor-nw-resize group touch-none"
            title="크기 조정"
        >
            <div class="absolute top-0 left-0 w-3 h-3 border-l-2 border-t-2 border-gray-400 dark:border-gray-500 group-hover:border-purple-500 transition-colors"></div>
        </div>
        @endif
    </div>
</div>

