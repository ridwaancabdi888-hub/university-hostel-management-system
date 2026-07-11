<x-dashboard-layout title="Add Staff User">
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">&larr; Back to User Management</a>
    </div>

    <div class="glass-card max-w-2xl rounded-lg p-lg">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <x-users.form />

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-outline-variant/20 pt-6 dark:border-night-border">
                <a href="{{ route('users.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Create User</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
