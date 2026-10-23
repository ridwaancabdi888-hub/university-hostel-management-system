import assert from 'node:assert/strict';
import test from 'node:test';

import {
    getAdjacentFocusable,
    getFocusableElements,
    restoreFocus,
} from '../../resources/js/modal-focus.js';

function element({ disabled = false, hidden = false, ariaHidden = null, connected = true } = {}) {
    return {
        disabled,
        hidden,
        isConnected: connected,
        focused: false,
        getAttribute(name) {
            return name === 'aria-hidden' ? ariaHidden : null;
        },
        focus() {
            this.focused = true;
        },
    };
}

function root(elements = []) {
    return {
        focused: false,
        querySelectorAll() {
            return elements;
        },
        focus() {
            this.focused = true;
        },
    };
}

test('focusable controls exclude disabled, hidden, and aria-hidden elements', () => {
    const available = element();
    const container = root([
        available,
        element({ disabled: true }),
        element({ hidden: true }),
        element({ ariaHidden: 'true' }),
    ]);

    assert.deepEqual(getFocusableElements(container), [available]);
});

test('focus navigation wraps in both directions', () => {
    const first = element();
    const middle = element();
    const last = element();
    const container = root([first, middle, last]);

    assert.equal(getAdjacentFocusable(container, last, 1), first);
    assert.equal(getAdjacentFocusable(container, first, -1), last);
    assert.equal(getAdjacentFocusable(container, middle, 1), last);
});

test('an empty dialog falls back to the dialog panel itself', () => {
    const container = root();

    assert.equal(getAdjacentFocusable(container, null), container);
});

test('focus is restored only to a usable trigger', () => {
    const trigger = element();
    const removedTrigger = element({ connected: false });

    assert.equal(restoreFocus(trigger), true);
    assert.equal(trigger.focused, true);
    assert.equal(restoreFocus(removedTrigger), false);
    assert.equal(removedTrigger.focused, false);
    assert.equal(restoreFocus(null), false);
});
