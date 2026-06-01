@props([
    'categories' => [],
    'selectedCategoryId' => null,
    'selectedCategoryName' => null,
    'modalId' => 'category-selector-modal',
    'inputName' => 'category_id',
    'inputId' => 'category_id'
])

<div class="space-y-2">
    {{-- 카테고리 선택 버튼 --}}
    <div class="flex items-center space-x-2">
        <input type="hidden" name="{{ $inputName }}" id="{{ $inputId }}" value="{{ $selectedCategoryId }}">
        
        {{-- 선택된 카테고리 표시 영역 --}}
        <div id="selected-category-display" class="flex-1 min-h-[38px] px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-between">
            <span id="selected-category-text" class="text-sm">
                @if($selectedCategoryName)
                    {{ $selectedCategoryName }}
                @else
                    <span class="text-gray-500 dark:text-gray-400">카테고리를 선택하세요</span>
                @endif
            </span>
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
        
        {{-- 카테고리 선택 버튼 --}}
        <button type="button" 
                id="open-category-selector"
                class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors duration-200">
            선택
        </button>
    </div>
    
    {{-- 선택 해제 버튼 (카테고리가 선택된 경우에만 표시) --}}
    @if($selectedCategoryId)
    <div class="flex justify-end">
        <button type="button" 
                id="clear-category-selection"
                class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            선택 해제
        </button>
    </div>
    @endif
</div>

{{-- 카테고리 선택 모달 --}}
<x-draggable-modal 
    :id="$modalId"
    title="카테고리 선택"
    width="500"
    height="400"
    :close-on-backdrop-click="true"
>
    <div class="p-4">
        {{-- 검색 영역 --}}
        <div class="mb-4">
            <div class="relative">
                <input type="text" 
                       id="category-search"
                       placeholder="카테고리 검색..."
                       class="w-full px-3 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        {{-- 카테고리 목록 --}}
        <div class="max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md">
            <div id="category-list" class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach($categories as $category)
                <div class="category-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150"
                     data-category-id="{{ $category->id }}"
                     data-category-name="{{ $category->name }}"
                     data-category-menu-count="{{ $category->menu_count ?? 0 }}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $category->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                메뉴 {{ $category->menu_count ?? 0 }}개
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    활성
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    비활성
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            {{-- 빈 상태 메시지 --}}
            <div id="no-categories-message" class="p-6 text-center text-gray-500 dark:text-gray-400 hidden">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">카테고리가 없습니다</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">등록된 카테고리가 없습니다.</p>
            </div>
        </div>
        
        {{-- 모달 푸터 --}}
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="button" 
                    id="cancel-category-selection"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors duration-200">
                취소
            </button>
            <button type="button" 
                    id="confirm-category-selection"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                선택
            </button>
        </div>
    </div>
</x-draggable-modal>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalId = '{{ $modalId }}';
    const inputId = '{{ $inputId }}';
    const selectedCategoryDisplay = document.getElementById('selected-category-display');
    const selectedCategoryText = document.getElementById('selected-category-text');
    const openCategorySelector = document.getElementById('open-category-selector');
    const clearCategorySelection = document.getElementById('clear-category-selection');
    const categorySearch = document.getElementById('category-search');
    const categoryList = document.getElementById('category-list');
    const noCategoriesMessage = document.getElementById('no-categories-message');
    const cancelCategorySelection = document.getElementById('cancel-category-selection');
    const confirmCategorySelection = document.getElementById('confirm-category-selection');
    
    let selectedCategory = null;
    let filteredCategories = [];
    
    // 초기 카테고리 목록 설정
    const allCategories = Array.from(document.querySelectorAll('.category-item')).map(item => ({
        id: item.dataset.categoryId,
        name: item.dataset.categoryName,
        menuCount: item.dataset.categoryMenuCount,
        element: item
    }));
    
    filteredCategories = [...allCategories];
    
    // 모달 열기
    openCategorySelector.addEventListener('click', function() {
        window.dispatchEvent(new CustomEvent('open-modal', { 
            detail: { modalId: modalId, action: 'open' } 
        }));
    });
    
    // 카테고리 검색
    categorySearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // 검색어가 없으면 모든 카테고리 표시
            filteredCategories = [...allCategories];
        } else {
            // 검색어에 맞는 카테고리만 필터링
            filteredCategories = allCategories.filter(category => 
                category.name.toLowerCase().includes(searchTerm)
            );
        }
        
        renderCategoryList();
    });
    
    // 카테고리 목록 렌더링
    function renderCategoryList() {
        // 모든 카테고리 숨기기
        allCategories.forEach(category => {
            category.element.style.display = 'none';
        });
        
        if (filteredCategories.length === 0) {
            noCategoriesMessage.classList.remove('hidden');
            categoryList.classList.add('hidden');
        } else {
            noCategoriesMessage.classList.add('hidden');
            categoryList.classList.remove('hidden');
            
            // 필터링된 카테고리만 표시
            filteredCategories.forEach(category => {
                category.element.style.display = 'block';
            });
        }
    }
    
    // 카테고리 선택
    allCategories.forEach(category => {
        category.element.addEventListener('click', function() {
            // 이전 선택 해제
            allCategories.forEach(cat => {
                cat.element.classList.remove('bg-indigo-50', 'dark:bg-indigo-900', 'border-indigo-200', 'dark:border-indigo-700');
                cat.element.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-700');
            });
            
            // 현재 선택 표시
            this.classList.remove('hover:bg-gray-50', 'dark:hover:bg-gray-700');
            this.classList.add('bg-indigo-50', 'dark:bg-indigo-900', 'border-indigo-200', 'dark:border-indigo-700');
            
            selectedCategory = {
                id: this.dataset.categoryId,
                name: this.dataset.categoryName
            };
            
            confirmCategorySelection.disabled = false;
        });
    });
    
    // 카테고리 선택 확인
    confirmCategorySelection.addEventListener('click', function() {
        if (selectedCategory) {
            // 숨겨진 입력 필드에 값 설정
            document.getElementById(inputId).value = selectedCategory.id;
            
            // 선택된 카테고리 표시 업데이트
            selectedCategoryText.textContent = selectedCategory.name;
            selectedCategoryText.classList.remove('text-gray-500', 'dark:text-gray-400');
            selectedCategoryText.classList.add('text-gray-900', 'dark:text-white');
            
            // 선택 해제 버튼 표시
            if (clearCategorySelection) {
                clearCategorySelection.style.display = 'block';
            }
            
            // 모달 닫기
            window.dispatchEvent(new CustomEvent('close-modal', { 
                detail: { modalId: modalId } 
            }));
            
            // 상태 초기화
            selectedCategory = null;
            confirmCategorySelection.disabled = true;
            categorySearch.value = '';
            filteredCategories = [...allCategories];
            renderCategoryList();
        }
    });
    
    // 카테고리 선택 취소
    cancelCategorySelection.addEventListener('click', function() {
        // 모달 닫기
        window.dispatchEvent(new CustomEvent('close-modal', { 
            detail: { modalId: modalId, action: 'close' } 
        }));
        
        // 상태 초기화
        selectedCategory = null;
        confirmCategorySelection.disabled = true;
        categorySearch.value = '';
        filteredCategories = [...allCategories];
        renderCategoryList();
        
        // 선택 상태 초기화
        allCategories.forEach(cat => {
            cat.element.classList.remove('bg-indigo-50', 'dark:bg-indigo-900', 'border-indigo-200', 'dark:border-indigo-700');
            cat.element.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-700');
        });
    });
    
    // 카테고리 선택 해제
    if (clearCategorySelection) {
        clearCategorySelection.addEventListener('click', function() {
            // 숨겨진 입력 필드 값 초기화
            document.getElementById(inputId).value = '';
            
            // 선택된 카테고리 표시 초기화
            selectedCategoryText.innerHTML = '<span class="text-gray-500 dark:text-gray-400">카테고리를 선택하세요</span>';
            selectedCategoryText.classList.remove('text-gray-900', 'dark:text-white');
            selectedCategoryText.classList.add('text-gray-500', 'dark:text-gray-400');
            
            // 선택 해제 버튼 숨기기
            this.style.display = 'none';
        });
    }
    
    // 모달이 열릴 때 검색어 초기화
    document.addEventListener('open-modal', function(event) {
        if (event.detail.modalId === modalId) {
            categorySearch.value = '';
            filteredCategories = [...allCategories];
            renderCategoryList();
            selectedCategory = null;
            confirmCategorySelection.disabled = true;
        }
    });
});
</script>
