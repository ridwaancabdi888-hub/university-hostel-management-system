<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 px-md py-sm bg-error dark:bg-night-error border border-transparent rounded-DEFAULT font-label-md text-on-error dark:text-night-error-container hover:opacity-90 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-error/40 dark:focus:ring-night-error/40 focus:ring-offset-2 dark:focus:ring-offset-night-bg transition-all duration-200']) }}>
    {{ $slot }}
</button>
