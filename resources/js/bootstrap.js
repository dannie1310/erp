window._ = require('lodash');

try {
    window.$ = window.jQuery = require('jquery');

    require('admin-lte/plugins/bootstrap/js/bootstrap');
    require('admin-lte');
    window.swal = require('sweetalert');
    window.moment = require('moment');
    require('moment/locale/es');
    moment.locale('es');
    require('admin-lte/plugins/daterangepicker/daterangepicker')
    require('admin-lte/plugins/datepicker/bootstrap-datepicker')
    require('admin-lte/plugins/datepicker/locales/bootstrap-datepicker.es')
    window.Chart = require('chart.js')
} catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
