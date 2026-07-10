@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-label-md text-on-surface-variant dark:text-night-on-surface-variant']) }}>
    {{ $value ?? $slot }}
</label>
