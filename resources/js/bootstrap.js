import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/* ===========================
   jQuery GLOBAL REAL
=========================== */

import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;
globalThis.$ = globalThis.jQuery = jQuery;