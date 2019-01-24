require('./bootstrap');
import Vue from 'vue';
import VueRouter from 'vue-router';
import VueSession from 'vue-session';
import VeeValidate, { Validator } from 'vee-validate';
import es from 'vee-validate/dist/locale/es';
import Datatable from 'vue2-datatable-component';
import './utils';

const VueInputMask = require('vue-inputmask').default;

Vue.use(VueInputMask);

import {routes} from './routes';
import store from './store';
import MainApp from './components/MainApp.vue';

Vue.use(VueRouter);
Vue.use(VueSession, {persist: true});
Vue.use(VeeValidate);
Validator.localize('es', es);

Vue.use(Datatable, {
    locale: {
        'Apply': 'Aplicar',
        'Apply and backup settings to local': 'Aplicar y gurdar la configuración local',
        'Clear local settings backup and restore': 'Limpiar la configuración local',
        'Using local settings': 'Usar configuración local',

        /* Table/TableBody.vue */
        'No Data': 'Sin resultados',

        /* index.vue */
        'Total': 'Total',
        ',': ',',

        /* PageSizeSelect.vue */
        'items / page': 'elementos / pagina'
    }
});

const router = new VueRouter({
    routes,
    mode: 'history'
});

// Creates a `nextMiddleware()` function which not only
// runs the default `next()` callback but also triggers
// the subsequent Middleware function.
function nextFactory(context, middleware, index) {
   const subsequentMiddleware = middleware[index];
   // If no subsequent Middleware exists,
   // the default `next()` callback is returned.
   if (!subsequentMiddleware) return context.next;

   return (...parameters) => {
       // Run the default Vue Router `next()` callback first.
       context.next(...parameters);
       // Then run the subsequent Middleware with a new
       // `nextMiddleware()` callback.
       const nextMiddleware = nextFactory(context, middleware, index + 1);
       subsequentMiddleware({...context, next: nextMiddleware});
   };
}

router.beforeEach((to, from, next) => {
    document.title = 'SAO - ' + to.meta.title;
    if (to.meta.middleware) {
        const middleware = Array.isArray(to.meta.middleware)
            ? to.meta.middleware
            : [to.meta.middleware];

        const context = {
            from,
            next,
            router,
            to,
        };
        const nextMiddleware = nextFactory(context, middleware, 1);

        return middleware[0]({...context, next: nextMiddleware});
    }
    return next();
});

const app = new Vue({
    el: '#app',
    router,
    store,
    components: {
        MainApp
    },
    mounted() {
        axios.interceptors.response.use(response => {
            return response;
        }, error => {
            let code = error.response.status
            switch (true) {
                case (code === 401):

                default:
                    return Promise.reject(error);
            }
        });

        axios.interceptors.response.use((response) => {
            return response;
        }, (error) => {
            if (!error.response) {
                alert('NETWORK ERROR')
            } else {
                const code = error.response.status
                const response = error.response.data
                const originalRequest = error.config;

                if (code === 401 && !originalRequest._retry) {
                    originalRequest._retry = true
                    app.$session.destroy();
                    window.location.href = "/login";
                }

                return Promise.reject(error)
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
