<x-guest-layout>
    <div class="flex flex-col items-center gap-base text-center">
        <h1 class="font-display text-headline-lg-mobile md:text-headline-md text-primary dark:text-night-primary tracking-tight">Create an Account</h1>
        <p class="font-label-md text-on-surface-variant dark:text-night-on-surface-variant max-w-[280px]">Register for your student housing profile.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-md">
        @csrf

        <!-- Name -->
        <div class="flex flex-col gap-xs">
            <x-input-label for="name" :value="__('Name')" />
            <div class="input-glow flex items-center h-[48px] rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container-lowest/50 px-sm transition-all duration-300 focus-within:border-primary dark:border-night-border dark:bg-night-surface dark:focus-within:border-night-primary">
                <span class="material-symbols-outlined mr-sm text-outline dark:text-night-on-surface-variant">person</span>
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                    class="!h-auto w-full !border-none !bg-transparent !px-0 !shadow-none" />
            </div>
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div class="flex flex-col gap-xs">
            <x-input-label for="email" :value="__('Email')" />
            <div class="input-glow flex items-center h-[48px] rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container-lowest/50 px-sm transition-all duration-300 focus-within:border-primary dark:border-night-border dark:bg-night-surface dark:focus-within:border-night-primary">
                <span class="material-symbols-outlined mr-sm text-outline dark:text-night-on-surface-variant">mail</span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                    class="!h-auto w-full !border-none !bg-transparent !px-0 !shadow-none" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-xs">
            <x-input-label for="password" :value="__('Password')" />
            <div class="input-glow flex items-center h-[48px] rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container-lowest/50 px-sm transition-all duration-300 focus-within:border-primary dark:border-night-border dark:bg-night-surface dark:focus-within:border-night-primary">
                <span class="material-symbols-outlined mr-sm text-outline dark:text-night-on-surface-variant">lock</span>
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password"
                    class="!h-auto w-full !border-none !bg-transparent !px-0 !shadow-none" />
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="flex flex-col gap-xs">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="input-glow flex items-center h-[48px] rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container-lowest/50 px-sm transition-all duration-300 focus-within:border-primary dark:border-night-border dark:bg-night-surface dark:focus-within:border-night-primary">
                <span class="material-symbols-outlined mr-sm text-outline dark:text-night-on-surface-variant">lock_reset</span>
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="!h-auto w-full !border-none !bg-transparent !px-0 !shadow-none" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="mt-base flex items-center justify-between gap-md">
            <a class="font-label-md text-on-surface-variant hover:text-primary dark:text-night-on-surface-variant dark:hover:text-night-primary" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="h-[48px]">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
