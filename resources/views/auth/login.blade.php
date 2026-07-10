<x-guest-layout>
    <div class="flex flex-col items-center gap-base text-center">
        <h1 class="font-display text-headline-lg-mobile md:text-headline-md text-primary dark:text-night-primary tracking-tight">Hostel Portal</h1>
        <p class="font-label-md text-on-surface-variant dark:text-night-on-surface-variant max-w-[280px]">Welcome back to your Academic Sanctuary management suite.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-md">
        @csrf

        <!-- Email Address -->
        <div class="flex flex-col gap-xs">
            <x-input-label for="email" :value="__('Email')" />
            <div class="input-glow flex items-center h-[48px] rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container-lowest/50 px-sm transition-all duration-300 focus-within:border-primary dark:border-night-border dark:bg-night-surface dark:focus-within:border-night-primary">
                <span class="material-symbols-outlined mr-sm text-outline dark:text-night-on-surface-variant">mail</span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                    class="!h-auto w-full !border-none !bg-transparent !px-0 !shadow-none" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-xs">
            <div class="flex items-center justify-between px-1">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="font-label-sm text-primary hover:underline dark:text-night-primary" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <div class="input-glow flex items-center h-[48px] rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container-lowest/50 px-sm transition-all duration-300 focus-within:border-primary dark:border-night-border dark:bg-night-surface dark:focus-within:border-night-primary">
                <span class="material-symbols-outlined mr-sm text-outline dark:text-night-on-surface-variant">lock</span>
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password"
                    class="!h-auto w-full !border-none !bg-transparent !px-0 !shadow-none" />
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-sm px-1">
            <input id="remember_me" type="checkbox" name="remember"
                class="h-5 w-5 rounded border-2 border-outline-variant text-primary focus:ring-primary/20 dark:border-night-border dark:bg-night-surface dark:text-night-primary">
            <label for="remember_me" class="cursor-pointer select-none font-label-md text-on-surface-variant dark:text-night-on-surface-variant">{{ __('Remember me') }}</label>
        </div>

        <x-primary-button class="mt-base h-[48px] w-full justify-center">
            {{ __('Log in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
