@php
    $quickLinks = array_filter(
        \App\Support\Navigation::for($role),
        fn (array $item) => $item['route'] !== 'dashboard'
    );
@endphp

<x-dashboard-layout title="Dashboard">
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Welcome back, {{ auth()->user()->name }}.
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            You're signed in as <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $role->label() }}</span>.
        </p>
    </div>

    @if (count($quickLinks))
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($quickLinks as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-500/50"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-nav-icon :name="$item['icon']" />
                    </span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</x-dashboard-layout>
