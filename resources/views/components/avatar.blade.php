@props(['name', 'url' => null, 'size' => 'h-10 w-10'])

@if ($url)
    <img src="{{ $url }}" alt="{{ $name }}" {{ $attributes->merge(['class' => "$size rounded-full object-cover"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$size flex items-center justify-center rounded-full bg-indigo-600 text-sm font-medium text-white"]) }}>
        {{ \Illuminate\Support\Str::of($name)->substr(0, 1)->upper() }}
    </span>
@endif
