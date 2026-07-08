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
    class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
    style="display: none;"
></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-in-out dark:border-gray-700 dark:bg-gray-800 lg:static lg:translate-x-0"
>
    <div class="flex h-16 items-center gap-2 border-b border-gray-200 px-6 dark:border-gray-700">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white">
            <x-nav-icon name="home" class="w-5 h-5" />
        </div>
        <div class="leading-tight">
            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">Hostel</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Academic Sanctuary</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($navigation as $item)
            @php $isActive = request()->routeIs(...($item['active'] ?? [$item['route']])); @endphp
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                    'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' => $isActive,
                    'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' => ! $isActive,
                ])
            >
                <x-nav-icon :name="$item['icon']" />
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="space-y-1 border-t border-gray-200 px-3 py-4 dark:border-gray-700">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
            <x-nav-icon name="settings" />
            Settings
        </a>
        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
            <x-nav-icon name="support" />
            Support
        </a>
    </div>
</aside>
