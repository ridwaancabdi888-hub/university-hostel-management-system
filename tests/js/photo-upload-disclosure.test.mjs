import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';

import { photoUploadDisclosure } from '../../resources/js/photo-upload-disclosure.js';

test('photo upload disclosure is closed by default and toggles in both directions', () => {
    const disclosure = photoUploadDisclosure();

    assert.equal(disclosure.open, false);
    disclosure.toggle();
    assert.equal(disclosure.open, true);
    disclosure.toggle();
    assert.equal(disclosure.open, false);
});

test('photo upload disclosure can start open for a validation response', () => {
    const disclosure = photoUploadDisclosure(true);

    assert.equal(disclosure.open, true);
    disclosure.close();
    assert.equal(disclosure.open, false);
});

test('block and room upload controls expose their disclosure relationship', async () => {
    const views = [
        ['blocks/show.blade.php', 'block-photo-upload-form'],
        ['rooms/show.blade.php', 'room-photo-upload-form'],
    ];

    for (const [view, formId] of views) {
        const source = await readFile(
            new URL(`../../resources/views/${view}`, import.meta.url),
            'utf8',
        );

        assert.match(source, /x-data="photoUploadDisclosure\(@js\(\$errors->any\(\)\)\)"/);
        assert.match(source, new RegExp(`aria-controls="${formId}"`));
        assert.match(source, /:aria-expanded="open\.toString\(\)"/);
        assert.match(source, new RegExp(`id="${formId}"[^>]+x-show="open"[^>]+x-cloak`));
        assert.doesNotMatch(source, /classList\.toggle/);
    }
});
