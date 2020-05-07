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
            default: require('./components/pages/Portal.vue').default,
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
            default: require('./components/globals/GoogleAuth.vue').default,
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
        component: require('./components/pages/Home.vue').default,
        meta: {
            title: 'Inicio',
            middleware: [auth, context],
            breadcrumb: {name: 'INICIO'}
        }
    },
    {
        path: '/configuracion',
        name: 'configuracion-area',
        components:  {
            default: require('./components/configuracion-general/configuracion-area/Configuracion.vue').default,
            menu: require('./components/pages/partials/MenuConfiguracion.vue').default
        },
        meta: {
            title: 'CONFIGURACIÓN',
            middleware: [auth, permission],
            permission: ['asignar_areas_subcontratantes'],
            general: true
        }
    },
    {
        path: '/configuracion/general',
        components: {
            default: require('./components/configuracion-general/configuracion-obra/Obra.vue').default,
            menu: require('./components/configuracion-general/partials/Menu.vue').default
        },
        children: [
            {
                path: 'obra',
                component: require('./components/configuracion-general/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'configuracion-general-obra',
                        component: require('./components/configuracion-general/configuracion-obra/Obra').default,
                        meta: {
                            title: 'Configuración de Obra',
                            breadcrumb: {parent: 'configuracion-general', name: 'OBRA'},
                            middleware: [auth, permission],
                            permission: ['actualizar_estado_obra', 'administracion_configuracion_obra', 'reactivar_obra'],
                            general: true,

                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/reportes-pbi',
        component: require('./components/reportes-pbi/Index.vue').default,
        children:[
            {
                path:'',
                name: 'reportes-pbi',
                meta: {
                    title: 'REPORTES PBI',
                    middleware: [auth, permission],
                    permission: ['consultar_reportes'],
                    general: true
                }
            },
        ]
    },
    {
        path:"/reportes-pbi/ver/:id",
        props: true,
        component: require('./components/reportes-pbi/Visor.vue').default,
        children:[
            {
                path:"/",
                name:"visor-reportes",
                component: require('./components/reportes-pbi/Visor.vue').default,
                meta: {
                    title: 'Reporte',
                    breadcrumb: {parent: 'reportes-pbi', name: 'VER'},
                    middleware: [auth, permission],
                    permission: ['consultar_reportes'],
                    general: true
                }
            }
        ]
    },
    {
        path: '/contabilidad-general',
        components:  {
            default: require('./components/contabilidad-general/partials/Layout.vue').default,
            menu: require('./components/contabilidad-general/partials/Menu.vue').default
        },
        children:[
            {
                path:'',
                name: 'contabilidad-general',
                meta: {
                    title: 'CONTABILIDAD GENERAL',
                    middleware: [auth, permission],
                    permission: ['editar_poliza','configurar_visibilidad_empresa_ctpq','configurar_editabilidad_empresa_ctpq','consultar_log_edicion_poliza'],
                    general: true
                }
            },
            {
                path: 'polizas',
                component: require('./components/contabilidad-general/poliza/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"poliza-contpaq",
                        component: require('./components/contabilidad-general/poliza/Index.vue').default,
                        meta: {
                            title: 'Pólizas',
                            breadcrumb: {parent: 'contabilidad-general', name: 'PÓLIZAS'},
                            middleware: [auth, permission],
                            permission: ['editar_poliza','consultar_poliza'],
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'solicitud-edicion-poliza',
                component: require('./components/contabilidad-general/solicitudes-edicion/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"solicitud-edicion-poliza",
                        component: require('./components/contabilidad-general/solicitudes-edicion/Index.vue').default,
                        meta: {
                            title: 'Solicitudes de Edición',
                            breadcrumb: {parent: 'contabilidad-general', name: 'SOLICITUDES DE EDICIÓN'},
                            middleware: [auth, permission],
                            permission: ['consultar_solicitud_edicion_poliza_ctpq'],
                            general: true
                        }
                    },
                ]
            },
            {
                path:"solicitud-edicion-poliza/carga-masiva",
                component: require('./components/contabilidad-general/solicitudes-edicion/carga-masiva/Create.vue').default,
                children:[
                    {
                        path:"/",
                        name:"solicitud-edicion-carga-masiva",
                        component: require('./components/contabilidad-general/solicitudes-edicion/carga-masiva/Create.vue').default,
                        meta: {
                            title: 'Carga masiva',
                            breadcrumb: {parent: 'solicitud-edicion-poliza', name: 'CARGA MASIVA'},
                            middleware: [auth, permission],
                            permission: ['registrar_solicitud_edicion_poliza_ctpq'],
                            general: true
                        }
                    }
                ],
            },
            {
                path:"solicitud-edicion-poliza/:id",
                props: true,
                component: require('./components/contabilidad-general/solicitudes-edicion/Show.vue').default,
                children:[
                    {
                        path:"/",
                        name:"solicitud-edicion-poliza-show",
                        props: true,
                        component: require('./components/contabilidad-general/solicitudes-edicion/Show.vue').default,
                        meta: {
                            title: 'Consultar Solicitud de Edición',
                            breadcrumb: {parent: 'solicitud-edicion-poliza', name: 'CONSULTAR'},
                            middleware: [auth, permission],
                            permission: 'consultar_solicitud_edicion_poliza_ctpq',
                            general: true
                        }
                    }
                ],
            },
            {
                path:"solicitud-edicion-poliza/:id/autorizar",
                props: true,
                component: require('./components/contabilidad-general/solicitudes-edicion/Autorizar.vue').default,
                children:[
                    {
                        path:"/",
                        name:"solicitud-edicion-poliza-autorizar",
                        props: true,
                        component: require('./components/contabilidad-general/solicitudes-edicion/Autorizar.vue').default,
                        meta: {
                            title: 'Autorizar / Rechazar Solicitud de Edición',
                            breadcrumb: {parent: 'solicitud-edicion-poliza', name: 'AUTORIZAR / RECHAZAR'},
                            middleware: [auth, permission],
                            permission: ['autorizar_solicitud_edicion_poliza_ctpq','rechazar_solicitud_edicion_poliza_ctpq'],
                            general: true
                        }
                    }
                ],
            },
            {
                path:"solicitud-edicion-poliza/:id/aplicar",
                props: true,
                component: require('./components/contabilidad-general/solicitudes-edicion/Aplicar.vue').default,
                children:[
                    {
                        path:"/",
                        name:"solicitud-edicion-poliza-aplicar",
                        props: true,
                        component: require('./components/contabilidad-general/solicitudes-edicion/Aplicar.vue').default,
                        meta: {
                            title: 'Aplicar Solicitud de Edición Autorizada',
                            breadcrumb: {parent: 'solicitud-edicion-poliza', name: 'APLICAR'},
                            middleware: [auth, permission],
                            permission: ['aplicar_solicitud_edicion_poliza_ctpq'],
                            general: true
                        }
                    }
                ],
            },
            {
                path: 'cfd-sat',
                component: require('./components/contabilidad-general/cfd-sat/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"cfd-sat",
                        component: require('./components/contabilidad-general/cfd-sat/Index.vue').default,
                        meta: {
                            title: 'CFD SAT',
                            breadcrumb: {parent: 'contabilidad-general', name: 'CFD SAT'},
                            middleware: [auth, permission],
                            permission: ['consultar_poliza'],
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'lista-empresa',
                component: require('./components/contabilidad-general/empresas/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"lista-empresa",
                        component: require('./components/contabilidad-general/empresas/Index.vue').default,
                        meta: {
                            title: 'Empresas',
                            breadcrumb: {parent: 'contabilidad-general', name: 'EMPRESAS'},
                            middleware: [auth, permission],
                            permission: ['configurar_visibilidad_empresa_ctpq','configurar_editabilidad_empresa_ctpq'],
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'consolidacion',
                component: require('./components/contabilidad-general/consolidacion/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"consolidacion",
                        component: require('./components/contabilidad-general/consolidacion/Index.vue').default,
                        meta: {
                            title: 'Consolidación',
                            breadcrumb: {parent: 'contabilidad-general', name: 'CONSOLIDACIÓN'},
                            middleware: [auth, permission],
                            permission: ['editar_empresa_consolidadora'],
                            general: true
                        }
                    }
                ]
            }
        ]
    },
    {
        path: '/control-interno',
        name: 'control-interno',
        components:  {
            default: require('./components/control-interno/Index.vue').default,
            menu: require('./components/control-interno/partials/Menu.vue').default
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
            default: require('./components/control-interno/partials/Layout.vue').default,
            menu: require('./components/control-interno/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'permisos-obra',
                component: require('./components/control-interno/Index').default,
                meta: {
                    title: 'Permisos',
                    breadcrumb: {parent: 'control-interno', name: 'PERMISOS ÁSIGNADOS'},
                    middleware: [auth]

                }
            },
            {
                path: 'por-obra',
                component: require('./components/control-interno/por-obra/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'por-obra',
                        component: require('./components/control-interno/por-obra/Index').default,
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
                component: require('./components/control-interno/por-usuario/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'por-usuario',
                        component: require('./components/control-interno/por-usuario/Index').default,
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
        path: '/control-interno/finanzas',
        components: {
            default: require('./components/control-interno/partials/Layout.vue').default,
            menu: require('./components/control-interno/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'control-finanzas',
                component: require('./components/control-interno/finanzas/Index').default,
                meta: {
                    title: 'Finanzas',
                    breadcrumb: {parent: 'control-interno', name: 'FINANZAS'},
                    middleware: [auth]

                }
            },
            {
                path: 'efos',
                component: require('./components/control-interno/finanzas/efos/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'lista-efos',
                        component: require('./components/control-interno/finanzas/efos/Index').default,
                        meta: {
                            title: 'Lista de EFOS',
                            breadcrumb: {parent: 'control-finanzas', name: 'EFOS'},
                            middleware: [auth, permission],
                            permission: 'consultar_efos',
                            general: true,

                        }
                    },
                ]
            },
            {
                path: 'transaccion-efo',
                component: require('./components/control-interno/finanzas/transaccion-efo/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'transaccion-efos',
                        component: require('./components/control-interno/finanzas/transaccion-efo/Index').default,
                        meta: {
                            title: 'Consulta de transacciones relacionadas con EFOS',
                            breadcrumb: {parent: 'control-finanzas', name: 'TRANSACCIONES CON EFOS'},
                            middleware: [auth, permission],
                            permission: 'consultar_transacciones_efos',
                            general: true,

                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/control-interno/incidencias',
        components: {
            default: require('./components/control-interno/partials/Layout.vue').default,
            menu: require('./components/control-interno/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'incidencia',
                component: require('./components/control-interno/incidencia/Index').default,
                meta: {
                    title: 'Incidencias',
                    breadcrumb: {parent: 'control-interno', name: 'INCIDENCIAS'},
                    middleware: [auth],
                    permission: 'consultar_incidencias',
                    general: true,

                }
            },
        ]
    },
    {
        path: '/sao/configuracion',
        name: 'configuracion',
        components: {
            default: require('./components/configuracion/Index.vue').default,
            menu: require('./components/configuracion/partials/Menu.vue').default
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
        component: require('./components/pages/Login.vue').default,
        meta: {
            title: 'INICIAR SESIÓN',
            middleware: guest,
        },
    },
    {
        path: '/sao/obras',
        name: 'obras',
        component: require('./components/pages/Obras.vue').default,
        meta: {
            title: 'Seleccionar Obra',
            middleware: auth,
            breadcrumb: {name: 'SELECCIONAR OBRA'}
        }
    },
    {
        path: '/sao/almacenes',
        components: {
            default: require('./components/almacenes/partials/Layout.vue').default,
            menu: require('./components/almacenes/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'almacenes',
                component: require('./components/almacenes/Index').default,
                meta: {
                    title: 'Almacenes',
                    breadcrumb: {parent:'home', name: 'ALMACENES'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'ajuste-inventario',
                component: require('./components/almacenes/ajuste-inventario/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'ajuste-inventario',
                        component: require('./components/almacenes/ajuste-inventario/Index').default,
                        meta: {
                            title: 'Ajuste de Inventarios',
                            breadcrumb: {parent: 'almacenes', name: 'AJUSTE DE INVENTARIOS'},
                            middleware: [auth, context],
                        }
                    },
                    {
                        path: 'create',
                        name: 'ajuste-create',
                        component: require('./components/almacenes/ajuste-inventario/Create').default,
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
                component: require('./components/almacenes/entrada-almacen/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'entrada-almacen',
                        component: require('./components/almacenes/entrada-almacen/Index').default,
                        meta: {
                            title: 'Entrada de Almacén',
                            breadcrumb: {parent: 'almacenes', name: 'ENTRADA ALMACEN'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_entrada_almacen'
                        }
                    },
                    {
                        path: 'create',
                        name: 'entrada-almacen-create',
                        component: require('./components/almacenes/entrada-almacen/Create').default,
                        meta: {
                            title: 'Registrar Entrada de Almacén',
                            breadcrumb: {name: 'REGISTRAR', parent: 'entrada-almacen'},
                            middleware: [auth, context, permission],
                            permission: ['registrar_entrada_almacen']
                        }
                    }
                ]
            },
            {
                path: 'salida-almacen',
                component: require('./components/almacenes/salida-almacen/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'salida-almacen',
                        component: require('./components/almacenes/salida-almacen/Index').default,
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
                        component:require('./components/almacenes/salida-almacen/Create').default,
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
                component: require('./components/almacenes/inventario-fisico/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'inventario-fisico',
                        component: require('./components/almacenes/inventario-fisico/Index').default,
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
                component: require('./components/almacenes/marbete/Layout').default,
                children: [
                    {
                        path:'/',
                        name: 'marbete',
                        component: require('./components/almacenes/marbete/Index').default,
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
                component: require('./components/almacenes/conteo/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'conteo',
                        component: require('./components/almacenes/conteo/Index').default,
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
            default: require('./components/catalogos/partials/Layout.vue').default,
            menu: require('./components/catalogos/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'catalogos',
                component: require('./components/catalogos/Index').default,
                meta: {
                    title: 'Catálogos',
                    breadcrumb: {parent:'home', name: 'CATÁLOGOS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'unidad',
                component: require('./components/catalogos/unidad/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'unidad',
                        component: require('./components/catalogos/unidad/Index').default,
                        meta: {
                            title: 'Catálogo de Unidades',
                            breadcrumb: {parent: 'catalogos', name: 'CATÁLOGO DE UNIDAD'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_unidad']
                        }
                    },
                ]
            },
            {
                path: 'empresa',
                component: require('./components/catalogos/empresa/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'empresa',
                        component: require('./components/catalogos/empresa/Index').default,
                        meta: {
                            title: 'Catálogo de Empresa',
                            breadcrumb: {parent: 'catalogos', name: 'CATÁLOGO DE EMPRESA'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'cliente',
                        name: 'cliente',
                        component: require('./components/catalogos/empresa/cliente/Index').default,
                        meta: {
                            title: 'Cliente',
                            breadcrumb: {
                                parent: 'empresa',
                                name: 'CLIENTE'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_cliente']
                        }
                    },
                    {
                        path: 'destajista',
                        name: 'destajista',
                        component: require('./components/catalogos/empresa/destajista/Index').default,
                        meta: {
                            title: 'Destajista',
                            breadcrumb: {
                                parent: 'empresa',
                                name: 'DESTAJISTA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_destajista']
                        }
                    }
                ]
            },
            {
                path: 'insumo-maquinaria',
                component: require('./components/catalogos/insumo-maquinaria/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'insumo-maquinaria',
                        component: require('./components/catalogos/insumo-maquinaria/Index').default,
                        meta: {
                            title: 'Maquinaria',
                            breadcrumb: {parent: 'catalogos', name: 'MAQUINARIA'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia-maq',
                        name: 'familia-maq',
                        component: require('./components/catalogos/insumo-maquinaria/familia/Index').default,
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
                        component: require('./components/catalogos/insumo-maquinaria/maquinaria/Index').default,
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
                component: require('./components/finanzas/insumo-servicio/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'insumo-servicio',
                        component: require('./components/finanzas/insumo-servicio/Index').default,
                        meta: {
                            title: 'Mano de Obra y Servicios',
                            breadcrumb: {parent: 'catalogos', name: 'MANO DE OBRA Y SERVICIOS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia-serv',
                        name: 'cat-familia-serv',
                        component: require('./components/finanzas/insumo-servicio/familia/Index').default,
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
                        component: require('./components/finanzas/insumo-servicio/servicio/Index').default,
                        meta: {
                            title: 'Servicio',
                            breadcrumb: {
                                parent: 'insumo-servicio',
                                name: 'SERVICIO'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_servicio']
                        }
                    },
                    {
                        path: 'mano-obra',
                        name: 'cat-mano-obra',
                        component: require('./components/finanzas/insumo-servicio/mano-obra/Index').default,
                        meta: {
                            title: 'Mano de Obra',
                            breadcrumb: {
                                parent: 'insumo-servicio',
                                name: 'MANO DE OBRA'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_mano_obra']
                        }
                    },
                ]
            },
            {
                path: 'catalogo-insumos',
                component: require('./components/compras/catalogos/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'catalogo-insumos',
                        component: require('./components/compras/catalogos/Index').default,
                        meta: {
                            title: 'Material, Herramienta y Equipo',
                            breadcrumb: {parent: 'catalogos', name: 'MATERIAL, HTA. Y EQUIPO'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia',
                        name: 'cat-familia',
                        component: require('./components/compras/catalogos/familia/Index').default,
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
                        component: require('./components/compras/catalogos/material/Index').default,
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
                        component: require('./components/compras/catalogos/herramienta/Index').default,
                        meta: {
                            title: 'Herramienta y Equipo',
                            breadcrumb: {
                                parent: 'catalogo-insumos',
                                name: 'HERRAMIENTA Y EQUIPO'
                            },
                            middleware: [auth, context, permission],
                            permission: ['consultar_insumo_herramienta_equipo']
                        }
                    },
                ]
            },
            {
                path:'proveedor-contratista',
                component: require('./components/catalogos/empresa/proveedor-contratista/Layout').default,
                children: [
                    {
                        path:'/',
                        name: 'proveedor-contratista',
                        component: require('./components/catalogos/empresa/proveedor-contratista/Index').default,
                        meta:{
                            title: 'Proveedor / Contratista',
                            breadcrumb: {name: 'PROVEEDOR-CONTRATISTA', parent: 'catalogos'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_proveedor']
                        }
                    },
                    {
                        path: 'create',
                        name: 'proveedor-contratista-create',
                        component: require('./components/catalogos/empresa/proveedor-contratista/Create').default,
                        meta: {
                            title: 'Registrar Proveedor / Contratista',
                            breadcrumb: { parent: 'proveedor-contratista', name: 'REGISTRAR PROVEEDOR-CONTRATISTA'},
                            middleware: [auth, context],
                            permission: 'registrar_proveedor'
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/sao/compras',
        components: {
            default: require('./components/compras/partials/Layout.vue').default,
            menu: require('./components/compras/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'compras',
                component: require('./components/compras/Index').default,
                meta: {
                    title: 'Compras',
                    breadcrumb: {parent:'home', name: 'COMPRAS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'asignacion-proveedores',
                component: require('./components/compras/asignacion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'asignacion-proveedores',
                        component: require('./components/compras/asignacion/Index').default,
                        meta: {
                            title: 'Asignación de Proveedores',
                            breadcrumb: {parent: 'compras', name: 'ASIGNACIÓN DE PROVEEDORES'},
                            middleware: [auth, context],
                        }
                    },
                    {
                        path: 'show',
                        name: 'asignacion-proveedores-show',
                        component: require('./components/compras/asignacion/Show').default,
                        props: true,
                        meta: {
                            title: 'Ver Asignación de Proveedores',
                            breadcrumb: { parent: 'asignacion-proveedores', name: 'VER'},
                            middleware: [auth, context],
                            // permission: 'registrar_proveedor'
                        }
                    },
                    {
                        path: 'create',
                        name: 'asignacion-proveedores-create',
                        component: require('./components/compras/asignacion/Create').default,
                        props: true,
                        meta: {
                            title: 'Registrar Asignación de Proveedores',
                            breadcrumb: { parent: 'asignacion-proveedores', name: 'REGISTRAR'},
                            middleware: [auth, context],
                            // permission: 'registrar_proveedor'
                        }
                    },
                ]
            },
            {
                path: 'catalogo-insumo',
                component: require('./components/compras/catalogos/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'catalogo-insumo',
                        component: require('./components/compras/catalogos/Index').default,
                        meta: {
                            title: 'Gestión de Insumos',
                            breadcrumb: {parent: 'compras', name: 'GESTIÓN DE INSUMOS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia',
                        name: 'familia',
                        component: require('./components/compras/catalogos/familia/Index').default,
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
                        component: require('./components/compras/catalogos/material/Index').default,
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
                        component: require('./components/compras/catalogos/herramienta/Index').default,
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
                path: 'cotizacion',
                component: require('./components/compras/cotizacion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'cotizacion',
                        component: require('./components/compras/cotizacion/Index').default,
                        meta: {
                            title: 'Cotizaciones',
                            breadcrumb: {parent: 'compras', name: 'COTIZACIONES'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_cotizacion_compra']
                        }
                    },
                    {
                        path: 'create',
                        name: 'cotizacion-create',
                        component: require('./components/compras/cotizacion/Create').default,
                        meta: {
                            title: 'Registrar Cotización',
                        breadcrumb: { parent: 'cotizacion', name: 'REGISTRAR COTIZACIÓN'},
                            middleware: [auth, context, permission],
                            permission: ['registrar_cotizacion_compra']
                        }
                    },
                    {
                        path: ':id/editar',
                        name: 'cotizacion-edit',
                        props: true,
                        component: require('./components/compras/cotizacion/Edit').default,
                        meta: {
                            title: 'Editar Cotización',
                            breadcrumb: { parent: 'cotizacion', name: 'EDITAR COTIZACIÓN'},
                            middleware: [auth, context, permission],
                            permission: ['editar_cotizacion_compra']
                        }
                    },
                ]
            },
            {
                path: 'orden-compra',
                component: require('./components/compras/orden-compra/partials/Layout.vue').default,
                children: [{
                    path: '/',
                    name: 'orden-compra',
                    component: require('./components/compras/orden-compra/Index').default,
                    meta: {
                        title: 'Ordenes de Compra',
                        breadcrumb: { parent: 'compras', name: 'ORDENES DE COMPRA' },
                        middleware: [auth, context, permission],
                        permission: ['consultar_orden_compra']
                    }
                }]
            },
            {
                path: 'requisicion',
                component: require('./components/compras/requisicion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'requisicion',
                        component: require('./components/compras/requisicion/Index').default,
                        meta: {
                            title: 'Requisiciones de Compra',
                            breadcrumb: {parent: 'compras', name: 'REQUISICIONES'},
                            middleware: [auth, context],
                        }
                    },
                    {
                        path: 'create',
                        name: 'requisicion-create',
                        component: require('./components/compras/requisicion/Create').default,
                        meta: {
                            title: 'Registrar Requisición de Compra',
                        breadcrumb: { parent: 'requisicion', name: 'REGISTRAR REQUISICIÓN'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_requisicion_compra'
                        }
                    },

                ]
            },
            {
                path: 'solicitud-compra',
                component: require('./components/compras/solicitud-compra/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'solicitud-compra',
                        component: require('./components/compras/solicitud-compra/Index').default,
                        meta: {
                            title: 'Solicitudes',
                            breadcrumb: {parent: 'compras', name: 'SOLICITUDES'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_solicitud_compra'
                        }
                    },
                    {
                        path: 'create',
                        name: 'solicitud-compra-create',
                        component: require('./components/compras/solicitud-compra/Create').default,
                        meta: {
                            title: 'Registrar Solicitud',
                            breadcrumb: { parent: 'solicitud-compra', name: 'REGISTRAR'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_solicitud_compra'
                        }
                    },
                    {
                        path: ':id/editar',
                        name: 'solicitud-compra-edit',
                        component: require('./components/compras/solicitud-compra/Edit').default,
                        props: true,
                        meta: {
                            title: 'Editar Solicitud',
                            breadcrumb: { parent: 'solicitud-compra', name: 'EDITAR'},
                            middleware: [auth, context, permission],
                            permission: 'editar_solicitud_compra'
                        }
                    }
                ]
            }
        ]
    },
    {
        path: '/sao/contabilidad',
        components: {
            default: require('./components/contabilidad/partials/Layout.vue').default,
            menu: require('./components/contabilidad/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'sistema_contable',
                component: require('./components/contabilidad/Index').default,
                meta: {
                    title: 'Contabilidad',
                    breadcrumb: { parent: 'home', name: 'CONTABILIDAD'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'cierre-periodo',
                name: 'cierre-periodo',
                component: require('./components/contabilidad/cierre-periodo/Index').default,
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
                component: require('./components/contabilidad/cuenta-almacen/Index').default,
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
                component: require('./components/contabilidad/cuenta-banco/Index').default,
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
                component: require('./components/contabilidad/cuenta-concepto/Index').default,
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
                component: require('./components/contabilidad/cuenta-costo/Index').default,
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
                component: require('./components/contabilidad/cuenta-empresa/Index').default,
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
                component: require('./components/contabilidad/cuenta-fondo/Index').default,
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
                component: require('./components/contabilidad/cuenta-general/Index').default,
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
                component: require('./components/contabilidad/cuenta-material/Index').default,
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
                component: require('./components/contabilidad/tipo-cuenta-contable/Index').default,
                meta: {
                    title: 'Tipos de Cuentas Contables',
                    breadcrumb: {name: 'TIPOS DE CUENTAS CONTABLES', parent: 'sistema_contable'},
                    middleware: [auth, context, permission],
                    permission: 'consultar_tipo_cuenta_contable'
                }
            },
            {
                path: 'poliza',
                component: require('./components/contabilidad/poliza/Layout.vue').default,
                children: [
                    {
                        path: '/',
                        name: 'poliza',
                        component: require('./components/contabilidad/poliza/Index').default,
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
                        component: require('./components/contabilidad/poliza/Show').default,
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
                        component: require('./components/contabilidad/poliza/Edit').default,
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
            default: require('./components/contratos/partials/Layout.vue').default,
            menu: require('./components/contratos/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'contratos',
                component: require('./components/contratos/Index').default,
                meta: {
                    title: 'Contratos',
                    breadcrumb: {parent:'home', name: 'CONTRATOS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'proyectado',
                component: require('./components/contratos/proyectado/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'proyectado',
                        component: require('./components/contratos/proyectado/Index').default,
                        meta: {
                            title: 'Contratos Proyectados',
                            breadcrumb: {parent: 'contratos', name: 'PROYECTADOS'},
                            middleware: [auth, context],

                        }
                    },
                ]
            },
            {
                path: 'subcontrato',
                component: require('./components/contratos/subcontrato/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'subcontrato',
                        component: require('./components/contratos/subcontrato/Index').default,
                        meta: {
                            title: 'Subcontratos',
                            breadcrumb: {parent: 'contratos', name: 'SUBCONTRATOS'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_subcontrato'
                        }
                    },
                ]
            },
            {
                path: 'estimacion',
                component: require('./components/contratos/estimacion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'estimacion',
                        component: require('./components/contratos/estimacion/Index').default,
                        meta: {
                            title: 'Estimaciones',
                            breadcrumb: {parent: 'contratos', name: 'ESTIMACIONES'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'create',
                        name: 'estimacion-create',
                        component: require('./components/contratos/estimacion/Create').default,
                        meta: {
                            title: 'Estimaciones',
                            breadcrumb: {parent: 'estimacion', name: 'REGISTRAR'},
                            middleware: [auth, context, permission],
                            permission: 'registrar_estimacion_subcontrato'
                        }
                    },
                    {
                        path: ':id/eliminar',
                        name: 'estimacion-delete',
                        props: true,
                        component: require('./components/contratos/estimacion/Delete').default,
                        meta: {
                            title: 'Eliminar Estimación',
                            breadcrumb: {parent: 'estimacion', name: 'ELIMINAR ESTIMACIÓN'},
                            middleware: [auth, context, permission],
                            permission: 'eliminar_estimacion_subcontrato'
                        }
                    },
                    {
                        path: ':id',
                        name: 'estimacion-show',
                        props: true,
                        component: require('./components/contratos/estimacion/Show').default,
                        meta: {
                            title: 'Información de Estimación',
                            breadcrumb: {parent: 'estimacion', name: 'VER ESTIMACIÓN'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: ':id/editar',
                        name: 'estimacion-edit',
                        props: true,
                        component: require('./components/contratos/estimacion/Edit').default,
                        meta: {
                            title: 'Editar Estimación',
                            breadcrumb: {parent: 'estimacion', name: 'EDITAR'},
                            middleware: [auth, context, permission],
                            permission: 'editar_estimacion_subcontrato'

                        }
                    },
                    {
                        path: 'formato-orden-pago',
                        name: 'formato-orden-pago',
                        component: require('./components/contratos/estimacion/formato-orden-pago/Index').default,
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
                component: require('./components/contratos/fondo-garantia/partials/Layout.vue').default,
                meta: {
                    middleware: [auth, context]
                },
                children: [
                    {
                        path: '/',
                        name: 'fondo-garantia',
                        component: require('./components/contratos/fondo-garantia/Index').default,
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
                    default: require('./components/contratos/fondo-garantia/solicitud-movimiento/partials/Layout.vue').default,
                },
                meta: {
                    middleware: [auth, context]
                },
                children: [
                    {
                        path: '/',
                        name: 'solicitud-movimiento-fg',
                        component: require('./components/contratos/fondo-garantia/solicitud-movimiento/Index').default,
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
            default: require('./components/finanzas/partials/Layout.vue').default,
            menu: require('./components/finanzas/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'finanzas',
                component: require('./components/finanzas/Index').default,
                meta: {
                    title: 'Finanzas',
                    breadcrumb: {parent:'home', name: 'FINANZAS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path:'banco',
                component: require('./components/finanzas/banco/Layout.vue').default,
                children: [
                    {
                        path:'/',
                        name: 'banco',
                        component: require('./components/finanzas/banco/Index.vue').default,
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
                        component: require('./components/finanzas/banco/Create').default,
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
                        component: require('./components/finanzas/banco/Show').default,
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
                        component: require('./components/finanzas/banco/Edit').default,
                        meta: {
                            title: 'Edición de Bancos',
                            breadcrumb: {name: 'EDICIÓN DE BANCOS', parent: 'banco'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: '/',
                        name: 'sucursal',
                        component: require('./components/finanzas/banco/sucursal/Index.vue').default,
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
                        component: require('./components/finanzas/banco/sucursal/Create').default,
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
                        component: require('./components/finanzas/banco/sucursal/Show').default,
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
                component: require('./components/finanzas/distribuir-recurso-remesa/Layout.vue').default,
                children: [
                    {
                        path: '/',
                        name: 'distribuir-recurso-remesa',
                        component: require('./components/finanzas/distribuir-recurso-remesa/Index').default,
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
                        component: require('./components/finanzas/distribuir-recurso-remesa/Create').default,
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
                        component: require('./components/finanzas/distribuir-recurso-remesa/Show').default,
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
                        component: require('./components/finanzas/distribuir-recurso-remesa/Autorizar').default,
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
                component: require('./components/finanzas/factura/Layout').default,
                children: [
                    {
                        path:'/',
                        name: 'factura',
                        component: require('./components/finanzas/factura/Index').default,
                        meta:{
                            title: 'Facturas',
                            breadcrumb: {name: 'FACTURAS', parent: 'finanzas'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_factura'
                        }
                    },
                    {
                        path: 'create',
                        name: 'factura-create',
                        component: require('./components/finanzas/factura/Create').default,
                        meta: {
                            title: 'Registrar Factura',
                            breadcrumb: {name: 'REGISTRAR', parent: 'factura'},
                            middleware: [auth, context, permission],
                            permission: ['registrar_factura']
                        }
                    }

                ]
            },
            {
                path:'fondo',
                component: require('./components/finanzas/fondo/Layout.vue').default,
                children: [
                    {
                        path: '/',
                        name: 'fondo',
                        component: require('./components/finanzas/fondo/Index.vue').default,
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
                        component: require('./components/finanzas/fondo/Create').default,
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
                        component: require('./components/finanzas/fondo/Show').default,
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
                component: require('./components/finanzas/gestion-cuenta-bancaria/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'gestion-cuenta-bancaria',
                        component: require('./components/finanzas/gestion-cuenta-bancaria/Index').default,
                        meta: {
                            title: 'Gestión de Cuentas Bancarias',
                            breadcrumb: {parent: 'finanzas', name: 'GESTIÓN DE CUENTAS BANCARIAS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'cuenta-empresa-bancaria',
                        name: 'cuenta-empresa-bancaria',
                        component: require('./components/finanzas/gestion-cuenta-bancaria/cuenta-empresa/Index').default,
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
                        component: require('./components/finanzas/gestion-cuenta-bancaria/solicitud-alta/Index').default,
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
                        component: require('./components/finanzas/gestion-cuenta-bancaria/solicitud-baja/Index').default,
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
                component: require('./components/finanzas/gestion-pago/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'gestion-pago',
                        component: require('./components/finanzas/gestion-pago/Index').default,
                        meta: {
                            title: 'Gestión de Pagos',
                            breadcrumb: {parent: 'finanzas', name: 'GESTIÓN DE PAGOS'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'pago',
                        name: 'pago',
                        component: require('./components/finanzas/gestion-pago/pago/Index').default,
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
                        component: require('./components/finanzas/gestion-pago/pago/Create').default,
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
                        component: require('./components/finanzas/gestion-pago/pago/RegistrarPago').default,
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
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Index').default,
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
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Create').default,
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
                        props: true,
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Autorizar').default,
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
                        component: require('./components/finanzas/gestion-pago/carga-masiva/Show').default,
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
                component: require('./components/finanzas/insumo-servicio/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'insumo-servicio',
                        component: require('./components/finanzas/insumo-servicio/Index').default,
                        meta: {
                            title: 'Insumo de Servicios',
                            breadcrumb: {parent: 'finanzas', name: 'INSUMO SERVICIO'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'familia-serv',
                        name: 'familia-serv',
                        component: require('./components/finanzas/insumo-servicio/familia/Index').default,
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
                        component: require('./components/finanzas/insumo-servicio/servicio/Index').default,
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
                component: require('./components/finanzas/solicitud/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'solicitud',
                        component: require('./components/finanzas/solicitud/Index').default,
                        meta: {
                            title: 'Solicitudes de Pago',
                            breadcrumb: {parent: 'finanzas', name: 'SOLICITUDES DE PAGO'},
                            middleware: [auth, context],

                        }
                    },
                    {
                        path: 'pago-anticipado',
                        name: 'pago-anticipado',
                        component: require('./components/finanzas/solicitud/pago-anticipado/Index').default,
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
                component: require('./components/finanzas/tesoreria/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'tesoreria',
                        component: require('./components/finanzas/tesoreria/Index').default,
                        meta: {
                            title: 'Tesorería',
                            breadcrumb: {parent: 'finanzas', name: 'TESORERÍA'},
                            middleware: [auth, context],
                        }
                    },
                    {
                        path: 'movimiento-bancario',
                        name: 'movimiento-bancario',
                        component: require('./components/finanzas/tesoreria/movimiento-bancario/Index').default,
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
                        component: require('./components/finanzas/tesoreria/traspaso-entre-cuentas/Index').default,
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
            default: require('./components/formato/partials/Layout.vue').default,
            menu: require('./components/formato/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'formatos',
                component: require('./components/formato/Index').default,
                meta: {
                    title: 'Formatos',
                    breadcrumb: { parent: 'home', name: 'FORMATOS'},
                    middleware: [auth, context, access],
                }
            },
            {
                path: 'orden-pago-estimacion',
                name: 'orden-pago-estimacion',
                component: require('./components/contratos/estimacion/formato-orden-pago/Index').default,
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
                component: require('./components/formato/estimacion/Index').default,
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
                component: require('./components/formato/compra/Index').default,
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
        path: '/sao/ventas',
        components: {
            default: require('./components/ventas/partials/Layout.vue').default,
            menu: require('./components/ventas/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'ventas',
                component: require('./components/ventas/Index').default,
                meta: {
                    title: 'Ventas',
                    breadcrumb: {parent:'home', name: 'VENTAS'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'venta',
                component: require('./components/ventas/partials/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'venta',
                        component: require('./components/ventas/venta/Index').default,
                        meta: {
                            title: 'Venta',
                            breadcrumb: {parent: 'ventas', name: 'VENTA'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_venta','registrar_venta','cancelar_venta']
                        }
                    },
                    {
                        path: 'create',
                        name: 'venta-create',
                        component: require('./components/ventas/venta/Create').default,
                        meta: {
                            title: 'Registrar Venta',
                            breadcrumb: {name: 'REGISTRAR', parent: 'venta'},
                            middleware: [auth, context, permission],
                            permission: ['registrar_venta']
                        }
                    },
                ]
            }
        ]
    },
    {
        path: '*',
        name: 'notFound',
        component: require('./components/pages/NotFound.vue').default,
    }
];
