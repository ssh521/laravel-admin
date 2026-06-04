<div>
    @if($showModal)
        @if($parentModalId)
            {{-- draggable-modal 안에 있을 때는 자체 모달 UI 사용 안 함 --}}
            <div class="p-4 text-[#111111] dark:text-gray-100">
                <h2 class="text-[20px] font-bold text-[#222222] dark:text-gray-100">
                    {{ $mode === 'create' ? '새 역할 추가' : ($mode === 'view' ? '역할 보기' : '역할 편집') }}
                </h2>
                <p class="mt-1 mb-4 text-[13px] text-gray-500 dark:text-gray-400">역할 이름과 연결할 권한을 선택합니다.</p>
        @else
            {{-- 독립적으로 사용될 때는 자체 모달 UI 사용 --}}
            <div class="fixed inset-0 z-40 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
                <div class="relative z-50 w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-[20px] font-bold text-[#222222] dark:text-gray-100">
                        {{ $mode === 'create' ? '새 역할 추가' : ($mode === 'view' ? '역할 보기' : '역할 편집') }}
                    </h2>
                    <p class="mt-1 mb-4 text-[13px] text-gray-500 dark:text-gray-400">역할 이름과 연결할 권한을 선택합니다.</p>
        @endif

                <div class="space-y-5 border-t border-[#8d8d8d] pt-5">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start">
                        <label class="w-[96px] shrink-0 pr-4 text-right text-[15px] font-bold leading-[32px] text-[#111111] dark:text-gray-100">역할 이름 :</label>
                        <div class="w-full">
                        <input type="text" class="admin-focus-border h-[32px] w-full rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-3 text-[14px] font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500" wire:model.defer="name" @if($mode==='view') disabled @endif>
                        @error('name')
                            <p class="mt-1 text-[13px] text-red-600">{{ $message }}</p>
                        @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start">
                        <span class="w-[96px] shrink-0 pr-4 text-right text-[15px] font-bold leading-[32px] text-[#111111] dark:text-gray-100">권한 :</span>
                        <div class="grid max-h-60 w-full grid-cols-1 gap-x-6 gap-y-3 overflow-y-auto pt-1 sm:grid-cols-2">
                            @forelse($availablePermissions as $permission)
                                <label for="permission-{{ $permission['id'] }}" class="flex items-center gap-2 text-[14px] font-bold">
                                    <input 
                                        type="checkbox" 
                                        id="permission-{{ $permission['id'] }}" 
                                        wire:model.defer="permissionIds"
                                        value="{{ $permission['id'] }}"
                                        @if($mode==='view') disabled @endif
                                        class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                    <span>{{ $permission['name'] }}</span>
                                </label>
                            @empty
                                <p class="text-[14px] text-gray-500 dark:text-gray-400">사용 가능한 권한이 없습니다.</p>
                            @endforelse
                        @error('permissionIds')
                            <p class="col-span-full mt-1 text-[13px] text-red-600">{{ $message }}</p>
                        @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-7 flex justify-center gap-2 border-t border-[#8d8d8d] pt-6">
                    <x-laravel-admin::admin.secondary-button type="button" class="min-w-[100px] justify-center" wire:click.stop="close">
                        취소
                    </x-laravel-admin::admin.secondary-button>
                    @if($mode!=='view')
                        <x-laravel-admin::admin.primary-button type="button" class="min-w-[100px] justify-center" wire:click.stop="save">
                            저장
                        </x-laravel-admin::admin.primary-button>
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
