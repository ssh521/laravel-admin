{{-- 체크박스 전체 선택/해제/반전 제어 컴포넌트 --}}
@props([
    'containerClass' => '',       // 컨테이너 추가 CSS 클래스
    'linkClass' => 'text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300', // 링크 CSS 클래스
    'separatorClass' => 'text-gray-500 dark:text-gray-400', // 구분자 CSS 클래스
])

<span class="checkbox-controls {{ $containerClass }}">
    <a href="javascript:void(0)" 
       onclick="selectAllCheckboxes()" 
       class="{{ $linkClass }}">전체</a>
    <span class="{{ $separatorClass }}"> | </span>
    
    <a href="javascript:void(0)" 
       onclick="unselectAllCheckboxes()" 
       class="{{ $linkClass }}">해제</a>
    <span class="{{ $separatorClass }}"> | </span>
    
    <a href="javascript:void(0)" 
       onclick="toggleAllCheckboxes()" 
       class="{{ $linkClass }}">반전</a>
</span>

{{-- 체크박스 제어를 위한 JavaScript 포함 --}}
@once
<script>
    // ==========================================
    // 체크박스 전체 선택/해제/반전 기능
    // ==========================================
    
    /**
     * 모든 체크박스를 선택
     * @param {string} scope - 선택할 범위 (CSS 선택자, 기본값: 전체 페이지)
     */
    function selectAllCheckboxes(scope = '') {
        const selector = scope ? `${scope} input[type="checkbox"]` : 'input[type="checkbox"]';
        const checkboxes = document.querySelectorAll(selector);
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }
    
    /**
     * 모든 체크박스 선택 해제
     * @param {string} scope - 해제할 범위 (CSS 선택자, 기본값: 전체 페이지)
     */
    function unselectAllCheckboxes(scope = '') {
        const selector = scope ? `${scope} input[type="checkbox"]` : 'input[type="checkbox"]';
        const checkboxes = document.querySelectorAll(selector);
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }
    
    /**
     * 모든 체크박스 선택 상태 반전
     * @param {string} scope - 반전할 범위 (CSS 선택자, 기본값: 전체 페이지)
     */
    function toggleAllCheckboxes(scope = '') {
        const selector = scope ? `${scope} input[type="checkbox"]` : 'input[type="checkbox"]';
        const checkboxes = document.querySelectorAll(selector);
        checkboxes.forEach(checkbox => {
            checkbox.checked = !checkbox.checked;
        });
    }
    
    /**
     * 선택된 체크박스의 값들을 배열로 반환
     * @param {string} scope - 확인할 범위 (CSS 선택자, 기본값: 전체 페이지)
     * @returns {Array} 선택된 체크박스의 값들
     */
    function getSelectedCheckboxValues(scope = '') {
        const selector = scope ? `${scope} input[type="checkbox"]:checked` : 'input[type="checkbox"]:checked';
        const checkedBoxes = document.querySelectorAll(selector);
        return Array.from(checkedBoxes).map(checkbox => checkbox.value);
    }
    
    /**
     * 선택된 체크박스의 개수 반환
     * @param {string} scope - 확인할 범위 (CSS 선택자, 기본값: 전체 페이지)
     * @returns {number} 선택된 체크박스의 개수
     */
    function getSelectedCheckboxCount(scope = '') {
        const selector = scope ? `${scope} input[type="checkbox"]:checked` : 'input[type="checkbox"]:checked';
        return document.querySelectorAll(selector).length;
    }
</script>
@endonce
