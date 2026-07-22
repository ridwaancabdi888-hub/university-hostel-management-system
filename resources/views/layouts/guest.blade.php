<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow, noarchive">
        <meta name="description" content="Private sign-in for the University Hostel Management System.">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
    <body class="font-sans text-on-background dark:text-night-on-surface antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-background px-margin-mobile py-xl dark:bg-night-bg md:px-margin-desktop">
            <!-- Atmospheric background layers -->
            <div class="pointer-events-none absolute inset-0 z-0">
                <div class="absolute -right-[5%] -top-[10%] h-[500px] w-[500px] rounded-full bg-secondary-container/20 blur-[120px] dark:bg-night-secondary-container/30"></div>
                <div class="absolute -left-[5%] -bottom-[10%] h-[600px] w-[600px] rounded-full bg-primary/5 blur-[100px] dark:bg-night-primary/10"></div>
            </div>

            <div class="relative z-10 flex w-full max-w-[480px] flex-col items-center gap-md">
                <a href="{{ route('login') }}" class="flex flex-col items-center gap-base text-center">
                    <span class="flex h-16 w-16 animate-float items-center justify-center rounded-xl bg-primary shadow-lg shadow-primary/20 dark:bg-night-primary dark:shadow-night-primary/20">
                        <span class="material-symbols-outlined text-[32px] text-on-primary dark:text-night-on-primary">school</span>
                    </span>
                </a>

                <div class="glass-card flex w-full flex-col gap-lg rounded-lg p-lg md:p-xl">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
