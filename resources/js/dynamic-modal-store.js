import { createDragHandler, createResizeHandler } from './modal-utils';

export function initDynamicModalStore() {
    if (!window.Alpine) {
        console.error('Alpine.js가 로드되지 않았습니다.');
        return;
    }

    Alpine.store('dynamicModals', {
        modals: [],
        nextId: 1,
        maxZIndex: 1000,

        add(config) {
            const modalId = config.id || `dm-${Date.now()}-${this.nextId++}`;
            const offset = (this.modals.length % 10) * 30;
            const isSingle = config.single || false;
            
            // 화면 중앙 옵션: x/y가 명시적으로 지정되지 않은 경우에만 적용
            let initialX, initialY;
            const hasExplicitX = config.x !== undefined && config.x !== null;
            const hasExplicitY = config.y !== undefined && config.y !== null;
            const shouldCenter = (config.center === true) || (isSingle && !hasExplicitX && !hasExplicitY);
            
            if (shouldCenter && !hasExplicitX && !hasExplicitY) {
                const width = config.width || 800;
                const height = config.height || 600;
                initialX = Math.max(0, (window.innerWidth - width) / 2);
                initialY = Math.max(0, (window.innerHeight - height) / 2);
            } else {
                initialX = hasExplicitX ? config.x : (100 + offset);
                initialY = hasExplicitY ? config.y : (100 + offset);
            }

            const modal = {
                id: modalId,
                title: config.title || '제목 없음',
                component: config.component || null,
                params: config.params || {},
                pos: {
                    x: initialX,
                    y: initialY
                },
                size: {
                    w: config.width || 800,
                    h: config.height || 600
                },
                minWidth: config.minWidth || 300,
                minHeight: config.minHeight || 200,
                zIndex: this.getNextZIndex(),
                resizable: config.resizable !== false,
                showCloseButton: config.showCloseButton !== false,
                single: isSingle,
                backdrop: config.backdrop !== undefined ? config.backdrop : (isSingle ? true : false),
                isMinimized: false,
                isMaximized: false,
                savedState: null // 최소화/최대화 전 상태 저장
            };

            // window.modalManager와의 호환성을 위해 등록 (필요시)
            if (window.modalManager) {
                window.modalManager.register(modalId, {
                    zIndex: modal.zIndex,
                    isOpen: true
                });
            }

            // Single 모달이 열리면 다른 모달들 비활성화
            if (modal.single) {
                this.disableOtherModals(modalId);
            }

            this.modals.push(modal);
            return modalId;
        },

        getNextZIndex() {
            this.maxZIndex += 1;
            return this.maxZIndex;
        },

        disableOtherModals(excludeModalId) {
            // 모든 모달을 비활성화 (싱글 모달 제외)
            this.modals.forEach(m => {
                if (m.id !== excludeModalId) {
                    const modalEl = document.querySelector(`[data-modal-id="${m.id}"]`);
                    if (modalEl) {
                        modalEl.style.pointerEvents = 'none';
                        modalEl.style.opacity = '0.5';
                        modalEl.style.cursor = 'not-allowed';
                    }
                }
            });
        },

        enableOtherModals(excludeModalId) {
            // 특정 모달을 제외한 다른 모달들을 활성화
            this.modals.forEach(m => {
                if (m.id !== excludeModalId) {
                    const modalEl = document.querySelector(`[data-modal-id="${m.id}"]`);
                    if (modalEl) {
                        modalEl.style.pointerEvents = '';
                        modalEl.style.opacity = '';
                        modalEl.style.cursor = '';
                    }
                }
            });
        },

        enableAllModals() {
            // 모든 모달을 활성화 (싱글 모달이 없을 때 사용)
            this.modals.forEach(m => {
                const modalEl = document.querySelector(`[data-modal-id="${m.id}"]`);
                if (modalEl) {
                    modalEl.style.pointerEvents = '';
                    modalEl.style.opacity = '';
                    modalEl.style.cursor = '';
                }
            });
        },

        bringToFront(modalId) {
            const modalIndex = this.modals.findIndex(m => m.id === modalId);
            if (modalIndex === -1) return;

            const newZIndex = this.getNextZIndex();

            // window.modalManager와의 호환성
            if (window.modalManager) {
                window.modalManager.bringToFront(modalId);
            }

            this.modals[modalIndex] = {
                ...this.modals[modalIndex],
                zIndex: newZIndex
            };
            this.modals = [...this.modals];
        },

        updatePosition(modalId, x, y) {
            const modal = this.modals.find(m => m.id === modalId);
            if (modal) {
                modal.pos.x = x;
                modal.pos.y = y;
            }
        },

        updateSize(modalId, width, height) {
            const modal = this.modals.find(m => m.id === modalId);
            if (modal) {
                modal.size.w = width;
                modal.size.h = height;
            }
        },

        remove(modalId) {
            const modal = this.modals.find(m => m.id === modalId);
            if (!modal) return;

            // Single 모달이 닫히면 모든 모달 활성화 (enableAllModals는 외부에서 호출)
            if (window.modalManager) {
                window.modalManager.unregister(modalId);
            }

            this.modals = this.modals.filter(m => m.id !== modalId);
        },

        getModal(modalId) {
            return this.modals.find(m => m.id === modalId);
        },

        minimize(modalId) {
            const modal = this.modals.find(m => m.id === modalId);
            if (!modal || modal.isMinimized) return;

            // 현재 상태 저장
            if (!modal.savedState) {
                modal.savedState = {
                    pos: { ...modal.pos },
                    size: { ...modal.size },
                    isMaximized: modal.isMaximized
                };
            }

            modal.isMinimized = true;
            modal.isMaximized = false;

            // 최소화: 작은 크기로 축소 (헤더만 보이게)
            modal.size.w = 300;
            modal.size.h = 40;
            modal.pos.y = window.innerHeight - 40;

            this.modals = [...this.modals];
        },

        maximize(modalId) {
            const modal = this.modals.find(m => m.id === modalId);
            if (!modal || modal.isMaximized) return;

            // 현재 상태 저장 (savedState가 없거나 최소화 상태가 아닐 때만)
            if (!modal.savedState || !modal.isMinimized) {
                modal.savedState = {
                    pos: { ...modal.pos },
                    size: { ...modal.size },
                    isMinimized: modal.isMinimized
                };
            }

            modal.isMaximized = true;
            modal.isMinimized = false;

            // 최대화: 전체 화면으로 확대
            modal.pos.x = 0;
            modal.pos.y = 0;
            modal.size.w = window.innerWidth;
            modal.size.h = window.innerHeight;

            this.modals = [...this.modals];
        },

        restore(modalId) {
            const modal = this.modals.find(m => m.id === modalId);
            if (!modal || (!modal.isMinimized && !modal.isMaximized)) return;

            // 저장된 상태로 복원
            if (modal.savedState) {
                modal.pos = { ...modal.savedState.pos };
                modal.size = { ...modal.savedState.size };
                // 복원 후 savedState를 유지하지 않음 (다시 최소화/최대화할 수 있도록)
                // modal.savedState = null;
            }

            modal.isMinimized = false;
            modal.isMaximized = false;

            this.modals = [...this.modals];
        },

        closeAll() {
            const modalIds = [...this.modals.map(m => m.id)];
            modalIds.forEach(id => this.remove(id));
        }
    });
}

export function dynamicModal(config = {}) {
    return {
        modalId: config.modalId,
        single: config.single || false,
        pos: { x: config.initialX || 100, y: config.initialY || 100 },
        size: { w: config.initialWidth || 800, h: config.initialHeight || 600 },
        minWidth: config.minWidth || 300,
        minHeight: config.minHeight || 200,
        zIndex: config.initialZIndex || 1000,
        isDragging: false,
        isResizing: false,
        isMinimized: false,
        isMaximized: false,

        init() {
            const storeModal = Alpine.store('dynamicModals').getModal(this.modalId);
            if (storeModal) {
                this.pos = { ...storeModal.pos };
                this.size = { ...storeModal.size };
                this.zIndex = storeModal.zIndex;
                this.minWidth = storeModal.minWidth;
                this.minHeight = storeModal.minHeight;
                this.isMinimized = storeModal.isMinimized || false;
                this.isMaximized = storeModal.isMaximized || false;
            }

            this.$watch(() => {
                const modal = Alpine.store('dynamicModals').getModal(this.modalId);
                return modal ? modal.zIndex : this.zIndex;
            }, (newZIndex) => {
                this.zIndex = newZIndex;
            });

            // 최소화/최대화 상태 감시
            this.$watch(() => {
                const modal = Alpine.store('dynamicModals').getModal(this.modalId);
                return modal ? { isMinimized: modal.isMinimized, isMaximized: modal.isMaximized } : { isMinimized: false, isMaximized: false };
            }, (state) => {
                this.isMinimized = state.isMinimized;
                this.isMaximized = state.isMaximized;
            });

            // 위치/크기 변경 감시
            this.$watch(() => {
                const modal = Alpine.store('dynamicModals').getModal(this.modalId);
                return modal ? { pos: modal.pos, size: modal.size } : null;
            }, (state) => {
                if (state) {
                    this.pos = { ...state.pos };
                    this.size = { ...state.size };
                }
            });
        },

        get style() {
            const storeModal = Alpine.store('dynamicModals').getModal(this.modalId);
            const currentZIndex = storeModal ? storeModal.zIndex : this.zIndex;
            const isMinimized = storeModal ? storeModal.isMinimized : this.isMinimized;
            const isMaximized = storeModal ? storeModal.isMaximized : this.isMaximized;

            return `
                position: fixed;
                left: ${this.pos.x}px;
                top: ${this.pos.y}px;
                width: ${this.size.w}px;
                height: ${this.size.h}px;
                z-index: ${currentZIndex};
            `;
        },

        startDrag(e) {
            const dragHandler = createDragHandler({
                getPos: () => this.pos,
                setPos: (x, y) => {
                    this.pos.x = x;
                    this.pos.y = y;
                },
                getSize: () => this.size,
                onDragStart: () => {
                    this.bringToFront();
                    this.isDragging = true;
                },
                onDragMove: (x, y) => {
                    this.pos.x = x;
                    this.pos.y = y;
                    Alpine.store('dynamicModals').updatePosition(this.modalId, x, y);
                },
                onDragEnd: () => {
                    this.isDragging = false;
                }
            });
            
            dragHandler(e);
        },

        startResize(e, direction) {
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
                minWidth: this.minWidth,
                minHeight: this.minHeight,
                direction: direction,
                onResizeStart: () => {
                    this.bringToFront();
                    this.isResizing = true;
                },
                onResizeMove: (width, height, posX, posY) => {
                    this.size.w = width;
                    this.size.h = height;
                    this.pos.x = posX;
                    this.pos.y = posY;
                    
                    const store = Alpine.store('dynamicModals');
                    store.updateSize(this.modalId, width, height);
                    store.updatePosition(this.modalId, posX, posY);
                },
                onResizeEnd: () => {
                    this.isResizing = false;
                }
            });
            
            resizeHandler(e);
        },

        bringToFront() {
            Alpine.store('dynamicModals').bringToFront(this.modalId);
            const modal = Alpine.store('dynamicModals').getModal(this.modalId);
            if (modal) {
                this.zIndex = modal.zIndex;
            }
        },

        minimize() {
            Alpine.store('dynamicModals').minimize(this.modalId);
            const modal = Alpine.store('dynamicModals').getModal(this.modalId);
            if (modal) {
                this.pos = { ...modal.pos };
                this.size = { ...modal.size };
                this.isMinimized = modal.isMinimized;
                this.isMaximized = modal.isMaximized;
            }
        },

        maximize() {
            Alpine.store('dynamicModals').maximize(this.modalId);
            const modal = Alpine.store('dynamicModals').getModal(this.modalId);
            if (modal) {
                this.pos = { ...modal.pos };
                this.size = { ...modal.size };
                this.isMinimized = modal.isMinimized;
                this.isMaximized = modal.isMaximized;
            }
        },

        restore() {
            Alpine.store('dynamicModals').restore(this.modalId);
            const modal = Alpine.store('dynamicModals').getModal(this.modalId);
            if (modal) {
                this.pos = { ...modal.pos };
                this.size = { ...modal.size };
                this.isMinimized = modal.isMinimized;
                this.isMaximized = modal.isMaximized;
            }
        },

        toggleMaximize() {
            const modal = Alpine.store('dynamicModals').getModal(this.modalId);
            if (modal?.isMaximized) {
                this.restore();
            } else {
                this.maximize();
            }
        },

        close() {
            if (window.Livewire) {
                Livewire.dispatch('dynamic-modal:close', [{ modalId: this.modalId }]);
            }
        }
    };
}

