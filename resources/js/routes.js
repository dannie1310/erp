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
        path: '/google-2fa',
        name: 'google-2fa',
        components: {
            default: require('./components/globals/GoogleAuth.vue'),
            menu: null
        },
        meta: {
            title: 'Veri',
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
        path: '/configuracion',
        name: 'configuracion_',
        components:  {
            default: require('./components/pages/Configuracion.vue'),
            menu: require('./components/pages/partials/MenuConfiguracion.vue')
        },
        meta: {
            title: 'CONFIGURACIÓN',
            middleware: [auth, permission],
            permission: 'asignar_areas_subcontratantes',
            general: true
        }
    },
    {
        path: '/control-interno',
        name: 'control-interno',
        components:  {
            default: require('./components/control-interno/Index.vue'),
            menu: require('./components/control-interno/partials/Menu.vue')
        },
        meta: {
            title: 'Control Interno',
            middleware: [auth, permission],
            permission: ['auditoria_consultar_permisos_por_obra','auditoria_consultar_permisos_por_usuario'],
            general: true,

        }
    },
    {
        path: '/control-interno/permisos',
        components: {
            default: require('./components/control-interno/partials/Layout.vue'),
            menu: require('./components/control-interno/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'permisos-obra',
                component: require('./components/control-interno/Index'),
                meta: {
                    title: 'Permisos',
                    breadcrumb: {parent: 'control-interno', name: 'PERMISOS ÁSIGNADOS'},
                    middleware: [auth]

                }
            },
            {
                path: 'por-obra',
                component: require('./components/control-interno/por-obra/partials/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'por-obra',
                        component: require('./components/control-interno/por-obra/Index'),
                        meta: {
                            title: 'Permisos Asignados por Obra',
                            breadcrumb: {parent: 'permisos-obra', name: 'PERMISOS POR OBRA'},
                            middleware: [auth, permission],
                            permission: 'auditoria_consultar_permisos_por_obra',
                            general: true,

                        }
                    },
                ]
            },
            {
                path: 'por-usuario',
                component: require('./components/control-interno/por-usuario/partials/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'por-usuario',
                        component: require('./components/control-interno/por-usuario/Index'),
                        meta: {
                            title: 'Permisos Asignados por Usuario',
                            breadcrumb: {parent: 'permisos-obra', name: 'PERMISOS POR USUARIO'},
                            middleware: [auth, permission],
                            permission: 'auditoria_consultar_permisos_por_usuario',
                            general: true,

                        }
                    },
                ]
            },
        ]
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
        path: '/auth',
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
        path: '/sao/almacenes',
        components: {
            default: require('./components/almacenes/partials/Layout.vue'),
            menu: require('./components/almacenes/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'almacenes',
                component: require('./components/almacenes/Index'),
                meta: {
                    title: 'Almacenes',
                    breadcrumb: {parent:'home', name: 'ALMACENES'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'ajuste-inventario',
                component: require('./components/almacenes/ajuste-inventario/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'ajuste-inventario',
                        component: require('./components/almacenes/ajuste-inventario/Index'),
                        meta: {
                            title: 'Ajuste de Inventarios',
                            breadcrumb: {parent: 'almacenes', name: 'AJUSTE DE INVENTARIOS'},
                            middleware: [auth, context],
                        }
                    },
                    {
                        path: 'create',
                        name: 'ajuste-create',
                        component: require('./components/almacenes/ajuste-inventario/Create'),
                        meta: {
                            title: 'Registrar Ajuste de Inventario',
                            breadcrumb: {name: 'REGISTRAR', parent: 'ajuste-inventario'},
                            middleware: [auth, context, permission],
                            permission: ['registrar_ajuste_positivo','registrar_ajuste_negativo']
                        }
                    },
                ]
            },
            {
                path: 'entrada-almacen',
                component: require('./components/almacenes/entrada-almacen/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'entrada-almacen',
                        component: require('./components/almacenes/entrada-almacen/Index'),
                        meta: {
                            title: 'Entrada de Almacén',
                            breadcrumb: {parent: 'almacenes', name: 'ENTRADA ALMACEN'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_entrada_almacen'
                        }
                    }
                ]
            },
            {
                path: 'salida-almacen',
                component: require('./components/almacenes/salida-almacen/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'salida-almacen',
                        component: require('./components/almacenes/salida-almacen/Index'),
                        meta: {
                            title: 'Salida de Almacén',
                            breadcrumb: {parent: 'almacenes', name: 'SALIDA ALMACEN'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_salida_almacen'

                        }
                    },
                    {
                        path:'create',
                        name:'salida-create',
                        component:require('./components/almacenes/salida-almacen/Create'),
                        meta:{
                            title:'Registrar Salida / Transferencia Almacén',
                            breadcrumb: {parent: 'salida-almacen', name: 'SALIDA - TRANSFERENCIA ALMACEN'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_salida_almacen'
                        }
                    }
                ]
            },
            {
                path: 'inventario-fisico',
                component: require('./components/almacenes/inventario-fisico/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'inventario-fisico',
                        component: require('./components/almacenes/inventario-fisico/Index'),
                        meta: {
                            title: 'Inventario Fisico',
                            breadcrumb: {parent: 'almacenes', name: 'INVENTARIO FÍSICO'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_inventario_fisico','iniciar_conteo_inventario_fisico']
                        }
                    },
                ]
            },
            {
                path:'marbete',
                component: require('./components/almacenes/marbete/Layout'),
                children: [
                    {
                        path:'/',
                        name: 'marbete',
                        component: require('./components/almacenes/marbete/Index'),
                        meta: {
                            title: 'Marbetes',
                            breadcrumb: {parent: 'almacenes', name: 'MARBETES'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_marbetes']
                        }
                    }
                ]
            },
            {
                path: 'conteo',
                component: require('./components/almacenes/conteo/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'conteo',
                        component: require('./components/almacenes/conteo/Index'),
                        meta: {
                            title: 'Conteos',
                            breadcrumb: {parent: 'almacenes', name: 'CONTEOS'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_conteos']

                        }
                    }
                ]
            },

        ]
    },
    {
        path: '/sao/catalogos',
        components: {
            default: require('./components/catalogos/partials/Layout.vue'),
            menu: require('./components/catalogos/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'catalogos',
                component: require('./components/catalogos/Index'),
                meta: {
                    title: 'Catálogos',
                    breadcrumb: {parent:'home', name: 'CATÁLOGOS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'insumo-maquinaria',
                component: require('./components/catalogos/insumo-maquinaria/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'insumo-maquinaria',
                        component: require('./components/catalogos/insumo-maquinaria/Index'),
                        meta: {
                            title: 'Catálogo de Maquinaria',
                            breadcrumb: {parent: 'catalogos', name: 'CATÁLOGO DE MAQUINARIA'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia-maq',
                        name: 'familia-maq',
                        component: require('./components/catalogos/insumo-maquinaria/familia/Index'),
                        meta: {
                            title: 'Familia',
                            breadcrumb: {
                                parent: 'insumo-maquinaria',
                                name: 'FAMILIA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_familia_maquinaria']
                        }
                    },
                    {
                        path: 'maquinaria',
                        name: 'maquinaria',
                        component: require('./components/catalogos/insumo-maquinaria/maquinaria/Index'),
                        meta: {
                            title: 'Maquinaria',
                            breadcrumb: {
                                parent: 'insumo-maquinaria',
                                name: 'MAQUINARIA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_maquinaria']
                        }
                    },
                ]
            },
            {
                path: 'insumo-servicio',
                component: require('./components/finanzas/insumo-servicio/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'insumo-servicio',
                        component: require('./components/finanzas/insumo-servicio/Index'),
                        meta: {
                            title: 'Catálogo de Servicio',
                            breadcrumb: {parent: 'catalogos', name: 'CATÁLOGO DE SERVICIO'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia-serv',
                        name: 'cat-familia-serv',
                        component: require('./components/finanzas/insumo-servicio/familia/Index'),
                        meta: {
                            title: 'Familia',
                            breadcrumb: {
                                parent: 'insumo-servicio',
                                name: 'FAMILIA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_familia_servicio']
                        }
                    },
                    {
                        path: 'servicio',
                        name: 'cat-servicio',
                        component: require('./components/finanzas/insumo-servicio/servicio/Index'),
                        meta: {
                            title: 'Servicios',
                            breadcrumb: {
                                parent: 'insumo-servicio',
                                name: 'SERVICIOS'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_servicio']
                        }
                    },
                ]
            },
            {
                path: 'catalogo-insumos',
                component: require('./components/compras/catalogos/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'catalogo-insumos',
                        component: require('./components/compras/catalogos/Index'),
                        meta: {
                            title: 'Catálogo de Insumos',
                            breadcrumb: {parent: 'catalogos', name: 'CATÁLOGO DE INSUMOS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia',
                        name: 'cat-familia',
                        component: require('./components/compras/catalogos/familia/Index'),
                        meta: {
                            title: 'Familia',
                            breadcrumb: {
                                parent: 'catalogo-insumos',
                                name: 'FAMILIA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_familia_material','consultar_familia_herramienta_equipo']
                        }
                    },
                    {
                        path: 'material',
                        name: 'cat-material',
                        component: require('./components/compras/catalogos/material/Index'),
                        meta: {
                            title: 'Material',
                            breadcrumb: {
                                parent: 'catalogo-insumos',
                                name: 'MATERIAL'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_material']
                        }
                    },
                    {
                        path: 'herramienta',
                        name: 'cat-herramienta',
                        component: require('./components/compras/catalogos/herramienta/Index'),
                        meta: {
                            title: 'Herramienta',
                            breadcrumb: {
                                parent: 'catalogo-insumos',
                                name: 'HERRAMIENTA Y EQUIPOS'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_herramienta_equipo']
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/sao/compras',
        components: {
            default: require('./components/compras/partials/Layout.vue'),
            menu: require('./components/compras/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'compras',
                component: require('./components/compras/Index'),
                meta: {
                    title: 'Compras',
                    breadcrumb: {parent:'home', name: 'COMPRAS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'catalogo-insumo',
                component: require('./components/compras/catalogos/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'catalogo-insumo',
                        component: require('./components/compras/catalogos/Index'),
                        meta: {
                            title: 'Gestión de Insumos',
                            breadcrumb: {parent: 'compras', name: 'GESTIÓN DE INSUMOS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia',
                        name: 'familia',
                        component: require('./components/compras/catalogos/familia/Index'),
                        meta: {
                            title: 'Familia',
                            breadcrumb: {
                                parent: 'catalogo-insumo',
                                name: 'FAMILIA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_familia_material','consultar_familia_herramienta_equipo']
                        }
                    },
                    {
                        path: 'material',
                        name: 'material',
                        component: require('./components/compras/catalogos/material/Index'),
                        meta: {
                            title: 'Material',
                            breadcrumb: {
                                parent: 'catalogo-insumo',
                                name: 'MATERIAL'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_material']
                        }
                    },
                    {
                        path: 'herramienta',
                        name: 'herramienta',
                        component: require('./components/compras/catalogos/herramienta/Index'),
                        meta: {
                            title: 'Herramienta y Equipo',
                            breadcrumb: {
                                parent: 'catalogo-insumo',
                                name: 'HERRAMIENTA Y EQUIPO'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_herramienta_equipo']
                        }
                    }
                ]
            },
            {
                path: 'orden-compra',
                component: require('./components/compras/orden-compra/partials/Layout.vue'),
                meta: {
                    middleware: [auth, context]
                },
                children: [{
                    path: '/',
                    name: 'orden-compra',
                    component: require('./components/compras/orden-compra/Index'),
                    meta: {
                        title: 'Ordenes de Compra',
                        breadcrumb: { parent: 'compras', name: 'ORDENES DE COMPRA' },
                        middleware: [auth, context, permission],
                        permission: ['consultar_orden_compra']
                    }
                }]
            },
            {
                path: 'solicitud-compra',
                component: require('./components/compras/solicitud-compra/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'solicitud-compra',
                        component: require('./components/compras/solicitud-compra/Index'),
                        meta: {
                            title: 'SOLICITUDES DE COMPRA',
                            breadcrumb: {parent: 'compras', name: 'SOLICITUDES DE COMPRA'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_solicitud_compra'
                        }
                    },
                    {
                        path: 'create',
                        name: 'solicitud-compra-create',
                        component: require('./components/compras/solicitud-compra/Create'),
                        meta: {
                            title: 'Registrar Solicitud de Compra',
                            breadcrumb: { parent: 'compras', name: 'REGISTRAR SOLICITUD DE COMPRA'},
                            middleware: [auth, context],
                            // permission: 'registrar_solicitud_compra'
                        }
                    },
                    {
                        path: ':id',
                        name: 'solicitud-compra-edit',
                        component: require('./components/compras/solicitud-compra/Edit'),
                        props: true,
                        meta: {
                            title: 'Editar Solicitud de Compra',
                            breadcrumb: { parent: 'compras', name: 'EDITAR'},
                            middleware: [auth, context],
                            // permission: 'editar_solicitud_compra'
                        }
                    }
                ]
            },
        ]
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
                path: 'proyectado',
                component: require('./components/contratos/proyectado/partials/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'proyectado',
                        component: require('./components/contratos/proyectado/Index'),
                        meta: {
                            title: 'Contratos Proyectados',
                            breadcrumb: {parent: 'contratos', name: 'PROYECTADOS'},
                            middleware: [auth, context],

                        }
                    },
                ]
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
                            title: 'ESTIMACIONES',
                            breadcrumb: {parent: 'contratos', name: 'ESTIMACIONES'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'create',
                        name: 'estimacion-create',
                        component: require('./components/contratos/estimacion/Create'),
                        meta: {
                            title: 'ESTIMACIONES',
                            breadcrumb: {parent: 'estimacion', name: 'NUEVA'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_estimacion_subcontrato'
                        }
                    },
                    {
                        path: ':id',
                        name: 'estimacion-show',
                        component: require('./components/contratos/estimacion/Show'),
                        meta: {
                            title: 'INFORMACIÓN DE ESTIMACIÓN',
                            breadcrumb: {parent: 'estimacion', name: 'VER ESTIMACIÓN'},
                            middleware: [auth, context,],

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
                                name: 'FORMATO DE ORDEN DE PAGO'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_formato_orden_pago_estimacion'
                        }
                    },
                ]
            },

            {
                path: 'fondo-garantia',
                component: require('./components/contratos/fondo-garantia/partials/Layout.vue'),
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
                            middleware: [auth, context, permission],
                            permission: ['consultar_fondo_garantia','generar_fondo_garantia','ajustar_saldo_fondo_garantia' +
                            'consultar_detalle_fondo_garantia']
                        }
                    }
                ]
            },

            {
                path: 'solicitud-movimiento',
                components: {
                    default: require('./components/contratos/fondo-garantia/solicitud-movimiento/partials/Layout.vue'),
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
                            title: 'Solicitudes de Movimiento a Fondo de Garantía',
                            breadcrumb: {parent: 'fondo-garantia', name: 'SOLICITUDES DE MOVIMIENTO'},
                            middleware: [auth, context, permission],
                            permission: ['autorizar_solicitud_movimiento_fondo_garantia',
                                'cancelar_solicitud_movimiento_fondo_garantia',
                                'consultar_solicitud_movimiento_fondo_garantia',
                                'rechazar_solicitud_movimiento_fondo_garantia',
                                'registrar_solicitud_movimiento_fondo_garantia',
                                'revertir_autorizacion_solicitud_movimiento_fondo_garantia']
                        }
                    }
                ]
            },
        ]
    },
    {
        path: '/sao/finanzas',
        components: {
            default: require('./components/finanzas/partials/Layout.vue'),
            menu: require('./components/finanzas/partials/Menu.vue')
        },
        children: [
            {
                path: '',
                name: 'finanzas',
                component: require('./components/finanzas/Index'),
                meta: {
                    title: 'Finanzas',
                    breadcrumb: {parent:'home', name: 'FINANZAS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path:'banco',
                component: require('./components/finanzas/banco/Layout.vue'),
                children: [
                    {
                        path:'/',
                        name: 'banco',
                        component: require('./components/finanzas/banco/Index.vue'),
                        meta:{
                            title: 'Bancos',
                            breadcrumb: {name: 'GESTIÓN DE BANCOS', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_banco'
                        }
                    },
                    {
                        path: 'create',
                        name: 'banco-create',
                        component: require('./components/finanzas/banco/Create'),
                        meta: {
                            title: 'Registrar Banco',
                            breadcrumb: {name: 'REGISTRAR', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_banco'
                        }
                    },
                    {
                        path: 'show',
                        name: 'banco-show',
                        props: true,
                        component: require('./components/finanzas/banco/Show'),
                        meta: {
                            title: 'Ver Banco',
                            breadcrumb: {name: 'VER', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_banco'
                        }
                    },
                    {
                        path: ':id',
                        name: 'banco-edit',
                        props: true,
                        component: require('./components/finanzas/banco/Edit'),
                        meta: {
                            title: 'Edición de Bancos',
                            breadcrumb: {name: 'EDICIÓN DE BANCOS', parent: 'banco'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: '/',
                        name: 'sucursal',
                        component: require('./components/finanzas/banco/sucursal/Index.vue'),
                        meta: {
                            title: 'Sucursales',
                            breadcrumb: { name: 'SUCURSALES', parent: 'banco'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_sucursal_banco'
                        }
                    },
                    {
                        path: 'create',
                        name: 'sucursal-create',
                        component: require('./components/finanzas/banco/sucursal/Create'),
                        meta: {
                            title: 'Registrar Sucursal',
                            breadcrumb: {name: 'REGISTRAR', parent: 'banco'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_sucursal_banco'

                        }
                    },
                    {
                        path: 'show',
                        name: 'sucursal-show',
                        component: require('./components/finanzas/banco/sucursal/Show'),
                        meta: {
                            title: 'Ver Sucursal',
                            breadcrumb: {name: 'VER', parent: 'banco'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_sucursal_banco'

                        }
                    },
                ]
            },
            {
                path: 'distribuir-recurso-remesa',
                component: require('./components/finanzas/distribuir-recurso-remesa/Layout.vue'),
                children: [
                    {
                        path: '/',
                        name: 'distribuir-recurso-remesa',
                        component: require('./components/finanzas/distribuir-recurso-remesa/Index'),
                        meta: {
                            title: 'Dispersión de Recursos Autorizados de Remesa',
                            breadcrumb: {name: 'DISPERSIÓN RECURSOS AUTORIZADOS DE REMESA', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_distribucion_recursos_remesa'
                        }
                    },
                    {
                        path: 'create',
                        name: 'distribuir-recurso-remesa-create',
                        component: require('./components/finanzas/distribuir-recurso-remesa/Create'),
                        meta: {
                            title: 'Registrar Dispersión de Recursos Autorizados',
                            breadcrumb: {name: 'REGISTRAR', parent: 'distribuir-recurso-remesa'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_distribucion_recursos_remesa'
                        }
                    },
                    {
                        path: ':id',
                        name: 'distribuir-recurso-remesa-show',
                        props: true,
                        component: require('./components/finanzas/distribuir-recurso-remesa/Show'),
                        meta: {
                            title: 'Consultar Dispersión de Recursos Autorizados',
                            breadcrumb: {name: 'VER', parent: 'distribuir-recurso-remesa'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_distribucion_recursos_remesa'
                        }
                    },
                    {
                        path: ':id/autorizar',
                        name: 'distribuir-recurso-remesa-autorizar',
                        props: true,
                        component: require('./components/finanzas/distribuir-recurso-remesa/Autorizar'),
                        meta: {
                            title: 'Autorizar Dispersión de Recursos Autorizados',
                            breadcrumb: {name: 'AUTORIZAR', parent: 'distribuir-recurso-remesa'},
                            middleware: [auth, context, permission],
                            permission: 'autorizar_distribucion_recursos_remesa'
                        }
                    }
                ]
            },
            {
                path:'factura',
                component: require('./components/finanzas/factura/Layout'),
                children: [
                    {
                        path:'/',
                        name: 'factura',
                        component: require('./components/finanzas/factura/Index'),
                        meta:{
                            title: 'Facturas',
                            breadcrumb: {name: 'FACTURAS', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_banco'
                        }
                    },

                ]
            },
            {
                path:'fondo',
                component: require('./components/finanzas/fondo/Layout.vue'),
                children: [
                    {
                        path: '/',
                        name: 'fondo',
                        component: require('./components/finanzas/fondo/Index.vue'),
                        meta: {
                            title: 'Fondos',
                            breadcrumb: {name: 'FONDOS', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_fondos'
                        }

                    },
                    {
                        path: 'create',
                        name: 'fondo-create',
                        component: require('./components/finanzas/fondo/Create'),
                        meta: {
                            title: 'Registrar Fondo',
                            breadcrumb: {name: 'REGISTRAR', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_fondos'
                        }
                    },
                    {
                        path: ':id',
                        name: 'fondo-show',
                        props: true,
                        component: require('./components/finanzas/fondo/Show'),
                        meta: {
                            title: 'Ver Fondo',
                            breadcrumb: {name: 'VER', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_fondos'
                        }
                    },
                ]
            },
            {
                path: 'gestion-cuenta-bancaria',
                component: require('./components/finanzas/gestion-cuenta-bancaria/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'gestion-cuenta-bancaria',
                        component: require('./components/finanzas/gestion-cuenta-bancaria/Index'),
                        meta: {
                            title: 'Gestión de Cuentas Bancarias',
                            breadcrumb: {parent: 'finanzas', name: 'GESTIÓN DE CUENTAS BANCARIAS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'cuenta-empresa-bancaria',
                        name: 'cuenta-empresa-bancaria',
                        component: require('./components/finanzas/gestion-cuenta-bancaria/cuenta-empresa/Index'),
                        meta: {
                            title: 'Cuentas Bancarias',
                            breadcrumb: {
                                parent: 'gestion-cuenta-bancaria',
                                name: 'CUENTAS BANCARIAS'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_solicitud_alta_cuenta_bancaria_empresa'
                        }
                    },
                    {
                        path: 'solicitud-alta',
                        name: 'solicitud-alta',
                        component: require('./components/finanzas/gestion-cuenta-bancaria/solicitud-alta/Index'),
                        meta: {
                            title: 'Solicitud de Alta de Cuenta Bancaria',
                            breadcrumb: {
                                parent: 'gestion-cuenta-bancaria',
                                name: 'SOLICITUD DE ALTA'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_solicitud_alta_cuenta_bancaria_empresa'
                        }
                    },
                    {
                        path: 'solicitud-baja',
                        name: 'solicitud-baja',
                        component: require('./components/finanzas/gestion-cuenta-bancaria/solicitud-baja/Index'),
                        meta: {
                            title: 'Solicitud de Baja de Cuenta Bancaria',
                            breadcrumb: {
                                parent: 'gestion-cuenta-bancaria',
                                name: 'SOLICITUD DE BAJA'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_solicitud_baja_cuenta_bancaria_empresa'
                        }
                    }
                ]
            },
            {
                path: 'gestion-pago',
                component: require('./components/finanzas/gestion-pago/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'gestion-pago',
                        component: require('./components/finanzas/gestion-pago/Index'),
                        meta: {
                            title: 'Gestión de Pagos',
                            breadcrumb: {parent: 'finanzas', name: 'GESTIÓN DE PAGOS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'pago',
                        name: 'pago',
                        component: require('./components/finanzas/gestion-pago/pago/Index'),
                        meta: {
                            title: 'Gestión de Pagos',
                            breadcrumb: {
                                parent: 'gestion-pago',
                                name: 'PAGOS'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_pagos'
                        }
                    },
                    {
                        path: 'create',
                        name: 'pago-create',
                        component: require('./components/finanzas/gestion-pago/pago/Create'),
                        meta: {
                            title: 'Registrar Pagos con Bitácora Bancaria (SANTANDER)',
                            breadcrumb: {name: 'REGISTRAR (BITÁCORA BSANT)', parent: 'pago'},
                            middleware: [auth, context, permission],
                            permission: 'cargar_distribucion_recursos_remesa'
                        }
                    },
                    {
                        path: 'registro-pago',
                        name: 'gestion-registro-pago',
                        component: require('./components/finanzas/gestion-pago/pago/RegistrarPago'),
                        meta: {
                            title: 'Registrar Pagos',
                            breadcrumb: {name: 'REGISTRAR PAGOS', parent: 'pago'},
                            middleware: [auth, context, permission],
                            permission: 'cargar_distribucion_recursos_remesa'
                        }
                    },
                    {
                        path: 'carga-masiva',
                        name: 'carga-masiva',
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Index'),
                        meta: {
                            title: 'Carga Masiva',
                            breadcrumb: {
                                parent: 'gestion-pago',
                                name: 'CARGA MASIVA'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_carga_layout_pago'
                        }
                    },
                    {
                        path: 'carga-create',
                        name: 'carga-masiva-create',
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Create'),
                        meta: {
                            title: 'Registrar Carga Masiva de Pagos',
                            breadcrumb: {name: 'REGISTRAR CARGA MASIVA DE PAGOS', parent: 'carga-masiva'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_carga_layout_pago'
                        }
                    },
                    {
                        path: ':id',
                        name: 'autorizar-layout',
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Autorizar'),
                        meta: {
                            title: 'Autorizar Layouts',
                            breadcrumb: { name: 'AUTORIZAR', parent:'carga-masiva'},
                            middleware: [auth, context],
                        }
                    },
                    {
                        path: ':id/consultar',
                        name: 'pago-masivo-show',
                        props: true,
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Show'),
                        meta: {
                            title: 'Consultar Layout registrados',
                            breadcrumb: {name: 'VER', parent: 'carga-masiva'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_carga_layout_pago'
                        }
                    }
                ]
            },
            {
                path: 'insumo-servicio',
                component: require('./components/finanzas/insumo-servicio/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'insumo-servicio',
                        component: require('./components/finanzas/insumo-servicio/Index'),
                        meta: {
                            title: 'Insumo de Servicios',
                            breadcrumb: {parent: 'finanzas', name: 'INSUMO SERVICIO'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia-serv',
                        name: 'familia-serv',
                        component: require('./components/finanzas/insumo-servicio/familia/Index'),
                        meta: {
                            title: 'Familia',
                            breadcrumb: {
                                parent: 'insumo-servicio',
                                name: 'FAMILIA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_familia_servicio']
                        }
                    },
                    {
                        path: 'servicio',
                        name: 'servicio',
                        component: require('./components/finanzas/insumo-servicio/servicio/Index'),
                        meta: {
                            title: 'Servicio',
                            breadcrumb: {
                                parent: 'insumo-servicio',
                                name: 'SERVICIO'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_servicio']
                        }
                    }
                ]
            },
            {
                path: 'solicitud',
                component: require('./components/finanzas/solicitud/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'solicitud',
                        component: require('./components/finanzas/solicitud/Index'),
                        meta: {
                            title: 'Solicitudes de Pago',
                            breadcrumb: {parent: 'finanzas', name: 'SOLICITUDES DE PAGO'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'pago-anticipado',
                        name: 'pago-anticipado',
                        component: require('./components/finanzas/solicitud/pago-anticipado/Index'),
                        meta: {
                            title: 'Solicitud de Pago Anticipado',
                            breadcrumb: {
                                parent: 'solicitud',
                                name: 'PAGO ANTICIPADO'
                            },
                            middleware: [auth, context, permission],
                            permission: 'consultar_solicitud_pago_anticipado'
                        }
                    },
                ]
            },
            {
                path: 'tesoreria',
                component: require('./components/finanzas/tesoreria/Layout'),
                children: [
                    {
                        path: '/',
                        name: 'tesoreria',
                        component: require('./components/finanzas/tesoreria/Index'),
                        meta: {
                            title: 'Tesorería',
                            breadcrumb: {parent: 'finanzas', name: 'TESORERÍA'},
                            middleware: [auth, context],
                        }
                    },
                    {
                        path: 'movimiento-bancario',
                        name: 'movimiento-bancario',
                        component: require('./components/finanzas/tesoreria/movimiento-bancario/Index'),
                        meta: {
                            title: 'Movimientos Bancarios',
                            breadcrumb: {parent: 'tesoreria', name: 'MOVIMIENTOS BANCARIOS'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_movimiento_bancario'
                        }
                    },
                    {
                        path: 'traspaso-entre-cuentas',
                        name: 'traspaso-entre-cuentas',
                        component: require('./components/finanzas/tesoreria/traspaso-entre-cuentas/Index'),
                        meta: {
                            title: 'Traspasos entre Cuentas',
                            breadcrumb: {parent: 'tesoreria', name: 'TRASPASOS ENTRE CUENTAS'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_traspaso_cuenta'
                        }
                    },
                ]
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
            {
                path: 'estimacion',
                name: 'formato-estimacion',
                component: require('./components/formato/estimacion/Index'),
                meta: {
                    title: 'Formato Estimación',
                    breadcrumb: {name: 'FORMATO ESTIMACIÓN', parent: 'formatos'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_formato_estimacion'
                }
            },
            {
                path: 'compra',
                name: 'formato-orden-compra',
                component: require('./components/formato/compra/Index'),
                meta: {
                    title: 'Formato Orden de Compra',
                    breadcrumb: {name: 'FORMATO ORDEN COMPRA', parent: 'formatos'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_orden_compra'
                }
            }
        ]
    },
    {
        path: '*',
        name: 'notFound',
        component: require('./components/pages/NotFound.vue'),
    }
];
