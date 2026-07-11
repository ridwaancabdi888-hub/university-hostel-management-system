<x-dashboard-layout title="User Management">
    <div class="mb-4 flex items-center justify-between">
        <p class="font-body-md text-on-surface-variant dark:text-night-on-surface-variant">Manage Admin, Warden, and Accountant accounts and their roles.</p>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Add New User
        </a>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="glass-card mb-6 rounded-lg p-md">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-outline dark:text-night-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <x-text-input id="search" name="search" type="text" class="block w-full !rounded-full !pl-11" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name or email..." />
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="role" value="Role" />
                <x-select id="role" name="role" class="mt-1 block w-full">
                    <option value="">All Roles</option>
                    @foreach ([\App\Enums\Role::Admin, \App\Enums\Role::Warden, \App\Enums\Role::Accountant] as $role)
                        <option value="{{ $role->value }}" @selected(($filters['role'] ?? '') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-primary-button type="submit">Apply Filters</x-primary-button>
            <a href="{{ route('users.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">Reset</a>
        </div>
    </form>

    <div class="glass-card overflow-x-auto rounded-lg">
        <table class="min-w-full divide-y divide-outline-variant/15 dark:divide-night-border">
            <thead class="bg-secondary-container/20 dark:bg-night-surface-high/50">
                <tr>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">User</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Role</th>
                    <th class="px-4 py-3 text-right font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/15 dark:divide-night-border">
                @forelse ($users as $user)
                    <tr class="transition hover:bg-secondary-container/10 dark:hover:bg-night-surface-high/40">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$user->name" size="h-10 w-10" class="ring-1 ring-outline-variant/30 dark:ring-night-border" />
                                <div>
                                    <div class="font-label-md font-semibold text-on-surface dark:text-night-on-surface">
                                        {{ $user->name }}
                                        @if (auth()->user()->is($user))
                                            <span class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">(You)</span>
                                        @endif
                                    </div>
                                    <div class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3"><x-users.role-badge :role="$user->role" /></td>
                        <td class="px-4 py-3 text-right font-label-md">
                            <a href="{{ route('users.edit', $user) }}" class="font-medium text-primary hover:underline dark:text-night-primary">Edit</a>
                            @unless (auth()->user()->is($user))
                                <span class="mx-2 text-outline-variant dark:text-night-border">|</span>
                                <x-delete-button :action="route('users.destroy', $user)" confirm="Remove this user? This also deletes their login account.">
                                    Remove
                                </x-delete-button>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center font-body-md text-on-surface-variant dark:text-night-on-surface-variant">No users match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-dashboard-layout>
