window._ = require('lodash');

try {
    window.$ = window.jQuery = require('jquery');
    require('jquery-ui/ui/widgets/autocomplete');

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
    window.VueTreeselect = require('@riophae/vue-treeselect');
    window.LOAD_CHILDREN_OPTIONS = require('@riophae/vue-treeselect');
    require('bootstrap-pincode-input');
} catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

axios.interceptors.response.use((response) => {
    return response;
}, (error) => {
    if (!error.response) {
        alert('NETWORK ERROR')
    } else {
        const code = error.response.status
        const message = error.response.data.message
        const originalRequest = error.config;
        switch (true) {
            case (code === 401 && !originalRequest._retry):
                swal({
                    title: "La sesión ha expirado",
                    text: "Volviendo a la página de Inicio de Sesión",
                    icon: "error",
                }).then((value) => {
                    localStorage.clear();
                    window.location.href = 'login';
                })
                break;
            case (code === 500):
                swal({
                    title: "¡Error!",
                    text: message,
                    icon: "error"
                });
                break;
            default:
                swal({
                    title: "¡Error!",
                    text: message,
                    icon: "error"
                });
        }
    }
});
