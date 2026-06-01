{{-- resources/views/livewire/admin/left-menu.blade.php - dTree 스타일 (Classic ASP 형식) --}}

@php
    $dtreeImg = fn ($name) => asset('images/dtree/' . $name);
@endphp

<div class="dtree">
    <div class="p-2">
        {{-- Root: 사이트명 --}}
        <div class="dTreeNode flex items-center">
            <img src="{{ $dtreeImg('base.gif') }}" alt="" class="dtree-img flex-shrink-0" />
            <a href="{{ route('admin.index') }}" class="node {{ request()->routeIs('admin.index') ? 'nodeSel' : '' }} ml-0.5" title="{{ config('app.name') }}">
                {{ config('app.name') }}
            </a>
        </div>
        @if($sidebarMenuCategories && $sidebarMenuCategories->count() > 0)
            @foreach($sidebarMenuCategories as $catIndex => $category)
                @php
                    $menus = $category->activeMenus;
                    $hasChildren = $menus->count() > 0;
                    $isLastCategory = $catIndex === $sidebarMenuCategories->count() - 1;
                @endphp
                <div class="dtree-node" x-data="window.dtreeNode('category-{{ $category->id }}')"
                    data-node-id="category-{{ $category->id }}">
                    {{-- 폴더 노드 (카테고리) --}}
                    <div class="dTreeNode flex items-center {{ $hasChildren ? 'cursor-pointer select-none' : '' }}"
                        @if($hasChildren) @click="toggleNode()" @endif>
                        <span class="dtree-indent flex items-center">
                            @if($hasChildren)
                                <a href="javascript:void(0)" class="dtree-toggle inline-block">
                                    <img x-show="!isOpen" src="{{ $dtreeImg($isLastCategory ? 'plusbottom.gif' : 'plus.gif') }}" alt="" class="dtree-img" />
                                    <img x-show="isOpen" x-cloak src="{{ $dtreeImg($isLastCategory ? 'minusbottom.gif' : 'minus.gif') }}" alt="" class="dtree-img" />
                                </a>
                            @else
                                <img src="{{ $dtreeImg($isLastCategory ? 'joinbottom.gif' : 'join.gif') }}" alt="" class="dtree-img" />
                            @endif
                        </span>
                        @if($hasChildren)
                            <img x-show="!isOpen" src="{{ $dtreeImg('folder.gif') }}" alt="" class="dtree-img flex-shrink-0" />
                            <img x-show="isOpen" x-cloak src="{{ $dtreeImg('folderopen.gif') }}" alt="" class="dtree-img flex-shrink-0" />
                        @else
                            <img src="{{ $dtreeImg('folder.gif') }}" alt="" class="dtree-img flex-shrink-0" />
                        @endif
                        <span class="dtree-text ml-0.5">{{ $category->name }}</span>
                    </div>
                    {{-- 하위 메뉴 --}}
                    @if($hasChildren)
                    <div x-show="isOpen" x-cloak x-collapse class="dtree-children clip">
                        @foreach($menus as $menuIndex => $menu)
                            @php
                                $isLastMenu = $menuIndex === $menus->count() - 1;
                                $useEmptyForLine = $isLastCategory;
                            @endphp
                            <div class="dTreeNode flex items-center">
                                <span class="dtree-indent flex items-center flex-shrink-0 ml-[0.1rem]">
                                    <img src="{{ $dtreeImg($useEmptyForLine ? 'empty.gif' : 'line.gif') }}" alt="" class="dtree-img" />
                                    <img src="{{ $dtreeImg($isLastMenu ? 'joinbottom.gif' : 'join.gif') }}" alt="" class="dtree-img" />
                                </span>
                                <img src="{{ $dtreeImg('page.gif') }}" alt="" class="dtree-img flex-shrink-0" />
                                @if($menu->target === '_blank')
                                    <a href="{{ $menu->url }}" target="_blank" title="{{ $menu->name }}"
                                        class="node ml-0 {{ $menu->isCurrentPage() ? 'nodeSel' : '' }}">
                                        {{ $menu->name }}
                                    </a>
                                    <img src="{{ $dtreeImg('globe.gif') }}" alt="" class="w-3 h-3 ml-1 opacity-60 flex-shrink-0" />
                                @else
                                    <a href="{{ $menu->url }}" target="{{ $menu->target ?? '_self' }}" title="{{ $menu->name }}"
                                        class="node ml-0 {{ $menu->isCurrentPage() ? 'nodeSel' : '' }}">
                                        {{ $menu->name }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="flex items-center justify-center px-4 py-4 text-gray-500 text-sm">
                등록된 메뉴가 없습니다.
            </div>
        @endif
    </div>
</div>
