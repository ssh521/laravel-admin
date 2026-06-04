import './bootstrap';
import draggableModal, { draggableModalAlert } from './modal-manager';
import './sidebarBackground';
import './dtree';
import Sortable from 'sortablejs';

// SortableJS를 전역으로 노출 (기존 코드와의 호환성을 위해)
window.Sortable = Sortable;

// Alpine.js 컴포넌트들
document.addEventListener('alpine:init', () => {
    
    // 다중 모달을 지원하는 드래그 가능한 모달 컴포넌트
    Alpine.data('draggableModal', draggableModal);
    // Alert 전용 모달 컴포넌트 등록
    Alpine.data('draggableModalAlert', draggableModalAlert);
    
    // 리스트 Sortable 관리 컴포넌트
    Alpine.data('listSortable', () => ({
        sortableInstance: null,
        
        initListSortable() {
            const listContainer = this.$el.querySelector('#stock-list-sortable');
            if (!listContainer) return;
            
            // 리스트 뷰일 때만 초기화 (차트 뷰가 아니고, 컨테이너가 보일 때)
            const isVisible = listContainer.offsetParent !== null;
            if (!isVisible) return;
            
            // 기존 Sortable 인스턴스가 있으면 제거
            if (this.sortableInstance) {
                try {
                    this.sortableInstance.destroy();
                } catch(e) {
                    // console.warn('Error destroying list sortable:', e);
                }
                this.sortableInstance = null;
            }
            
            const livewireId = listContainer.dataset.livewireId;
            if (!livewireId || !window.Sortable) return;
            
            this.sortableInstance = new window.Sortable(listContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                filter: '.sortable-disabled', // 편집 중인 항목은 드래그 불가
                onStart: (evt) => {
                    // console.log('✅ Stock list drag started', evt);
                },
                onEnd: (evt) => {
                    // console.log('✅ Stock list drag ended', evt);
                    const items = Array.from(listContainer.querySelectorAll('[data-stock-id]'));
                    const newOrder = items.map(item => parseInt(item.dataset.stockId));
                    this.updateStockOrder(newOrder, livewireId);
                }
            });
            
            // console.log('Stock list sortable initialized');
        },
        
        updateStockOrder(newOrder, livewireId) {
            // console.log('Order changed:', newOrder);
            
            // Livewire 3 방식으로 호출
            if (window.Livewire && livewireId) {
                const component = window.Livewire.find(livewireId);
                if (component) {
                    // console.log('Calling updateStockOrder on component:', livewireId);
                    component.call('updateStockOrder', newOrder);
                } else {
                    // console.warn('Livewire component not found:', livewireId);
                    window.Livewire.dispatch('update-stock-order', { order: newOrder });
                }
            } else {
                // console.error('Livewire not available or livewire-id missing');
            }
        },
        
        init() {
            // Sortable 초기화
            setTimeout(() => {
                this.$nextTick(() => {
                    this.initListSortable();
                });
            }, 200);
            
            // 컴포넌트가 파괴될 때 정리
            this.$el.addEventListener('livewire:before-destroy', () => {
                if (this.sortableInstance) {
                    try {
                        this.sortableInstance.destroy();
                    } catch(e) {
                        // console.warn('Error destroying sortable on cleanup:', e);
                    }
                    this.sortableInstance = null;
                }
            });
            
            // Livewire 업데이트 감지 - Sortable 재초기화
            document.addEventListener('livewire:updated', () => {
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.initListSortable();
                    }, 100);
                });
            });
        }
    }));
    
    // 차트 그리드 컬럼 관리 컴포넌트
    Alpine.data('chartGridCols', () => ({
        cols: 1,
        lastModalWidth: 0,
        sortableInstance: null,
        
        updateCols(forcedWidth = null) {
            let modalWidth = 0;
            let source = 'Unknown';
            
            if (forcedWidth && forcedWidth > 0) {
                modalWidth = forcedWidth;
                source = 'Event';
            } else {
                modalWidth = 900;
                source = 'Default';
            }
            
            if (modalWidth !== this.lastModalWidth && modalWidth > 0) {
                this.lastModalWidth = modalWidth;
                
                if (modalWidth < 800) this.cols = 1;
                else if (modalWidth < 1200) this.cols = 2;
                else this.cols = 3;
                
                // console.log('Grid Updated - Width:', modalWidth, 'Cols:', this.cols, 'Source:', source);
            }
        },
        
        initChartSortable() {
            const chartContainer = this.$el.querySelector('#chart-list-sortable');
            if (!chartContainer) return;
            
            // 차트 뷰일 때만 초기화
            const isVisible = chartContainer.offsetParent !== null;
            if (!isVisible) return;
            
            // 기존 Sortable 인스턴스가 있으면 제거
            if (this.sortableInstance) {
                try {
                    this.sortableInstance.destroy();
                } catch(e) {
                    // console.warn('Error destroying chart sortable:', e);
                }
                this.sortableInstance = null;
            }
            
            const livewireId = chartContainer.dataset.livewireId;
            if (!livewireId || !window.Sortable) return;
            
            this.sortableInstance = new window.Sortable(chartContainer, {
                animation: 150,
                handle: '.chart-drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: (evt) => {
                    const items = Array.from(chartContainer.querySelectorAll('[data-stock-id]'));
                    const newOrder = items.map(item => parseInt(item.dataset.stockId));
                    this.updateStockOrder(newOrder, livewireId);
                }
            });
            
            // console.log('Chart sortable initialized');
        },
        
        updateStockOrder(newOrder, livewireId) {
            // console.log('Order changed:', newOrder);
            
            // Livewire 3 방식으로 호출
            if (window.Livewire && livewireId) {
                const component = window.Livewire.find(livewireId);
                if (component) {
                    // console.log('Calling updateStockOrder on component:', livewireId);
                    component.call('updateStockOrder', newOrder);
                } else {
                    // console.warn('Livewire component not found:', livewireId);
                    // 폴백: Livewire를 통해 직접 호출 시도
                    window.Livewire.dispatch('update-stock-order', { order: newOrder });
                }
            } else {
                // console.error('Livewire not available or livewire-id missing');
            }
        },
        
        init() {
            // 초기 그리드 설정 (지연 실행)
            setTimeout(() => {
                this.$nextTick(() => { this.updateCols(); });
            }, 100);
            
            // Sortable 초기화
            setTimeout(() => {
                this.$nextTick(() => {
                    this.initChartSortable();
                });
            }, 200);
            
            // 컴포넌트가 파괴될 때 정리
            this.$el.addEventListener('livewire:before-destroy', () => {
                if (this.sortableInstance) {
                    try {
                        this.sortableInstance.destroy();
                    } catch(e) {
                        // console.warn('Error destroying sortable on cleanup:', e);
                    }
                    this.sortableInstance = null;
                }
            });
            
            // Livewire 업데이트 감지 - Sortable 재초기화
            document.addEventListener('livewire:updated', () => {
                this.$nextTick(() => {
                    this.updateCols();
                    // Livewire 업데이트 후 Sortable 재초기화
                    setTimeout(() => {
                        this.initChartSortable();
                    }, 100);
                });
            });
        }
    }));    
   
    // 토글 가능한 드롭다운 컴포넌트
    Alpine.data('dropdown', () => ({
        isOpen: false,
        
        toggle() {
            this.isOpen = !this.isOpen;
        },
        
        close() {
            this.isOpen = false;
        },
        
        init() {
            // 외부 클릭 시 닫기
            this.$watch('isOpen', (value) => {
                if (value) {
                    this.$nextTick(() => {
                        document.addEventListener('click', this.closeOnClickOutside.bind(this));
                    });
                } else {
                    document.removeEventListener('click', this.closeOnClickOutside.bind(this));
                }
            });
        },
        
        closeOnClickOutside(event) {
            if (!this.$el.contains(event.target)) {
                this.close();
            }
        }
    }));

    // 탭 컴포넌트
    Alpine.data('tabs', (defaultTab = null) => ({
        activeTab: defaultTab,
        
        setTab(tabName) {
            this.activeTab = tabName;
        },
        
        isActive(tabName) {
            return this.activeTab === tabName;
        }
    }));

});
