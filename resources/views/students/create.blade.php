<x-dashboard-layout title="Register Student">
    <div class="mb-4">
        <a href="{{ route('students.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">&larr; Back to Student Directory</a>
    </div>

    <div class="glass-card max-w-3xl rounded-lg p-lg">
        <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
            @csrf

            <x-students.form />

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-outline-variant/20 pt-6 dark:border-night-border">
                <a href="{{ route('students.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Register Student</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
