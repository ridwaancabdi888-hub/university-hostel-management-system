@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'h-[48px] border-2 border-outline-variant/40 bg-surface-container-lowest px-sm font-body-md text-on-surface rounded-DEFAULT shadow-none focus:border-primary focus:ring-0 dark:border-night-border dark:bg-night-surface dark:text-night-on-surface dark:focus:border-night-primary transition-colors duration-200']) }}>
    {{ $slot }}
</select>
