@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'font-label-md text-error dark:text-night-error space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
