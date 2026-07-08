@props(['block' => null, 'hostels'])

<div>
    <x-input-label for="hostel_id" value="Hostel" />
    <x-select id="hostel_id" name="hostel_id" class="mt-1 block w-full" required>
        <option value="">Select a hostel</option>
        @foreach ($hostels as $hostel)
            <option value="{{ $hostel->id }}" @selected(old('hostel_id', $block->hostel_id ?? '') == $hostel->id)>
                {{ $hostel->name }}
            </option>
        @endforeach
    </x-select>
    <x-input-error :messages="$errors->get('hostel_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required
        :value="old('name', $block->name ?? '')" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="code" value="Code" />
    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
        :value="old('code', $block->code ?? '')" />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('description', $block->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
