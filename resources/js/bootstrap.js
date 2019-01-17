
window._ = require('lodash');

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    require('admin-lte');
    window.Swal = require('sweetalert2');
    window.moment = require('moment');
    require('moment/locale/es');
    moment.locale('es');
    require('admin-lte/plugins/daterangepicker/daterangepicker')
    require('admin-lte/plugins/datepicker/bootstrap-datepicker')
    require('admin-lte/plugins/datepicker/locales/bootstrap-datepicker.es')
} catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
