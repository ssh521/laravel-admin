<div>
    <x-laravel-admin::admin.draggable-modal id="user-detail-modal"
        x-on:closed-modal.window="if ($event.detail.modalId === 'user-detail-modal') $wire.resetUser()"
        title="사용자 상세 정보" :show-close-button="true" :show-resize-handle="true" :close-on-escape="true"
        :close-on-backdrop-click="false" :width="500" :height="480">

        <div class="px-2 py-2">
            @if($user)
            <div class="space-y-2">
                <!-- 기본 정보 -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">

                    <div class="flex items-center justify-between mb-3">

                        @can('view', $user)
                        <button type="button" wire:click="openUserDetailModal"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            기본 정보
                        </button>
                        @else
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                            기본 정보
                        </span>
                        @endcan

                        @can('update', $user)
                        <button type="button" wire:click="openUserEditModal"
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">
                            수정
                        </button>
                        @endcan
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">이름</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">이메일</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- 계정 상태 -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">계정 상태</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">이메일 인증:</span>

                            @can('update', $user)
                            <button type="button"
                                wire:click="setEmailVerified({{ $user->id }}, {{ $user->email_verified_at ? 0 : 1 }})"
                                wire:loading.attr="disabled" wire:target="setEmailVerified"
                                wire:loading.class="opacity-60 cursor-not-allowed" class="inline-flex items-center gap-2 rounded-md px-1 py-1 transition
                                                hover:bg-gray-100 dark:hover:bg-gray-700" role="switch"
                                aria-checked="{{ $user->email_verified_at ? 'true' : 'false' }}"
                                title="클릭하여 이메일 인증 상태 변경">
                                <span
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 items-center rounded-full transition-colors
                                                    {{ $user->email_verified_at ? 'bg-green-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition
                                                        {{ $user->email_verified_at ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                </span>
                                <span
                                    class="text-xs {{ $user->email_verified_at ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                                    {{ $user->email_verified_at ? '인증됨' : '미인증' }}
                                </span>
                            </button>
                            @else
                            @if($user->email_verified_at)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                인증됨
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                미인증
                            </span>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- 액션 버튼 -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                @can('update', $user)
                <x-laravel-admin::admin.primary-button type="button" wire:click="editUser" >
                    수정
                </x-laravel-admin::admin.primary-button>
                @endcan
                <x-laravel-admin::admin.secondary-button type="button" @click="$dispatch('close-modal', { modalId: 'user-detail-modal' })">닫기
                </x-laravel-admin::admin.secondary-button>

            </div>
            @endif
        </div>
    </x-laravel-admin::admin.draggable-modal>

    <!-- 자식 모달: 사용자 수정 폼 -->
    <x-laravel-admin::admin.draggable-modal id="user-edit-modal" title="사용자 수정" :show-close-button="true" :show-resize-handle="true"
        :close-on-escape="true" :close-on-backdrop-click="false" :width="500" :height="400">
        <div class="px-2 py-2">
            <livewire:admin.users.user-form />
        </div>
    </x-laravel-admin::admin.draggable-modal>
</div>
