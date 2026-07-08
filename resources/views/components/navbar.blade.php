@props(['title' => null])

<header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
    <button @click="sidebarOpen = ! sidebarOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
        </svg>
    </button>

    <h1 class="flex-1 truncate text-lg font-semibold text-gray-900 dark:text-gray-100">
        {{ $title }}
    </h1>

    <!-- Dark mode toggle -->
    <button
        @click="darkMode = ! darkMode"
        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
        aria-label="Toggle dark mode"
    >
        <svg x-show="! darkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
        </svg>
        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
        </svg>
    </button>

    <!-- User dropdown -->
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-medium text-white">
                    {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                </span>
                <span class="hidden text-left sm:block">
                    <span class="block font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->role->label() }}</span>
                </span>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-dropdown-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</header>
