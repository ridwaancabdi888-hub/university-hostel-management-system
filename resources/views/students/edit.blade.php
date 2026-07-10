<x-dashboard-layout title="Edit Student">
    <div class="mb-4">
        <a href="{{ route('students.show', $student) }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">&larr; Back to Profile</a>
    </div>

    <div class="glass-card max-w-3xl rounded-lg p-lg">
        <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-students.form :student="$student" />

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-outline-variant/20 pt-6 dark:border-night-border">
                <a href="{{ route('students.show', $student) }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
