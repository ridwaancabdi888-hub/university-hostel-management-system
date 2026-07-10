@props(['roomType' => null])

<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus
        placeholder="e.g. Single Premium"
        :value="old('name', $roomType->name ?? '')" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="default_capacity" value="Default Bed Capacity" />
    <x-text-input id="default_capacity" name="default_capacity" type="number" min="1" max="20" class="mt-1 block w-full" required
        :value="old('default_capacity', $roomType->default_capacity ?? '')" />
    <x-input-error :messages="$errors->get('default_capacity')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="monthly_rate" value="Monthly Rent ($)" />
    <x-text-input id="monthly_rate" name="monthly_rate" type="number" step="0.01" min="0" max="99999.99" class="mt-1 block w-full" required
        :value="old('monthly_rate', $roomType->monthly_rate ?? '')" />
    <x-input-error :messages="$errors->get('monthly_rate')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('description', $roomType->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="amenities" value="Amenities (comma-separated)" />
    <x-text-input id="amenities" name="amenities" type="text" class="mt-1 block w-full"
        placeholder="e.g. Private Bathroom, Air Conditioning, Study Desk"
        :value="old('amenities', implode(', ', $roomType->amenities ?? []))" />
    <x-input-error :messages="$errors->get('amenities')" class="mt-2" />
</div>
