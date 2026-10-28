import './bootstrap';
import './gallery-carousel';
import * as modalFocus from './modal-focus';
import { photoUploadDisclosure } from './photo-upload-disclosure';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.modalFocus = modalFocus;
window.photoUploadDisclosure = photoUploadDisclosure;

Alpine.start();
