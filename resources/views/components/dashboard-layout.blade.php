@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <meta name="description" content="Private University Hostel Management System dashboard.">

        <title>{{ $title ? $title.' - '.config('app.name') : config('app.name') }}</title>

        <script>
            if (
                localStorage.getItem('darkMode') === 'true' ||
                (! ('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
            ) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="font-sans antialiased"
        x-data="{
            sidebarOpen: false,
            darkMode: document.documentElement.classList.contains('dark'),
        }"
        x-init="$watch('darkMode', value => {
            localStorage.setItem('darkMode', value);
            document.documentElement.classList.toggle('dark', value);
        })"
    >
        <div class="flex h-screen overflow-hidden bg-background dark:bg-night-bg">
            <x-sidebar />

            <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
                <x-navbar :title="$title" />

                <main class="min-w-0 flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 lg:p-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-DEFAULT border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-DEFAULT border border-error/20 bg-error-container px-4 py-3 text-sm text-on-error-container dark:border-night-error/30 dark:bg-night-error-container dark:text-night-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
