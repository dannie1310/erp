require('./bootstrap');
import Vue from 'vue';
import VueSession from 'vue-session';
import VeeValidate, { Validator } from 'vee-validate';
import es from 'vee-validate/dist/locale/es';
import Datatable from 'vue2-datatable-component';
import './utils';

const VueInputMask = require('vue-inputmask').default;

Vue.use(VueInputMask);

import store from './store';
import MainApp from './components/MainApp.vue';
import {es as datatableEs} from "./datatable.es";
import VueProgressBar from 'vue-progressbar';
import router from './router';


Vue.use(VueSession, {persist: true});
Vue.use(VeeValidate);
Vue.component('treeselect', VueTreeselect.Treeselect);
Validator.localize('es', es);
Vue.use(Datatable, { locale: datatableEs });

Vue.use(VueProgressBar, {
    color: '#68a34d',
    failedColor: 'red',
    height: '10px'
});



const app = new Vue({
    el: '#app',
    router,
    store,
    components: {
        MainApp
    },
    mounted() {
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
                            app.$store.commit('auth/logout');
                            app.$session.destroy();
                            return app.$router.push({name: 'login'});
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
    },

    methods: {
        can(permiso) {
            let permisos = this.$session.get('permisos');

            if (permisos) {
                return permisos.find(perm => {
                    return perm.name == permiso;
                })
            }

            return false;
        }
    }
});
