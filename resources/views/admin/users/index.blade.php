{{-- 사용자 관리 페이지 - 회원 목록 조회, 검색, 상세 모달 기능 --}}
<x-laravel-admin::admin.layouts.admin title="사용자 관리">
    {{-- 페이지 헤더 --}}
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.users.select-user') }}">사용자 선택(모달폼 예제)</a>
            </x-slot>
            <x-slot name="description">
                {{ __('User List') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    @php
        $users = $data;
        $search = request('search');
    @endphp

    {{-- 메인 컨텐츠 영역 --}}
    <div class="w-full bg-white border border-[#d8d8d0] px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[560px] border border-[#d9d9d9] bg-white px-6 py-7 sm:px-10 sm:py-10 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-2">
                <h1 class="text-[26px] font-bold leading-none text-[#222222] dark:text-gray-100">
                    {{ __('회원 리스트') }}
                    @if($search)
                        <span class="text-[16px] font-normal text-gray-600 dark:text-gray-400">
                            - "{{ $search }}" 검색 결과
                        </span>
                    @endif
                </h1>

                <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-base font-semibold">
                    @can('viewAny', Ssh521\LaravelAdmin\Models\User::class)
                        <a href="{{ route('admin.users.index') }}">{{ __('목록보기') }}</a>
                        <span class="text-[#222222] dark:text-gray-400">|</span>
                    @endcan

                    @can('create', Ssh521\LaravelAdmin\Models\User::class)
                        <a href="{{ route('admin.users.create') }}">{{ __('등록하기') }}</a>
                    @endcan
                </div>
            </div>

            <x-laravel-admin::admin.session-messages />

            <form class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end" action="{{ route('admin.users.index') }}" method="GET">
                <label for="user-search" class="sr-only">Search</label>
                <input
                    id="user-search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    class="admin-focus-border h-[28px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 text-base text-[#111111] outline-none sm:w-[260px] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="{{ __('사용자 검색') }}"
                    onfocus="this.style.borderColor='#005fcc'; this.style.boxShadow='0 0 0 1px #005fcc';"
                    onblur="this.style.borderColor='#7d7d7d'; this.style.boxShadow='none';"
                >

                @if($search)
                    <a href="{{ route('admin.users.index') }}" class="inline-flex h-[28px] items-center rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-3 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                        {{ __('초기화') }}
                    </a>
                @endif

                <button type="submit" class="h-[28px] cursor-pointer rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-4 text-base font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                    {{ __('검색') }}
                </button>
            </form>

            {{-- 사용자 목록 테이블 --}}
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-base text-[#111111] dark:text-gray-100">
                    <thead>
                        <tr class="border-y border-[#cfcfcf] bg-[#dedede] text-[#555555] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">Name</th>
                            <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap">E-mail</th>
                            <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap hidden sm:table-cell">이메일 인증</th>
                            <th scope="col" class="h-10 px-2 text-left font-bold whitespace-nowrap hidden sm:table-cell">가입일</th>
                            <th scope="col" class="h-10 px-2 text-right font-bold whitespace-nowrap">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] transition-colors hover:bg-white dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                                <th scope="row" class="h-10 whitespace-nowrap px-4 text-left font-bold">
                                    @can('view', $user)
                                        <x-laravel-admin::admin.modal-trigger
                                            text="{{ $user->name }}"
                                            modal-id="user-detail-modal"
                                            variant="primary"
                                            type="link"
                                            data-user-id="{{ $user->id }}"
                                            modal-type="single"
                                        />
                                    @else
                                        {{ $user->name }}
                                    @endcan
                                </th>
                                <td class="h-10 whitespace-nowrap px-4">
                                    @can('view', $user)
                                        <a href="{{ route('admin.users.show', $user->id) }}">{{ $user->email }}</a>
                                    @else
                                        {{ $user->email }}
                                    @endcan
                                </td>
                                <td class="h-10 whitespace-nowrap px-4 hidden sm:table-cell">
                                    @if($user->email_verified_at)
                                        <span>{{ __('인증됨') }}</span>
                                    @else
                                        <span>{{ __('미인증') }}</span>
                                    @endif
                                </td>
                                <td class="h-10 whitespace-nowrap px-4 hidden sm:table-cell">
                                    {{ $user->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="h-10 whitespace-nowrap px-4 text-right">
                                    @can('update', $user)
                                        <a href="{{ route('admin.users.edit', $user->id) }}">{{ __('Edit') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b border-[#e6e6e6] bg-[#fbfbfb] dark:border-gray-700 dark:bg-gray-800">
                                <td colspan="5" class="h-10 px-4 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                        {{ __('":keyword"에 대한 검색 결과가 없습니다.', ['keyword' => $search]) }}
                                    @else
                                        {{ __('등록된 사용자가 없습니다.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-sm">
                {!! $users->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>

    {{-- 사용자 상세/수정 모달 (Livewire 컴포넌트) --}}
    <livewire:admin.users.user-detail-modal />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-user-id]').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');

                    if (userId) {
                        Livewire.dispatch('admin-users:user-detail-modal:open', {
                            userId: parseInt(userId)
                        });
                    }
                });
            });

            Livewire.on('admin-users:user:saved', () => {
                window.location.reload();
            });

        });
    </script>
</x-laravel-admin::admin.layouts.admin>
