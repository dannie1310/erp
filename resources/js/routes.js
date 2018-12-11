//Components
import Home from './components/pages/Home.vue';
import Login from './components/auth/Login.vue';
import Obras from './components/auth/Obras.vue';
import NotFound from './components/pages/NotFound.vue';

//Middlewares
import auth from "./middleware/auth";
import guest from "./middleware/guest";
import context from "./middleware/context";

//Routes
export const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: {
            title: 'INICIO',
            middleware: [auth, context],
            breadcrumb: [{
                name: 'INICIO'
            }]
        },
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            title: 'INICIAR SESIÓN',
            middleware: [guest]
        },
    },
    {
        path: '/obras',
        name: 'obras',
        component: Obras,
        meta: {
            title: 'SELECCIONAR OBRA',
            middleware: auth,
            breadcrumb: [{
                name: 'SELECCIONAR OBRA'
            }]
        }
    },
    {
        path: '*',
        name: 'notFound',
        component: NotFound,
    }
];