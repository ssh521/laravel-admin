<div>
    @if($showModal)
        @if($parentModalId)
            {{-- draggable-modal 안에 있을 때는 자체 모달 UI 사용 안 함 --}}
            <div class="px-2 py-2">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    @if($mode === 'edit')
                        사용자 역할 관리
                    @else
                        사용자 역할 조회
                    @endif
                </h2>
        @else
            {{-- 독립적으로 사용될 때는 자체 모달 UI 사용 --}}
            <div class="fixed inset-0 z-[9999] flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
                <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @if($mode === 'edit')
                            사용자 역할 관리
                        @else
                            사용자 역할 조회
                        @endif
                    </h2>
        @endif

                <div class="space-y-4">
                @if($user)
                    <!-- 사용자 정보 -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            @if($user->profile_photo_path)
                                <img src="{{ $user->profile_photo_url }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-12 h-12 rounded-full">
                            @else
                                <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 dark:text-gray-300 font-medium text-lg">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- 현재 역할 표시 -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">현재 역할</h5>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->roles as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-gray-500 dark:text-gray-400 text-sm">할당된 역할이 없습니다.</span>
                            @endforelse
                        </div>
                    </div>

                    @if($mode === 'edit')
                        <!-- 역할 선택 섹션 -->
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">역할 선택</h5>
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @forelse($availableRoles as $role)
                                    <label class="flex items-center space-x-3 p-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                        <input type="checkbox" 
                                               value="{{ $role->id }}"
                                               wire:click="toggleRole({{ $role->id }})"
                                               @if(in_array($role->id, $selectedRoles)) checked @endif
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $role->name }}</div>
                                            @if($role->permissions->count() > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $role->permissions->count() }}개의 권한
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        <p class="mt-2">사용 가능한 역할이 없습니다.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                @endif
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300" wire:click.stop="close">취소</button>
                    @if($mode === 'edit')
                        <button class="px-4 py-2 rounded bg-blue-600 text-white" wire:click.stop="save">저장</button>
                    @endif
                </div>
            @if($parentModalId)
                </div>
            @else
                </div>
            </div>
            @endif
    @endif

    <!-- 성공 메시지 -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('message') }}
        </div>
    @endif
</div>
