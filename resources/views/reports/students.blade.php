<x-dashboard-layout title="Student Report">
    <x-reports-tabs active="students" />

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Student population broken down by status, year level, gender, and course.</p>
        <x-report-export-buttons type="students" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 sm:col-span-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Students</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $total }}</p>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">By Status</h3>
            <div class="mt-4">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">By Year Level</h3>
            <div class="mt-4">
                <canvas id="yearLevelChart" height="200"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">By Gender</h3>
            <div class="mt-4">
                <canvas id="genderChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Course</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Students</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($byCourse as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $row->course }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row->count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! $byStatus->map(fn ($r) => \App\Enums\StudentStatus::from($r->status)->label())->toJson() !!},
                    datasets: [{
                        data: {!! $byStatus->pluck('count')->toJson() !!},
                        backgroundColor: ['#f59e0b', '#10b981', '#ef4444', '#4f46e5', '#6b7280'],
                    }],
                },
            });

            new Chart(document.getElementById('yearLevelChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! $byYearLevel->map(fn ($r) => \App\Enums\YearLevel::from($r->year_level)->label())->toJson() !!},
                    datasets: [{
                        data: {!! $byYearLevel->pluck('count')->toJson() !!},
                        backgroundColor: ['#4f46e5', '#0ea5e9', '#f59e0b', '#10b981', '#ec4899'],
                    }],
                },
            });

            new Chart(document.getElementById('genderChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! $byGender->map(fn ($r) => \App\Enums\Gender::from($r->gender)->label())->toJson() !!},
                    datasets: [{
                        data: {!! $byGender->pluck('count')->toJson() !!},
                        backgroundColor: ['#0ea5e9', '#ec4899', '#6b7280'],
                    }],
                },
            });
        </script>
    @endpush
</x-dashboard-layout>
