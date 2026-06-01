<div
    x-data="{
        init() {
            Livewire.on('dynamic-modal:add-to-store', (data) => {
                const modal = Array.isArray(data) ? (data[0]?.modal || data[0]) : (data.modal || data);
                if (!modal?.id) return;

                Alpine.store('dynamicModals').add({ ...modal.config, id: modal.id, component: modal.component, params: modal.params });
            });

            Livewire.on('dynamic-modal:close-all', () => {
                Alpine.store('dynamicModals').closeAll();
            });

            Livewire.on('dynamic-modal:bring-to-front', (data) => {
                const modalId = Array.isArray(data) ? data[0]?.modalId : data.modalId;
                if (modalId) Alpine.store('dynamicModals').bringToFront(modalId);
            });

            Livewire.on('dynamic-modal:remove-from-store', (data) => {
                const modalId = Array.isArray(data) ? data[0]?.modalId : data.modalId;
                if (modalId) {
                    const modal = Alpine.store('dynamicModals').getModal(modalId);
                    const wasSingle = modal?.single === true;
                    Alpine.store('dynamicModals').remove(modalId);

                    // 싱글 모달이 닫히면 모든 모달 활성화
                    if (wasSingle) {
                        Alpine.store('dynamicModals').enableAllModals();
                    }
                }
            });

            Livewire.on('dynamic-modal:enable-all-modals', () => {
                Alpine.store('dynamicModals').enableAllModals();
            });
        }
    }"
    class="dynamic-modal-container"
    @dynamic-modal:close-all.window="Alpine.store('dynamicModals').closeAll()"
>
    @foreach($modals as $modal)
        @php
            $isSingle = $modal['config']['single'] ?? false;
            // backdrop이 명시적으로 설정되지 않았으면 single일 때만 true
            $hasBackdrop = $modal['config']['backdrop'] ?? ($isSingle ? true : false);
            $singleModalId = null;
            if ($isSingle) {
                $singleModalId = $modal['id'];
            }
        @endphp

        @if($hasBackdrop)
            <!-- Backdrop 오버레이 (모달보다 먼저 렌더링, 낮은 z-index) -->
            <!-- Single 모달인 경우: 페이지 콘텐츠 위에 위치하여 클릭 차단 -->
            <!-- 일반 모달인 경우: pointer-events-none으로 클릭 통과 -->
            <div
                x-data="{
                    modalId: '{{ $modal['id'] }}',
                    get backdropZIndex() {
                        const modal = Alpine.store('dynamicModals').getModal(this.modalId);
                        // 모달보다 낮은 z-index로 설정 (모달이 위에 표시되도록)
                        // store 등록 전 fallback은 모달 기본 z-index(1000대)보다 낮아야 클릭을 막지 않습니다.
                        return modal ? modal.zIndex - 1 : 999;
                    },
                    get pointerEvents() {
                        // Single 모달인 경우 클릭 차단, 일반 모달은 클릭 통과
                        const modal = Alpine.store('dynamicModals').getModal(this.modalId);
                        return modal ? (modal.single ? 'auto' : 'none') : @js($isSingle ? 'auto' : 'none');
                    }
                }"
                class="fixed inset-0 bg-black/50 dark:bg-black/70"
                :style="`z-index: ${backdropZIndex}; pointer-events: ${pointerEvents};`"
                wire:key="backdrop-{{ $modal['id'] }}"
            ></div>
        @endif

        <x-laravel-admin::dynamic-modal
            :modalId="$modal['id']"
            :title="$modal['config']['title']"
            :showCloseButton="$modal['config']['showCloseButton']"
            :showResizeHandles="$modal['config']['resizable']"
            :single="$isSingle"
            :backdrop="false"
            wire:key="modal-{{ $modal['id'] }}"
        >
            @if($modal['component'])
                {{-- Livewire 컴포넌트 동적 렌더링 --}}
                @livewire($modal['component'], $modal['params'], key($modal['id']))
            @else
                {{-- 기본 콘텐츠 --}}
                <div class="p-4">
                    <p class="text-gray-600 dark:text-gray-400">컴포넌트가 지정되지 않았습니다.</p>
                </div>
            @endif
        </x-laravel-admin::dynamic-modal>
    @endforeach
</div>
