<x-laravel-admin::admin.layouts.admin title="부모창-자식창 데이터 전달 테스트">

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">부모창-자식창 데이터 전달 테스트</h1>

        <!-- 부모창 입력폼 -->
        <div x-data="userForm()" class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4">사용자 정보 입력</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">사용자 ID</label>
                    <input type="text"
                           id="user_id"
                           name="user_id"
                           placeholder="ID"
                           x-model="formUserId"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="user_name" class="block text-sm font-medium text-gray-700 mb-1">이름</label>
                    <input type="text"
                           id="user_name"
                           name="user_name"
                           placeholder="이름"
                           x-model="formUserName"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="user_email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
                    <input type="email"
                           id="user_email"
                           name="user_email"
                           placeholder="이메일"
                           x-model="formUserEmail"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- 사용자 검색 버튼 -->
            <div class="flex gap-4">
                <button type="button"
                        @click="openUserSearchModal()"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    사용자 검색
                </button>

                <button type="button"
                        @click="clearForm()"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    폼 초기화
                </button>
            </div>

            <!-- 선택된 사용자 정보 표시 -->
            <div x-show="selectedUser" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                <h3 class="text-sm font-medium text-green-800 mb-2">선택된 사용자 정보:</h3>
                <div x-text="selectedUser ? `ID: ${selectedUser.userId}, 이름: ${selectedUser.userName}, 이메일: ${selectedUser.userEmail}` : ''"></div>
            </div>
        </div>

        <!-- 사용자 검색 모달 -->
        <x-laravel-admin::admin.draggable-modal
            id="user-search-modal"
            title="사용자 검색"
            width="800"
            height="550">
            <livewire:admin.users.user-search-modal
                :modal-id="'user-search-modal'"
            />
        </x-laravel-admin::admin.draggable-modal>
    </div>

    <script>
        function userForm() {
            return {
                selectedUser: null,
                formUserId: '',
                formUserName: '',
                formUserEmail: '',

                openUserSearchModal() {
                    Livewire.dispatch('admin-users:user-search-modal:prefill', { userId: this.formUserId || '', userName: this.formUserName || '', userEmail: this.formUserEmail || '' });

                    this.$dispatch('open-modal', { modalId: 'user-search-modal' });

                    // 다른 형식으로 모달 열기
                    //window.dispatchEvent(new CustomEvent('open-modal', { detail: { modalId: 'user-search-modal' } }));
                },


                init() {
                    // 사용자 선택 이벤트 리스너
                    window.addEventListener('user-selected', (event) => {
                        this.fillUser(event.detail);
                    });
                },

                fillUser(data) {
                    //console.log('부모창에서 사용자 데이터 수신:', data);

                    // 폼 필드에 값 설정
                    this.formUserId = data.userId;
                    this.formUserName = data.userName;
                    this.formUserEmail = data.userEmail;

                    // 선택된 사용자 정보 저장
                    this.selectedUser = data;

                    // 모달 닫기
                    this.$dispatch('close-modal', { modalId: 'user-search-modal' });
                },

                clearForm() {
                    // 폼 초기화
                    this.formUserId = '';
                    this.formUserName = '';
                    this.formUserEmail = '';

                    // 선택된 사용자 정보 초기화
                    this.selectedUser = null;

                    console.log('폼이 초기화되었습니다.');
                }
            }
        }
    </script>
</x-laravel-admin::admin.layouts.admin>