<x-guest-layout>
    <div class="flex flex-col items-center gap-base text-center">
        <h1 class="font-display text-headline-lg-mobile md:text-headline-md text-primary dark:text-night-primary tracking-tight">Reset Password</h1>
        <p class="font-label-md text-on-surface-variant dark:text-night-on-surface-variant">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-md">
        @csrf

        <!-- Email Address -->
        <div class="flex flex-col gap-xs">
            <x-input-label for="email" :value="__('Email')" />
            <div class="input-glow flex items-center h-[48px] rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container-lowest/50 px-sm transition-all duration-300 focus-within:border-primary dark:border-night-border dark:bg-night-surface dark:focus-within:border-night-primary">
                <span class="material-symbols-outlined mr-sm text-outline dark:text-night-on-surface-variant">mail</span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                    class="!h-auto w-full !border-none !bg-transparent !px-0 !shadow-none" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="mt-base h-[48px] w-full justify-center">
            {{ __('Email Password Reset Link') }}
        </x-primary-button>
    </form>
</x-guest-layout>
