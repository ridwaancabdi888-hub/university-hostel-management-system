@props(['photos' => [], 'placeholderIcon' => 'apartment'])

<div
    x-data="galleryCarousel(@js($photos))"
    x-init="init()"
    x-on:mouseenter="stop()"
    x-on:mouseleave="start()"
    x-on:focusin="stop()"
    x-on:focusout="$nextTick(() => { if (!$el.contains(document.activeElement)) start() })"
    aria-label="Photo gallery"
>
    <template x-if="photos.length === 0">
        <div class="flex h-64 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container/60 to-secondary-container/20 dark:from-night-surface-high dark:to-night-surface">
            <span class="material-symbols-outlined text-[48px] text-primary/40 dark:text-night-primary/40">{{ $placeholderIcon }}</span>
        </div>
    </template>

    <template x-if="photos.length > 0">
        <div>
            <div class="relative h-80 w-full overflow-hidden rounded-lg sm:h-96">
                <template x-for="(photo, index) in photos" :key="index">
                    <img :src="photo" x-show="index === active" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="h-full w-full object-cover" :alt="`Gallery photo ${index + 1} of ${photos.length}`">
                </template>

                <div x-show="photos.length > 1" class="absolute inset-x-3 bottom-3 flex items-center justify-between gap-2">
                    <div class="flex gap-2">
                        <button type="button" @click="previous()" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/65 text-white hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white" aria-label="Show previous photo">
                            <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                        </button>
                        <button type="button" @click="next()" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/65 text-white hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white" aria-label="Show next photo">
                            <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                        </button>
                    </div>
                    <button type="button" @click="togglePaused()" class="inline-flex h-10 items-center gap-1 rounded-full bg-black/65 px-3 font-label-sm text-white hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white" :aria-label="paused ? 'Resume photo rotation' : 'Pause photo rotation'">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true" x-text="paused ? 'play_arrow' : 'pause'"></span>
                        <span x-text="paused ? 'Play' : 'Pause'"></span>
                    </button>
                </div>
            </div>

            <p x-show="photos.length > 1" class="sr-only" aria-live="polite" x-text="`Showing photo ${active + 1} of ${photos.length}`"></p>

            <div class="mt-3 grid grid-cols-3 gap-3">
                <template x-for="(photo, index) in photos" :key="index">
                    <button type="button" @click="select(index)" class="h-20 w-full overflow-hidden rounded-DEFAULT ring-offset-2 transition focus:outline-none focus:ring-2 focus:ring-primary sm:h-24" :class="index === active ? 'ring-2 ring-primary' : ''" :aria-current="index === active ? 'true' : null" :aria-label="`Show photo ${index + 1} of ${photos.length}`">
                        <img :src="photo" class="h-full w-full object-cover transition hover:opacity-80" alt="">
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
