@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-[48px] border-2 border-outline-variant/40 bg-surface-container-lowest px-sm font-body-md text-on-surface placeholder:text-outline-variant rounded-DEFAULT shadow-none focus:border-primary focus:ring-0 dark:border-night-border dark:bg-night-surface dark:text-night-on-surface dark:placeholder:text-night-on-surface-variant/50 dark:focus:border-night-primary transition-colors duration-200']) }}>
