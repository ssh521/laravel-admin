// dTree JavaScript 기능
function registerDtreeNode() {
    if (typeof Alpine === 'undefined') return;
    
    const dtreeNodeData = (nodeId) => ({
        isOpen: false,
        
        init() {
            // 페이지 로드 시 저장된 상태 복원
            this.restoreState();
            
            // 상태 변경 시 localStorage에 저장
            this.$watch('isOpen', (value) => {
                this.saveState(value);
            });
        },
        
        toggleNode() {
            this.isOpen = !this.isOpen;
        },
        
        saveState(value) {
            try {
                localStorage.setItem(`dtree-${nodeId}`, value.toString());
                //console.log(`Saved state for ${nodeId}:`, value);
            } catch (error) {
                //console.error('Failed to save tree state:', error);
            }
        },
        
        restoreState() {
            try {
                const savedState = localStorage.getItem(`dtree-${nodeId}`);
                if (savedState !== null) {
                    this.isOpen = savedState === 'true';
                    //console.log(`Restored state for ${nodeId}:`, this.open);
                }
            } catch (error) {
                //console.error('Failed to restore tree state:', error);
            }
        }
    });
    
    // Alpine이 이미 초기화된 경우 즉시 등록
    if (Alpine && Alpine.data) {
        Alpine.data('dtreeNode', dtreeNodeData);
    }
}

// Alpine 초기화 전에 등록 시도
document.addEventListener('alpine:init', () => {
    registerDtreeNode();
});

// Alpine이 이미 로드된 경우 즉시 등록
if (typeof Alpine !== 'undefined' && Alpine.data) {
    registerDtreeNode();
}

// 전역 폴백: Alpine 컴포넌트 등록 타이밍 이슈 방지용
// x-data="dtreeNode('...')" 가 즉시 평가되어도 사용할 수 있도록 보장
window.dtreeNode = function(nodeId) {
    return {
        isOpen: false,
        init() {
            this.restoreState();
            this.$watch('isOpen', (value) => {
                this.saveState(value);
            });
        },
        toggleNode() {
            this.isOpen = !this.isOpen;
        },
        saveState(value) {
            try {
                localStorage.setItem(`dtree-${nodeId}`, value.toString());
            } catch (error) {
            }
        },
        restoreState() {
            try {
                const savedState = localStorage.getItem(`dtree-${nodeId}`);
                if (savedState !== null) {
                    this.isOpen = savedState === 'true';
                }
            } catch (error) {
            }
        }
    };
};

window.toggleAllNodes = function() {
    const header = document.querySelector('.dtree-header');
    const allOpen = header.__x.$data.allOpen;
    
    // 모든 dtree 노드 컴포넌트 찾기
    const nodes = document.querySelectorAll('[x-data*="dtreeNode"]');
    
    nodes.forEach(node => {
        const alpineData = node.__x.$data;
        if (alpineData) {
            alpineData.isOpen = !allOpen;
        }
    });
    
    // 헤더 상태 업데이트
    header.__x.$data.allOpen = !allOpen;
};

// 전체 트리 상태 초기화 함수 (필요시 사용)
window.resetTreeState = function() {
    const keys = Object.keys(localStorage);
    keys.forEach(key => {
        if (key.startsWith('dtree-')) {
            localStorage.removeItem(key);
        }
    });
    location.reload();
};

// 트리 상태 확인 함수 (디버깅용)
window.getTreeState = function() {
    const state = {};
    const keys = Object.keys(localStorage);
    keys.forEach(key => {
        if (key.startsWith('dtree-')) {
            state[key] = localStorage.getItem(key);
        }
    });
    //console.log('Current tree state:', state);
    return state;
};

// 모든 dtree 노드의 상태를 복원하는 함수 (모바일 메뉴 열릴 때 사용)
window.restoreAllDtreeNodes = (function() {
    let timeoutId = null;
    return function() {
        // 중복 호출 방지를 위해 기존 타이머 취소
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        // 약간의 지연을 두어 DOM이 완전히 렌더링된 후 실행
        timeoutId = setTimeout(() => {
            const nodes = document.querySelectorAll('[data-node-id^="category-"]');
            nodes.forEach(node => {
                const alpineData = node.__x?.$data;
                if (alpineData && typeof alpineData.restoreState === 'function') {
                    alpineData.restoreState();
                }
            });
            timeoutId = null;
        }, 150);
    };
})();

