import { createDragHandler, createResizeHandler } from './modal-utils';

// 전역 모달 관리자 초기화
if (!window.modalManager) {
    window.modalManager = {
        modals: new Map(),
        maxZIndex: 1000,
        
        register(modalId, component) {
            this.modals.set(modalId, component);
        },
        
        unregister(modalId) {
            this.modals.delete(modalId);
        },
        
        bringToFront(modalId) {
            this.maxZIndex += 1;
            const modal = this.modals.get(modalId);
            if (modal) {
                modal.zIndex = this.maxZIndex;
            }
            return this.maxZIndex;
        },
        
        getNextZIndex() {
            this.maxZIndex += 1;
            return this.maxZIndex;
        }
    };
}

// 다중 모달을 지원하는 드래그 가능한 모달 컴포넌트
export default function draggableModal(config = {}) {
    return {
        // 기본 설정값
        defaultConfig: {
            initialX: 100,
            initialY: 100,
            initialWidth: 800,
            initialHeight: 600,
            minWidth: 300,
            minHeight: 200,
            maxWidth: window.innerWidth - 50,
            maxHeight: window.innerHeight - 50,
            zIndex: 1000
        },

        // 상태 변수들
        modalId: config.modalId || 'modal-' + Math.random().toString(36).substr(2, 9),
        isOpen: false,
        zIndex: 1000,
        pos: { x: 100, y: 100 },
        size: { w: 800, h: 600 },
        alwaysCenter: config.alwaysCenter || false,
        resizable: config.resizable || false,

        init() {
            //console.log('draggableModal 초기화:', this.modalId, config);
            
            // 설정 병합 및 타입 변환
            const finalConfig = { ...this.defaultConfig, ...config };
            
            // 숫자로 변환
            this.zIndex = window.modalManager.getNextZIndex();
            this.pos.x = parseInt(finalConfig.initialX) || 100;
            this.pos.y = parseInt(finalConfig.initialY) || 100;
            this.size.w = parseInt(finalConfig.initialWidth) || 800;
            this.size.h = parseInt(finalConfig.initialHeight) || 600;
            
            //console.log('최종 설정:', {
            //    modalId: this.modalId,
            //    pos: this.pos,
            //    size: this.size,
            //    zIndex: this.zIndex
            //});
            
            // 모달 매니저에 등록
            window.modalManager.register(this.modalId, this);
            
        },

        get style() {
            return `top: ${this.pos.y}px; left: ${this.pos.x}px; width: ${this.size.w}px; height: ${this.size.h}px; z-index: ${this.zIndex}`;
        },

        openModal() {
            //console.log('모달 열기:', this.modalId);
            
            // 모달이 등록되어 있지 않으면 다시 등록
            if (!window.modalManager.modals.has(this.modalId)) {
                window.modalManager.register(this.modalId, this);
            }
            
            this.isOpen = true;
            this.bringToFront();
            
            // alwaysCenter가 true이면 항상 중앙으로 위치 재계산
            if (this.alwaysCenter) {
                this.centerModal();
            }
            
            // 모달 열기 이벤트 디스패치
            window.dispatchEvent(new CustomEvent('opened-modal', {
                detail: { modalId: this.modalId }
            }));
        },

        dispatchClosedEvent() {
            // window 이벤트로 통일 (Livewire/Alpine 어디서든 수신 가능)
            window.dispatchEvent(new CustomEvent('closed-modal', {
                detail: { modalId: this.modalId }
            }));

            // 기존 Alpine DOM 이벤트 수신 로직도 유지(호환)
            this.$dispatch('closed-modal', { modalId: this.modalId });
        },

        closeModal() {
            this.isOpen = false;
            window.modalManager.unregister(this.modalId);
            this.dispatchClosedEvent();
        },

        bringToFront() {
            this.zIndex = window.modalManager.bringToFront(this.modalId);
        },

        centerModal() {
            // 화면 중앙으로 모달 위치 계산
            const centerX = Math.floor((window.innerWidth - this.size.w) / 2);
            const centerY = Math.floor((window.innerHeight - this.size.h) / 2);
            
            // 화면 경계 내에서 중앙 위치 보장
            this.pos.x = Math.max(0, centerX);
            this.pos.y = Math.max(0, centerY);
        },

        close() {
            // 템플릿(닫기 버튼/esc)에서 호출되는 close()는 closeModal()로 통일
            this.closeModal();
        },

        dragStart(e) {
            // alwaysCenter가 true이면 드래그 비활성화
            if (this.alwaysCenter) {
                return;
            }

            const dragHandler = createDragHandler({
                getPos: () => this.pos,
                setPos: (x, y) => {
                    this.pos.x = x;
                    this.pos.y = y;
                },
                getSize: () => this.size,
                onDragStart: () => {
                    this.bringToFront();
                }
            });
            
            dragHandler(e);
        },

        resizeStart(e, direction = 'se') {
            // resizable이 false이면 리사이즈 비활성화
            if (!this.resizable) {
                return;
            }

            // maxWidth, maxHeight를 리사이즈 시점에 동적으로 계산 (화면 크기 변경 대응)
            const maxWidth = window.innerWidth - 50;
            const maxHeight = window.innerHeight - 50;

            const resizeHandler = createResizeHandler({
                getPos: () => this.pos,
                setPos: (x, y) => {
                    this.pos.x = x;
                    this.pos.y = y;
                },
                getSize: () => this.size,
                setSize: (w, h) => {
                    this.size.w = w;
                    this.size.h = h;
                },
                minWidth: this.defaultConfig.minWidth,
                minHeight: this.defaultConfig.minHeight,
                maxWidth: maxWidth,
                maxHeight: maxHeight,
                direction: direction,
                onResizeStart: () => {
                    this.bringToFront();
                },
                onResizeMove: (width, height, posX, posY) => {
                    this.size.w = width;
                    this.size.h = height;
                    this.pos.x = posX;
                    this.pos.y = posY;
                    
                    // 실시간 리사이즈 이벤트 디스패치
                    window.dispatchEvent(new CustomEvent('modal-resizing', {
                        detail: { 
                            modalId: this.modalId,
                            width: width,
                            height: height
                        }
                    }));
                },
                onResizeEnd: () => {
                    // 리사이즈 완료 이벤트 디스패치
                    window.dispatchEvent(new CustomEvent('modal-resized', {
                        detail: { 
                            modalId: this.modalId,
                            width: this.size.w,
                            height: this.size.h
                        }
                    }));
                    
                    console.log('모달 리사이즈 완료:', this.modalId, 'Size:', this.size.w + 'x' + this.size.h, 'Direction:', direction);
                }
            });
            
            resizeHandler(e);
        }
    };
} 



// Alert 전용(비드래그) 단일 모달 컴포넌트
export function draggableModalAlert(config = {}) {
    return {
        modalId: config.modalId || 'alert-modal',
        title: config.title || '알림',
        message: config.message || '',
        type: config.type || 'info',
        isOpen: false,

        init() {
            console.log('Draggable Alert 모달 초기화:', this.modalId);
        },

        openModal() {
            console.log('Draggable Alert 모달 열기:', this.modalId);
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
            this.disableOtherModals();
        },

        close() {
            console.log('Draggable Alert 모달 닫기:', this.modalId);
            this.isOpen = false;
            document.body.style.overflow = '';
            this.enableOtherModals();
        },

        disableOtherModals() {
            const otherModals = document.querySelectorAll('[id^="draggable-modal"]:not([id*="alert"])');
            otherModals.forEach(modal => {
                modal.style.zIndex = '1000';
                modal.style.pointerEvents = 'none';
            });
        },

        enableOtherModals() {
            const otherModals = document.querySelectorAll('[id^="draggable-modal"]:not([id*="alert"])');
            otherModals.forEach(modal => {
                modal.style.zIndex = '';
                modal.style.pointerEvents = '';
            });
        }
    };
}