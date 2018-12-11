//Components
import Home from './components/Home.vue';
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
            middleware: [auth, context]
        },
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            middleware: [guest]
        },
    },
    {
        path: '/obras',
        name: 'obras',
        component: Obras,
        meta: {
            middleware: auth
        }
    },
    {
        path: '*',
        name: 'notFound',
        component: NotFound,
    }
];