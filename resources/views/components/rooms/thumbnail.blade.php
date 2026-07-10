@props(['room', 'size' => 'h-11 w-14'])

@if ($room->photoUrl())
    <img src="{{ $room->photoUrl() }}" alt="Room {{ $room->room_number }}" {{ $attributes->merge(['class' => "$size rounded-md object-cover"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$size flex items-center justify-center rounded-md bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-300 dark:from-gray-700 dark:to-gray-800 dark:text-gray-600"]) }}>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75V8.25a1.5 1.5 0 011.5-1.5h1.5a1.5 1.5 0 011.5 1.5V12m-4.5 6.75h19.5M2.25 18.75V15a1.5 1.5 0 011.5-1.5h16.5a1.5 1.5 0 011.5 1.5v3.75M6.75 12h6a1.5 1.5 0 011.5 1.5v.75m6-3.75V8.25a1.5 1.5 0 00-1.5-1.5h-6a1.5 1.5 0 00-1.5 1.5v6" />
        </svg>
    </span>
@endif
