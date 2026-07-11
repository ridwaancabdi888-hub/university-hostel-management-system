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

<div class="mt-4">
    <x-input-label for="photo" value="Block Photo" />
    <input id="photo" name="photo" type="file" accept="image/*"
        class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-500/10 dark:file:text-indigo-400">
    <x-input-error :messages="$errors->get('photo')" class="mt-2" />

    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Or paste an image URL instead:</p>
    <x-text-input id="photo_url" name="photo_url" type="url" class="mt-1 block w-full" placeholder="https://example.com/block-photo.jpg"
        :value="old('photo_url')" />
    <x-input-error :messages="$errors->get('photo_url')" class="mt-2" />

    @if ($block?->photoUrl())
        <div class="mt-2 flex items-center gap-2">
            <img src="{{ $block->photoUrl() }}" alt="{{ $block->name }}" class="h-12 w-20 rounded-md object-cover">
            <span class="text-xs text-gray-500 dark:text-gray-400">Current photo — upload a file or paste a URL to replace it.</span>
        </div>
    @endif
</div>
