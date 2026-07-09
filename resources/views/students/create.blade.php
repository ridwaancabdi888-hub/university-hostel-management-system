<x-dashboard-layout title="Register Student">
    <div class="mb-4">
        <a href="{{ route('students.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Student Directory</a>
    </div>

    <div class="max-w-3xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
            @csrf

            <x-students.form />

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                <a href="{{ route('students.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Register Student</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
