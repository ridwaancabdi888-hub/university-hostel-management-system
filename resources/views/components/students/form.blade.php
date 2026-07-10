@props(['student' => null])

@php $user = $student?->user; @endphp

<div class="space-y-8">
    <section>
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Account</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                <x-input-label for="password" :value="$student ? 'New Password (optional)' : 'Password'" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password"
                    :required="! $student" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="photo" value="Student Photo" />
                <input id="photo" name="photo" type="file" accept="image/*"
                    class="mt-1 block w-full font-body-md text-on-surface-variant file:mr-4 file:rounded-DEFAULT file:border-0 file:bg-secondary-container/50 file:px-4 file:py-2 file:font-label-md file:font-semibold file:text-primary hover:file:bg-secondary-container dark:text-night-on-surface-variant dark:file:bg-night-secondary-container dark:file:text-night-primary">
                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                @if ($student?->photoUrl())
                    <div class="mt-2 flex items-center gap-2">
                        <x-avatar :name="$user->name" :url="$student->photoUrl()" size="h-10 w-10" />
                        <span class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Current photo — upload a new file to replace it.</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section>
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">University Details</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="student_id" value="Student ID" />
                <x-text-input id="student_id" name="student_id" type="text" class="mt-1 block w-full" required
                    placeholder="e.g. STU-2026-0001"
                    :value="old('student_id', $student->student_id ?? '')" />
                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="course" value="Course" />
                <x-text-input id="course" name="course" type="text" class="mt-1 block w-full" required
                    :value="old('course', $student->course ?? '')" />
                <x-input-error :messages="$errors->get('course')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="year_level" value="Year Level" />
                <x-select id="year_level" name="year_level" class="mt-1 block w-full" required>
                    @foreach (\App\Enums\YearLevel::cases() as $level)
                        <option value="{{ $level->value }}" @selected(old('year_level', $student->year_level?->value ?? '') === $level->value)>
                            {{ $level->label() }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('year_level')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="status" value="Status" />
                <x-select id="status" name="status" class="mt-1 block w-full" required>
                    @foreach (\App\Enums\StudentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $student->status?->value ?? 'pending') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
        </div>
    </section>

    <section>
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Profile</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="date_of_birth" value="Date of Birth" />
                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full"
                    :value="old('date_of_birth', $student?->date_of_birth?->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="gender" value="Gender" />
                <x-select id="gender" name="gender" class="mt-1 block w-full">
                    <option value="">Prefer not to say</option>
                    @foreach (\App\Enums\Gender::cases() as $gender)
                        <option value="{{ $gender->value }}" @selected(old('gender', $student->gender?->value ?? '') === $gender->value)>
                            {{ $gender->label() }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="phone" value="Phone" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                    :value="old('phone', $student->phone ?? '')" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="address" value="Address" />
                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                    :value="old('address', $student->address ?? '')" />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>
        </div>
    </section>

    <section>
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Guardian Information</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="guardian_name" value="Guardian Name" />
                <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full"
                    :value="old('guardian_name', $student->guardian_name ?? '')" />
                <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="guardian_relationship" value="Relationship" />
                <x-text-input id="guardian_relationship" name="guardian_relationship" type="text" class="mt-1 block w-full"
                    placeholder="e.g. Mother, Father"
                    :value="old('guardian_relationship', $student->guardian_relationship ?? '')" />
                <x-input-error :messages="$errors->get('guardian_relationship')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="guardian_phone" value="Guardian Phone" />
                <x-text-input id="guardian_phone" name="guardian_phone" type="text" class="mt-1 block w-full"
                    :value="old('guardian_phone', $student->guardian_phone ?? '')" />
                <x-input-error :messages="$errors->get('guardian_phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="guardian_email" value="Guardian Email" />
                <x-text-input id="guardian_email" name="guardian_email" type="email" class="mt-1 block w-full"
                    :value="old('guardian_email', $student->guardian_email ?? '')" />
                <x-input-error :messages="$errors->get('guardian_email')" class="mt-2" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="guardian_address" value="Guardian Address" />
                <x-text-input id="guardian_address" name="guardian_address" type="text" class="mt-1 block w-full"
                    :value="old('guardian_address', $student->guardian_address ?? '')" />
                <x-input-error :messages="$errors->get('guardian_address')" class="mt-2" />
            </div>
        </div>
    </section>

    <section>
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Emergency Contact</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="emergency_contact_name" value="Contact Name" />
                <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full"
                    :value="old('emergency_contact_name', $student->emergency_contact_name ?? '')" />
                <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="emergency_contact_relationship" value="Relationship" />
                <x-text-input id="emergency_contact_relationship" name="emergency_contact_relationship" type="text" class="mt-1 block w-full"
                    :value="old('emergency_contact_relationship', $student->emergency_contact_relationship ?? '')" />
                <x-input-error :messages="$errors->get('emergency_contact_relationship')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="emergency_contact_phone" value="Contact Phone" />
                <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="text" class="mt-1 block w-full"
                    :value="old('emergency_contact_phone', $student->emergency_contact_phone ?? '')" />
                <x-input-error :messages="$errors->get('emergency_contact_phone')" class="mt-2" />
            </div>
        </div>
    </section>
</div>
