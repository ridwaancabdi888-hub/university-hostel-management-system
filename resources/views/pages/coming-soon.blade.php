<x-dashboard-layout :title="$title">
    <div class="mx-auto flex max-w-2xl flex-col items-center rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        <p class="mt-4 text-xs font-medium uppercase tracking-wide text-indigo-600 dark:text-indigo-400">Coming in a future phase</p>
    </div>
</x-dashboard-layout>
