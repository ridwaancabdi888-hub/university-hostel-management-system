<x-dashboard-layout :title="$block->name">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('blocks.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">&larr; Back to Blocks</a>
        <a href="{{ route('blocks.edit', $block) }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Edit Block
        </a>
    </div>

    <div class="glass-card rounded-lg p-lg">
        <h2 class="font-headline-sm text-on-surface dark:text-night-on-surface">{{ $block->name }}</h2>
        <p class="mt-1 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">
            {{ $block->hostel->name }}{{ $block->code ? ' · '.$block->code : '' }} &middot; {{ $floorCount }} floor{{ $floorCount === 1 ? '' : 's' }}
        </p>
        @if ($block->description)
            <p class="mt-3 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $block->description }}</p>
        @endif
    </div>

    <div class="glass-card mt-6 rounded-lg p-lg" x-data="{}">
        <div class="flex items-center justify-between">
            <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Gallery</h3>
            <button type="button" @click="$refs.photoForm.classList.toggle('hidden')" class="inline-flex items-center gap-2 font-label-md font-medium text-primary hover:underline dark:text-night-primary">
                <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span>
                Add Photo
            </button>
        </div>

        <form x-ref="photoForm" method="POST" action="{{ route('blocks.photos', $block) }}" enctype="multipart/form-data" class="{{ $errors->any() ? '' : 'hidden' }} mt-4 rounded-DEFAULT border border-outline-variant/30 p-md dark:border-night-border">
            @csrf
            <x-input-label for="photos" value="Upload up to 4 photos (replaces the current gallery)" />
            <input id="photos" name="photos[]" type="file" accept="image/*" multiple
                class="mt-1 block w-full font-body-md text-on-surface-variant file:mr-4 file:rounded-DEFAULT file:border-0 file:bg-secondary-container/50 file:px-4 file:py-2 file:font-label-md file:font-semibold file:text-primary hover:file:bg-secondary-container dark:text-night-on-surface-variant dark:file:bg-night-secondary-container dark:file:text-night-primary">
            <x-input-error :messages="$errors->get('photos')" class="mt-2" />
            <x-input-error :messages="$errors->get('photos.0')" class="mt-2" />
            <div class="mt-3">
                <x-primary-button type="submit">Save Photos</x-primary-button>
            </div>
        </form>

        <div class="mt-4">
            <x-photo-gallery :photos="$block->photoUrls()" placeholder-icon="apartment" />
        </div>
    </div>
</x-dashboard-layout>
