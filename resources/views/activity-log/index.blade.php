<x-dashboard-layout title="Activity Log">
    <div class="mb-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">A system-wide audit trail of who changed what, and when.</p>
    </div>

    <form method="GET" action="{{ route('activity-log.index') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="subject_type" value="Model" />
                <x-select id="subject_type" name="subject_type" class="mt-1 block w-full">
                    <option value="">All</option>
                    @foreach ($subjectTypes as $type)
                        <option value="{{ $type }}" @selected(($filters['subject_type'] ?? '') === $type)>{{ class_basename($type) }}</option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-input-label for="event" value="Event" />
                <x-select id="event" name="event" class="mt-1 block w-full">
                    <option value="">All</option>
                    @foreach (['created', 'updated', 'deleted'] as $event)
                        <option value="{{ $event }}" @selected(($filters['event'] ?? '') === $event)>{{ ucfirst($event) }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-primary-button type="submit">Apply Filters</x-primary-button>
            <a href="{{ route('activity-log.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">When</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Causer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Event</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Model</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Changes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($activities as $activity)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $activity->created_at->format('M j, Y g:i A') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $activity->causer?->name ?? 'System' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span @class([
                                'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold uppercase tracking-wide',
                                'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' => $activity->event === 'created',
                                'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' => $activity->event === 'updated',
                                'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' => $activity->event === 'deleted',
                            ])>{{ $activity->event }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            @if ($activity->event === 'updated' && $activity->properties->has('attributes'))
                                @foreach ($activity->properties->get('attributes') as $field => $value)
                                    <div>
                                        <span class="font-medium">{{ $field }}:</span>
                                        {{ $activity->properties->get('old')[$field] ?? '—' }} &rarr; {{ $value }}
                                    </div>
                                @endforeach
                            @elseif ($activity->properties->isNotEmpty())
                                {{ $activity->properties->except(['old'])->toJson() }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No activity recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</x-dashboard-layout>
