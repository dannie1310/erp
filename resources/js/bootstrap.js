window._ = require('lodash');

try {
    window.$ = window.jQuery = require('jquery');
    require('jquery-ui/ui/widgets/autocomplete');
    window.PromiseWindow = require('promise-window');
    require('admin-lte/plugins/bootstrap/js/bootstrap');
    require('admin-lte');
    window.swal = require('sweetalert');
    window.moment = require('moment');
    require('moment/locale/es');
    moment.locale('es');
    require('admin-lte/plugins/daterangepicker/daterangepicker');
    require('admin-lte/plugins/bootstrap-slider/css/bootstrap-slider.css');
    require('admin-lte/plugins/ion-rangeslider/css/ion.rangeSlider.css');
    /*require('admin-lte/plugins/datepicker/bootstrap-datepicker')*/
    /*require('admin-lte/plugins/datepicker/locales/bootstrap-datepicker.es')*/
    window.Chart = require('chart.js');
    window.VueTreeselect = require('@riophae/vue-treeselect');
    window.LOAD_CHILDREN_OPTIONS = require('@riophae/vue-treeselect');
    require('bootstrap-pincode-input');
} catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

axios.interceptors.response.use((response) => {
    if(response.data.message == 'code_not_send') {
        return new Promise((resolve, reject) => {
            axios
                .get('/api/SEGURIDAD_ERP/google-2fa/isVerified')
                .then(r => r.data)
                .then(data => {
                    var w = 500;
                    var h = data.verified ? 250 : 512.5;
                    var left = (screen.width/2)-(w/2);
                    var top = (screen.height/2)-(h/2);

                    PromiseWindow.open('/google-2fa?verified=' + data.verified, {
                        width: w,
                        height: h,
                        window: {
                            scrollbars: 'no',
                            toolbar: 'no',
                            resizable: 'no',
                            top: top,
                            left: left
                        },
                        windowName: 'Verificación de dos pasos Google Auth'
                    }).then((data) => {
                            response.config.headers.verification_code = data.result;
                            axios.request(response.config)
                                .then(data => {
                                    resolve(data);
                                    return response;
                                }).catch(error => {
                                    reject(error);
                                });
                        })
                        .catch(error => {
                            swal({
                                title: "¡Error!",
                                text: 'No se envió el código de verificación',
                                icon: "error"
                            });
                        })
                });
        })
            .then(data => {
                return data;
            })
    } else {
        return response;
    }
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
            case (code === 466):
                swal({
                    title: "¡Error!",
                    text: message,
                    icon: "error"
                });
                return error.response;
                break;
            case (code === 400):
                swal({
                    title: "Atención",
                    text: message,
                    icon: "warning"
                });
                break;
            case (code === 399):
                swal({
                    title: "Atención",
                    text: message,
                    icon: "warning",
                }).then((value) => {
                    window.history.back();
                });
                break;
            default:
                swal({
                    title: "¡Error!",
                    text: message,
                    icon: "error"
                });
                break;
        }
    }
});
