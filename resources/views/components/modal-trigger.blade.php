@props([
    'text' => 'Open Modal',
    'modalId' => null,
    'modal-id' => null, // kebab-case 지원
    'variant' => 'primary', // primary, secondary, danger
    'type' => 'button', // button, link
    'modalType' => 'multi', // multi, single, alert
    'data' => null, // 모달에 전달할 추가 데이터
    'data-json' => null // JSON 형태의 데이터
])

@php
    // 버튼 스타일 클래스 정의
    $buttonClasses = match($variant) {
        'primary' => 'bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-400 text-white',
        'secondary' => 'bg-gray-600 dark:bg-gray-500 hover:bg-gray-700 dark:hover:bg-gray-400 text-white',
        'danger' => 'bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-400 text-white',
        default => 'bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-400 text-white'
    };
    
    // 링크 스타일 클래스 정의
    $linkClasses = match($variant) {
        'primary' => 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300',
        'secondary' => 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300',
        'danger' => 'text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300',
        default => 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300'
    };
    
    // 공통 클래스
    $commonClasses = 'font-medium transition-colors duration-200 cursor-pointer';
@endphp

@php
    // modalId 처리 (kebab-case 우선, 없으면 camelCase)
    $finalModalId = $modalId ?? $attributes->get('modal-id');
    
    // 데이터 처리
    $eventData = ['modalId' => $finalModalId];
    
    // 추가 데이터가 있으면 포함
    if ($data) {
        $eventData['data'] = $data;
    }
    
    if ($attributes->get('data-json')) {
        $eventData['dataJson'] = $attributes->get('data-json');
    }
    
    // 모달 타입에 따른 이벤트 결정
    $eventName = match($modalType) {
        'multi' => 'open-modal',
        'single' => 'open-modal',
        'alert' => 'open-modal',
        default => 'open-modal'
    };
@endphp

@if($type === 'link')
    {{-- 링크 형태로 렌더링 --}}
    <a 
        @click.prevent="$dispatch('{{ $eventName }}', { modalId: '{{ $finalModalId }}' })"
        class="{{ $commonClasses }} {{ $linkClasses }}"
        {{ $attributes->merge(['class' => '']) }}>
        {{ $text }}
    </a>
@else
    {{-- 버튼 형태로 렌더링 (기본값) --}}
    <button 
        @click="$dispatch('{{ $eventName }}', { modalId: '{{ $finalModalId }}' })"
        class="px-4 py-2 rounded-md {{ $commonClasses }} {{ $buttonClasses }}"
        {{ $attributes->merge(['class' => '']) }}>
        {{ $text }}
    </button>
@endif 