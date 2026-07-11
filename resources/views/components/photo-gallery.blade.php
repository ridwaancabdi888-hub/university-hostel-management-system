@props(['photos' => [], 'placeholderIcon' => 'apartment'])

<div
    x-data="{
        photos: @js($photos),
        active: 0,
        timer: null,
        start() {
            if (this.photos.length > 1) {
                this.timer = setInterval(() => { this.active = (this.active + 1) % this.photos.length }, 5000);
            }
        },
    }"
    x-init="start()"
>
    <template x-if="photos.length === 0">
        <div class="flex h-64 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container/60 to-secondary-container/20 dark:from-night-surface-high dark:to-night-surface">
            <span class="material-symbols-outlined text-[48px] text-primary/40 dark:text-night-primary/40">{{ $placeholderIcon }}</span>
        </div>
    </template>

    <template x-if="photos.length > 0">
        <div>
            <div class="h-80 w-full overflow-hidden rounded-lg sm:h-96">
                <template x-for="(photo, index) in photos" :key="index">
                    <img :src="photo" x-show="index === active" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="h-full w-full object-cover" alt="Photo">
                </template>
            </div>

            <div class="mt-3 grid grid-cols-3 gap-3">
                <template x-for="(photo, index) in photos" :key="index">
                    <button type="button" x-show="index !== active" @click="active = index; clearInterval(timer); start()" class="h-20 w-full overflow-hidden rounded-DEFAULT sm:h-24">
                        <img :src="photo" class="h-full w-full object-cover transition hover:opacity-80" alt="Photo thumbnail">
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
