<div>
    <x-laravel-admin::draggable-modal
        id="role-detail-modal"
        x-on:closed-modal.window="if ($event.detail.modalId === 'role-detail-modal') $wire.resetRole()"
        title="역할 상세 정보"
        :show-close-button="true"
        :show-resize-handle="true"
        :close-on-escape="true"
        :close-on-backdrop-click="true"
        :width="500"
        :height="480">

        <div class="px-2 py-2">
                @if($role)
                    <div class="space-y-2">
                        <!-- 기본 정보 -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">기본 정보</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">역할 이름</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">생성일</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">마지막 업데이트</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $role->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- 권한 정보 -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">할당된 권한</h3>
                            @if($role->permissions->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($role->permissions as $permission)
                                        <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">할당된 권한이 없습니다.</p>
                            @endif
                        </div>
                    </div>

                    <!-- 액션 버튼 -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                        @can('update', $role)
                        <x-laravel-admin::danger-button type="button" wire:click="openEditModal">
                            모달창에서 수정하기
                        </x-laravel-admin::danger-button>
                        <x-laravel-admin::primary-button type="button" wire:click="editRole">
                            수정
                        </x-laravel-admin::primary-button>
                        @endcan
                        <x-laravel-admin::secondary-button type="button" @click="$dispatch('close-modal', { modalId: 'role-detail-modal' })">닫기</x-laravel-admin::secondary-button>

                    </div>
                @endif
            </div>
        </x-laravel-admin::draggable-modal>

        <!-- 자식 모달: 역할 수정 폼 -->
        <x-laravel-admin::draggable-modal
            id="role-edit-modal"
            x-on:closed-modal.window="if ($event.detail.modalId === 'role-edit-modal') $wire.resetEditForm()"
            title="역할 수정"
            :show-close-button="true"
            :show-resize-handle="true"
            :close-on-escape="true"
            :close-on-backdrop-click="true"
            :width="500"
            :height="600">
            <div class="px-2 py-2">
                <livewire:admin.roles.role-form parent-modal-id="role-edit-modal" />
            </div>
        </x-laravel-admin::draggable-modal>
</div>
