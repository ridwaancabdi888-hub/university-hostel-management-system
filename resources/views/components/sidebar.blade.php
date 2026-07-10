@php
    $navigation = \App\Support\Navigation::for(auth()->user()->role);
@endphp

<div
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-on-surface/50 lg:hidden"
    style="display: none;"
></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-outline-variant/20 bg-surface-container-lowest/80 backdrop-blur-xl transition-transform duration-200 ease-in-out dark:border-night-border dark:bg-night-surface/80 lg:static lg:translate-x-0"
>
    <div class="flex h-16 items-center gap-3 border-b border-outline-variant/20 px-6 dark:border-night-border">
        <div class="flex h-9 w-9 items-center justify-center rounded-DEFAULT bg-primary text-on-primary dark:bg-night-primary dark:text-night-on-primary">
            <x-nav-icon name="home" class="!text-[18px]" />
        </div>
        <div class="leading-tight">
            <p class="font-headline-sm text-sm font-bold text-on-surface dark:text-night-on-surface">Hostel</p>
            <p class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Academic Sanctuary</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($navigation as $item)
            @php $isActive = request()->routeIs(...($item['active'] ?? [$item['route']])); @endphp
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'flex items-center gap-3 rounded-[12px] border-l-[3px] px-3 py-2 font-label-md transition',
                    'border-primary bg-secondary-container/60 text-primary dark:border-night-primary dark:bg-night-secondary-container dark:text-night-primary' => $isActive,
                    'border-transparent text-on-surface-variant hover:bg-secondary-container/30 hover:text-on-surface dark:text-night-on-surface-variant dark:hover:bg-night-surface-high dark:hover:text-night-on-surface' => ! $isActive,
                ])
            >
                <x-nav-icon :name="$item['icon']" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="space-y-1 border-t border-outline-variant/20 px-3 py-4 dark:border-night-border">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-[12px] border-l-[3px] border-transparent px-3 py-2 font-label-md text-on-surface-variant hover:bg-secondary-container/30 hover:text-on-surface dark:text-night-on-surface-variant dark:hover:bg-night-surface-high dark:hover:text-night-on-surface">
            <x-nav-icon name="settings" />
            Settings
        </a>
        <a href="#" class="flex items-center gap-3 rounded-[12px] border-l-[3px] border-transparent px-3 py-2 font-label-md text-on-surface-variant hover:bg-secondary-container/30 hover:text-on-surface dark:text-night-on-surface-variant dark:hover:bg-night-surface-high dark:hover:text-night-on-surface">
            <x-nav-icon name="support" />
            Support
        </a>
    </div>
</aside>
