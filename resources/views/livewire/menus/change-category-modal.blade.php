<div class="p-4">
    <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
        <p class="text-sm text-blue-700 dark:text-blue-300">
            {{ count($selectedMenuIds) }}개의 메뉴가 선택되었습니다.
        </p>
    </div>

    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">변경할 카테고리를 선택하세요:</label>

        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
            <input type="radio" value="" wire:model="categoryId"
                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">미분류</span>
        </label>

        @foreach($categories as $category)
            <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                <input type="radio" value="{{ $category['id'] }}" wire:model="categoryId"
                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ $category['name'] }}
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $category['count'] }}개 메뉴)</span>
                </span>
            </label>
        @endforeach
    </div>

    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md"
                wire:click="$dispatch('close-modal', { modalId: 'category-selection-modal' })">
            취소
        </button>
        <button type="button"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                wire:click="save"
                @disabled(count($selectedMenuIds) === 0)
        >
            변경
        </button>
    </div>
</div>


