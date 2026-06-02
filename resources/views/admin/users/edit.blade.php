<x-laravel-admin::layouts.admin title="사용자 수정">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
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
        </x-laravel-admin::admin-header>
    </x-slot>

    @php
        $inputClass = 'admin-focus-border h-[32px] rounded-sm border border-transparent border-b-[#777777] bg-[#fafafa] px-3 text-[14px] font-bold text-[#111111] outline-none dark:bg-gray-700 dark:text-white dark:border-b-gray-500';
        $labelClass = 'w-[116px] shrink-0 pr-4 text-right text-[15px] font-bold leading-[32px] text-[#111111] dark:text-gray-100';
        $rowClass = 'flex flex-col gap-1 sm:flex-row sm:items-start';
        $focusIn = "this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';";
        $focusOut = "this.style.borderColor='transparent'; this.style.borderBottomColor='#777777'; this.style.boxShadow='none';";
    @endphp

    <div class="mx-auto w-full max-w-7xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[720px] bg-white px-8 py-10 sm:px-16 lg:px-24 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-10">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('User Information') }}</h1>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mx-auto max-w-[1060px] text-[#111111] dark:text-gray-100">
                    <div class="border-t border-[#8d8d8d] pt-7">
                        <div class="space-y-4">
                            <div class="{{ $rowClass }}">
                                <label for="name" class="{{ $labelClass }}">{{ __('이름') }} :</label>
                                <div class="w-full sm:w-[320px]">
                                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" placeholder="Enter user name" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('name')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="email" class="{{ $labelClass }}">{{ __('E-mail') }} :</label>
                                <div class="w-full sm:w-[520px]">
                                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" placeholder="Enter email address" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('email')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 border-t border-[#8d8d8d] pt-7">
                        <div class="space-y-4">
                            <div class="{{ $rowClass }}">
                                <label for="password" class="{{ $labelClass }}">{{ __('비밀번호') }} :</label>
                                <div class="w-full sm:w-[320px]">
                                    <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Enter new password (leave blank to keep current)" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('password')" />
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <label for="confirm-password" class="{{ $labelClass }}">{{ __('확인') }} :</label>
                                <div class="w-full sm:w-[320px]">
                                    <input id="confirm-password" name="confirm-password" type="password" autocomplete="new-password" placeholder="Confirm new password" class="{{ $inputClass }} w-full" onfocus="{{ $focusIn }}" onblur="{{ $focusOut }}">
                                    <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('confirm-password')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 border-t border-[#8d8d8d] pt-7">
                        <div class="space-y-4">
                            <div class="{{ $rowClass }}">
                                <span class="{{ $labelClass }}">{{ __('인증') }} :</span>
                                <div class="min-h-[32px]">
                                    <label for="email_verified" class="flex min-h-[32px] items-center gap-2 text-[14px] font-bold">
                                        <input id="email_verified" name="email_verified" type="checkbox" value="1" @checked(old('email_verified', $user->email_verified_at ? '1' : null) == '1') class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                        <span>{{ __('Email Verified') }}</span>
                                    </label>
                                    <div class="mt-1 text-[13px] font-bold text-gray-600 dark:text-gray-400">
                                        @if ($user->email_verified_at)
                                            {{ __('Verified on') }}: {{ $user->email_verified_at->format('Y-m-d H:i:s') }}
                                        @else
                                            {{ __('Not Verified') }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="{{ $rowClass }}">
                                <span class="{{ $labelClass }}">{{ __('역할') }} :</span>
                                <div class="min-h-[32px] w-full max-w-[720px] pt-1">
                                    @can('assignRoles', $user)
                                        <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($roles as $role)
                                                <label for="role_{{ $role->id }}" class="flex items-center gap-2 text-[14px] font-bold">
                                                    <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}" @checked($user->roles->contains($role->id)) class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                                    <span>{{ $role->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <x-laravel-admin::input-error-message class="mt-2 text-[13px]" :messages="$errors->get('roles')" />
                                    @else
                                        <div class="text-[14px] font-bold">
                                            @forelse ($user->roles as $role)
                                                <span>{{ $role->name }}</span>@if (! $loop->last)<span>, </span>@endif
                                            @empty
                                                <span class="text-gray-500 dark:text-gray-400">역할이 할당되지 않음</span>
                                            @endforelse
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 border-t border-[#8d8d8d] pt-7">
                        <div class="space-y-4">
                            <div class="{{ $rowClass }}">
                                <span class="{{ $labelClass }}">{{ __('권한') }} :</span>
                                <div class="min-h-[32px] w-full max-w-[900px] pt-1">
                                    @can('assignPermissions', $user)
                                        <label for="select-all-permissions" class="mb-4 flex items-center gap-2 text-[14px] font-bold">
                                            <input id="select-all-permissions" type="checkbox" class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]" onchange="toggleAllPermissions(this)">
                                            <span>전체 선택/해제</span>
                                        </label>
                                        <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($permissions as $permission)
                                                <label for="permission_{{ $permission->id }}" class="flex items-center gap-2 text-[14px] font-bold">
                                                    <input id="permission_{{ $permission->id }}" name="permissions[]" type="checkbox" value="{{ $permission->id }}" @checked($user->permissions->contains($permission->id)) class="size-5 border-[#777777] text-[#663601] focus:ring-[#663601]">
                                                    <span>{{ $permission->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-[14px] font-bold">
                                            @forelse ($user->permissions as $permission)
                                                <span>{{ $permission->name }}</span>@if (! $loop->last)<span>, </span>@endif
                                            @empty
                                                <span class="text-gray-500 dark:text-gray-400">권한이 할당되지 않음</span>
                                            @endforelse
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 border-t border-[#8d8d8d] pt-10">
                        <div class="flex justify-center">
                            <button type="submit" class="h-[34px] min-w-[140px] border border-[#777777] bg-[#eeeeee] px-8 text-[15px] font-bold leading-none text-[#111111] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                                {{ __('수정하기') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($user->id != auth()->user()->id)
        @can('delete', $user)
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('정말 삭제하시겠습니까?')" class="mx-auto mt-3 flex w-full max-w-7xl justify-start px-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="cursor-pointer text-[13px] font-semibold text-[#003399] hover:underline dark:text-[#e7e7d6]">
                    {{ __('삭제하기') }}
                </button>
            </form>
        @endcan
    @endif

    <script>
        function toggleAllPermissions(selectAllCheckbox) {
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            const isChecked = selectAllCheckbox.checked;

            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            const selectAllCheckbox = document.getElementById('select-all-permissions');

            if (selectAllCheckbox) {
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
</x-laravel-admin::layouts.admin>
