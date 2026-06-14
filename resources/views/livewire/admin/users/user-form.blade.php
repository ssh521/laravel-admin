<div>
    @if($showModal)
    @if($parentModalId)
    {{-- draggable-modal 안에 있을 때는 자체 모달 UI 사용 안 함 --}}
    <div class="px-2 py-2">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ $mode === 'create' ? '새 사용자 추가' : ($mode === 'view' ? '사용자 보기' : '사용자 편집') }}
        </h2>
        <button wire:click="$parent.showUserDetail({{ $userId }})">사용자 상세 정보</button>
        @else
        {{-- 독립적으로 사용될 때는 자체 모달 UI 사용 --}}
        <div class="fixed inset-0 z-[9999] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $mode === 'create' ? '새 사용자 추가' : ($mode === 'view' ? '사용자 보기' : '사용자 편집') }}
                </h2>
                @endif

                <div class="space-y-4">
                    @if($user && $mode === 'view')

                    <!-- 사용자 정보 -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">

                        <div class="flex items-center space-x-4">
                            @if($user->profile_photo_path)
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                class="w-12 h-12 rounded-full">
                            @else
                            <div
                                class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
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

                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">가입일</dt>
                                <dd class="text-sm text-gray-900 dark:text-white tabular-nums">
                                    {{ $user->created_at?->format('Y-m-d H:i:s') }}
                                </dd>
                            </div>

                            <div class="flex flex-col gap-1">
                                <dt class="text-sm font-medium text-gray-700 dark:text-gray-300">마지막 업데이트</dt>
                                <dd class="text-sm text-gray-900 dark:text-white tabular-nums">
                                    {{ $user->updated_at?->format('Y-m-d H:i:s') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @endif

                    @if($mode === 'edit')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">이름</label>
                        <input type="text"
                            class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"
                            wire:model.defer="name">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">이메일</label>
                        <input type="email"
                            class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"
                            wire:model.defer="email">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">비밀번호 @if($mode
                            === 'edit')<span class="text-xs text-gray-500">(수정 시에만 입력)</span>@endif</label>
                        <input type="password"
                            class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"
                            wire:model.defer="password">
                        @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    @if($mode!=='view')
                    <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                        wire:click.stop="save">저장</button>
                    @endif
                    <x-laravel-admin::admin.secondary-button type="button"
                        @click="$dispatch('close-modal', { modalId: '{{ $parentModalId }}' })">닫기</x-laravel-admin::admin.secondary-button>
                </div>
                @endif
            </div>
