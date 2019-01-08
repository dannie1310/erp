//Middlewares
import auth from "./middleware/auth";
import guest from "./middleware/guest";
import context from "./middleware/context";


//Routes
export const routes = [
    {
        path: '/',
        name: 'home',
        component: require('./components/pages/Home.vue'),
        meta: {
            title: 'Inicio',
            middleware: [auth, context],
            breadcrumb: {name: 'INICIO'}
        }
    },
    {
        path: '/login',
        name: 'login',
        component: require('./components/pages/Login.vue'),
        meta: {
            title: 'INICIAR SESIÓN',
            middleware: guest,
        },
    },
    {
        path: '/obras',
        name: 'obras',
        component: require('./components/pages/Obras.vue'),
        meta: {
            title: 'Seleccionar Obra',
            middleware: auth,
            breadcrumb: {name: 'SELECCIONAR OBRA'}
        }
    },
    {
        path: '/contabilidad',
        components: {
            default: require('./components/contabilidad/partials/Layout.vue'),
            menu: require('./components/contabilidad/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'contabilidad',
                component: require('./components/contabilidad/Index'),
                meta: {
                    title: 'Contabilidad',
                    breadcrumb: { parent: 'home', name: 'CONTABILIDAD'},
                    middleware: [auth, context]
                }
            },
            {
                path: 'cuenta-almacen',
                name: 'cuenta-almacen',
                component: require('./components/contabilidad/cuenta-almacen/Index'),
                meta: {
                    title: 'Cuentas de Almacén',
                    breadcrumb: {name: 'CUENTAS DE ALMACÉN', parent: 'contabilidad'},
                    middleware: [auth, context]
                }
            },
            {
                path: 'poliza',
                component: require('./components/contabilidad/poliza/Layout.vue'),
                meta: {
                    middleware: [auth, context]
                },
                children: [
                    {
                        path: '/',
                        name: 'poliza',
                        component: require('./components/contabilidad/poliza/Index'),
                        meta: {
                            title: 'Prepólizas Generadas',
                            breadcrumb: {parent: 'contabilidad', name: 'PREPÓLIZAS GENERADAS'},
                            middleware: [auth, context]
                        }
                    },
                    {
                        path: ':id',
                        name: 'poliza-show',
                        props: true,
                        component: require('./components/contabilidad/poliza/Show'),
                        meta: {
                            title: 'Ver Prepóliza Generada',
                            breadcrumb: {parent: 'poliza', name: 'VER'},
                            middleware: [auth, context]
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'poliza-edit',
                        component: require('./components/contabilidad/poliza/Edit'),
                        props: true,
                        meta: {
                            title: 'Editar Prepóliza Generada',
                            breadcrumb: { parent: 'poliza-show', name: 'EDITAR'},
                            middleware: [auth, context]
                        }
                    }
                ]
            }
        ]
    },
    {
        path: '*',
        name: 'notFound',
        component: require('./components/pages/NotFound.vue'),
    }
];