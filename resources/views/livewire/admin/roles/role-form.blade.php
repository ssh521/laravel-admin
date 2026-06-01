<div>
    @if($showModal)
        @if($parentModalId)
            {{-- draggable-modal 안에 있을 때는 자체 모달 UI 사용 안 함 --}}
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $mode === 'create' ? '새 역할 추가' : ($mode === 'view' ? '역할 보기' : '역할 편집') }}
                </h2>
        @else
            {{-- 독립적으로 사용될 때는 자체 모달 UI 사용 --}}
            <div class="fixed inset-0 z-40 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
                <div class="relative z-50 w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ $mode === 'create' ? '새 역할 추가' : ($mode === 'view' ? '역할 보기' : '역할 편집') }}
                    </h2>
        @endif

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">역할 이름</label>
                        <input type="text" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" wire:model.defer="name" @if($mode==='view') disabled @endif>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">권한</label>
                        <div class="max-h-60 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700">
                            @forelse($availablePermissions as $permission)
                                <div class="flex items-center py-1">
                                    <input 
                                        type="checkbox" 
                                        id="permission-{{ $permission['id'] }}" 
                                        wire:model.defer="permissionIds"
                                        value="{{ $permission['id'] }}"
                                        @if($mode==='view') disabled @endif
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-600 dark:border-gray-500">
                                    <label for="permission-{{ $permission['id'] }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $permission['name'] }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">사용 가능한 권한이 없습니다.</p>
                            @endforelse
                        </div>
                        @error('permissionIds')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300" wire:click.stop="close">취소</button>
                    @if($mode!=='view')
                        <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700" wire:click.stop="save">저장</button>
                    @endif
                </div>
            @if($parentModalId)
                </div>
            @else
                </div>
            </div>
            @endif
    @endif
</div>
