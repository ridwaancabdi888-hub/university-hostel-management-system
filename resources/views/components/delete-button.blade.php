@props(['action', 'confirm' => 'Are you sure you want to delete this?'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm('{{ $confirm }}')">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'font-medium text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300']) }}>
        {{ $slot->isEmpty() ? 'Delete' : $slot }}
    </button>
</form>
