@props([
    'title' => '알림',
    'message' => '',
    'type' => 'info', // info, success, warning, error
    'showCloseButton' => true,
    'closeOnBackdropClick' => false,
    'closeOnEscape' => true
])

@php
$modalId = $attributes->get('id', 'alert-modal-' . uniqid());

// 타입에 따른 스타일 클래스
$typeClasses = match($type) {
    'success' => 'border-green-500 bg-green-50 dark:bg-green-900/20',
    'warning' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20',
    'error' => 'border-red-500 bg-red-50 dark:bg-red-900/20',
    'info' => 'border-blue-500 bg-blue-50 dark:bg-blue-900/20',
    default => 'border-gray-500 bg-gray-50 dark:bg-gray-900/20'
};

$iconClasses = match($type) {
    'success' => 'text-green-600 dark:text-green-400',
    'warning' => 'text-yellow-600 dark:text-yellow-400',
    'error' => 'text-red-600 dark:text-red-400',
    'info' => 'text-blue-600 dark:text-blue-400',
    default => 'text-gray-600 dark:text-gray-400'
};

$buttonClasses = match($type) {
    'success' => 'bg-green-600 hover:bg-green-700 text-white',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white',
    'error' => 'bg-red-600 hover:bg-red-700 text-white',
    'info' => 'bg-blue-600 hover:bg-blue-700 text-white',
    default => 'bg-gray-600 hover:bg-gray-700 text-white'
};
@endphp

<div x-data="draggableModalAlert({
    modalId: '{{ $modalId }}',
    title: '{{ $title }}',
    message: '{{ $message }}',
    type: '{{ $type }}'
})" 
x-show="isOpen" 
@keydown.escape.window="{{ $closeOnEscape ? 'close()' : '' }}"
@open-modal.window="
    if ($event.detail.modalId === '{{ $modalId }}') {
        openModal();
    }
"
@close-modal.window="
    if ($event.detail.modalId === '{{ $modalId }}') {
        close();
    }
"
class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 dark:bg-black/70"
id="{{ $modalId }}"
{{ $attributes->merge(['class' => '']) }}>

    <!-- Alert 모달 박스 (움직일 수 없음) -->
    <div x-cloak x-show="isOpen" 
         @click.away="{{ $closeOnBackdropClick ? 'close()' : '' }}"
         class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border-2 {{ $typeClasses }} w-full mx-4 transform transition-all duration-300"
         :style="`max-width: min(640px, calc(100vw - 40px)); max-height: calc(100vh - 40px);`"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <!-- 헤더 -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <!-- 아이콘 -->
                <div class="flex-shrink-0">
                    @if($type === 'success')
                        <svg class="w-6 h-6 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    @elseif($type === 'warning')
                        <svg class="w-6 h-6 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    @elseif($type === 'error')
                        <svg class="w-6 h-6 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="w-6 h-6 {{ $iconClasses }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
                
                <!-- 제목 -->
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
            </div>

            @if($showCloseButton)
            <button @click.stop="close()" @touchstart.stop="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            @endif
        </div>

        <!-- 메시지 -->
        <div class="p-4">
            <p class="text-gray-700 dark:text-gray-300">
                {{ $message ?: $slot }}
            </p>
        </div>

        <!-- 버튼 -->
        <div class="flex justify-end p-4 border-t border-gray-200 dark:border-gray-700">
            <button @click.stop="close()" @touchstart.stop="close()"
                    class="px-4 py-2 rounded-md font-medium transition-colors duration-200 {{ $buttonClasses }}">
                확인
            </button>
        </div>
    </div>
</div>