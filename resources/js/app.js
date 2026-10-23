import './bootstrap';
import './gallery-carousel';
import * as modalFocus from './modal-focus';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.modalFocus = modalFocus;

Alpine.start();
