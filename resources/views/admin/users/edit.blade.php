<x-layouts.admin title="사용자 수정">
    <x-slot name="header">
        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                @can('viewAny', Ssh521\LaravelAdmin\Models\User::class)
                - <a href="{{ route('admin.users.index') }}">회원 리스트</a>
                @endcan

            </x-slot>
            <x-slot name="description">
                {{ __('Edit User') }}
            </x-slot>
        </x-admin-header>
    </x-slot>

    <div class="w-full md:max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl p-6">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium">
                        {{ __('User Information') }}
                    </h3>
                    <p class="mt-1 text-sm">
                        {{ __('사용자 정보를 수정합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800">
                        ← {{ __('Back') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Field -->
                    <div>
                        <x-label for="name" :value="__('Name')" />
                        <x-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $user->name)" placeholder="Enter user name" autocomplete="name" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Email Field -->
                    <div>
                        <x-label for="email" :value="__('Email')" />
                        <x-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email', $user->email)" placeholder="Enter email address"
                            autocomplete="email" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('email')" />
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password Field -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative">
                            <x-input id="password" name="password" type="password" class="mt-1 block w-full pr-10"
                                placeholder="Enter new password (leave blank to keep current)"
                                autocomplete="new-password" />
                            <button type="button" onclick="togglePasswordVisibility('password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error-message class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <x-input-label for="confirm-password" :value="__('Confirm Password')" />
                        <div class="relative">
                            <x-input id="confirm-password" name="confirm-password" type="password"
                                class="mt-1 block w-full pr-10" placeholder="Confirm new password"
                                autocomplete="new-password" />
                            <button type="button" onclick="togglePasswordVisibility('confirm-password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error-message class="mt-2" :messages="$errors->get('confirm-password')" />
                    </div>
                </div>

                <!-- Email Verification Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label :value="__('Email Verification Status')" />
                        <div class="mt-2 flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="email_verified" name="email_verified" type="checkbox" value="1" {{
                                    old('email_verified', $user->email_verified_at ? '1' : null) == '1' ? 'checked' : ''
                                }}
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                                dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700
                                dark:border-gray-600">
                                <label for="email_verified" class="ms-2 text-sm font-medium">
                                    {{ __('Email Verified') }}
                                </label>
                            </div>
                            @if($user->email_verified_at)
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Verified on') }}: {{ $user->email_verified_at->format('Y-m-d H:i:s') }}
                            </span>
                            @else
                            <span class="text-sm text-red-600 dark:text-red-400">
                                {{ __('Not Verified') }}
                            </span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs">
                            {{ __('체크박스를 선택하면 이메일 인증 상태로 변경됩니다.') }}
                        </p>
                    </div>
                </div>

                <x-section-border />

                <!-- Roles Section -->
                @can('assignRoles', $user)
                <div>
                    <h4 class="text-md font-medium mb-4">{{ __('Assign Roles') }}</h4>
                    <x-input-label for="roles_multiple" :value="__('Select Roles')" />
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($roles as $role)
                        <div class="flex items-center">
                            <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}" {{
                                $user->roles->contains($role->id) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                            focus:ring-2">
                            <label for="role_{{ $role->id }}" class="ms-2 text-sm font-medium">
                                {{ $role->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <x-input-error-message class="mt-2" :messages="$errors->get('roles')" />
                </div>
                @else
                <div>
                    <h4 class="text-md font-medium mb-4">{{ __('Current Roles') }}</h4>
                    <div
                        class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                        @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                        <span
                            class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded mr-2 mb-1">{{
                            $role->name }}</span>
                        @endforeach
                        @else
                        <span class="text-gray-500 dark:text-gray-400">역할이 할당되지 않음</span>
                        @endif
                    </div>
                </div>
                @endcan

                <x-section-border />

                <!-- Permissions Section -->
                @can('assignPermissions', $user)
                <div>
                    <h4 class="text-md font-medium mb-4">{{ __('Assign Permissions') }}</h4>

                    <!-- 전체 선택/해제 체크박스 -->
                    <div
                        class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center">
                            <input id="select-all-permissions" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                onchange="toggleAllPermissions(this)">
                            <label for="select-all-permissions" class="ms-2 text-sm font-medium cursor-pointer">
                                전체 선택/해제
                            </label>
                        </div>
                    </div>

                    <x-input-label for="Permissions-checkbox" :value="__('Select Permissions')" />
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($permissions as $permission)
                        <div class="flex items-center">
                            <input id="permission_{{ $permission->id }}" name="permissions[]" type="checkbox"
                                value="{{ $permission->id }}" {{ $user->permissions->contains($permission->id) ?
                            'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                            focus:ring-2">
                            <label for="permission_{{ $permission->id }}" class="ms-2 text-sm font-medium">
                                {{ $permission->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div>
                    <h4 class="text-md font-medium mb-4">{{ __('Current Permissions') }}</h4>
                    <div
                        class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                        @if($user->permissions->count() > 0)
                        @foreach($user->permissions as $permission)
                        <span
                            class="inline-block bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-2 py-1 rounded mr-2 mb-1">{{
                            $permission->name }}</span>
                        @endforeach
                        @else
                        <span class="text-gray-500 dark:text-gray-400">권한이 할당되지 않음</span>
                        @endif
                    </div>
                </div>
                @endcan


                <div class="flex items-center justify-end mt-6">
                    <x-primary-button>
                        {{ __('Update User') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6">
                @if($user->id != auth()->user()->id)
                @can('delete', $user)
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        onclick="return confirm('정말 삭제하시겠습니까?')">{{ __('Delete') }}</button>
                </form>
                @endcan
                @endif
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            var passwordInput = document.getElementById(fieldId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }

        function toggleAllPermissions(selectAllCheckbox) {
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            const isChecked = selectAllCheckbox.checked;
            
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        }

        // 페이지 로드 시 전체 선택 체크박스 상태 업데이트
        document.addEventListener('DOMContentLoaded', function() {
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            const selectAllCheckbox = document.getElementById('select-all-permissions');
            
            if (selectAllCheckbox) {
                // 개별 체크박스 변경 시 전체 선택 상태 업데이트
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedCount = document.querySelectorAll('input[name="permissions[]"]:checked').length;
                        const totalCount = permissionCheckboxes.length;
                        
                        if (checkedCount === 0) {
                            selectAllCheckbox.checked = false;
                            selectAllCheckbox.indeterminate = false;
                        } else if (checkedCount === totalCount) {
                            selectAllCheckbox.checked = true;
                            selectAllCheckbox.indeterminate = false;
                        } else {
                            selectAllCheckbox.checked = false;
                            selectAllCheckbox.indeterminate = true;
                        }
                    });
                });
                
                // 초기 상태 설정
                const checkedCount = document.querySelectorAll('input[name="permissions[]"]:checked').length;
                const totalCount = permissionCheckboxes.length;
                
                if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === totalCount) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }
        });
    </script>
</x-layouts.admin>