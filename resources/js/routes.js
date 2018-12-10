//Components
import Home from './components/Home.vue';
import Login from './components/auth/Login.vue';
import Dashboard from './components/Dashboard.vue';

//Middlewares
import auth from "./middleware/auth";
import guest from "./middleware/guest";

//Routes
export const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: {
            middleware: [guest]
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
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: {
            middleware: [auth]
        },
    },

];