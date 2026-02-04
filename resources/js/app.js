import './bootstrap';
// import '@tabler/core/dist/js/tabler.min.js';

import Alpine from 'alpinejs';
import gatePassForm from './components/gate-pass-form';

window.Alpine = Alpine;

Alpine.data('gatePassForm', gatePassForm);

Alpine.start();
