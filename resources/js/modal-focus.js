export const FOCUSABLE_SELECTOR = [
    'a[href]',
    'button',
    'input:not([type="hidden"])',
    'textarea',
    'select',
    'details',
    '[tabindex]:not([tabindex="-1"])',
].join(', ');

export function getFocusableElements(root) {
    if (! root?.querySelectorAll) return [];

    return [...root.querySelectorAll(FOCUSABLE_SELECTOR)].filter((element) =>
        ! element.disabled
        && ! element.hidden
        && element.getAttribute?.('aria-hidden') !== 'true',
    );
}

export function getAdjacentFocusable(root, current, direction = 1) {
    const elements = getFocusableElements(root);

    if (elements.length === 0) return root ?? null;

    const currentIndex = elements.indexOf(current);
    if (currentIndex === -1) {
        return direction < 0 ? elements.at(-1) : elements[0];
    }

    const offset = direction < 0 ? -1 : 1;
    return elements[(currentIndex + offset + elements.length) % elements.length];
}

export function restoreFocus(element) {
    if (element?.isConnected !== false && typeof element?.focus === 'function') {
        element.focus();
        return true;
    }

    return false;
}
