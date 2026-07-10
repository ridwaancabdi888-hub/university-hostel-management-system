@props(['action', 'confirm' => 'Are you sure you want to delete this?'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm('{{ $confirm }}')">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'font-label-md font-medium text-error hover:opacity-75 dark:text-night-error']) }}>
        {{ $slot->isEmpty() ? 'Delete' : $slot }}
    </button>
</form>
