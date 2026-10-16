import assert from 'node:assert/strict';
import test from 'node:test';

globalThis.window = {};
const { galleryCarousel } = await import('../../resources/js/gallery-carousel.js');

function harness({ photos = ['one', 'two', 'three'], reducedMotion = false } = {}) {
    let intervalCallback = null;
    let cleared = 0;
    let visibilityHandler = null;
    const page = {
        hidden: false,
        addEventListener(name, callback) {
            if (name === 'visibilitychange') visibilityHandler = callback;
        },
        removeEventListener(name, callback) {
            if (name === 'visibilitychange' && callback === visibilityHandler) visibilityHandler = null;
        },
    };
    const timerApi = {
        setInterval(callback) {
            intervalCallback = callback;
            return 17;
        },
        clearInterval() {
            cleared += 1;
            intervalCallback = null;
        },
    };
    const carousel = galleryCarousel(photos, {
        document: page,
        timerApi,
        prefersReducedMotion: () => reducedMotion,
    });

    return {
        carousel,
        page,
        tick: () => intervalCallback?.(),
        visibilityChange: () => visibilityHandler?.(),
        intervalActive: () => intervalCallback !== null,
        clearCount: () => cleared,
    };
}

test('navigation wraps in both directions and rejects invalid selections', () => {
    const { carousel } = harness();
    carousel.previous();
    assert.equal(carousel.active, 2);
    carousel.next();
    assert.equal(carousel.active, 0);
    carousel.select(1);
    assert.equal(carousel.active, 1);
    carousel.select(99);
    assert.equal(carousel.active, 1);
});

test('automatic rotation advances and can be paused and resumed', () => {
    const { carousel, tick, intervalActive } = harness();
    carousel.init();
    assert.equal(intervalActive(), true);
    tick();
    assert.equal(carousel.active, 1);
    carousel.togglePaused();
    assert.equal(intervalActive(), false);
    carousel.togglePaused();
    assert.equal(intervalActive(), true);
});

test('reduced-motion preference disables automatic rotation', () => {
    const { carousel, intervalActive } = harness({ reducedMotion: true });
    carousel.init();
    assert.equal(carousel.paused, true);
    assert.equal(intervalActive(), false);
    carousel.togglePaused();
    assert.equal(intervalActive(), true, 'an explicit Play action may opt in to rotation');
});

test('rotation stops while the page is hidden and resumes when visible', () => {
    const { carousel, page, visibilityChange, intervalActive } = harness();
    carousel.init();
    page.hidden = true;
    visibilityChange();
    assert.equal(intervalActive(), false);
    page.hidden = false;
    visibilityChange();
    assert.equal(intervalActive(), true);
    carousel.destroy();
    assert.equal(intervalActive(), false);
});

test('empty and single-photo galleries never start a timer', () => {
    for (const photos of [[], ['only']]) {
        const { carousel, intervalActive } = harness({ photos });
        carousel.init();
        assert.equal(intervalActive(), false);
    }
});
