<div class="p-4">
    <!-- 검색 폼 -->
    <div class="mb-6">
        <div class="flex gap-2">
            <input type="text" wire:model.live="search" placeholder="이름 또는 이메일로 검색..."
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button wire:click="clearSearch" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                초기화
            </button>
        </div>
        <div class="mt-2 text-sm">
            페이지: {{ $users->currentPage() }} / {{ $users->lastPage() }} · 총 {{ $users->total() }}명
        </div>
    </div>



    <!-- 사용자 리스트 -->
    <div class="max-h-96 overflow-y-auto">
        @if($users->count() > 0)
        <div class="space-y-2">
            @foreach($users as $user)
            <div class="p-1 border border-gray-200 rounded-md hover:bg-blue-50 hover:border-blue-300 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-between cursor-pointer"
                        wire:click="selectUser({{ $user->id }})">
                        <div class="text-xs mr-2">{{ $user->id }}</div>
                        <div class="font-medium mr-2">{{ $user->name }}</div>
                        <div class="text-sm">{{ $user->email }}</div>
                    </div>
                    <div class="flex items-center justify-between">

                        <!-- 모달 열기: Livewire 메서드 호출로 통일 (Blade에서 직접 $dispatch 사용 금지) -->

                        
                        @if($canView($user))
                        <div class="text-xs cursor-pointer hover:text-blue-600 mr-2"
                        wire:click="openUserViewModal({{ $user->id }})">
                            보기
                        </div>
                        @endif

                        @if($canEdit($user))
                        <div class="text-xs cursor-pointer hover:text-blue-600 mr-2"
                        wire:click="openUserEditModal({{ $user->id }})">
                            수정
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            @if($search)
            "{{ $search }}"에 대한 검색 결과가 없습니다.
            @else
            등록된 사용자가 없습니다.
            @endif
        </div>
        @endif
    </div>

    <!-- 페이지네이션 -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <livewire:admin.users.user-form />

</div>
