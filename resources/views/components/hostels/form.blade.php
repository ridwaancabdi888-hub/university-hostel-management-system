@props(['hostel' => null])

<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus
        :value="old('name', $hostel->name ?? '')" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="code" value="Code" />
    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
        :value="old('code', $hostel->code ?? '')" />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="address" value="Address" />
    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
        :value="old('address', $hostel->address ?? '')" />
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('description', $hostel->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
