<div>
    <x-laravel-admin::admin.draggable-modal
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
                    <div class="text-[#111111] dark:text-gray-100">
                        <div class="border-t border-[#8d8d8d] pt-5">
                            <h3 class="text-[20px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ $role->name }}</h3>
                            <div class="mt-5 space-y-3 text-[14px]">
                                <div class="flex">
                                    <span class="w-[116px] shrink-0 pr-4 text-right font-bold">{{ __('생성일') }} :</span>
                                    <span>{{ $role->created_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[116px] shrink-0 pr-4 text-right font-bold">{{ __('수정일') }} :</span>
                                    <span>{{ $role->updated_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-[116px] shrink-0 pr-4 text-right font-bold">{{ __('권한 수') }} :</span>
                                    <span>{{ $role->permissions->count() }}개</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-[#8d8d8d] pt-5">
                            <h3 class="text-[15px] font-bold text-[#111111] dark:text-gray-100">할당된 권한</h3>
                            @if($role->permissions->count() > 0)
                                <div class="mt-3 max-h-56 overflow-y-auto pr-1">
                                    <div class="flex flex-wrap gap-x-3 gap-y-2 text-[14px] font-bold">
                                    @foreach($role->permissions as $permission)
                                        <span>{{ $permission->name }}</span>
                                    @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="mt-3 text-[14px] text-gray-500 dark:text-gray-400">할당된 권한이 없습니다.</p>
                            @endif
                        </div>
                    </div>

                    <!-- 액션 버튼 -->
                    <div class="flex flex-wrap justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                        @can('update', $role)
                        <x-laravel-admin::admin.danger-button type="button" wire:click="openEditModal">
                            모달창에서 수정하기
                        </x-laravel-admin::admin.danger-button>
                        <x-laravel-admin::admin.primary-button type="button" wire:click="editRole">
                            수정
                        </x-laravel-admin::admin.primary-button>
                        @endcan
                        <x-laravel-admin::admin.secondary-button type="button" @click="$dispatch('close-modal', { modalId: 'role-detail-modal' })">닫기</x-laravel-admin::admin.secondary-button>

                    </div>
                @endif
            </div>
        </x-laravel-admin::admin.draggable-modal>

        <!-- 자식 모달: 역할 수정 폼 -->
        <x-laravel-admin::admin.draggable-modal
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
        </x-laravel-admin::admin.draggable-modal>
</div>
