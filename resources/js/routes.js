//Middlewares
import access from "./middleware/access";
import auth from "./middleware/auth";
import guest from "./middleware/guest";
import context from "./middleware/context";
import permission from "./middleware/permission";


//Routes
export const routes = [
    {
        path: '/',
        name: 'portal',
        components: {
            default: require('./components/pages/Portal.vue'),
            menu: null
        },
        meta: {
            title: 'Índice de Aplicaciones',
            middleware: [auth]
        }
    },
    {
        path: '/sao',
        name: 'home',
        component: require('./components/pages/Home.vue'),
        meta: {
            title: 'Inicio',
            middleware: [auth, context],
            breadcrumb: {name: 'INICIO'}
        }
    },
    {
        path: '/sao/configuracion',
        name: 'configuracion',
        components: {
            default: require('./components/configuracion/Index.vue'),
            menu: require('./components/configuracion/partials/Menu.vue')
        },
        meta: {
            title: 'CONFIGURACIÓN',
            middleware: [auth, context, access],
            breadcrumb: {
                name: 'CONFIGURACIÓN',
                parent: 'home'
            },
            permission: 'administracion_configuracion_obra'
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
        path: '/sao/obras',
        name: 'obras',
        component: require('./components/pages/Obras.vue'),
        meta: {
            title: 'Seleccionar Obra',
            middleware: auth,
            breadcrumb: {name: 'SELECCIONAR OBRA'}
        }
    },
    {
        path: '/sao/contabilidad',
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
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'cierre-periodo',
                name: 'cierre-periodo',
                component: require('./components/contabilidad/cierre-periodo/Index'),
                meta: {
                    title: 'Cierres de periodo',
                    breadcrumb: {name: 'CIERRES DE PERIODO', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cierre_periodo'
                }
            },
            {
                path: 'cuenta-almacen',
                name: 'cuenta-almacen',
                component: require('./components/contabilidad/cuenta-almacen/Index'),
                meta: {
                    title: 'Cuentas de Almacén',
                    breadcrumb: {name: 'CUENTAS DE ALMACÉN', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_almacen'
                }
            },
            {
                path: 'cuenta-banco',
                name: 'cuenta-banco',
                component: require('./components/contabilidad/cuenta-banco/Index'),
                meta: {
                    title: 'Cuentas de Bancos',
                    breadcrumb: {name: 'CUENTAS DE BANCOS', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_contable_bancaria'
                }
            },
            {
                path: 'cuenta-concepto',
                name: 'cuenta-concepto',
                component: require('./components/contabilidad/cuenta-concepto/Index'),
                meta: {
                    title: 'Cuentas de Conceptos',
                    breadcrumb: {name: 'CUENTAS DE CONCEPTOS', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_concepto'
                }
            },
            {
                path: 'cuenta-costo',
                name: 'cuenta-costo',
                component: require('./components/contabilidad/cuenta-costo/Index'),
                meta: {
                    title: 'Cuentas de Costos',
                    breadcrumb: {name: 'CUENTAS DE COSTOS', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_costo'
                }
            },
            {
                path: 'cuenta-empresa',
                name: 'cuenta-empresa',
                component: require('./components/contabilidad/cuenta-empresa/Index'),
                meta: {
                    title: 'Cuentas de Empresas',
                    breadcrumb: {name: 'CUENTAS DE EMPRESAS', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_empresa'
                }
            },
            {
                path: 'cuenta-fondo',
                name: 'cuenta-fondo',
                component: require('./components/contabilidad/cuenta-fondo/Index'),
                meta: {
                    title: 'Cuentas de Fondos',
                    breadcrumb: {name: 'CUENTAS DE FONDOS', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_fondo'
                }
            },
            {
                path: 'cuenta-general',
                name: 'cuenta-general',
                component: require('./components/contabilidad/cuenta-general/Index'),
                meta: {
                    title: 'Cuentas Generales',
                    breadcrumb: {name: 'CUENTAS GENERALES', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_general'
                }
            },
            {
                path: 'cuenta-material',
                name: 'cuenta-material',
                component: require('./components/contabilidad/cuenta-material/Index'),
                meta: {
                    title: 'Cuentas de Materiales',
                    breadcrumb: {name: 'CUENTAS DE MATERIALES', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_cuenta_material'
                }
            },
            {
                path: 'tipo-cuenta-contable',
                name: 'tipo-cuenta-contable',
                component: require('./components/contabilidad/tipo-cuenta-contable/Index'),
                meta: {
                    title: 'Tipos de Cuentas Contables',
                    breadcrumb: {name: 'TIPOS DE CUENTAS CONTABLES', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_tipo_cuenta_contable'
                }
            },
            {
                path: 'poliza',
                component: require('./components/contabilidad/poliza/Layout.vue'),
                children: [
                    {
                        path: '/',
                        name: 'poliza',
                        component: require('./components/contabilidad/poliza/Index'),
                        meta: {
                            title: 'Prepólizas Generadas',
                            breadcrumb: {parent: 'sistema_contable', name: 'PREPÓLIZAS GENERADAS'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_prepolizas_generadas'
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
                            middleware: [auth, context, permission],
                            permission: 'consultar_prepolizas_generadas'
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
                            middleware: [auth, context, permission],
                            permission: 'editar_prepolizas_generadas'
                        }
                    }
                ]
            }
        ]
    },
    {
        path: '/sao/contratos',
        components: {
            default: require('./components/contratos/partials/Layout.vue'),
            menu: require('./components/contratos/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'contratos',
                component: require('./components/contratos/Index'),
                meta: {
                    title: 'Contratos',
                    breadcrumb: {parent:'home', name: 'CONTRATOS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'estimacion',
                component: require('./components/contratos/estimacion/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'estimacion',
                        component: require('./components/contratos/estimacion/Index'),
                        meta: {
                            title: 'Estimacion',
                            breadcrumb: {parent: 'contratos', name: 'ESTIMACION'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'formato-orden-pago',
                        name: 'formato-orden-pago',
                        component: require('./components/contratos/estimacion/formato-orden-pago/Index'),
                        meta: {
                            title: 'Formato Orden Pago Estimación',
                            breadcrumb: {
                                parent: 'estimacion',
                                name: 'FORMATO'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_formato_orden_pago_estimacion'
                        }
                    },
                ]
            },
        ]
    },

    {
        path: '/contratos/fondo-garantia',
        components: {
            default: require('./components/contratos/fondo-garantia/partials/Layout.vue'),
            menu: require('./components/contratos/fondo-garantia/partials/Menu.vue')
        },
        meta: {
            middleware: [auth, context]
        },
        children: [
            {
                path: '/',
                name: 'fondo-garantia',
                component: require('./components/contratos/fondo-garantia/Index'),
                meta: {
                    title: 'Fondos de Garantía',
                    breadcrumb: {parent: 'contratos', name: 'FONDOS DE GARANTÍA'},
                    middleware: [auth, context]
                }
            }
        ]
    },

    {
        path: '/contratos/fondo-garantia/solicitud-movimiento',
        components: {
            default: require('./components/contratos/fondo-garantia/solicitud-movimiento/partials/Layout.vue'),
            menu: require('./components/contratos/fondo-garantia/solicitud-movimiento/partials/Menu.vue')
        },
        meta: {
            middleware: [auth, context]
        },
        children: [
            {
                path: '/',
                name: 'solicitud-movimiento-fg',
                component: require('./components/contratos/fondo-garantia/solicitud-movimiento/Index'),
                meta: {
                    title: 'Solicitudes de Movimiento a Fondo de Garantia',
                    breadcrumb: {parent: 'fondo-garantia', name: 'SOLICITUDES DE MOVIMIENTO'},
                    middleware: [auth, context]
                }
            }
        ]
    },

    {
        path: '/sao/formatos',
        components: {
            default: require('./components/formato/partials/Layout.vue'),
            menu: require('./components/formato/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'formatos',
                component: require('./components/formato/Index'),
                meta: {
                    title: 'Formatos',
                    breadcrumb: { parent: 'home', name: 'FORMATOS'},
                    middleware: [auth, context, access],
                }
            },
            {
                path: 'orden-pago-estimacion',
                name: 'orden-pago-estimacion',
                component: require('./components/contratos/estimacion/formato-orden-pago/Index'),
                meta: {
                    title: 'Orden de Pago Estimación',
                    breadcrumb: {name: 'ORDEN DE PAGO ESTIMACIÓN', parent: 'formatos'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_formato_orden_pago_estimacion'
                }
            },
        ]
    },
    {
        path: '/sao/tesoreria',
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
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'movimiento-bancario',
                component: require('./components/tesoreria/movimiento-bancario/Layout.vue'),
                children: [
                    {
                        path: '/',
                        name: 'movimiento-bancario',
                        component: require('./components/tesoreria/movimiento-bancario/Index'),
                        meta: {
                            title: 'Movimientos Bancarios',
                            breadcrumb: {parent: 'tesoreria', name: 'MOVIMIENTOS BANCARIOS'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_movimiento_bancario'
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