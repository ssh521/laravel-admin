<div class="w-full lg:max-w-7xl mx-auto">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">

        <div class="text-2xl text-left mt-1 font-bold dark:text-white">
            {{ __('공지사항 리스트') }}
            @if($search || $isPublished !== null)
            <span class="text-lg font-normal text-gray-600 dark:text-gray-400">
                @if($search && $isPublished !== null)
                - "{{ $search }}" 검색 결과 ({{ $isPublished ? '발행됨' : '미발행' }})
                @elseif($search)
                - "{{ $search }}" 검색 결과
                @elseif($isPublished !== null)
                - {{ $isPublished ? '발행됨' : '미발행' }} 필터
                @endif
            </span>
            @endif
        </div>

        <div class="flex justify-between items-center pb-4">
            <div>
                @can('viewAny', Ssh521\LaravelAdmin\Models\AdminNotice::class)
                <a href="{{ route('admin.notices.index') }}"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">목록보기</a>
                <span class="mx-1">|</span>
                @endcan

                @can('create', Ssh521\LaravelAdmin\Models\AdminNotice::class)
                <a href="{{ route('admin.notices.create') }}"
                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">등록하기</a>
                @endcan
            </div>

            <div>
                <form class="flex items-center ml-1 space-x-2" x-data
                    x-init="$nextTick(() => $refs.searchInput?.focus())">
                    <!-- 발행 상태 필터 -->
                    <div class="relative">
                        <select wire:model.live="isPublished"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 dark:focus:ring-blue-500 focus:border-blue-500 dark:focus:border-blue-500 block w-32 pl-2 pr-8 p-1.5">
                            <option value="">전체</option>
                            <option value="1">발행됨</option>
                            <option value="0">미발행</option>
                        </select>
                    </div>

                    <!-- 검색 입력 -->
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="w-full relative">
                        <x-admin.input type="text" wire:model.live.debounce.300ms="search" x-ref="searchInput"
                            class="w-40 pl-2 pr-8" placeholder="공지사항 검색" />
                        @if($search)
                        <button wire:click="$set('search', '')"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @endif
                    </div>

                    <button type="button"
                        class="p-1 text-sm font-medium text-white bg-gray-500 rounded-lg border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 30 30"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-2-2m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="sr-only">Search</span>
                    </button>

                    <!-- 필터 초기화 버튼 -->
                    @if($search || $isPublished !== null)
                    <button wire:click="$set('search', ''); $set('isPublished', null)" type="button"
                        class="p-1 text-sm font-medium text-white bg-red-500 rounded-lg border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300"
                        title="필터 초기화">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="sr-only">Reset</span>
                    </button>
                    @endif
                </form>
            </div>
        </div>

        <x-session-messages />

        <div
            class="relative overflow-x-auto my-2 border-gray-200 dark:border-gray-700 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">

            <table class="w-full min-w-[300px] text-sm text-left rtl:text-right">
                <thead
                    class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-200 dark:bg-gray-700 border-b border-t border-gray-300 dark:border-gray-600">
                    <tr>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            제목
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap hidden sm:table-cell">
                            작성자
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            첨부파일
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            발행 상태
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap hidden sm:table-cell">
                            발행일
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap hidden sm:table-cell">
                            작성일
                        </th>
                        <th scope="col" class="px-2 py-3 whitespace-nowrap">
                            <span class="sr-only">Edit</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notices as $key => $notice)
                    <tr
                        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <th scope="row" class="px-2 py-2 font-medium text-gray-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                @can('view', $notice)
                                <div class="flex items-center gap-1">
                                    <button 
                                        type="button"
                                        wire:click="showNoticeDetailModal({{ $notice->id }})"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-left">
                                        {{ $notice->title }}
                                    </button>
                                </div>
                                @else
                                {{ $notice->title }}
                                @endcan
                            </div>
                        </th>
                        <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell dark:text-white">
                            @if($notice->author)
                            <button type="button"
                                @click="$dispatch('admin-users:user-form:open', { payload: { mode: 'view', userId: {{ $notice->author->id }} } })"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-left">
                                {{ $notice->author->name }}
                            </button>
                            @else
                            <span class="text-gray-500 dark:text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                            @if($notice->attachments && $notice->attachments->count() > 0)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $notice->attachments->count() }}</span>

                                @can('update', $notice)
                                    <x-dynamic-modal-trigger
                                        text="관리"
                                        type="link"
                                        variant="primary"
                                        component="admin.notices.notice-attachment-modal"
                                        key-id="notice-attachments-{{ $notice->id }}"
                                        :params="['noticeId' => $notice->id, 'modalKeyId' => 'notice-attachments-'.$notice->id]"
                                        title="첨부파일 관리"
                                        width="900"
                                        height="600"
                                        :single="true"
                                    />
                                @endcan
                            </div>
                            @else
                            <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                            @can('update', $notice)
                            <button wire:click="togglePublishStatus({{ $notice->id }})"
                                class="px-2 py-1 text-xs rounded-full transition-colors duration-200 hover:opacity-80 cursor-pointer
                                    @if($notice->is_published)
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                {{ $notice->is_published ? '발행됨' : '미발행' }}
                            </button>
                            @else
                            @if($notice->is_published)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                발행됨
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                미발행
                            </span>
                            @endif
                            @endcan
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell dark:text-white">
                            {{ $notice->published_at ? $notice->published_at->format('Y-m-d H:i') : '-' }}
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell dark:text-white">
                            {{ $notice->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-2 py-2 text-right whitespace-nowrap">
                            <div class="flex gap-2 justify-end">
                                @can('update', $notice)
                                <a href="{{ route('admin.notices.edit', $notice->id) }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('Edit') }}
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-2 py-4 text-center">
                            @if($search || $isPublished !== null)
                            검색 결과가 없습니다.
                            @else
                            등록된 공지사항이 없습니다.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="my-3">
            {!! $notices->appends(request()->query())->links() !!}
        </div>

    </div>

    {{-- 공지사항 상세 정보 모달 --}}
    <livewire:admin.notices.notice-detail-modal />

    <livewire:admin.users.user-form />

</div>
