import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';

import { dropdownMenu } from '../../resources/js/dropdown-menu.js';

test('dropdown menu toggles and closes predictably', () => {
    const menu = dropdownMenu();

    assert.equal(menu.open, false);
    menu.toggle();
    assert.equal(menu.open, true);
    menu.close();
    assert.equal(menu.open, false);
});

test('closing with Escape restores focus to the trigger', () => {
    const menu = dropdownMenu();
    const trigger = {
        isConnected: true,
        focused: false,
        focus() {
            this.focused = true;
        },
    };

    menu.open = true;
    menu.$refs = { trigger };
    menu.$nextTick = (callback) => callback();
    menu.closeAndRestoreFocus();

    assert.equal(menu.open, false);
    assert.equal(trigger.focused, true);
});

test('focus is not restored to a trigger removed from the page', () => {
    const menu = dropdownMenu();
    const trigger = {
        isConnected: false,
        focus() {
            throw new Error('removed trigger must not receive focus');
        },
    };

    menu.open = true;
    menu.$refs = { trigger };
    menu.$nextTick = (callback) => callback();
    menu.closeAndRestoreFocus();

    assert.equal(menu.open, false);
});

test('navbar dropdown triggers expose state and panel relationships', async () => {
    const navbar = await readFile(
        new URL('../../resources/views/components/navbar.blade.php', import.meta.url),
        'utf8',
    );
    const dropdown = await readFile(
        new URL('../../resources/views/components/dropdown.blade.php', import.meta.url),
        'utf8',
    );

    assert.equal((navbar.match(/x-ref="trigger"/g) ?? []).length, 2);
    assert.equal((navbar.match(/:aria-controls="\$id\('dropdown-panel'\)"/g) ?? []).length, 2);
    assert.equal((navbar.match(/:aria-expanded="open\.toString\(\)"/g) ?? []).length, 2);
    assert.match(dropdown, /@keydown\.escape\.prevent\.stop="closeAndRestoreFocus\(\)"/);
    assert.match(dropdown, /:id="\$id\('dropdown-panel'\)"/);
});
