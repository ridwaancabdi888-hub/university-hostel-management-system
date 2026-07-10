<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-2 px-md py-sm bg-surface-container-lowest dark:bg-night-surface-high border border-outline-variant/40 dark:border-night-border rounded-DEFAULT font-label-md text-on-surface-variant dark:text-night-on-surface-variant hover:bg-secondary-container/30 dark:hover:bg-night-surface focus:outline-none focus:ring-2 focus:ring-primary/30 dark:focus:ring-night-primary/30 focus:ring-offset-2 dark:focus:ring-offset-night-bg disabled:opacity-40 transition-all duration-200']) }}>
    {{ $slot }}
</button>
