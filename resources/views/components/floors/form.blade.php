@props(['floor' => null, 'blocks'])

<div>
    <x-input-label for="block_id" value="Block" />
    <x-select id="block_id" name="block_id" class="mt-1 block w-full" required>
        <option value="">Select a block</option>
        @foreach ($blocks as $block)
            <option value="{{ $block->id }}" @selected(old('block_id', $floor->block_id ?? '') == $block->id)>
                {{ $block->name }} ({{ $block->hostel->name }})
            </option>
        @endforeach
    </x-select>
    <x-input-error :messages="$errors->get('block_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required
        placeholder="e.g. Ground Floor"
        :value="old('name', $floor->name ?? '')" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="level" value="Level" />
    <x-text-input id="level" name="level" type="number" min="0" class="mt-1 block w-full" required
        placeholder="e.g. 0 for ground floor, 1 for 1st floor"
        :value="old('level', $floor->level ?? '')" />
    <x-input-error :messages="$errors->get('level')" class="mt-2" />
</div>
