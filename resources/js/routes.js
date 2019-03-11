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
                name: 'sistema_contable',
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
                path: 'cuenta-banco',
                name: 'cuenta-banco',
                component: require('./components/contabilidad/cuenta-banco/Index'),
                meta: {
                    title: 'Cuentas de Bancos',
                    breadcrumb: {name: 'CUENTAS DE BANCOS', parent: 'contabilidad'},
                    middleware: [auth, context]
                }
            },
            {
                path: 'cuenta-empresa',
                name: 'cuenta-empresa',
                component: require('./components/contabilidad/cuenta-empresa/Index'),
                meta: {
                    title: 'Cuentas de Empresas',
                    breadcrumb: {name: 'CUENTAS DE EMPRESAS', parent: 'contabilidad'},
                    middleware: [auth, context]
                }
            },
            {
                path: 'cuenta-fondo',
                name: 'cuenta-fondo',
                component: require('./components/contabilidad/cuenta-fondo/Index'),
                meta: {
                    title: 'Cuentas de Fondos',
                    breadcrumb: {name: 'CUENTAS DE FONDOS', parent: 'contabilidad'},
                    middleware: [auth, context]
                }
            },
            {
                path: 'cuenta-general',
                name: 'cuenta-general',
                component: require('./components/contabilidad/cuenta-general/Index'),
                meta: {
                    title: 'Cuentas Generales',
                    breadcrumb: {name: 'CUENTAS GENERALES', parent: 'contabilidad'},
                    middleware: [auth, context]
                }
            },
            {
                path: 'cuenta-material',
                name: 'cuenta-material',
                component: require('./components/contabilidad/cuenta-material/Index'),
                meta: {
                    title: 'Cuentas de Materiales',
                    breadcrumb: {name: 'CUENTAS DE MATERIALES', parent: 'contabilidad'},
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
        path: '/tesoreria',
        components: {
            default: require('./components/tesoreria/partials/Layout.vue'),
            menu: require('./components/tesoreria/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'tesoreria',
                component: require('./components/tesoreria/Index'),
                meta: {
                    title: 'Tesorería',
                    breadcrumb: { parent: 'home', name: 'TESORERIA'},
                    middleware: [auth, context]
                }
            },
            {
                path: 'movimiento-bancario',
                component: require('./components/tesoreria/movimiento-bancario/Layout.vue'),
                meta: {
                    middleware: [auth, context]
                },
                children: [
                    {
                        path: '/',
                        name: 'movimiento-bancario',
                        component: require('./components/tesoreria/movimiento-bancario/Index'),
                        meta: {
                            title: 'Movimientos Bancarios',
                            breadcrumb: {parent: 'tesoreria', name: 'MOVIMIENTOS BANCARIOS'},
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