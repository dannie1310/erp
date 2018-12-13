require('./bootstrap');
import Vue from 'vue';
import VueRouter from 'vue-router';
import VueSession from 'vue-session';
import VeeValidate from 'vee-validate';
import {routes} from './routes';
import store from './store';
import MainApp from './components/MainApp.vue';

Vue.use(VueRouter);
Vue.use(VueSession, {persist: true});
Vue.use(VeeValidate);

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
    document.title = to.meta.title;
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

/*const files = require.context('./', true, /\.vue$/i)

files.keys().map(key => {
    return Vue.component(_.last(key.split('/')).split('.')[0], files(key))
});*/

const app = new Vue({
    el: '#app',
    router,
    store,
    components: {
        MainApp
    },
    mounted() {
        axios.interceptors.response.use(null, function(error) {
            switch (error.response.status) {
                case 401:
                    app.$store.commit('auth/logout');
                    app.$session.destroy();
                    return app.$router.push({ name: 'login' });

                default:
                    return Promise.reject(error);
            }
        });
    }
});
