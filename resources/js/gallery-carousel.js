export function galleryCarousel(photos, environment = {}) {
    const timerApi = environment.timerApi ?? globalThis;
    const page = environment.document ?? globalThis.document;
    const prefersReducedMotion = environment.prefersReducedMotion
        ?? (() => globalThis.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false);

    return {
        photos: Array.isArray(photos) ? photos : [],
        active: 0,
        timer: null,
        paused: false,
        reducedMotion: false,
        visibilityHandler: null,

        init() {
            this.reducedMotion = prefersReducedMotion();
            this.paused = this.reducedMotion;
            this.visibilityHandler = () => page.hidden ? this.stop() : this.start();
            page.addEventListener?.('visibilitychange', this.visibilityHandler);
            this.start();
        },

        destroy() {
            this.stop();
            page.removeEventListener?.('visibilitychange', this.visibilityHandler);
        },

        start() {
            this.stop();
            if (this.photos.length < 2 || this.paused || this.reducedMotion || page.hidden) return;
            this.timer = timerApi.setInterval(() => this.next(), 5000);
        },

        stop() {
            if (this.timer !== null) timerApi.clearInterval(this.timer);
            this.timer = null;
        },

        next() {
            if (this.photos.length) this.active = (this.active + 1) % this.photos.length;
        },

        previous() {
            if (this.photos.length) this.active = (this.active - 1 + this.photos.length) % this.photos.length;
        },

        select(index) {
            if (index >= 0 && index < this.photos.length) this.active = index;
        },

        togglePaused() {
            if (this.reducedMotion && this.paused) this.reducedMotion = false;
            this.paused = !this.paused;
            this.paused ? this.stop() : this.start();
        },
    };
}

window.galleryCarousel = galleryCarousel;
