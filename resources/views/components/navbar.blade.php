@props(['title' => null])

<header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-outline-variant/20 bg-surface-container-lowest/80 px-4 backdrop-blur-xl dark:border-night-border dark:bg-night-surface/80 sm:px-6">
    <button @click="sidebarOpen = ! sidebarOpen" class="text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface lg:hidden">
        <span class="material-symbols-outlined">menu</span>
    </button>

    <h1 class="flex-1 truncate font-headline-sm text-on-surface dark:text-night-on-surface">
        {{ $title }}
    </h1>

    <!-- Notifications -->
    @php
        $unreadNotifications = auth()->user()->unreadNotifications()->latest()->limit(8)->get();
        $unreadCount = auth()->user()->unreadNotifications()->count();
    @endphp
    <x-dropdown align="right" width="w-80">
        <x-slot name="trigger">
            <button class="relative rounded-[12px] p-2 text-on-surface-variant hover:bg-secondary-container/30 hover:text-on-surface dark:text-night-on-surface-variant dark:hover:bg-night-surface-high dark:hover:text-night-on-surface" aria-label="Notifications">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                @if ($unreadCount > 0)
                    <span class="absolute right-1.5 top-1.5 flex h-2 w-2 rounded-full bg-error dark:bg-night-error"></span>
                @endif
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="flex items-center justify-between border-b border-outline-variant/20 px-4 py-2 dark:border-night-border">
                <span class="font-label-md font-semibold text-on-surface dark:text-night-on-surface">Notifications</span>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="font-label-sm text-primary hover:underline dark:text-night-primary">Mark all read</button>
                    </form>
                @endif
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse ($unreadNotifications as $notification)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-3 text-left hover:bg-secondary-container/20 dark:hover:bg-night-surface-high">
                            <span class="block font-label-md font-medium text-on-surface dark:text-night-on-surface">{{ $notification->data['title'] ?? 'Notification' }}</span>
                            <span class="mt-0.5 block font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $notification->data['message'] ?? '' }}</span>
                            <span class="mt-0.5 block font-label-sm text-outline dark:text-night-on-surface-variant/70">{{ $notification->created_at->diffForHumans() }}</span>
                        </button>
                    </form>
                @empty
                    <p class="px-4 py-6 text-center font-label-md text-on-surface-variant dark:text-night-on-surface-variant">You're all caught up.</p>
                @endforelse
            </div>

            <div class="border-t border-outline-variant/20 px-4 py-2 dark:border-night-border">
                <a href="{{ route('notifications.index') }}" class="font-label-sm text-primary hover:underline dark:text-night-primary">View all notifications</a>
            </div>
        </x-slot>
    </x-dropdown>

    <!-- Dark mode toggle -->
    <button
        @click="darkMode = ! darkMode"
        class="rounded-[12px] p-2 text-on-surface-variant hover:bg-secondary-container/30 hover:text-on-surface dark:text-night-on-surface-variant dark:hover:bg-night-surface-high dark:hover:text-night-on-surface"
        aria-label="Toggle dark mode"
    >
        <span x-show="! darkMode" class="material-symbols-outlined text-[22px]">dark_mode</span>
        <span x-show="darkMode" class="material-symbols-outlined text-[22px]">light_mode</span>
    </button>

    <!-- User dropdown -->
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex items-center gap-2 rounded-[12px] px-2 py-1.5 hover:bg-secondary-container/30 dark:hover:bg-night-surface-high">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary font-label-md font-medium text-on-primary dark:bg-night-primary dark:text-night-on-primary">
                    {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                </span>
                <span class="hidden text-left sm:block">
                    <span class="block font-label-md font-medium text-on-surface dark:text-night-on-surface">{{ auth()->user()->name }}</span>
                    <span class="block font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ auth()->user()->role->label() }}</span>
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
