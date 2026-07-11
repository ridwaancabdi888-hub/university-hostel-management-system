@props(['user' => null])

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="name" value="Full Name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus
            :value="old('name', $user->name ?? '')" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required
            :value="old('email', $user->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password" :value="$user ? 'New Password (optional)' : 'Password'" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password"
            :required="! $user" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="role" value="Role" />
        <x-select id="role" name="role" class="mt-1 block w-full" required>
            @foreach ([\App\Enums\Role::Admin, \App\Enums\Role::Warden, \App\Enums\Role::Accountant] as $role)
                <option value="{{ $role->value }}" @selected(old('role', $user->role->value ?? '') === $role->value)>
                    {{ $role->label() }}
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
        <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">
            Student accounts are created from the Student Directory, not here.
        </p>
    </div>
</div>
