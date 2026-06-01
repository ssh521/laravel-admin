@props([
    'text' => 'Open Modal',
    'component' => null, // 필수: Livewire 컴포넌트 이름 (예: 'admin.users.user-table')
    'keyId' => null, // 선택: 고유 키 (중복 방지용)
    'key-id' => null, // kebab-case 지원
    'params' => [], // 선택: 컴포넌트에 전달할 파라미터
    'title' => '제목 없음', // 모달 제목
    'width' => 800, // 모달 너비
    'height' => 600, // 모달 높이
    'minWidth' => 300, // 최소 너비
    'minHeight' => 200, // 최소 높이
    'x' => null, // 초기 X 위치
    'y' => null, // 초기 Y 위치
    'center' => false, // 화면 가운데 위치 (x/y가 지정되지 않았을 때만 적용)
    'resizable' => true, // 리사이즈 가능 여부
    'showCloseButton' => true, // 닫기 버튼 표시 여부
    'single' => false, // 단일 모달 모드
    'backdrop' => null, // 배경 오버레이 표시 여부 (null이면 single일 때만 true)
    'variant' => 'primary', // primary, secondary, danger, success, warning, info
    'type' => 'button', // button, link
    'method' => 'alpine', // alpine, livewire, javascript
    'icon' => false, // 아이콘 SVG 경로 또는 클래스
])

@php
    // 버튼 스타일 클래스 정의
    $buttonClasses = match($variant) {
        'primary' => 'bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-400 text-white',
        'secondary' => 'bg-gray-600 dark:bg-gray-500 hover:bg-gray-700 dark:hover:bg-gray-400 text-white',
        'danger' => 'bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-400 text-white',
        'success' => 'bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-400 text-white',
        'warning' => 'bg-yellow-600 dark:bg-yellow-500 hover:bg-yellow-700 dark:hover:bg-yellow-400 text-white',
        'info' => 'bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-400 text-white',
        default => 'bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-400 text-white'
    };
    
    // 링크 스타일 클래스 정의
    $linkClasses = match($variant) {
        'primary' => 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300',
        'secondary' => 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300',
        'danger' => 'text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300',
        'success' => 'text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300',
        'warning' => 'text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300',
        'info' => 'text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300',
        default => 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300'
    };
    
    // 공통 클래스
    $commonClasses = 'font-medium transition-colors duration-200 cursor-pointer';
    
    // 컴포넌트가 필수인지 확인
    if (!$component) {
        throw new \InvalidArgumentException('component 속성은 필수입니다.');
    }
    
    // 설정 객체 생성
    // backdrop: single이 true이고 명시적으로 설정되지 않았으면 true, 그 외에는 명시적 값 또는 false
    $isSingle = $single === true;
    $backdropValue = $backdrop !== null ? ($backdrop === true) : ($isSingle ? true : false);
    
    $config = [
        'title' => $title,
        'width' => (int) $width,
        'height' => (int) $height,
        'minWidth' => (int) $minWidth,
        'minHeight' => (int) $minHeight,
        'resizable' => $resizable !== false,
        'showCloseButton' => $showCloseButton !== false,
        'single' => $isSingle,
        'backdrop' => $backdropValue,
    ];

    if ($center === true) {
        $config['center'] = true;
    }
    
    if ($x !== null) {
        $config['x'] = (int) $x;
    }
    if ($y !== null) {
        $config['y'] = (int) $y;
    }
    // keyId 처리 (kebab-case 우선, 없으면 camelCase, 둘 다 없으면 자동 생성)
    $finalKeyId = $attributes->get('key-id') ?? $keyId ?? 'modal-' . str_replace('.', '-', $component) . '-' . uniqid();
@endphp

@if($method === 'livewire')
    {{-- Livewire 방법 --}}
    @if($type === 'link')
        <a 
            wire:click="openDynamicModal(@js($component), @js($finalKeyId), @js($params), @js($config))"
            class="{{ $commonClasses }} {{ $linkClasses }}"
            {{ $attributes->merge(['class' => '']) }}>
            @if($icon)
                <span class="inline-flex items-center gap-2">
                    {!! $icon !!}
                    {{ $text }}
                </span>
            @else
                {{ $text }}
            @endif
        </a>
    @else
        <button 
            wire:click="openDynamicModal(@js($component), @js($finalKeyId), @js($params), @js($config))"
            class="px-4 py-2 rounded-md {{ $commonClasses }} {{ $buttonClasses }}"
            {{ $attributes->merge(['class' => '']) }}>
            @if($icon)
                <span class="inline-flex items-center gap-2">
                    {!! $icon !!}
                    {{ $text }}
                </span>
            @else
                {{ $text }}
            @endif
        </button>
    @endif
@elseif($method === 'javascript')
    {{-- JavaScript 방법 --}}
    @php
        $payloadJson = json_encode([
            'keyId' => $finalKeyId,
            'component' => $component,
            'params' => $params,
            'config' => $config,
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    @endphp
    @if($type === 'link')
        <a 
            href="#"
            data-dm-trigger="dynamic-modal"
            data-dm-payload="{{ $payloadJson }}"
            class="{{ $commonClasses }} {{ $linkClasses }}"
            {{ $attributes->merge(['class' => '']) }}>
            @if($icon)
                <span class="inline-flex items-center gap-2">
                    {!! $icon !!}
                    {{ $text }}
                </span>
            @else
                {{ $text }}
            @endif
        </a>
    @else
        <button 
            data-dm-trigger="dynamic-modal"
            data-dm-payload="{{ $payloadJson }}"
            class="px-4 py-2 rounded-md {{ $commonClasses }} {{ $buttonClasses }}"
            {{ $attributes->merge(['class' => '']) }}>
            @if($icon)
                <span class="inline-flex items-center gap-2">
                    {!! $icon !!}
                    {{ $text }}
                </span>
            @else
                {{ $text }}
            @endif
        </button>
    @endif
@else
    {{-- Alpine.js 방법 (기본값) --}}
    @php
        // Alpine.js에서 사용할 수 있도록 데이터를 안전하게 준비
        $alpineData = [
            'keyId' => $finalKeyId,
            'component' => $component,
            'params' => is_array($params) ? $params : [],
            'config' => $config,
        ];
    @endphp
    <div 
        x-data="{ 
            modalData: @js($alpineData),
            openModal() { 
                if (window.Livewire) { 
                    Livewire.dispatch('dynamic-modal:open', [this.modalData]); 
                } else {
                    console.error('Livewire가 로드되지 않았습니다.');
                }
            } 
        }"
    >
        @if($type === 'link')
            <a 
                @click.prevent="openModal()"
                class="{{ $commonClasses }} {{ $linkClasses }}"
                {{ $attributes->merge(['class' => '']) }}>
                @if($icon)
                    <span class="inline-flex items-center gap-2">
                        {!! $icon !!}
                        {{ $text }}
                    </span>
                @else
                    {{ $text }}
                @endif
            </a>
        @else
            <button 
                @click="openModal()"
                class="px-4 py-2 rounded-md {{ $commonClasses }} {{ $buttonClasses }}"
                {{ $attributes->merge(['class' => '']) }}>
                @if($icon)
                    <span class="inline-flex items-center gap-2">
                        {!! $icon !!}
                        {{ $text }}
                    </span>
                @else
                    {{ $text }}
                @endif
            </button>
        @endif
    </div>
@endif

