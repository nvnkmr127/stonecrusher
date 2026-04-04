import './bootstrap';
import '@tabler/core/dist/js/tabler.min.js';

import Alpine from 'alpinejs';
import gatePassForm from './components/gate-pass-form';
import initAjaxLoader from './ajax-loader';

window.Alpine = Alpine;

Alpine.data('gatePassForm', gatePassForm);

// Start AJAX loader for filtering
document.addEventListener('DOMContentLoaded', () => {
    initAjaxLoader();
});

Alpine.start();
