<div>
    {{-- 모달 내용 --}}
    @if($showModal)
        <div class="p-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    [{{ $categoryName }}] 권한 관리
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    메뉴 카테고리에 허용할 역할을 선택합니다.
                </p>
            </div>

            @if($isLoading)
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">로딩 중...</p>
                </div>
            @else
                <div class="space-y-3">
                    @forelse($availableRoles as $role)
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="role-{{ $role['id'] }}" 
                                wire:model="selectedRoles"
                                value="{{ $role['id'] }}"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <label for="role-{{ $role['id'] }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $role['name'] }}
                            </label>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">사용 가능한 역할이 없습니다.</p>
                    @endforelse
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <x-laravel-admin::admin.secondary-button
                        type="button"
                        wire:click="close">
                        취소
                    </x-laravel-admin::admin.secondary-button>
                    <x-laravel-admin::admin.primary-button
                        type="button"
                        wire:click="save">
                        저장
                    </x-laravel-admin::admin.primary-button>
                </div>
            @endif
        </div>
    @endif
</div>
