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
        components: {
            default: require('./components/pages/Layout.vue').default,
            menu: require('./components/pages/Menu.vue').default,
        },
        children: [
            {
                path: '',
                name: 'home',
                component: require('./components/pages/Home.vue').default,
                meta: {
                    title: 'Sistemas',
                    middleware: [auth, context],
                    breadcrumb: {name: 'SAO ERP'}
                }
            },
            {
                path: 'modal/lista_archivos/:id',
                name: 'modal_lista_archivos',
                component: require('./components/globals/archivos/List').default,
                props: route => ({
                    id: route.params.id,
                    relacionadas: false,
                }),
                meta: {
                    middleware: [auth, context],
                },
            },
            {
                path: 'modal/lista_archivos_relacionados/:tipo/:id',
                name: 'modal_lista_archivos_relacionados',
                component: require('./components/globals/archivos/List').default,
                props: route => ({
                    tipo: route.params.tipo,
                    id: route.params.id,
                    relacionadas: true,
                }),
                meta: {
                    middleware: [auth, context],
                },
            },
            {
                path: 'modal/cotizacion/:id',
                name: 'modal_cotizacion',
                component: require('./components/compras/cotizacion/Show').default,
                props: true,
                meta: {
                    middleware: [auth, context, permission],
                    permission: 'consultar_cotizacion_compra'
                },
            },
            {
                path: 'modal/contrato_proyectado/:id',
                name: 'modal_contrato_proyectado',
                component: require('./components/contratos/proyectado/Show').default,
                props: true,
                meta: {
                    middleware: [auth, context, permission],
                    permission: 'consultar_contrato_proyectado'
                },
            },
            {
                path: 'modal/solicitud_compra/:id',
                name: 'modal_solicitud_compra',
                component: require('./components/compras/solicitud-compra/Show').default,
                props: true,
                meta: {
                    middleware: [auth, context, permission],
                    permission: 'consultar_solicitud_compra'
                },
            },
            {
                path: 'modal/solicitud_pago_anticipado/:id',
                name: 'modal_solicitud_pago_anticipado',
                component: require('./components/finanzas/solicitud/pago-anticipado/Show').default,
                props: true,
                meta: {
                    middleware: [auth, context, permission],
                    permission: 'consultar_solicitud_pago_anticipado'
                },
            },
            {
                path: 'modal/pago/:id',
                name: 'modal_pago',
                component: require('./components/finanzas/gestion-pago/pago/Show').default,
                props: true,
                meta: {
                    middleware: [auth, context, permission],
                    permission: 'consultar_pagos'
                },
            },
            {
                path: 'solicitud-recepcion-cfdi',
                component: require('./components/solicitud-recepcion-cfdi/partials/Layout.vue').default,
                children: [
                    {
                        path: '',
                        name: 'solicitud-recepcion-cfdi',
                        component: require('./components/solicitud-recepcion-cfdi/recepcion-cfdi/Index').default,
                        meta: {
                            title: 'Listado de Solicitudes de Revisión de CFDI',
                            breadcrumb: {parent:'home', name: 'SOLICITUDES DE REVISIÓN DE CFDI'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_solicitud_recepcion_cfdi_proyecto'
                        }
                    },
                ]
            },
            {
                path: 'recepcion-cfdi',
                components: {
                    default: require('./components/solicitud-recepcion-cfdi/partials/Layout.vue').default,
                    menu: require('./components/solicitud-recepcion-cfdi/partials/Menu.vue').default
                },
                children: [
                    {
                        path: '',
                        name: 'recepcion-cfdi',
                        component: require('./components/solicitud-recepcion-cfdi/recepcion-cfdi/BandejaEntrada').default,
                        meta: {
                            title: 'Recepción de CFDI a Revisión',
                            breadcrumb: {parent:'home', name: 'RECEPCIÓN DE CFDI'},
                            middleware: [auth, context, permission],
                            permission: 'aprobar_solicitud_recepcion_cfdi'
                        }
                    },
                    {
                        path: ':id/aprobar',
                        name: 'solicitud-recepcion-cfdi-aprobar',
                        component: require('./components/solicitud-recepcion-cfdi/recepcion-cfdi/Aprobar').default,
                        props: true,
                        meta: {
                            title: 'Aprobar Solicitud de Revisión de CFDI',
                            breadcrumb: { parent: 'recepcion-cfdi', name: 'APROBAR'},
                            middleware: [auth, context, permission],
                            permission: 'aprobar_solicitud_recepcion_cfdi'
                        }
                    },
                    {
                        path: ':id/rechazar',
                        name: 'solicitud-recepcion-cfdi-rechazar',
                        component: require('./components/solicitud-recepcion-cfdi/recepcion-cfdi/Rechazar').default,
                        props: true,
                        meta: {
                            title: 'Rechazar Solicitud de Revisión de CFDI',
                            breadcrumb: { parent: 'recepcion-cfdi', name: 'RECHAZAR'},
                            middleware: [auth, context, permission],
                            permission: 'rechazar_solicitud_recepcion_cfdi'
                        }
                    },
                    {
                        path: ':id',
                        name: 'solicitud-recepcion-cfdi-show-proyecto',
                        component: require('./components/solicitud-recepcion-cfdi/recepcion-cfdi/Show').default,
                        props: true,
                        meta: {
                            title: 'Consultar Solicitud de Revisión de CFDI',
                            breadcrumb: { parent: 'recepcion-cfdi', name: 'VER SOLICITUD'},
                            middleware: [auth, permission],
                            permission: 'consultar_solicitud_recepcion_cfdi_proyecto',
                        }
                    },
                ]
            },
            {
                path: 'compras',
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
                        path: 'asignacion-proveedor',
                        component: require('./components/compras/asignacion/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'asignacion-proveedor',
                                component: require('./components/compras/asignacion/Index').default,
                                meta: {
                                    title: 'Asignación de Proveedores',
                                    breadcrumb: {parent: 'compras', name: 'ASIGNACIÓN DE PROVEEDORES'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_asignacion_proveedor'
                                }
                            },
                            {
                                path: 'create/seleccionar-solicitud-compra',
                                name: 'seleccionar-solicitud-compra',
                                component: require('./components/compras/asignacion/SeleccionarSolicitud').default,
                                meta: {
                                    title: 'Registrar Asignación de Proveedores',
                                    breadcrumb: { parent: 'asignacion-proveedor', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_asignacion_proveedor'
                                }
                            },
                            {
                                path: ':id_solicitud/create',
                                name: 'asignacion-create',
                                component: require('./components/compras/asignacion/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Cotización',
                                    breadcrumb: { parent: 'asignacion-proveedor', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_asignacion_proveedor'
                                }
                            },
                            {
                                path: 'createLayout',
                                name: 'asignacion-proveedor-layout-create',
                                component: require('./components/compras/asignacion/CreateLayout').default,
                                props:true,
                                meta: {
                                    title: 'Registrar Asignación de Proveedores Layout',
                                    breadcrumb: { parent: 'asignacion-proveedor-create', name: 'REGISTRAR LAYOUT'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_asignacion_proveedor'
                                }
                            },
                            {
                                path: ':id',
                                name: 'asignacion-proveedor-show',
                                component: require('./components/compras/asignacion/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Asignación de Proveedores',
                                    breadcrumb: { parent: 'asignacion-proveedor', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_asignacion_proveedor'
                                }
                            },
                            {
                                path: ':id/edit',
                                name: 'asignacion-proveedor-edit',
                                component: require('./components/compras/asignacion/Edit').default,
                                props: true,
                                meta: {
                                    title: 'Editar Asignación de Proveedores',
                                    breadcrumb: { parent: 'asignacion-proveedor', name: 'EDITAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_orden_compra'
                                }
                            }
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
                                path: ':id/editar',
                                name: 'cotizacion-edit',
                                props: true,
                                component: require('./components/compras/cotizacion/Edit').default,
                                meta: {
                                    title: 'Editar Cotización',
                                    breadcrumb: { parent: 'cotizacion', name: 'EDITAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['editar_cotizacion_compra', 'cargar_layout_cotizacion_compra']
                                }
                            },
                            {
                                path: ':id',
                                name: 'cotizacion-show',
                                component: require('./components/compras/cotizacion/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Cotización',
                                    breadcrumb: { parent: 'cotizacion', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_cotizacion_compra'
                                }
                            },
                            {
                                path: 'create/seleccionar_solicitud_compra',
                                name: 'cotizacion-selecciona-solicitud-compra',
                                component: require('./components/compras/cotizacion/SeleccionaSolicitud').default,
                                meta: {
                                    title: 'Seleccionar Solicitud de Compra',
                                    breadcrumb: { parent: 'cotizacion', name: 'SELECCIONAR SOLICITUD'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_cotizacion_compra']
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'cotizacion-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_cotizacion_compra'],
                                }),
                                meta: {
                                    title: 'Documentos de Cotización',
                                    breadcrumb: { parent: 'cotizacion', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_cotizacion_compra'
                                }
                            }
                        ]
                    },
                    {
                        path: 'orden-compra',
                        component: require('./components/compras/orden-compra/partials/Layout.vue').default,
                        children: [
                            {
                                path: '/',
                                name: 'orden-compra',
                                component: require('./components/compras/orden-compra/Index').default,
                                meta: {
                                    title: 'Órdenes de Compra',
                                    breadcrumb: { parent: 'compras', name: 'ÓRDENES DE COMPRA' },
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_orden_compra']
                                }
                            },
                            {
                                path: ':id/edit',
                                name: 'orden-compra-edit',
                                props: true,
                                component: require('./components/compras/orden-compra/Edit').default,
                                meta: {
                                    title: 'Editar Orden Compra',
                                    breadcrumb: { parent: 'orden-compra', name: 'EDITAR'},
                                    middleware: [auth, context],
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'orden-compra-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_orden_compra'],
                                }),
                                meta: {
                                    title: 'Documentos de Orden de Compra',
                                    breadcrumb: { parent: 'orden-compra', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_orden_compra'
                                }
                            }
                        ]
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
                                    title: 'Registrar Requisición',
                                    breadcrumb: { parent: 'requisicion', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_requisicion_compra'
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'requisicion-compra-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_requisicion_compra'],
                                }),
                                meta: {
                                    title: 'Documentos de Solicitud',
                                    breadcrumb: { parent: 'solicitud-compra', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_solicitud_compra'
                                }
                            }
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
                                path: ':id',
                                name: 'solicitud-show',
                                component: require('./components/compras/solicitud-compra/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Solicitud',
                                    breadcrumb: { parent: 'solicitud-compra', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_solicitud_compra'
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
                            },
                            {
                                path: ':id/documentos',
                                name: 'solicitud-compra-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_solicitud_compra'],
                                }),
                                meta: {
                                    title: 'Documentos de Solicitud',
                                    breadcrumb: { parent: 'solicitud-compra', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_solicitud_compra'
                                }
                            },
                            {
                                path: ':id_solicitud/cotizacion/create',
                                name: 'cotizacion-create',
                                component: require('./components/compras/cotizacion/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Cotización',
                                    breadcrumb: { parent: 'cotizacion', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_cotizacion_compra']
                                }
                            },
                            {
                                path: ':id_solicitud/invitacion-compra/create',
                                name: 'invitacion-compra-create',
                                component: require('./components/compras/invitacion/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Invitación a Cotizar',
                                    breadcrumb: { parent: 'invitacion-compra-selecciona-solicitud', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_invitacion_cotizar_compra'
                                }
                            },
                            {
                                path: ':id/comparativa-cotizaciones',
                                name: 'comparativa-cotizacion-compra-consultar',
                                component: require('./components/compras/comparativa-cotizacion/Show').default,
                                props: true,
                                meta: {
                                    title: 'Comparativa de Cotizaciones',
                                    breadcrumb: { parent: 'comparativa-cotizacion-compra', name: 'COMPARATIVA COTIZACIONES'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_cotizacion_compra'
                                }
                            },
                        ]
                    },
                    {
                        path: 'invitacion-compra',
                        component: require('./components/compras/invitacion/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'invitacion-compra',
                                component: require('./components/compras/invitacion/Index').default,
                                meta: {
                                    title: 'Invitaciones a Cotizar',
                                    breadcrumb: {parent: 'compras', name: 'INVITACIONES'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_invitacion_cotizar_compra'
                                }
                            },
                            {
                                path: 'create/seleccionar_solicitud_compra',
                                name: 'invitacion-compra-selecciona-solicitud',
                                component: require('./components/compras/invitacion/SeleccionaSolicitud').default,
                                meta: {
                                    title: 'Seleccionar Solicitud de Compra',
                                    breadcrumb: { parent: 'invitacion-compra', name: 'SELECCIONAR SOLICITUD'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_invitacion_cotizar_compra']
                                }
                            },
                            {
                                path: ':id',
                                name: 'invitacion-compra-show',
                                component: require('./components/compras/invitacion/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Invitación a Cotizar',
                                    breadcrumb: { parent: 'invitacion-compra', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_invitacion_cotizar_compra'
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'invitacion-cotizar-compra-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id_invitacion: route.params.id,
                                    permiso: ['registrar_invitacion_cotizar_compra'],
                                }),
                                meta: {
                                    title: 'Documentos de Invitación a Cotizar',
                                    breadcrumb: { parent: 'invitacion-compra', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_invitacion_cotizar_contrato'
                                }
                            },
                            {
                                path: ':id_solicitud/cotizacion/create',
                                name: 'invitacion-create',
                                component: require('./components/compras/invitacion/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Invitación',
                                    breadcrumb: { parent: 'invitacion', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_invitacion_cotizar_compra']
                                }
                            },
                        ]
                    },
                    {
                        path: 'comparativa-cotizacion',
                        component: require('./components/compras/comparativa-cotizacion/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'comparativa-cotizacion-compra',
                                component: require('./components/compras/comparativa-cotizacion/Index').default,
                                meta: {
                                    title: 'Lista de Solicitudes de Compra Cotizadas o Con Invitación',
                                    breadcrumb: {parent: 'compras', name: 'SOLICITUDES DE COMPRA COTIZADAS O CON INVITACIÓN'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_cotizacion_compra'
                                }
                            },
                        ]
                    },
                ]
            },
            {
                path: 'almacenes',
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
                            breadcrumb: {parent:'home', name: 'SISTEMA DE ALMACENES'},
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
                            },
                            {
                                path: ':id/documentos',
                                name: 'entrada-almacen-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_entrada_almacen'],
                                }),
                                meta: {
                                    title: 'Documentos de Entrada de Almacén',
                                    breadcrumb: { parent: 'entrada-almacen', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_entrada_almacen'
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
                            },
                            {
                                path: ':id/documentos',
                                name: 'salida-almacen-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_salida_almacen'],
                                }),
                                meta: {
                                    title: 'Documentos de Salida de Almacén',
                                    breadcrumb: { parent: 'salida-almacen', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_salida_almacen'
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
                    {
                        path:'lista-almacenes',
                        component: require('./components/almacenes/kardex-material/Layout').default,
                        children: [
                            {
                                path:'/',
                                name: 'lista-almacenes',
                                component: require('./components/almacenes/kardex-material/Index').default,
                                meta: {
                                    title: 'Lista de Almacenes',
                                    breadcrumb: {parent: 'almacenes', name: 'ALMACENES'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_kardex_movimiento_material'
                                }
                            },
                            {
                                path: ':id_almacen/materiales',
                                name: 'consultar-materiales-almacen',
                                component: require('./components/almacenes/kardex-material/Show').default,
                                props: true,
                                meta: {
                                    title: 'Lista de Materiales',
                                    breadcrumb: {name: 'MATERIALES', parent: 'lista-almacenes'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_kardex_movimiento_material'
                                }
                            },
                            {
                                path: ':id_almacen/materiales/:id_material/kardex',
                                name: 'kardex',
                                component: require('./components/almacenes/kardex-material/Kardex').default,
                                props: true,
                                meta: {
                                    title: 'Kardex de Material',
                                    breadcrumb: {name: 'KARDEX', parent: 'lista-almacenes'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_kardex_movimiento_material'
                                }
                            },
                        ]
                    },
                ]
            },
            {
                path: 'contratos',
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
                        path: 'avance-subcontrato',
                        component: require('./components/contratos/avance-subcontrato/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'avance-subcontrato',
                                component: require('./components/contratos/avance-subcontrato/Index').default,
                                meta: {
                                    title: 'Listado de Avance de Subcontratos',
                                    breadcrumb: {parent: 'contratos', name: 'AVANCE DE SUBCONTRATO'},
                                    middleware: [auth, context],

                                }
                            },
                            {
                                path: 'create',
                                name: 'avance-subcontrato-seleccionar-create',
                                component: require('./components/contratos/avance-subcontrato/SeleccionarSubcontrato').default,
                                meta: {
                                    title: 'Seleccionar Subcontrato',
                                    breadcrumb: { parent: 'avance-subcontrato', name: 'SELECCIONAR SUBCONTRATO'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_avance_subcontrato']
                                }
                            },
                            {
                                path: ':id/create',
                                name: 'avance-subcontrato-create',
                                props: true,
                                component: require('./components/contratos/avance-subcontrato/Create').default,
                                meta: {
                                    title: 'Registrar Avance de Subcontrato',
                                    breadcrumb: { parent: 'avance-subcontrato-seleccionar-create', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_avance_subcontrato']
                                }
                            },
                            {
                                path: ':id',
                                name: 'avance-subcontrato-show',
                                props: true,
                                component: require('./components/contratos/avance-subcontrato/Show').default,
                                meta: {
                                    title: 'Consultar Avance de Subcontrato',
                                    breadcrumb: { parent: 'avance-subcontrato', name: 'CONSULTAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_avance_subcontrato']
                                }
                            },
                            {
                                path: ':id/editar',
                                name: 'avance-subcontrato-edit',
                                props: route => ({
                                    id: route.params.id,
                                }),
                                component: require('./components/contratos/avance-subcontrato/Edit').default,
                                meta: {
                                    title: 'Editar Avance de Subcontrato',
                                    breadcrumb: { parent: 'avance-subcontrato', name: 'EDITAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['editar_avance_subcontrato']
                                }
                            },
                            {
                                path: ':id/delete',
                                name: 'avance-subcontrato-delete',
                                props: route => ({
                                    id: route.params.id,
                                }),
                                component: require('./components/contratos/avance-subcontrato/Delete').default,
                                meta: {
                                    title: 'Eliminar Avance de Subcontrato',
                                    breadcrumb: { parent: 'avance-subcontrato', name: 'ELIMINAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['eliminar_avance_subcontrato'],
                                }
                            },
                        ]
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
                                    breadcrumb: {parent: 'contratos', name: 'CONTRATOS PROYECTADOS'},
                                    middleware: [auth, context],

                                }
                            },
                            {
                                path: 'createLayout',
                                name: 'proyectado-layout-create',
                                component: require('./components/contratos/proyectado/CreateLayout').default,
                                props:true,
                                meta: {
                                    title: 'Registrar Contratos Proyectados Layout',
                                    breadcrumb: {parent: 'proyectado', name: 'REGISTRAR LAYOUT'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_contrato_proyectado'
                                }
                            },
                            {
                                path: 'create',
                                name: 'proyectado-create',
                                component: require('./components/contratos/proyectado/Create').default,
                                meta: {
                                    title: 'Registrar Contratos Proyectados',
                                    breadcrumb: {parent: 'proyectado', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_contrato_proyectado'
                                }
                            },
                            {
                                path: ':id',
                                name: 'proyectado-show',
                                component: require('./components/contratos/proyectado/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Contrato Proyectado',
                                    breadcrumb: { parent: 'proyectado', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_contrato_proyectado'
                                }
                            },
                            {
                                path: ':id/edit',
                                name: 'proyectado-edit',
                                component: require('./components/contratos/proyectado/Edit').default,
                                props: true,
                                meta: {
                                    title: 'Editar Contrato Proyectado',
                                    breadcrumb: { parent: 'proyectado', name: 'EDITAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'editar_contrato_proyectado'
                                }
                            },
                            {
                                path: ':id_contrato/presupuesto/create',
                                name: 'presupuesto-create',
                                component: require('./components/contratos/presupuesto/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Presupuesto Contratista',
                                    breadcrumb: { parent: 'presupuesto-selecciona-contrato-proyectado', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_presupuesto_contratista']
                                }
                            },
                            {
                                path: ':id_contrato/asignacion-contratista/create',
                                name: 'asignacion-contratista-create',
                                component: require('./components/contratos/asignacion-contratista/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Asignación Contratistas',
                                    breadcrumb: { parent: 'asignacion-contratista-selecciona-contrato-proyectado', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_asignacion_contratista']
                                }
                            },
                            {
                                path: ':id_contrato/asignacion-contratista/layoutCreate',
                                name: 'asignacion-contratista-layout-create',
                                component: require('./components/contratos/asignacion-contratista/CreateLayout').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Asignación Contratistas Layout',
                                    breadcrumb: { parent: 'asignacion-contratista-selecciona-contrato-proyectado', name: 'REGISTRAR LAYOUT'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_asignacion_contratista']
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'proyectado-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_orden_compra'],
                                }),
                                meta: {
                                    title: 'Documentos de Contratos Proyectados',
                                    breadcrumb: { parent: 'proyectado', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_contrato_proyectado'
                                }
                            }
                        ]
                    },
                    {
                        path: 'invitacion-cotizar',
                        component: require('./components/contratos/invitacion/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'invitacion-cotizar-contrato',
                                component: require('./components/contratos/invitacion/Index').default,
                                meta: {
                                    title: 'Invitaciones a Cotizar',
                                    breadcrumb: {parent: 'contratos', name: 'INVITACIONES'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_invitacion_cotizar_contrato'
                                }
                            },
                            {
                                path: 'create/seleccionar_contrato',
                                name: 'invitacion-contrato-selecciona-contrato',
                                component: require('./components/contratos/invitacion/SeleccionaContrato').default,
                                meta: {
                                    title: 'Seleccionar Contrato Proyectado',
                                    breadcrumb: { parent: 'invitacion-cotizar-contrato', name: 'SELECCIONAR CONTRATO'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_invitacion_cotizar_contrato']
                                }
                            },
                            {
                                path: ':id',
                                name: 'invitacion-contrato-show',
                                component: require('./components/contratos/invitacion/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Invitación a Cotizar',
                                    breadcrumb: { parent: 'invitacion-cotizar-contrato', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_invitacion_cotizar_contrato'
                                }
                            },
                            {
                                path: ':id_contrato/invitacion/create',
                                name: 'invitacion-contrato-create',
                                component: require('./components/contratos/invitacion/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Invitación',
                                    breadcrumb: { parent: 'invitacion-cotizar-contrato', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_cotizacion_compra']
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'invitacion-cotizar-contrato-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id_invitacion: route.params.id,
                                    permiso: ['registrar_invitacion_cotizar_contrato'],
                                }),
                                meta: {
                                    title: 'Documentos de Invitación a Cotizar',
                                    breadcrumb: { parent: 'invitacion-cotizar-contrato', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_invitacion_cotizar_contrato'
                                }
                            }
                        ]
                    },
                    {
                        path: 'presupuesto',
                        component: require('./components/contratos/presupuesto/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'presupuesto',
                                component: require('./components/contratos/presupuesto/Index').default,
                                meta: {
                                    title: 'Presupuesto Contratista',
                                    breadcrumb: {parent: 'contratos', name: 'PRESUPUESTO CONTRATISTA'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_presupuesto_contratista'
                                }
                            },
                            {
                                path: ':id/editar',
                                name: 'presupuesto-edit',
                                props: true,
                                component: require('./components/contratos/presupuesto/Edit').default,
                                meta: {
                                    title: 'Editar Presupuesto Contratista',
                                    breadcrumb: { parent: 'presupuesto', name: 'EDITAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'editar_presupuesto_contratista'
                                }
                            },
                            {
                                path: ':id',
                                name: 'presupuesto-show',
                                component: require('./components/contratos/presupuesto/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Presupuesto Contratista',
                                    breadcrumb: { parent: 'presupuesto', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_presupuesto_contratista'
                                }
                            },
                            {
                                path: 'create/seleccionar_contrato_proyectado',
                                name: 'presupuesto-selecciona-contrato-proyectado',
                                component: require('./components/contratos/presupuesto/SeleccionaContratoProyectado').default,
                                meta: {
                                    title: 'Seleccionar Contrato Proyectado',
                                    breadcrumb: { parent: 'presupuesto', name: 'SELECCIONAR CONTRATO'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_presupuesto_contratista']
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'presupuesto-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_presupuesto_contratista'],
                                }),
                                meta: {
                                    title: 'Documentos de Presupuesto',
                                    breadcrumb: { parent: 'presupuesto', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_presupuesto_contratista'
                                }
                            }
                        ]
                    },
                    {
                        path: 'comparativa-cotizacion',
                        component: require('./components/compras/comparativa-cotizacion/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'comparativa-cotizacion-contrato',
                                component: require('./components/contratos/comparativa-cotizacion/Index').default,
                                meta: {
                                    title: 'Lista de Contratos Proyectados Cotizados o Con Invitación',
                                    breadcrumb: {parent: 'contratos', name: 'CONTRATOS PROYECTADOS COTIZADAS O CON INVITACIÓN'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_presupuesto_contratista'
                                }
                            },
                        ]
                    },
                    {
                        path: ':id/comparativa-cotizaciones',
                        name: 'comparativa-cotizacion-contrato-consultar',
                        component: require('./components/contratos/comparativa-cotizacion/Show').default,
                        props: true,
                        meta: {
                            title: 'Comparativa de Cotizaciones',
                            breadcrumb: { parent: 'comparativa-cotizacion-contrato', name: 'COMPARATIVA COTIZACIONES'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_presupuesto_contratista'
                        }
                    },
                    {
                        path: 'asignacion-contratista',
                        component: require('./components/contratos/asignacion-contratista/partials/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'asignacion-contratista',
                                component: require('./components/contratos/asignacion-contratista/Index').default,
                                meta: {
                                    title: 'Asignación Contratistas',
                                    breadcrumb: {parent: 'contratos', name: 'ASIGNACIONES'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_asignacion_contratista'
                                }
                            },
                            {
                                path: 'create/seleccionar_contrato_proyectado',
                                name: 'asignacion-contratista-selecciona-contrato-proyectado',
                                component: require('./components/contratos/asignacion-contratista/SeleccionaContratoProyectado').default,
                                meta: {
                                    title: 'Seleccionar Contrato Proyectado',
                                    breadcrumb: { parent: 'asignacion-contratista', name: 'SELECCIONAR CONTRATO'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_asignacion_contratista']
                                }
                            },
                            {
                                path: ':id',
                                name: 'asignacion-contratista-show',
                                component: require('./components/contratos/asignacion-contratista/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Asignación de Contratistas',
                                    breadcrumb: { parent: 'asignacion-contratista', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_asignacion_contratista'
                                }
                            },
                            {
                                path: ':id/eliminar',
                                name: 'asignacion-contratista-delete',
                                props: true,
                                component: require('./components/contratos/asignacion-contratista/Delete').default,
                                meta: {
                                    title: 'Eliminar Asignación de Contratista',
                                    breadcrumb: {parent: 'asignacion-contratista', name: 'ELIMINAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['eliminar_asignacion_contratista']
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
                            {
                                path: ':id/documentos',
                                name: 'subcontrato-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_subcontrato'],
                                }),
                                meta: {
                                    title: 'Documentos de Subcontrato',
                                    breadcrumb: { parent: 'subcontrato', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_subcontrato'
                                }
                            },
                            {
                                path: ':id/edit',
                                name: 'subcontrato-edit',
                                component: require('./components/contratos/subcontrato/Edit').default,
                                props: route => ({
                                    id: route.params.id,
                                }),
                                meta: {
                                    title: 'Editar Subcontrato',
                                    breadcrumb: { parent: 'subcontrato', name: 'EDITAR'},
                                    middleware: [auth, context],
                                    permission: ['editar_subcontrato']
                                }
                            },
                            {
                                path: ':id',
                                name: 'subcontrato-delete',
                                props: true,
                                component: require('./components/contratos/subcontrato/Delete').default,
                                meta: {
                                    title: 'Eliminar Subcontrato',
                                    breadcrumb: {parent: 'subcontrato', name: 'ELIMINAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['eliminar_subcontrato']
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
                                name: 'estimacion-seleccionar-create',
                                component: require('./components/contratos/estimacion/SeleccionarSubcontrato').default,
                                meta: {
                                    title: 'Seleccionar Subcontrato',
                                    breadcrumb: { parent: 'estimacion', name: 'SELECCIONAR SUBCONTRATO'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_estimacion_subcontrato']
                                }
                            },
                            {
                                path: ':id/create',
                                name: 'estimacion-create',
                                props: true,
                                component: require('./components/contratos/estimacion/Create').default,
                                meta: {
                                    title: 'Estimación',
                                    breadcrumb: { parent: 'estimacion-seleccionar-create', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_estimacion_subcontrato']
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
                                    middleware: [auth, context, permission],
                                    permission :'consultar_estimacion_subcontrato'
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
                                path: ':id/editarLayout',
                                name: 'estimacion-edit-layout',
                                props: true,
                                component: require('./components/contratos/estimacion/EditLayout').default,
                                meta: {
                                    title: 'Editar Estimación Layout',
                                    breadcrumb: {parent: 'estimacion', name: 'EDITAR LAYOUT'},
                                    middleware: [auth, context, permission],
                                    permission: 'editar_estimacion_subcontrato'
                                }
                            },
                            {
                                path: ':id/editarCondiciones',
                                name: 'estimacion-edit-condiciones',
                                props: true,
                                component: require('./components/contratos/estimacion/EditCondiciones').default,
                                meta: {
                                    title: 'Editar Condiciones Estimación',
                                    breadcrumb: {parent: 'estimacion', name: 'EDITAR CONDICIONES'},
                                    middleware: [auth, context, permission],
                                    permission: 'editar_estimacion_subcontrato'
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'estimacion-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_estimacion_subcontrato'],
                                }),
                                meta: {
                                    title: 'Documentos de Estimacion',
                                    breadcrumb: { parent: 'estimacion', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_estimacion_subcontrato'
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
                        path: 'solicitud-cambio',
                        component: require('./components/contratos/solicitud-cambio/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'solicitud-cambio',
                                component: require('./components/contratos/solicitud-cambio/Index').default,
                                meta: {
                                    title: 'Solicitudes de Cambio a Subcontratos',
                                    breadcrumb: {parent: 'contratos', name: 'SOLICITUDES DE CAMBIO'},
                                    middleware: [auth, context],
                                }
                            },
                            {
                                path: 'create',
                                name: 'solicitud-cambio-create',
                                component: require('./components/contratos/solicitud-cambio/Create').default,
                                meta: {
                                    title: 'Solicitud de Cambio a Subcontrato',
                                    breadcrumb: {parent: 'solicitud-cambio', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_solicitud_cambio_subcontrato'
                                }
                            },
                            {
                                path: ':id/cancelar',
                                name: 'solicitud-cambio-cancelar',
                                props: true,
                                component: require('./components/contratos/solicitud-cambio/Cancelar').default,
                                meta: {
                                    title: 'Solicitud de Cambio a Subcontrato',
                                    breadcrumb: {parent: 'solicitud-cambio', name: 'CANCELAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'cancelar_solicitud_cambio_subcontrato'
                                }
                            },
                            {
                                path: ':id',
                                name: 'solicitud-cambio-show',
                                props: true,
                                component: require('./components/contratos/solicitud-cambio/Show').default,
                                meta: {
                                    title: 'Solicitud de Cambio a Subcontrato',
                                    breadcrumb: {parent: 'solicitud-cambio', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission :'consultar_solicitud_cambio_subcontrato'
                                }
                            },
                            {
                                path: ':id/aplicar',
                                name: 'solicitud-cambio-aplicar',
                                props: true,
                                component: require('./components/contratos/solicitud-cambio/Aplicar').default,
                                meta: {
                                    title: 'Solicitud de Cambio a Subcontrato',
                                    breadcrumb: {parent: 'solicitud-cambio', name: 'APLICAR'},
                                    middleware: [auth, context, permission],
                                    permission :'aplicar_solicitud_cambio_subcontrato'
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'solicitud-cambio-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_solicitud_cambio_subcontrato'],
                                }),
                                meta: {
                                    title: 'Documentos de Solicitud de Cambio a Subcontrato',
                                    breadcrumb: { parent: 'solicitud-cambio', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_solicitud_cambio_subcontrato'
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
                path: 'finanzas',
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
                        path: 'comprobante-fondo',
                        component: require('./components/finanzas/comprobante-fondo/Layout.vue').default,
                        children: [
                            {
                                path: '/',
                                name: 'comprobante-fondo',
                                component: require('./components/finanzas/comprobante-fondo/Index').default,
                                meta: {
                                    title: 'Comprobantes de Fondo',
                                    breadcrumb: {name: 'COMPROBANTE DE FONDO', parent: 'finanzas'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_comprobante_fondo'
                                }
                            },
                            {
                                path: 'create',
                                name: 'comprobante-fondo-create',
                                component: require('./components/finanzas/comprobante-fondo/Create').default,
                                meta: {
                                    title: 'Registrar Comprobantes de Fondo',
                                    breadcrumb: {name: 'REGISTRAR', parent: 'comprobante-fondo'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_comprobante_fondo'
                                }
                            },
                            {
                                path: ':id',
                                name: 'comprobante-fondo-show',
                                props: true,
                                component: require('./components/finanzas/comprobante-fondo/Show').default,
                                meta: {
                                    title: 'Consultar Comprobante de Fondo',
                                    breadcrumb: {name: 'VER', parent: 'comprobante-fondo'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_comprobante_fondo'
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
                                    title: 'Lista de Facturas',
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
                            },
                            {
                                path: ':id/documentos',
                                name: 'factura-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_factura'],
                                }),
                                meta: {
                                    title: 'Documentos de Factura',
                                    breadcrumb: { parent: 'factura', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_factura'
                                }
                            },
                            {
                                path: ':id/revisar',
                                name: 'factura-revisar',
                                component: require('./components/finanzas/factura/Revision').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['revisar_factura'],
                                }),
                                meta: {
                                    title: 'Revisión de Factura',
                                    breadcrumb: { parent: 'factura', name: 'REVISIÓN FACTURA'},
                                    middleware: [auth, context, permission],
                                    permission: 'revisar_factura'
                                }
                            },
                            {
                                path: ':id/varios',
                                name: 'factura-varios-revisar',
                                component: require('./components/finanzas/factura/RevisionVario').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_factura_varios'],
                                }),
                                meta: {
                                    title: 'Factura de Varios',
                                    breadcrumb: { parent: 'factura', name: 'FACTURA VARIOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_factura_varios'
                                }
                            }
                        ]
                    },
                    {
                        path:'cfdi',
                        component: require('./components/finanzas/factura/Layout').default,
                        children: [
                            {
                                path:'/',
                                name: 'cfdi',
                                component: require('./components/finanzas/cfdi/Index').default,
                                meta:{
                                    title: 'CFDI',
                                    breadcrumb: {name: 'CFDI', parent: 'finanzas'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_cfdi'
                                }
                            },
                        ]
                    },
                    {
                        path:'conciliacion-bancaria',
                        component: require('./components/finanzas/conciliacion-bancaria/Layout').default,
                        children: [
                            {
                                path:'/',
                                name: 'conciliacion-bancaria',
                                component: require('./components/finanzas/conciliacion-bancaria/Index').default,
                                meta:{
                                    title: 'Conciliación Bancaria',
                                    breadcrumb: {name: 'CONCILIACION BANCARIA', parent: 'finanzas'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_conciliacion_bancaria'
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
                                    breadcrumb: {name: 'REGISTRAR', parent: 'fondo'},
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
                                    breadcrumb: {name: 'VER', parent: 'fondo'},
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
                                name: 'pago',
                                component: require('./components/finanzas/gestion-pago/pago/Index').default,
                                meta: {
                                    title: 'Listado de Pagos Registrados',
                                    breadcrumb: {
                                        parent: 'finanzas', name: 'PAGOS'
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
                                path: ':id',
                                name: 'pago-show',
                                props: true,
                                component: require('./components/finanzas/gestion-pago/pago/Show').default,
                                meta: {
                                    title: 'Consultar Pago',
                                    breadcrumb: {name: 'VER', parent: 'pago'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_pagos'
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'pago-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['consultar_pagos'],
                                }),
                                meta: {
                                    title: 'Documentos de Pago',
                                    breadcrumb: { parent: 'pago', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_pagos'
                                }
                            },
                        ]
                    },
                    {
                        path: 'registro-pago',
                        component: require('./components/finanzas/gestion-pago/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'registro-pago',
                                component: require('./components/finanzas/gestion-pago/pago/registro/Index').default,
                                meta: {
                                    title: 'Listado de Transacciones Pendientes de Pago',
                                    breadcrumb: {
                                        parent: 'pago',
                                        name: 'TRANSACCIONES PENDIENTES DE PAGO'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_pago'
                                }
                            },
                            {
                                path: ':id/pago',
                                name: 'registrar-pago',
                                component: require('./components/finanzas/gestion-pago/pago/registro/Create').default,
                                props: true,
                                meta: {
                                    title: 'Registrar Pago',
                                    breadcrumb: {name: 'REGISTRAR', parent: 'registro-pago'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_pago'
                                }
                            },
                        ]
                    },
                    {
                        path: 'pago-manual',
                        component: require('./components/finanzas/pago-manual/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'pago-manual',
                                component: require('./components/finanzas/pago-manual/Index').default,
                                meta: {
                                    title: 'Lista de Pagos Pendientes de Aplicar Manualmente',
                                    breadcrumb: {
                                        parent: 'finanzas',
                                        name: 'PAGOS PENDIENTES DE APLICAR'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_pago_manual'
                                },
                            },
                            {
                                path: ':id/aplicar',
                                name: 'pago-aplicar',
                                props: true,
                                component: require('./components/finanzas/pago-manual/Aplicar').default,
                                meta: {
                                    title: 'Pago Pendiente de Aplicación',
                                    breadcrumb: {name: 'APLICAR PAGO MANUAL', parent: 'pago-manual'},
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_pago_manual'
                                }
                            }
                        ]
                    },
                    {
                        path: 'carga-masiva',
                        component: require('./components/finanzas/gestion-pago/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'carga-masiva',
                                component: require('./components/finanzas/gestion-pago/carga-masiva/Index').default,
                                meta: {
                                    title: 'Carga Masiva',
                                    breadcrumb: {
                                        parent: 'pago',
                                        name: 'CARGA MASIVA'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_carga_layout_pago'
                                }
                            },
                            {
                                path: 'carga-masiva-create',
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
                                    title: 'Lista de Solicitudes de Pago Anticipado Registradas',
                                    breadcrumb: {
                                        parent: 'solicitud',
                                        name: 'PAGO ANTICIPADO'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_solicitud_pago_anticipado'
                                }
                            },
                            {
                                path: ':id',
                                name: 'solicitud-pago-anticipado-show',
                                props: true,
                                component: require('./components/finanzas/solicitud/pago-anticipado/Show').default,
                                meta: {
                                    title: 'Ver Solicitud',
                                    breadcrumb: {name: 'VER', parent: 'pago-anticipado'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_solicitud_pago_anticipado'
                                }
                            },
                            {
                                path: ':id/documentos',
                                name: 'solicitud-pago-anticipado-documentos',
                                component: require('./components/globals/archivos/Files').default,
                                props: route => ({
                                    id: route.params.id,
                                    permiso: ['registrar_solicitud_pago_anticipado'],
                                }),
                                meta: {
                                    title: 'Documentos de Solicitud de Pago Anticipado',
                                    breadcrumb: { parent: 'pago-anticipado', name: 'DOCUMENTOS'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_solicitud_pago_anticipado'
                                }
                            }
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
                path: 'contabilidad',
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
                        path: 'asociar-poliza-cfdi',
                        component: require('./components/contabilidad/asociar-poliza-cfdi/Layout.vue').default,
                        children:[
                            {
                                path:"/",
                                name:"asociar-poliza-cfdi",
                                component: require('./components/contabilidad/asociar-poliza-cfdi/Index.vue').default,
                                meta: {
                                    title: 'Asociar CFDI con Pólizas de Contpaq',
                                    breadcrumb: {parent: 'sistema_contable', name: 'ASOCIAR CFDI CON PÓLIZAS DE CONTPAQ'},
                                    middleware: [auth, context, permission],
                                    permission: ['asociar_poliza_contpaq_cfdi'],
                                }
                            },
                        ]
                    },
                    {
                        path: 'cfdi-pendientes-carga-add',
                        component: require('./components/contabilidad/cfdi-add-contpaq/Layout.vue').default,
                        children:[
                            {
                                path:"/",
                                name:"cfdi-pendientes-carga-add",
                                component: require('./components/contabilidad/cfdi-add-contpaq/Index.vue').default,
                                meta: {
                                    title: 'Cargar CFDI a ADD de Contpaq',
                                    breadcrumb: {parent: 'sistema_contable', name: 'CARGAR CFDI A ADD'},
                                    middleware: [auth, context, permission],
                                    permission: ['consultar-cfdi-pendientes-carga-add'],
                                }
                            },
                        ]
                    },
                    {
                        path: 'cuentas-proveedor',
                        props:true,
                        component: require('./components/contabilidad/asociacion-cuenta-proveedor/Index.vue').default,
                        children:[
                            {
                                path:"/",
                                name:"cuentas-proveedor-en-sao",
                                component: require('./components/contabilidad/asociacion-cuenta-proveedor/Index.vue').default,
                                meta: {
                                    title: 'Asociación Cuenta Proveedor',
                                    breadcrumb: {parent: 'sistema_contable', name: 'ASOCIACIÓN CTA PROVEEDOR'},
                                    middleware: [auth,context, permission],
                                    permission: ['asociar_cuentas_contpaq_con_proveedor'],
                                }
                            }
                        ]
                    },
                    {
                        path: 'poliza-cfdi',
                        component: require('./components/contabilidad/poliza-cfdi/Layout.vue').default,
                        children:[
                            {
                                path:"/",
                                name:"poliza-cfdi-proyecto",
                                component: require('./components/contabilidad/poliza-cfdi/Index.vue').default,
                                meta: {
                                    title: 'Pólizas-CFDI',
                                    breadcrumb: {parent: 'sistema_contable', name: 'PÓLIZAS CFDI'},
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_poliza'],
                                    general: true
                                }
                            },
                            {
                                path: ':id_empresa/:id',
                                name: 'poliza-cfdi-show',
                                props: true,
                                component: require('./components/contabilidad/poliza-cfdi/Show').default,
                                meta: {
                                    title: 'Consultar Póliza',
                                    breadcrumb: {parent: 'poliza-cfdi-proyecto', name: 'CONSULTAR'},
                                    middleware: [auth, permission],
                                    permission: 'consultar_poliza',
                                    general: true
                                }
                            },
                        ]
                    },
                    {
                        path: 'informe-sat',
                        name: 'informe-sat',
                        component: require('./components/contabilidad/informes/InformeSAT.vue').default,
                        meta: {
                            title: 'Informe CFDI vs Pasivos',
                            breadcrumb: {name: 'INFORME CFDI vs Pasivos', parent: 'sistema_contable'},
                            middleware: [auth, permission],
                            permission: ['consultar_informe_sat'],
                            general: true
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
                            },
                        ]
                    },
                    {
                        path: 'poliza-contpaq',
                        component: require('./components/contabilidad/poliza-contpaq/Layout.vue').default,
                        children:[
                            {
                                path:"/",
                                name:"poliza-contpaq-en-sao",
                                component: require('./components/contabilidad/poliza-contpaq/Index.vue').default,
                                meta: {
                                    title: 'Pólizas Contpaq',
                                    breadcrumb: {parent: 'sistema_contable', name: 'PÓLIZAS CONTPAQ'},
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_poliza_ctpq'],
                                }
                            },
                            {
                                path: ':id',
                                name: 'poliza-contpaq-en-sao-show',
                                props: true,
                                component: require('./components/contabilidad/poliza-contpaq/Show').default,
                                meta: {
                                    title: 'Ver Póliza Contpaq',
                                    breadcrumb: {parent: 'poliza-contpaq-en-sao', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_poliza_ctpq'
                                }
                            },
                            {
                                path: ':id/asociar-cfdi',
                                name: 'poliza-contpaq-en-sao-asociar-cfdi',
                                props: true,
                                component: require('./components/contabilidad/poliza-contpaq/AsociaCFDI.vue').default,
                                meta: {
                                    title: 'Asociar CFDI a Póliza',
                                    breadcrumb: {parent: 'poliza-contpaq-en-sao', name: 'ASOCIAR CFDI'},
                                    middleware: [auth, context, permission],
                                    permission: 'asociar-cfdi-a-poliza-contpaq-desde-sao',
                                }
                            },
                        ]
                    },
                ]
            },
            {
                path: 'ventas',
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
                path: 'catalogos',
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
                        path: 'almacen',
                        component: require('./components/catalogos/almacen/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'almacen',
                                component: require('./components/catalogos/almacen/Index').default,
                                meta: {
                                    title: 'Catálogo de Almacenes',
                                    breadcrumb: {parent: 'catalogos', name: 'ALMACENES'},
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_almacen_material','consultar_almacen_maquinaria', 'consultar_almacen_maquina_controladora_insumo', 'consultar_almacen_mano_obra', 'consultar_almacen_servicio', 'consultar_almacen_herramienta']
                                }
                            },
                        ]
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
                                    breadcrumb: {parent: 'catalogos', name: 'UNIDAD'},
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
                                    breadcrumb: {parent: 'catalogos', name: 'EMPRESA'},
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
                                    middleware: [auth, context, permission],
                                    permission: 'registrar_proveedor'
                                }
                            },
                        ]
                    },

                    {
                        path: 'unificacion-proveedores',
                        component: require('./components/catalogos/unificacion-proveedores/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'unificacion-proveedores',
                                component: require('./components/catalogos/unificacion-proveedores/Index').default,
                                meta: {
                                    title: 'Unificacion de Proveedores',
                                    breadcrumb: {parent: 'catalogos', name: 'UNIFICACIÓN PROVEEDORES'},
                                    middleware: [auth, context,permission],
                                    permission: ['consultar_unificacion_proveedores']
                                }
                            },
                            {
                                path: 'create',
                                name: 'unificacion-proveedores-create',
                                component: require('./components/catalogos/unificacion-proveedores/Create').default,
                                meta: {
                                    title: 'Registrar Unificación de Proveedores',
                                    breadcrumb: { parent: 'unificacion-proveedores', name: 'REGISTRAR UNIFICACIÓN PROVEEDORES'},
                                    middleware: [auth, context,permission],
                                    permission: 'registrar_unificacion_proveedores'
                                }
                            },
                        ]
                    },
                ]
            },
            {
                path: 'formatos',
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
                path: 'acarreos',
                components: {
                    default: require('./components/acarreos/partials/Layout.vue').default,
                    menu: require('./components/acarreos/partials/Menu.vue').default
                },
                children: [
                    {
                        path: '',
                        name: 'acarreos',
                        component: require('./components/acarreos/Index').default,
                        meta: {
                            title: 'Acarreos',
                            breadcrumb: {parent:'home', name: 'ACARREOS'},
                            middleware: [auth, context, access]
                        }
                    },
                    {
                        path: 'catalogo',
                        component: require('./components/acarreos/catalogos/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'catalogo',
                                component: require('./components/acarreos/catalogos/Index').default,
                                meta: {
                                    title: 'Catálogos',
                                    breadcrumb: {parent: 'acarreos', name: 'CATÁLOGOS'},
                                    middleware: [auth, context],

                                }
                            },
                            {
                                path: 'camion',
                                component: require('./components/acarreos/catalogos/camion/Layout').default,
                                children: [
                                    {
                                        path: '/',
                                        name: 'camion',
                                        component: require('./components/acarreos/catalogos/camion/Index').default,
                                        meta: {
                                            title: 'Camiones',
                                            breadcrumb: {parent: 'catalogo', name: 'CAMIONES'},
                                            middleware: [auth, context, permission],
                                            permission: ['consultar_camion']
                                        }
                                    },
                                    {
                                        path: ':id',
                                        name: 'camion-show',
                                        props: true,
                                        component: require('./components/acarreos/catalogos/camion/Show').default,
                                        meta: {
                                            title: 'Consultar Camión',
                                            breadcrumb: {parent: 'camion', name: 'CONSULTAR'},
                                            middleware: [auth, context, permission],
                                            permission: 'consultar_camion'
                                        }
                                    },
                                    {
                                        path: ':id/edit',
                                        name: 'camion-edit',
                                        props: true,
                                        component: require('./components/acarreos/catalogos/camion/Edit').default,
                                        meta: {
                                            title: 'Editar Camión',
                                            breadcrumb: {parent: 'camion', name: 'EDITAR'},
                                            middleware: [auth, context, permission],
                                            permission: 'editar_camion'
                                        }
                                    },
                                ]
                            },
                            {
                                path: 'marca',
                                name: 'marca',
                                component: require('./components/acarreos/catalogos/marca/Index').default,
                                meta: {
                                    title: 'Marcas',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'MARCAS'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_marca']
                                }
                            },
                            {
                                path: 'materiales',
                                name: 'materiales',
                                component: require('./components/acarreos/catalogos/material/Index').default,
                                meta: {
                                    title: 'Materiales',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'MATERIALES'
                                    },
                                    middleware: [auth, context],
                                    //permission: ['consultar_material']
                                }
                            },
                            {
                                path: 'empresa-acarreo',
                                name: 'empresa-acarreo',
                                component: require('./components/acarreos/catalogos/empresa/Index').default,
                                meta: {
                                    title: 'Empresas',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'EMPRESAS'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_empresa']
                                }
                            },
                            {
                                path: 'impresora',
                                name: 'impresora',
                                component: require('./components/acarreos/catalogos/impresora/Index').default,
                                meta: {
                                    title: 'Impresoras',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'IMPRESORAS'
                                    },
                                    middleware: [auth, context],
                                    // permission: ['consultar_origen']
                                }
                            },
                            {
                                path: 'operador',
                                name: 'operador',
                                component: require('./components/acarreos/catalogos/operador/Index').default,
                                meta: {
                                    title: 'Operadores',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'OPERADORES'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_operador']
                                }
                            },
                            {
                                path: 'origen',
                                name: 'origen',
                                component: require('./components/acarreos/catalogos/origen/Index').default,
                                meta: {
                                    title: 'Orígenes',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'ORÍGENES'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_origen']
                                }
                            },
                            {
                                path: 'telefono',
                                name: 'telefono',
                                component: require('./components/acarreos/catalogos/telefono/Index').default,
                                meta: {
                                    title: 'Teléfonos',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'TELÉFONOS'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_telefono']
                                }
                            },
                            {
                                path: 'tiro',
                                name: 'tiro',
                                component: require('./components/acarreos/catalogos/tiro/Index').default,
                                meta: {
                                    title: 'Tiros',
                                    breadcrumb: {
                                        parent: 'catalogo',
                                        name: 'TIROS'
                                    },
                                    middleware: [auth, context, permission],
                                    permission: ['consultar_tiro']
                                }
                            },
                        ]
                    },
                ]
            },
            {
                path: 'configuracion',
                components: {
                    default: require('./components/configuracion/Index.vue').default,
                    menu: require('./components/configuracion/partials/Menu.vue').default
                },
                children: [
                    {
                        path: '',
                        name: 'configuracion',
                        component: require('./components/configuracion/Index').default,
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
                ],
            },
            {
                path: 'control_obra',
                components: {
                    default: require('./components/control-obra/partials/Layout.vue').default,
                    menu: require('./components/control-obra/partials/Menu.vue').default
                },
                children: [
                    {
                        path: '',
                        name: 'control_obra',
                        component: require('./components/control-obra/Index').default,
                        meta: {
                            title: 'Control de Obra',
                            breadcrumb: {parent:'home', name: 'CONTROL DE OBRA'},
                            middleware: [auth, context, access]
                        }
                    },
                    {
                        path: 'avance-obra',
                        component: require('./components/control-obra/avance-obra/Layout').default,
                        children: [
                            {
                                path: '/',
                                name: 'avance-obra',
                                component: require('./components/control-obra/avance-obra/Index').default,
                                meta: {
                                    title: 'Avance de Obra',
                                    breadcrumb: {parent: 'control_obra', name: 'AVANCE DE OBRA'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_avance_obra'
                                }
                            },
                            {
                                path: 'create',
                                name: 'avance-obra-create',
                                component: require('./components/control-obra/avance-obra/Create').default,
                                meta: {
                                    title: 'Registrar Avance de Obra',
                                    breadcrumb: { parent: 'avance-obra', name: 'REGISTRAR'},
                                    middleware: [auth, context, permission],
                                    permission: ['registrar_avance_obra'],
                                }
                            },
                            {
                                path: ':id',
                                name: 'avance-obra-show',
                                component: require('./components/control-obra/avance-obra/Show').default,
                                props: true,
                                meta: {
                                    title: 'Consultar Avance de Obra',
                                    breadcrumb: { parent: 'avance-obra', name: 'VER'},
                                    middleware: [auth, context, permission],
                                    permission: 'consultar_avance_obra'
                                }
                            },
                            {
                                path: ':id/aprobar',
                                name: 'avance-obra-aprobar',
                                component: require('./components/control-obra/avance-obra/Aprobar').default,
                                props: true,
                                meta: {
                                    title: 'Aprobar Avance de Obra',
                                    breadcrumb: { parent: 'avance-obra', name: 'APROBAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'aprobar_avance_obra'
                                }
                            },
                            {
                                path: ':id/revertir',
                                name: 'avance-obra-revertir',
                                component: require('./components/control-obra/avance-obra/Revertir').default,
                                props: true,
                                meta: {
                                    title: 'Revertir aprobación Avance de Obra',
                                    breadcrumb: { parent: 'avance-obra', name: 'REVERTIR'},
                                    middleware: [auth, context, permission],
                                    permission: 'revertir_avance_obra'
                                }
                            },
                            {
                                path: ':id/delete',
                                name: 'avance-obra-delete',
                                component: require('./components/control-obra/avance-obra/Delete').default,
                                props: true,
                                meta: {
                                    title: 'Eliminar Avance de Obra',
                                    breadcrumb: { parent: 'avance-obra', name: 'ELIMINAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'eliminar_avance_obra'
                                }
                            },
                            {
                                path: ':id/editar',
                                name: 'avance-obra-edit',
                                component: require('./components/control-obra/avance-obra/Edit').default,
                                props: true,
                                meta: {
                                    title: 'Editar Avance de Obra',
                                    breadcrumb: { parent: 'avance-obra', name: 'EDITAR'},
                                    middleware: [auth, context, permission],
                                    permission: 'editar_avance_obra'
                                }
                            },
                        ]
                    }
                ]
            },
        ],
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
        path:'/activo-fijo',
        components: {
            default: require('./components/activo-fijo/partials/Layout.vue').default,
            menu: require('./components/activo-fijo/partials/Menu.vue').default,
        },
        children: [
            {
                path:'',
                name: 'activo-fijo',
                component: require('./components/activo-fijo/Index').default,
                meta: {
                    title: 'ACTIVO FIJO',
                    middleware: [auth],
                    breadcrumb: {name: 'ACTIVO FIJO'}
                }
            },
            {
                path:"resguardo",
                component: require('./components/activo-fijo/resguardos/Layout.vue').default,
                children: [
                    {
                        path: '/',
                        name: 'resguardos-index',
                        component: require('./components/activo-fijo/resguardos/Index').default,
                        meta: {
                            title: 'Resguardos',
                            breadcrumb: {name: 'RESGUARDOS', parent: 'activo-fijo'},
                            middleware: [auth, permission],
                            permission: ['consulta_resguardo_activo_fijo'],
                            general: true
                        }
                    }
                ]
            },
            {
                path:"activo",
                component: require('./components/activo-fijo/activo/Layout.vue').default,
                children: [
                    {
                        path: '/',
                        name: 'activo',
                        component: require('./components/activo-fijo/activo/Index.vue').default,
                        meta: {
                            title: 'Activos',
                            breadcrumb: {name: 'ACTIVOS', parent: 'activo-fijo'},
                            middleware: [auth,permission],
                            permission: ['consultar_impresion_etiqueta'],
                            general: true
                        }
                    },
                    {
                        path: 'impresion-etiqueta',
                        name: 'impresion-etiqueta',
                        component: require('./components/activo-fijo/activo/impresion-etiqueta/Index').default,
                        meta: {
                            title: 'Impresión de Etiqueta',
                            breadcrumb: {name: 'IMPRESIÓN DE ETIQUETA', parent: 'activo'},
                            middleware: [auth, permission],
                            permission: 'consultar_impresion_etiqueta',
                            general: true
                        }
                    },
                ]
            },
        ],
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
                    middleware: [auth],
                    permission: ['editar_poliza','configurar_visibilidad_empresa_ctpq','configurar_editabilidad_empresa_ctpq','consultar_log_edicion_poliza'],
                    general: true
                }
            },
            {
                path:"seleccionar-empresa",
                name:"seleccionar-empresa",
                component: require('./components/contabilidad-general/poliza/SeleccionarEmpresa.vue').default,
                meta: {
                    title: 'Editar Pólizas',
                    breadcrumb: {parent: 'contabilidad-general', name: 'SELECCIONAR EMPRESA'},
                    middleware: [auth, permission],
                    permission: ['editar_poliza','consultar_poliza'],
                    general: true
                }
            },
            {
                path: ':id_empresa/polizas',
                props: true,
                component: require('./components/contabilidad-general/poliza/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"poliza-contpaq",
                        props: true,
                        component: require('./components/contabilidad-general/poliza/Index.vue').default,
                        meta: {
                            title: 'Listado de pólizas para edición',
                            breadcrumb: {parent: 'seleccionar-empresa', name: 'PÓLIZAS'},
                            middleware: [auth, permission],
                            permission: ['editar_poliza','consultar_poliza'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'poliza-contpaq-show',
                        props: true,
                        component: require('./components/contabilidad-general/poliza/Show').default,
                        meta: {
                            title: 'Consultar Póliza',
                            breadcrumb: {parent: 'poliza-contpaq', name: 'CONSULTAR'},
                            middleware: [auth, permission],
                            permission: 'consultar_poliza',
                            general: true
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'poliza-contpaq-edit',
                        props: true,
                        component: require('./components/contabilidad-general/poliza/Edit').default,
                        meta: {
                            title: 'Editar Póliza',
                            breadcrumb: {parent: 'poliza-contpaq', name: 'EDITAR'},
                            middleware: [auth, permission],
                            permission: 'editar_poliza',
                            general: true
                        }
                    },
                    {
                        path: ':id/asociar-cfdi',
                        name: 'poliza-contpaq-asociar-cfdi',
                        props: true,
                        component: require('./components/contabilidad-general/poliza/asociacion/AsociaCFDI.vue').default,
                        meta: {
                            title: 'Asociar CFDI a Póliza',
                            breadcrumb: {parent: 'poliza-contpaq', name: 'ASOCIAR CFDI'},
                            middleware: [auth, permission],
                            permission: ['editar_poliza','asociar-cfdi-a-poliza-contpaq'],
                            general: true
                        }
                    },
                ]
            },
            {
                path:"seleccionar-empresa-asociacion",
                name:"seleccionar-empresa-asociacion",
                component: require('./components/contabilidad-general/poliza/asociacion/SeleccionarEmpresaAsoacion.vue').default,
                meta: {
                    title: 'Asociación de Pólizas con CFDI',
                    breadcrumb: {parent: 'contabilidad-general', name: 'SELECCIONAR EMPRESA'},
                    middleware: [auth, permission],
                    permission: ['asociar-cfdi-a-poliza-contpaq'],
                    general: true
                }
            },
            {
                path: ':id_empresa/polizas-asociacion',
                component: require('./components/contabilidad-general/poliza/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"poliza-contpaq-asociacion",
                        props: true,
                        component: require('./components/contabilidad-general/poliza/asociacion/Index.vue').default,
                        meta: {
                            title: 'Listado de polizas para asociación con CFDI',
                            breadcrumb: {parent: 'seleccionar-empresa-asociacion', name: 'ASOCIACIÓN DE PÓLIZAS'},
                            middleware: [auth, permission],
                            permission: ['asociar-cfdi-a-poliza-contpaq'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'poliza-contpaq-asociacion-show',
                        props: true,
                        component: require('./components/contabilidad-general/poliza/asociacion/Show').default,
                        meta: {
                            title: 'Consultar Póliza',
                            breadcrumb: {parent: 'poliza-contpaq-asociacion', name: 'CONSULTAR'},
                            middleware: [auth, permission],
                            permission: 'asociar-cfdi-a-poliza-contpaq',
                            general: true
                        }
                    },
                    {
                        path: ':id/asociar-cfdi',
                        name: 'poliza-contpaq-asociacion-asociar-cfdi',
                        props: true,
                        component: require('./components/contabilidad-general/poliza/asociacion/AsociaCFDI.vue').default,
                        meta: {
                            title: 'Asociar CFDI a Póliza',
                            breadcrumb: {parent: 'poliza-contpaq-asociacion', name: 'ASOCIAR CFDI'},
                            middleware: [auth, permission],
                            permission: 'asociar-cfdi-a-poliza-contpaq',
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'poliza-cfdi',
                component: require('./components/contabilidad-general/poliza-cfdi/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"poliza-cfdi",
                        component: require('./components/contabilidad-general/poliza-cfdi/Index.vue').default,
                        meta: {
                            title: 'Pólizas-CFDI',
                            breadcrumb: {parent: 'contabilidad-general', name: 'PÓLIZAS CFDI'},
                            middleware: [auth, permission],
                            permission: ['consultar_poliza'],
                            general: true
                        }
                    },
                    {
                        path: ':id_empresa/:id',
                        name: 'poliza-contpaq-cfdi-show',
                        props: true,
                        component: require('./components/contabilidad-general/poliza-cfdi/Show').default,
                        meta: {
                            title: 'Consultar Póliza',
                            breadcrumb: {parent: 'poliza-contpaq', name: 'CONSULTAR'},
                            middleware: [auth, permission],
                            permission: 'consultar_poliza',
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'diferencias',
                component: require('./components/contabilidad-general/diferencias-polizas/diferencia-poliza/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"diferencia-poliza",
                        component: require('./components/contabilidad-general/diferencias-polizas/diferencia-poliza/Index.vue').default,
                        meta: {
                            title: 'Diferencias en Pólizas',
                            breadcrumb: {parent: 'poliza-contpaq', name: 'DIFERENCIAS'},
                            middleware: [auth, permission],
                            permission: ['consultar_poliza'],
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'diferencias/informe',
                component: require('./components/contabilidad-general/diferencias-polizas/diferencia-poliza/InformeDiferencias.vue').default,
                children:[
                    {
                        path:"/",
                        name:"informe-diferencia-poliza",
                        component: require('./components/contabilidad-general/diferencias-polizas/diferencia-poliza/InformeDiferencias.vue').default,
                        meta: {
                            title: 'Informe de Diferencias en Pólizas',
                            breadcrumb: {parent: 'contabilidad-general', name: 'INFORME DE DIFERENCIAS'},
                            middleware: [auth, permission],
                            permission: ['consultar_poliza'],
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'diferencias/detectar',
                component: require('./components/contabilidad-general/diferencias-polizas/diferencia-poliza/DetectarDiferencia.vue').default,
                children:[
                    {
                        path:"/",
                        name:"detectar-diferencias-polizas",
                        component: require('./components/contabilidad-general/diferencias-polizas/diferencia-poliza/DetectarDiferencia.vue').default,
                        meta: {
                            title: 'Detectar Diferencias en Pólizas',
                            breadcrumb: {parent: 'diferencia-poliza', name: 'DETECTAR'},
                            middleware: [auth, permission],
                            permission: ['consultar_poliza'],
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
                            middleware: [auth],
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
                            middleware: [auth],
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
                path: 'cuentas-saldo-negativo',
                component: require('./components/contabilidad-general/cuentas-saldos-negativos/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"cuentas-saldo-negativo",
                        component: require('./components/contabilidad-general/cuentas-saldos-negativos/Index.vue').default,
                        meta: {
                            title: 'Cuentas contables con saldos negativos',
                            breadcrumb: {parent: 'contabilidad-general', name: 'CUENTAS CON SALDOS NEGATIVOS'},
                            middleware: [auth, permission],
                            permission: ['consultar_poliza'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'cuenta-saldo-negativo-detalle',
                        props: true,
                        component: require('./components/contabilidad-general/cuentas-saldos-negativos/Informe').default,
                        meta: {
                            title: 'Detalle de Saldo',
                            breadcrumb: {parent: 'cuentas-saldo-negativo', name: 'DETALLE'},
                            middleware: [auth, permission],
                            permission: 'consultar_poliza',
                            general: true
                        },
                        children:[
                            {
                                path: 'movimientos/:aniomes',
                                name: 'cuenta-saldo-negativo-detalle-movimientos',
                                props: true,
                                component: require('./components/contabilidad-general/cuentas-saldos-negativos/Movimientos').default,
                                meta: {
                                    title: 'Detalle de Movimientos',
                                    breadcrumb: {parent: 'cuenta-saldo-negativo-detalle', name: 'MOVIMIENTOS'},
                                    middleware: [auth, permission],
                                    permission: 'consultar_poliza',
                                    general: true
                                },
                            }
                        ]
                    },
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
            },
            {
                path: 'asociacion-cuenta-proveedor',
                component: require('./components/contabilidad-general/asociacion-cuenta-proveedor/SeleccionarEmpresa.vue').default,
                children:[
                    {
                        path:"/",
                        name:"asociacion-cuenta-proveedor",
                        component: require('./components/contabilidad-general/asociacion-cuenta-proveedor/SeleccionarEmpresa.vue').default,
                        meta: {
                            title: 'Asociar Cuentas Con Proveedores',
                            breadcrumb: {parent: 'contabilidad-general', name: 'SELECCIONAR EMPRESA'},
                            middleware: [auth,permission],
                            permission:['asociar_cuentas_contpaq_con_proveedor'],
                            general: true
                        }
                    }
                ]
            },
            {
                path: ':id_empresa/cuentas',
                props:true,
                component: require('./components/contabilidad-general/asociacion-cuenta-proveedor/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"cuentas-proveedor",
                        component: require('./components/contabilidad-general/asociacion-cuenta-proveedor/Index.vue').default,
                        meta: {
                            title: 'Asociación Cuenta Proveedor',
                            breadcrumb: {parent: 'asociacion-cuenta-proveedor', name: 'ASOCIACIÓN CTA PROVEEDOR'},
                            middleware: [auth],
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'contabilidad-electronica',
                component: require('./components/contabilidad-general/contabilidad-electronica/Index.vue').default,
                children:[
                    {
                        path:"/",
                        name:"contabilidad-electronica",
                        component: require('./components/contabilidad-general/contabilidad-electronica/Index.vue').default,
                        meta: {
                            title: 'Lectura de Balanza XML',
                            breadcrumb: {parent: 'contabilidad-general', name: 'LECTURA DE BALANZA XML'},
                            middleware: [auth, permission],
                            permission: 'consultar_contabilidad_electronica',
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'layouts-pasivos',
                component: require('./components/contabilidad-general/layout-pasivos/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"layouts-pasivos",
                        component: require('./components/contabilidad-general/layout-pasivos/Index.vue').default,
                        meta: {
                            title: 'Lista de layouts para carga de pasivos a IFS',
                            breadcrumb: {parent: 'contabilidad-general', name: 'LAYOUTS PASIVOS'},
                            middleware: [auth, permission],
                            permission: 'consultar_layouts_pasivos',
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'layouts-pasivos-show',
                        props: true,
                        component: require('./components/contabilidad-general/layout-pasivos/Show.vue').default,
                        meta: {
                            title: 'Consultar Pasivos por Layout',
                            breadcrumb: {parent: 'layouts-pasivos', name: 'CONSULTAR'},
                            middleware: [auth, permission],
                            permission: 'consultar_layouts_pasivos',
                            general: true
                        }
                    },
                    {
                        path: ':id/validar',
                        name: 'layouts-pasivos-validar',
                        props: true,
                        component: require('./components/contabilidad-general/layout-pasivos/Validar.vue').default,
                        meta: {
                            title: 'Validar Pasivos',
                            breadcrumb: {parent: 'layouts-pasivos', name: 'VALIDAR'},
                            middleware: [auth, permission],
                            permission: 'consultar_layouts_pasivos',
                            general: true
                        }
                    },
                    {
                        path: 'cargar/layout',
                        component: require('./components/contabilidad-general/layout-pasivos/CargarLayout.vue').default,
                        children: [
                            {
                                path: "/",
                                name: "cargar-pasivos",
                                component: require('./components/contabilidad-general/layout-pasivos/CargarLayout.vue').default,
                                meta: {
                                    title: 'Carga de Layout',
                                    breadcrumb: {parent: 'layouts-pasivos', name: 'CARGA DE PASIVOS'},
                                    //middleware: [auth,permission],
                                    permission: 'cargar_layouts_pasivos',
                                    general: true
                                }
                            },
                        ]
                    },
                ]
            },

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
        path: '/control-interno/empresas-factureras',
        components: {
            default: require('./components/control-interno/partials/Layout.vue').default,
            menu: require('./components/control-interno/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'busqueda-empresas-factureras',
                component: require('./components/control-interno/empresas-factureras/Busqueda').default,
                meta: {
                    title: 'Busqueda de Empresas Factureras',
                    breadcrumb: {parent: 'control-interno', name: 'EMPRESAS FACTURERAS'},
                    middleware: [auth],
                    permission: 'consultar_incidencias',
                    general: true,

                }
            },
        ]
    },
    {
        path: '/control-recurso',
        components:  {
            default: require('./components/control-recursos/partials/Layout.vue').default,
            menu: require('./components/control-recursos/partials/Menu.vue').default
        },
        children:[
            {
                path:'',
                name: 'control-recurso',
                meta: {
                    title: 'CONTROL RECURSOS',
                    middleware: [auth],
                }
            },
            {
                path: 'factura-recurso',
                component: require('./components/control-recursos/factura/Layout.vue').default,
                children: [
                    {
                        path: '/',
                        name: 'factura-recurso',
                        component: require('./components/control-recursos/factura/Index').default,
                        meta: {
                            title: 'Facturas',
                            breadcrumb: {parent: 'control-recurso', name: 'FACTURAS'},
                            middleware: [auth, permission],
                            permission: ['consultar_factura_recursos','registrar_factura_recursos'],
                            general: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'factura-recurso-create',
                        component: require('./components/control-recursos/factura/Create.vue').default,
                        meta: {
                            title: 'Registrar Factura',
                            breadcrumb: {name: 'REGISTRAR', parent: 'factura-recurso'},
                            middleware: [auth, permission],
                            permission: ['registrar_factura_recursos'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'factura-recurso-show',
                        component: require('./components/control-recursos/factura/Show').default,
                        props: true,
                        meta: {
                            title: 'Consultar Factura',
                            breadcrumb: { parent: 'factura-recurso', name: 'VER'},
                            middleware: [auth, permission],
                            permission: 'consultar_factura_recursos',
                            general: true
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'factura-recurso-edit',
                        props: true,
                        component: require('./components/control-recursos/factura/Edit').default,
                        meta: {
                            title: 'Editar Factura',
                            breadcrumb: {parent: 'factura-recurso', name: 'EDITAR'},
                            middleware: [auth, permission],
                            permission: 'editar_factura_recursos',
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'layout-bancario',
                component: require('./components/control-recursos/layout-bancario/Layout.vue').default,
                children: [
                    {
                        path: '/',
                        name: 'layout-bancario',
                        component: require('./components/control-recursos/layout-bancario/DescargaLayout').default,
                        meta: {
                            title: '',
                            breadcrumb: {parent: 'control-recurso', name: 'LAYOUT BANCARIO'},
                            middleware: [auth, permission],
                            permission: ['consultar_factura_recursos'],
                            general: true,
                        }
                    }
                ]
            },
        ]
    },
    {
        path: '/fiscal',
        components:  {
            default: require('./components/fiscal/partials/Layout.vue').default,
            menu: require('./components/fiscal/partials/Menu.vue').default
        },
        children:[
            {
                path:'',
                name: 'fiscal',
                meta: {
                    title: 'FISCAL',
                    middleware: [auth],
                }
            },
            {
                path: 'no-localizados',
                component: require('./components/fiscal/no-localizados/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"no-localizados",
                        component: require('./components/fiscal/no-localizados/Index.vue').default,
                        meta: {
                            title: 'Lista de contribuyentes no localizados por el SAT',
                            breadcrumb: {parent: 'fiscal', name: 'NO LOCALIZADOS SAT'},
                            middleware: [auth,permission],
                            permission: ['consultar_proveedores_no_localizados'],
                            general: true
                        }
                    },
                    {
                        path: 'informe',
                        name: 'informe-no-localizados',
                        component: require('./components/fiscal/no-localizados/InformeNoLocalizados.vue').default,
                        meta: {
                            title: 'Informe de Proveedores No Localizados',
                            breadcrumb: {name: 'INFORME', parent: 'fiscal'},
                            middleware: [auth, permission],
                            permission: ['consultar_informe_listado_efos_vs_cfdi_recibidos'],
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'informe-general-sat',
                name: 'informe-general-sat',
                component: require('./components/fiscal/informes/InformeSAT.vue').default,
                meta: {
                    title: 'Informe CFDI vs. Pasivos',
                    breadcrumb: {name: 'INFORME CFDI VS. PASIVOS', parent: 'fiscal'},
                    middleware: [auth, permission],
                    permission: ['consultar_informe_general_sat'],
                    general: true
                }
            },
            {
                path: 'informes/costos-cfdi-vs-costos-balanza',
                name: 'informe-general-cfdi-costos-balanza',
                component: require('./components/fiscal/informes/costos-cfdi-vs-costos-balanza/Informe.vue').default,
                meta: {
                    title: 'Informe Costos CFDI vs. Costos Balanza',
                    breadcrumb: {name: 'INFORME COSTOS CFDI VS. COSTOS BALANZA', parent: 'fiscal'},
                    middleware: [auth, permission],
                    permission: ['consultar_informe_general_cfdi_costos_balanza'],
                    general: true
                }
            },
            {
                path: 'efos-empresa',
                component: require('./components/fiscal/efos/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"efos-empresa",
                        component: require('./components/fiscal/efos/Index.vue').default,
                        meta: {
                            title: 'EFOS',
                            breadcrumb: {parent: 'fiscal', name: 'EFOS'},
                            middleware: [auth, permission],
                            permission: ['consultar_efos_empresa'],
                            general: true
                        }
                    },
                    {
                        path: 'informe',
                        name: 'informe-efos-vs-cfd',
                        component: require('./components/fiscal/efos/InformeEFOSCFD').default,
                        meta: {
                            title: 'Informe Listado EFOS vs CFDI Recibidos',
                            breadcrumb: {name: 'INFORME', parent: 'fiscal'},
                            middleware: [auth, permission],
                            permission: ['consultar_informe_listado_efos_vs_cfdi_recibidos'],
                            general: true
                        }
                    },
                    {
                        path: 'informe-desglosado',
                        name: 'informe-efos-vs-cfd-5a',
                        component: require('./components/fiscal/efos/InformeEFOSCFD5A').default,
                        meta: {
                            title: 'Informe Listado EFOS vs CFDI Recibidos (Desglosado)',
                            breadcrumb: {name: 'INFORME DESGLOSADO', parent: 'fiscal'},
                            middleware: [auth, permission],
                            permission: ['consultar_informe_listado_efos_vs_cfdi_recibidos'],
                            general: true
                        }
                    }

                ]
            },
            {
                path: 'cfd',
                component: require('./components/fiscal/cfd/cfd-sat/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"cfd-sat",
                        component: require('./components/fiscal/cfd/cfd-sat/Index.vue').default,
                        meta: {
                            title: 'CFDI SAT',
                            breadcrumb: {parent: 'fiscal', name: 'CFDI SAT'},
                            middleware: [auth, permission],
                            permission: ['consultar_poliza','consultar_autocorreccion_cfd_efo', 'consultar_informe_cfd_x_empresa_x_mes','consultar_no_deducido_cfd_efo'],
                            general: true
                        }
                    },
                    {
                        path: 'autocorreccion-cfd-efos',
                        component: require('./components/fiscal/cfd/autocorreccion-cfd-efo/Layout.vue').default,
                        children: [
                            {
                                path: '/',
                                name: 'autocorreccion-cfd-efos',
                                component: require('./components/fiscal/cfd/autocorreccion-cfd-efo/Index.vue').default,
                                meta: {
                                    title: 'Autocorrección de CFDI EFOS',
                                    breadcrumb: {parent: 'cfd-sat', name: 'AUTOCORRECCIÓN DE CFDI'},
                                    middleware: [auth, permission],
                                    permission: 'consultar_autocorreccion_cfd_efo',
                                    general: true,

                                }
                            },
                            {
                                path: 'create',
                                name: 'autocorreccion-cfd-efos-create',
                                component: require('./components/fiscal/cfd/autocorreccion-cfd-efo/Create.vue').default,
                                meta: {
                                    title: 'Registrar Autocorrección de CFDI EFOS',
                                    breadcrumb: {name: 'REGISTRAR', parent: 'autocorreccion-cfd-efos'},
                                    middleware: [auth, permission],
                                    permission: ['registrar_autocorreccion_cfd_efo'],
                                    general: true
                                }
                            }
                        ]
                    },
                    {
                        path: 'no-deducidos-cfd-efos',
                        component: require('./components/fiscal/cfd/no-deducidos-cfd-efo/Layout.vue').default,
                        children: [
                            {
                                path: '/',
                                name: 'no-deducidos-cfd-efos',
                                component: require('./components/fiscal/cfd/no-deducidos-cfd-efo/Index.vue').default,
                                meta: {
                                    title: 'CFDI No Deducidos de EFOS Definitivos',
                                    breadcrumb: {parent: 'cfd-sat', name: 'CFDI NO DEDUCIDOS'},
                                    middleware: [auth, permission],
                                    permission: 'consultar_no_deducido_cfd_efo',
                                    general: true,

                                }
                            },
                            {
                                path: 'create',
                                name: 'no-deducidos-cfd-efos-create',
                                component: require('./components/fiscal/cfd/no-deducidos-cfd-efo/Create.vue').default,
                                meta: {
                                    title: 'Registrar CFDI No Deducidos de EFOS Definitivos',
                                    breadcrumb: {name: 'REGISTRAR', parent: 'no-deducidos-cfd-efos'},
                                    middleware: [auth, permission],
                                    permission: ['registrar_no_deducido_cfd_efo'],
                                    general: true,
                                }
                            }
                        ]
                    },
                    {
                        path: 'informe-rep-faltantes',
                        component: require('./components/fiscal/cfd/rep-pendientes/Layout.vue').default,
                        children: [
                            {
                                path: 'por-cfdi',
                                name: 'informe-rep-faltantes',
                                component: require('./components/fiscal/cfd/cfd-sat/InformeREPPendientes').default,
                                meta: {
                                    title: 'Lista de CFDIs con REPs faltantes',
                                    breadcrumb: {name: 'REP FALTANTES', parent: 'fiscal'},
                                    middleware: [auth, permission],
                                    permission: ['consultar_informe_cfd_x_empresa_x_mes'],
                                    general: true
                                }
                            },
                            {
                                path: 'por-proveedor',
                                name: 'informe-rep-faltantes-proveedor',
                                component: require('./components/fiscal/cfd/cfd-sat/InformeREPPendientesProveedor').default,
                                meta: {
                                    title: 'Lista de proveedores que adeudan REP',
                                    breadcrumb: {name: 'POR PROVEEDOR', parent: 'informe-rep-faltantes'},
                                    middleware: [auth, permission],
                                    permission: ['consultar_informe_cfd_x_empresa_x_mes'],
                                    general: true
                                }
                            },
                            {
                                path: 'por-proveedor/:id_proveedor/historico-notificaciones',
                                component: require('./components/fiscal/cfd/rep-pendientes/notificacion/Layout.vue').default,
                                children: [
                                    {
                                        path: '/',
                                        name: 'historico-notificacion-rep',
                                        props: true,
                                        component: require('./components/fiscal/cfd/rep-pendientes/notificacion/Index.vue').default,
                                        meta: {
                                            title: 'Listado de Notificaciones Enviadas',
                                            breadcrumb: {
                                                name: 'LISTA DE NOTIFICACIONES',
                                                parent: 'informe-rep-faltantes-proveedor'
                                            },
                                            middleware: [auth, permission],
                                            permission: ['consultar_informe_cfd_x_empresa_x_mes'],
                                            general: true
                                        }
                                    },
                                    {
                                        path: ':id',
                                        name: 'historico-notificacion-rep-show',
                                        props: true,
                                        component: require('./components/fiscal/cfd/rep-pendientes/notificacion/Show.vue').default,
                                        meta: {
                                            title: 'Detalle de Notificación Enviada',
                                            breadcrumb: {parent: 'historico-notificacion-rep', name: 'NOTIFICACIÓN'},
                                            middleware: [auth, permission],
                                            permission: 'consultar_informe_cfd_x_empresa_x_mes',
                                            general: true
                                        }
                                    },
                                ]
                            },
                            {
                                path: ':id/envio-comunicado',
                                //props:true,
                                props: route => ({
                                    id: route.params.id,
                                }),
                                name: 'envio-comunicado-rep-faltantes',
                                component: require('./components/fiscal/cfd/rep-pendientes/notificacion/Create').default,
                                meta: {
                                    title: 'Enviar Comunicado de REP Faltantes',
                                    breadcrumb: {name: 'Enviar Comunicado', parent: 'informe-rep-faltantes-proveedor'},
                                    middleware: [auth, permission],
                                    permission: ['consultar_informe_cfd_x_empresa_x_mes'],
                                    general: true
                                }
                            },
                        ]
                    },
                    {
                        path: 'polizas-vs-cfdi',
                        component: require('./components/fiscal/cfd/rep-pendientes/Layout.vue').default,
                        children: [
                            {
                                path: 'polizas-egreso-sin-cfdi',
                                name: 'polizas-egreso-sin-cfdi',
                                component: require('./components/fiscal/polizas-vs-cfdi/polizas-egreso/Index').default,
                                meta: {
                                    title: 'Lista de Pólizas de Egreso Sin CFDI',
                                    breadcrumb: {name: 'PÓLIZAS EGRESO SIN CFDI', parent: 'fiscal'},
                                    middleware: [auth, permission],
                                    permission: ['consultar_informe_cfd_x_empresa_x_mes'],
                                    general: true
                                }
                            },
                            {
                                path: ':id_empresa/:id',
                                name: 'show-poliza-egreso-sin-cfdi',
                                props: true,
                                component: require('./components/fiscal/polizas-vs-cfdi/polizas-egreso/Show').default,
                                meta: {
                                    title: 'Póliza',
                                    breadcrumb: {parent: 'polizas-egreso-sin-cfdi', name: 'CONSULTAR PÓLIZA'},
                                    middleware: [auth, permission],
                                    permission: 'consultar_informe_cfd_x_empresa_x_mes',
                                    general: true
                                }
                            },
                        ]
                    },
                    {
                        path: 'informe',
                        name: 'informe-cfd-empresa-tiempo',
                        component: require('./components/fiscal/cfd/cfd-sat/InformeCFDEmpresaMes').default,
                        meta: {
                            title: 'Informe CFDI Cargados x Empresa x Mes',
                            breadcrumb: {name: 'INFORME', parent: 'fiscal'},
                            middleware: [auth, permission],
                            permission: ['consultar_informe_cfd_x_empresa_x_mes'],
                            general: true
                        }
                    },
                    {
                        path: 'informe-completo',
                        name: 'informe-cfdi-completo',
                        component: require('./components/fiscal/cfd/cfd-sat/InformeCFDICompleto').default,
                        meta: {
                            title: 'Informe de CFDI Cargados x Empresa x Mes (Completo)',
                            breadcrumb: {name: 'INFORME', parent: 'fiscal'},
                            middleware: [auth, permission],
                            permission: ['consultar_informe_cfdi_x_empresa_desglosado'],
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'fechas-inhabiles-sat',
                component: require('./components/fiscal/fechas-inhabiles-sat/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"fechas-inhabiles-sat",
                        component: require('./components/fiscal/fechas-inhabiles-sat/Index.vue').default,
                        meta: {
                            title: 'Fechas Inhábiles SAT',
                            breadcrumb: {parent: 'fiscal', name: 'FECHAS INHÁBILES'},
                            middleware: [auth, permission],
                            permission: ['consultar_fechas_inhabiles_sat'],
                            general: true
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/empresas-boletinadas',
        components:  {
            default: require('./components/empresas-boletinadas/partials/Layout.vue').default,
            menu: require('./components/empresas-boletinadas/partials/Menu.vue').default
        },
        children:[
            {
                path: '',
                component: require('./components/empresas-boletinadas/partials/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"empresas-boletinadas",
                        component: require('./components/empresas-boletinadas/Index.vue').default,
                        meta: {
                            title: 'Empresas Boletinadas',
                            breadcrumb: { name: 'EMPRESAS BOLETINADAS'},
                            middleware: [auth],
                            general: true
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/padron-proveedores',
        components:  {
            default: require('./components/padron-proveedores/partials/Layout.vue').default,
            menu: require('./components/padron-proveedores/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'padron-proveedores',
                component: require('./components/padron-proveedores/Index').default,
                meta: {
                    title: 'Padrón de Proveedores',
                    middleware: [auth, permission],
                    permission: ['consultar_expediente_proveedor'],
                    general: true
                }
            },
            {
                path: 'gestion-empresas-boletinadas',
                component: require('./components/padron-proveedores/gestion-empresas-boletinadas/partials/Layout.vue').default,
                children:[
                    {
                        path:"/",
                        name:"empresas-boletinadas-index",
                        component: require('./components/padron-proveedores/gestion-empresas-boletinadas/Index.vue').default,
                        meta: {
                            title: 'Empresas Boletinadas',
                            breadcrumb: {parent: 'padron-proveedores', name: 'EMPRESAS BOLETINADAS'},
                            middleware: [auth,permission],
                            permission: ['consultar_empresa_boletinada'],
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'gestion-proveedores',
                component: require('./components/padron-proveedores/gestion-proveedores/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'proveedores-index',
                        component: require('./components/padron-proveedores/gestion-proveedores/Index.vue').default,
                        meta: {
                            title: 'Listado de Proveedores',
                            breadcrumb: {parent: 'padron-proveedores', name: 'PROVEEDORES'},
                            middleware: [auth, permission],
                            permission: 'consultar_expediente_proveedor',
                            general: true,
                        }
                    },
                    {
                        path: 'iniciar-expediente',
                        name: 'proveedores-iniciar-expediente',
                        component: require('./components/padron-proveedores/gestion-proveedores/Create').default,
                        meta: {
                            title: 'Iniciar Expediente de Proveedor',
                            breadcrumb: {name: 'INICIAR EXPEDIENTE', parent: 'proveedores-index'},
                            middleware: [auth, permission],
                            permission: ['iniciar_expediente_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'entrar-a-expediente',
                        component: require('./components/padron-proveedores/gestion-proveedores/Edit').default,
                        props: true,
                        meta: {
                            title: 'Expediente de Proveedor',
                            breadcrumb: {name: 'EXPEDIENTE', parent: 'proveedores-index'},
                            middleware: [auth, permission],
                            permission: ['consultar_expediente_proveedor'],
                            general: true
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/finanzas-general',
        components:  {
            default: require('./components/finanzas-general/partials/Layout.vue').default,
            menu: require('./components/finanzas-general/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'finanzas-general',
                component: require('./components/finanzas-general/Index').default,
                meta: {
                    title: 'FINANZAS',
                    middleware: [auth],
                }
            },
            {
                path: 'indicadores',
                component: require('./components/finanzas-general/indicadores/Layout').default,
                children: [
                    {
                        path: 'solicitudes-pago',
                        name: 'detalle-indicador-solicitudes-pago',
                        component: require('./components/finanzas-general/indicadores/solicitud-pago/Index').default,
                        meta: {
                            title: 'Solicitudes de Pago Pendientes de Aplicar',
                            breadcrumb: {parent: 'finanzas-general', name: 'SOLICITUDES DE PAGO PENDIENTES'},
                            middleware: [auth],
                        }
                    },
                ]
            },
            {
                path: 'solicitud-pago',
                component: require('./components/finanzas-general/solicitudes-pago/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'solicitud-pago',
                        component: require('./components/finanzas-general/solicitudes-pago/Index').default,
                        meta: {
                            title: 'Solicitudes de Pago',
                            breadcrumb: {parent: 'finanzas-general', name: 'SOLICITUDES DE PAGO'},
                            middleware: [auth, permission],
                            permission: 'consultar_solicitudes_pago_global',
                            general: true,
                        }
                    },
                    {
                        path: 'transacciones',
                        name: 'enlistar-solicitudes-pago',
                        component: require('./components/finanzas-general/solicitudes-pago/autorizar/Index').default,
                        meta: {
                            title: 'Lista de Solicitudes de Pago a Autorizar',
                            breadcrumb: {name: 'AUTORIZAR SOLICITUD', parent: 'solicitud-pago'},
                            middleware: [auth, permission],
                            permission: ['consultar_solicitudes_pago_global'],
                            general: true
                        }
                    },
                    {
                        path: 'autorizar',
                        name: 'autorizar-solicitudes-pago',
                        component: require('./components/finanzas-general/solicitudes-pago/autorizar/IndexAutorizacion').default,
                        meta: {
                            title: 'Autorizar Solicitudes de Pago',
                            breadcrumb: {name: 'AUTORIZAR SOLICITUDES', parent: 'solicitud-pago'},
                            middleware: [auth, permission],
                            permission: ['autorizar_rechazar_solicitud_pago'],
                            general: true
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/remesas',
        components:  {
            default: require('./components/remesas/partials/Layout.vue').default,
            menu: require('./components/remesas/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'remesas',
                meta: {
                    title: 'SISTEMA DE REMESAS',
                    middleware: [auth, permission],
                    permission: ['consultar_limite_remesa'],
                    general: true
                }
            },
            {
                path: 'configuracion',
                component: require('./components/remesas/configuracion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'configuracion-remesa',
                        component: require('./components/remesas/configuracion/Index').default,
                        meta: {
                            title: 'Configuración',
                            breadcrumb: {parent: 'remesas', name: 'CONFIGURACIÓN'},
                            middleware: [auth, permission],
                            permission: 'consultar_limite_remesa',
                            general: true,
                        }
                    },
                ]
            },
            {
                path: 'proveedor-no-localizado',
                component: require('./components/remesas/proveedor-no-localizado/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'proveedor-no-localizado',
                        component: require('./components/remesas/proveedor-no-localizado/Index').default,
                        meta: {
                            title: 'Proveedor No Localizado',
                            breadcrumb: {parent: 'remesas', name: 'PROVEEDOR NO LOCALIZADO'},
                            middleware: [auth, permission],
                            permission: 'consultar_limite_remesa',
                            general: true,
                        }
                    },
                    {
                        path: 'transacciones',
                        name: 'enlistar-transacciones-no-localizados',
                        component: require('./components/remesas/proveedor-no-localizado/autorizar-pago-factura/Index').default,
                        meta: {
                            title: 'Lista de Transacciones de  Proveedores No Localizados en Remesa',
                            breadcrumb: {name: 'AUTORIZAR TRANSACCIÓN', parent: 'proveedor-no-localizado'},
                            middleware: [auth, permission],
                            permission: ['consultar_transaccion_proveedor_no_localizado'],
                            general: true
                        }
                    },
                    {
                        path: 'autorizar',
                        name: 'autorizar-transacciones-no-localizados',
                        component: require('./components/remesas/proveedor-no-localizado/autorizar-pago-factura/IndexAutorizacion').default,
                        meta: {
                            title: 'Autorizar Transacciones de  Proveedores No Localizados en Remesa',
                            breadcrumb: {name: 'AUTORIZAR TRANSACCIONES', parent: 'proveedor-no-localizado'},
                            middleware: [auth, permission],
                            permission: ['autorizar_rechazar_transaccion_proveedor_no_localizado'],
                            general: true
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/portal-proveedor',
        components:  {
            default: require('./components/portal-proveedor/partials/Layout.vue').default,
            menu: require('./components/portal-proveedor/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'proveedor',
                component: require('./components/portal-proveedor/Index').default,
                meta: {
                    title: 'Portal de Proveedores',
                    middleware: [auth, permission],
                    breadcrumb: {name: 'PORTAL DE PROVEEDORES'},
                    permission: ['consultar_cotizacion_proveedor'],
                    general: true
                }
            },
            {
                path: 'cotizacion',
                component: require('./components/portal-proveedor/cotizacion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'cotizacion-proveedor',
                        component: require('./components/portal-proveedor/cotizacion/Index').default,
                        meta: {
                            title: 'Lista de Cotizaciones',
                            breadcrumb: {parent: 'proveedor', name: 'COTIZACIONES'},
                            middleware: [auth, permission],
                            permission: 'consultar_cotizacion_proveedor',
                            general: true
                        }
                    },
                    {
                        path: 'enviar',
                        name: 'cotizacion-proveedor-para-envio',
                        component: require('./components/portal-proveedor/cotizacion/IndexEnvio').default,
                        meta: {
                            title: 'Lista de Cotizaciones Disponibles a Enviar',
                            breadcrumb: {parent: 'proveedor', name: 'COTIZACIONES A ENVIAR'},
                            middleware: [auth, permission],
                            permission: 'registrar_cotizacion_proveedor',
                            general: true
                        }
                    },
                    {
                        path: 'create',
                        name: 'cotizacion-proveedor-seleccionar-solicitud',
                        component: require('./components/portal-proveedor/cotizacion/SeleccionarSolicitud').default,
                        meta: {
                            title: 'Seleccionar Invitación',
                                breadcrumb: { parent: 'cotizacion-proveedor', name: 'SELECCIONAR INVITACIÓN'},
                            middleware: [auth, permission],
                            permission: ['registrar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id_invitacion/invitacion',
                        name: 'cotizacion-proveedor-invitacion',
                        component: require('./components/portal-proveedor/cotizacion/PrevisualizarInvitacion').default,
                        props: true,
                        meta: {
                            title: 'Detalle de Invitación',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'INVITACIÓN'},
                            middleware: [auth, permission],
                            permission: ['consultar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id/create',
                        name: 'cotizacion-proveedor-create',
                        component: require('./components/portal-proveedor/cotizacion/Create').default,
                        props: true,
                        meta: {
                            title: 'Registrar Cotización',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'REGISTRAR'},
                            middleware: [auth, permission],
                            permission: ['registrar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id_invitacion_antecedente/contraoferta/:id_invitacion/create',
                        name: 'contraoferta-cotizacion-proveedor-create',
                        component: require('./components/portal-proveedor/cotizacion/CreateContraoferta').default,
                        props: true,
                        meta: {
                            title: 'Registrar Contraoferta',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'REGISTRAR'},
                            middleware: [auth, permission],
                            permission: ['registrar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'cotizacion-proveedor-show',
                        component: require('./components/portal-proveedor/cotizacion/Show').default,
                        props: true,
                        meta: {
                            title: 'Consultar Cotización',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'VER'},
                            middleware: [auth, permission],
                            permission: 'consultar_cotizacion_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/editar',
                        name: 'cotizacion-proveedor-edit',
                        props: true,
                        component: require('./components/portal-proveedor/cotizacion/Edit').default,
                        meta: {
                            title: 'Editar Cotización',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'EDITAR'},
                            middleware: [auth, permission],
                            permission: ['editar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id_invitacion/enviar',
                        name: 'cotizacion-proveedor-send',
                        props: true,
                        component: require('./components/portal-proveedor/cotizacion/Enviar').default,
                        meta: {
                            title: 'Enviar Cotización',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'ENVIAR'},
                            middleware: [auth, permission],
                            permission: ['registrar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id/:base_datos/:id_obra/documentos',
                        name: 'cotizacion-proveedor-documentos',
                        component: require('./components/globals/archivos/Files').default,
                        props: route => ({
                            id: route.params.id,
                            global: true,
                            sin_contexto: 1,
                            base_datos: route.params.base_datos,
                            id_obra: route.params.id_obra,
                            permiso: ['registrar_cotizacion_proveedor'],
                        }),
                        meta: {
                            title: 'Documentos de Cotización',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'DOCUMENTOS'},
                            middleware: [auth, permission],
                            permission: 'consultar_cotizacion_proveedor',
                            general: true
                        }
                    }
                ]
            },
            {
                path: 'invitacion-cotizar',
                component: require('./components/portal-proveedor/invitacion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'invitacion-proveedor',
                        component: require('./components/portal-proveedor/invitacion/Index').default,
                        meta: {
                            title: 'Invitaciones a Cotizar',
                            breadcrumb: {parent: 'proveedor', name: 'INVITACIONES'},
                            middleware: [auth, permission],
                            permission: 'consultar_invitacion_cotizar_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'invitacion-proveedor-show',
                        component: require('./components/portal-proveedor/invitacion/Show').default,
                        props: true,
                        meta: {
                            title: 'Consultar Invitación a Cotizar',
                            breadcrumb: { parent: 'invitacion-proveedor', name: 'VER'},
                            middleware: [auth, permission],
                            permission: 'consultar_invitacion_cotizar_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/documentos',
                        name: 'invitacion-proveedor-documentos',
                        component: require('./components/globals/archivos/Files').default,
                        props: route => ({
                            id_invitacion: route.params.id,
                            permiso: ['registrar_cotizacion_proveedor'],
                            global: true,
                            sin_contexto: 1,
                        }),
                        meta: {
                            title: 'Documentos de Invitación a Cotizar',
                            breadcrumb: { parent: 'invitacion-proveedor', name: 'DOCUMENTOS'},
                            middleware: [auth, permission],
                            permission: 'consultar_invitacion_cotizar_proveedor',
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'entrega-cfdi',
                components:  {
                    default: require('./components/solicitud-recepcion-cfdi/Layout.vue').default,
                    menu: require('./components/solicitud-recepcion-cfdi/partials/Menu.vue').default
                },
                children:[
                    {
                        path:'',
                        name: 'entrega-cfdi',
                        component: require('./components/solicitud-recepcion-cfdi/entrega-cfdi/Index.vue').default,
                        meta: {
                            title: 'Listado de Solicitudes de Revisión de CFDI',
                            middleware: [auth, permission],
                            permission: ['consultar_solicitud_recepcion_cfdi_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: 'seleccionar-cfdi',
                        name: 'seleccionar-cfdi',
                        component: require('./components/solicitud-recepcion-cfdi/entrega-cfdi/SeleccionarCFDI.vue').default,
                        meta: {
                            title: 'Seleccionar CFDI',
                            breadcrumb: {name: 'SELECCIONAR CFDI', parent: 'entrega-cfdi'},
                            middleware: [auth, permission],
                            permission: ['registrar_solicitud_recepcion_cfdi'],
                            general: true
                        }
                    },
                    {
                        path: ':id_cfdi/create',
                        props : true,
                        name: 'solicitud-recepcion-cfdi-create',
                        component: require('./components/solicitud-recepcion-cfdi/entrega-cfdi/Create.vue').default,
                        meta: {
                            title: 'Registrar Solicitud de Revisión de CFDI',
                            breadcrumb: {name: 'REGISTRAR SOLICITUD', parent: 'seleccionar-cfdi'},
                            middleware: [auth, permission],
                            permission: ['registrar_solicitud_recepcion_cfdi'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'solicitud-recepcion-cfdi-show',
                        component: require('./components/solicitud-recepcion-cfdi/entrega-cfdi/Show').default,
                        props: true,
                        meta: {
                            title: 'Consultar Solicitud de Revisión de CFDI',
                            breadcrumb: { parent: 'entrega-cfdi', name: 'VER SOLICITUD'},
                            middleware: [auth, permission],
                            permission: 'consultar_solicitud_recepcion_cfdi_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/cancelar',
                        name: 'solicitud-recepcion-cfdi-cancelar',
                        component: require('./components/solicitud-recepcion-cfdi/entrega-cfdi/Cancelar').default,
                        props: true,
                        meta: {
                            title: 'Cancelar Solicitud de Revisión de CFDI',
                            breadcrumb: { parent: 'entrega-cfdi', name: 'CANCELAR'},
                            middleware: [auth, permission],
                            permission: 'cancelar_solicitud_recepcion_cfdi',
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'expediente',
                component: require('./components/portal-proveedor/invitacion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'expediente',
                        component: require('./components/portal-proveedor/expediente/Edit').default,
                        props: true,
                        meta: {
                            title: 'Expediente de Proveedor',
                            breadcrumb: {name: 'EXPEDIENTE', parent: 'proveedores-index'},
                            middleware: [auth],
                        }
                    },
                ]
            },
            {
                path: 'presupuesto',
                component: require('./components/portal-proveedor/presupuesto/Layout').default,
                children: [
                    {
                        path: ':id/create',
                        name: 'presupuesto-proveedor-create',
                        component: require('./components/portal-proveedor/presupuesto/Create').default,
                        props: true,
                        meta: {
                            title: 'Registrar Presupuesto',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'REGISTRAR'},
                            middleware: [auth, permission],
                            permission: ['registrar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id_invitacion_antecedente/contraoferta/:id_invitacion/create',
                        name: 'contraoferta-presupuesto-proveedor-create',
                        component: require('./components/portal-proveedor/presupuesto/CreateContraoferta').default,
                        props: true,
                        meta: {
                            title: 'Registrar Contraoferta',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'REGISTRAR'},
                            middleware: [auth, permission],
                            permission: ['registrar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'presupuesto-proveedor-show',
                        component: require('./components/portal-proveedor/presupuesto/Show').default,
                        props: true,
                        meta: {
                            title: 'Consultar Presupuesto',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'VER'},
                            middleware: [auth, permission],
                            permission: 'consultar_cotizacion_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/editar',
                        name: 'presupuesto-proveedor-edit',
                        component: require('./components/portal-proveedor/presupuesto/Edit').default,
                        props: true,
                        meta: {
                            title: 'Editar Presupuesto',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'EDITAR'},
                            middleware: [auth, permission],
                            permission: 'editar_cotizacion_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/delete',
                        name: 'presupuesto-proveedor-delete',
                        component: require('./components/portal-proveedor/presupuesto/Delete').default,
                        props: true,
                        meta: {
                            title: 'Eliminar Presupuesto',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'ELIMINAR'},
                            middleware: [auth, permission],
                            permission: 'eliminar_cotizacion_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/enviar',
                        name: 'presupuesto-proveedor-send',
                        props: true,
                        component: require('./components/portal-proveedor/presupuesto/Enviar').default,
                        meta: {
                            title: 'Enviar Presupuesto',
                            breadcrumb: { parent: 'cotizacion-proveedor', name: 'ENVIAR'},
                            middleware: [auth, permission],
                            permission: ['registrar_cotizacion_proveedor'],
                            general: true
                        }
                    },
                ]
            },
            {
                path: 'solicitud-autorizacion-avance',
                component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'solicitud-autorizacion-avance',
                        component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/Index').default,
                        meta: {
                            title: 'Lista de Solicitud de Autorización de Avance',
                            breadcrumb: {parent: 'proveedor', name: 'SOLICITUD DE AUTORIZACIÓN DE AVANCE DE ESTIMACIONES'},
                            middleware: [auth, permission],
                            permission: 'consultar_solicitud_autorizacion_avance_proveedor',
                            general: true
                        }
                    },
                    {
                        path: 'seleccionar-subcontrato',
                        name: 'solicitud-autorizacion-avance-seleccionar-subcontrato',
                        component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/SeleccionarSubcontrato').default,
                        meta: {
                            title: 'Seleccionar Subcontrato',
                            breadcrumb: { parent: 'solicitud-autorizacion-avance', name: 'SELECCIONAR SUBCONTRATO'},
                            middleware: [auth, permission],
                            permission: ['registrar_solicitud_autorizacion_avance_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id/:base/create',
                        name: 'solicitud-autorizacion-avance-create',
                        component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/Create').default,
                        props: route => ({
                            base_b64: route.params.base,
                            id: route.params.id,
                        }),
                        meta: {
                            title: 'Registrar Solicitud de Autorización de Avance de Estimación',
                            breadcrumb: { parent: 'solicitud-autorizacion-avance', name: 'REGISTRAR'},
                            middleware: [auth, permission],
                            permission: ['registrar_solicitud_autorizacion_avance_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id/:base',
                        name: 'solicitud-autorizacion-avance-show',
                        component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/Show').default,
                        props: route => ({
                            base_b64: route.params.base,
                            id: route.params.id,
                        }),
                        meta: {
                            title: 'Consultar Solicitud de Autorización de Avance de Estimación',
                            breadcrumb: { parent: 'solicitud-autorizacion-avance', name: 'VER'},
                            middleware: [auth, permission],
                            permission: 'consultar_solicitud_autorizacion_avance_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/:base/editar',
                        name: 'solicitud-autorizacion-avance-edit',
                        props: route => ({
                            base_b64: route.params.base,
                            id: route.params.id,
                        }),
                        component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/Edit').default,
                        meta: {
                            title: 'Editar Solicitud de Autorización de Avance de Estimación',
                            breadcrumb: { parent: 'solicitud-autorizacion-avance', name: 'EDITAR'},
                            middleware: [auth, permission],
                            permission: ['editar_solicitud_autorizacion_avance_proveedor'],
                            general: true
                        }
                    },
                    {
                        path: ':id/:base/editarLayout',
                        name: 'solicitud-autorizacion-avance-edit-layout',
                        props: route => ({
                            base_b64: route.params.base,
                            id: route.params.id,
                            solicitud: route.params.solicitud
                        }),
                        component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/EditLayout').default,
                        meta: {
                            title: 'Editar Solicitud Layout',
                            breadcrumb: {parent: 'solicitud-autorizacion-avance', name: 'EDITAR LAYOUT'},
                            middleware: [auth, permission],
                            permission: 'editar_solicitud_autorizacion_avance_proveedor',
                            general: true
                        }
                    },
                    {
                        path: ':id/:base/delete',
                        name: 'solicitud-autorizacion-avance-delete',
                        props: route => ({
                            base_b64: route.params.base,
                            id: route.params.id,
                        }),
                        component: require('./components/portal-proveedor/solicitud-autorizacion-estimacion/Delete').default,
                        meta: {
                            title: 'Eliminar Solicitud de Autorización de Avance de Estimación',
                            breadcrumb: { parent: 'solicitud-autorizacion-avance', name: 'ELIMINAR'},
                            middleware: [auth, permission],
                            permission: ['eliminar_solicitud_autorizacion_avance_proveedor'],
                            general: true
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/seguimiento',
        components:  {
            default: require('./components/seguimiento/partials/Layout.vue').default,
            menu: require('./components/seguimiento/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'seguimiento',
                component: require('./components/seguimiento/Index').default,
                meta: {
                    title: 'Seguimiento',
                    middleware: [auth, permission],
                    breadcrumb: {name: 'SEGUIMIENTO'},
                    permission: ['consultar_factura_cuenta_x_cobrar'],
                    general: true
                }
            },
            {
                path: 'factura',
                component: require('./components/seguimiento/factura/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'factura-seg',
                        component: require('./components/seguimiento/factura/Index').default,
                        meta: {
                            title: 'Lista de Facturas',
                            breadcrumb: {parent: 'seguimiento', name: 'FACTURAS'},
                            middleware: [auth, permission],
                            permission: 'consultar_factura_cuenta_x_cobrar',
                            general: true
                        }
                    },
                    {
                        path: 'create',
                        name: 'factura-seg-create',
                        props:true,
                        component: require('./components/seguimiento/factura/Create').default,
                        meta: {
                            title: 'Registrar Factura',
                            breadcrumb: {name: 'REGISTRAR', parent: 'factura-seg'},
                            middleware: [auth, permission],
                            permission: ['registrar_factura_cuenta_x_cobrar'],
                            general: true
                        }
                    },
                    {
                        path: ':id',
                        name: 'factura-seg-show',
                        component: require('./components/seguimiento/factura/Show').default,
                        props: true,
                        meta: {
                            title: 'Consulta de Factura',
                            breadcrumb: {name: 'VER', parent: 'factura-seg'},
                            middleware: [auth, permission],
                            permission: ['consultar_factura_cuenta_x_cobrar'],
                            general: true
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '/concursos',
        component: require('./components/concursos/concurso/Layout').default,
        components:  {
            default: require('./components/concursos/partials/Layout.vue').default,
            menu: require('./components/concursos/partials/Menu.vue').default
        },
        children: [
            {
                path: '/',
                name: 'concursos',
                component: require('./components/concursos/concurso/Index').default,
                meta: {
                    title: 'Lista de Concursos',
                    breadcrumb: {name: 'CONCURSO'},
                    middleware: [auth, permission],
                    permission: 'consultar_concurso',
                    general: true
                }
            },
            {
                path: 'create',
                name: 'concurso-create',
                component: require('./components/concursos/concurso/Create').default,
                meta: {
                    title: 'Registrar Concurso',
                    breadcrumb: {name: 'REGISTRAR', parent: 'concursos'},
                    middleware: [auth, permission],
                    permission: ['registrar_concurso'],
                    general: true
                }
            },
            {
                path: ':id/editar',
                name: 'concurso-edit',
                props: true,
                component: require('./components/concursos/concurso/Edit').default,
                meta: {
                    title: 'Editar Concurso',
                    breadcrumb: { parent: 'concursos', name: 'EDITAR'},
                    middleware: [auth, permission],
                    permission: ['editar_concurso'],
                    general: true
                }
            },
            {
                path: ':id/consultar',
                name: 'concurso-show',
                props: true,
                component: require('./components/concursos/concurso/Show').default,
                meta: {
                    title: 'Consultar Concurso',
                    breadcrumb: { parent: 'concursos', name: 'CONSULTAR'},
                    middleware: [auth, permission],
                    permission: ['consultar_concurso'],
                    general: true
                }
            },
            {
                path: ':id/registro-fallo',
                name: 'concurso-registro-fallo',
                props: true,
                component: require('./components/concursos/concurso/RegistrarFallo').default,
                meta: {
                    title: 'Registrar Fallo de Concurso',
                    breadcrumb: { parent: 'concursos', name: 'REGISTRAR FALLO'},
                    middleware: [auth, permission],
                    permission: ['editar_concurso'],
                    general: true
                }
            },
        ]
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
        path: '/sao/control_presupuesto',
        components: {
            default: require('./components/control-presupuesto/partials/Layout.vue').default,
            menu: require('./components/control-presupuesto/partials/Menu.vue').default
        },
        children: [
            {
                path: '',
                name: 'control_presupuesto',
                component: require('./components/control-presupuesto/Index').default,
                meta: {
                    title: 'Control Presupuesto',
                    breadcrumb: {parent:'home', name: 'CONTROL PRESUPUESTO'},
                    middleware: [auth, context, access]
                }
            },
            {
                path: 'presupuesto-obra',
                component: require('./components/presupuesto/concepto/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'presupuesto-obra',
                        component: require('./components/presupuesto/concepto/Index').default,
                        meta: {
                            title: 'Árbol de Presupuesto',
                            breadcrumb: {parent: 'control_presupuesto', name: 'ÁRBOL DE PRESUPUESTO'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_presupuesto']
                        }
                    },
                    {
                        path: ':id/editar',
                        name: 'concepto-edit',
                        props: true,
                        component: require('./components/presupuesto/concepto/Edit').default,
                        meta: {
                            title: 'Editar Conceptos',
                            breadcrumb: { parent: 'presupuesto-obra', name: 'EDITAR'},
                            middleware: [auth, context, permission],
                            permission: ['editar_clave_concepto']
                        }
                    },
                    {
                        path: ':id',
                        name: 'concepto-show',
                        component: require('./components/presupuesto/concepto/Show').default,
                        props: true,
                        meta: {
                            title: 'Consultar Concepto',
                            breadcrumb: { parent: 'presupuesto-obra', name: 'VER'},
                            middleware: [auth, context, permission],
                            permission: 'consultar_presupuesto'
                        }
                    },
                ]
            },
            {
                path: 'solicitud-cambio-presupuesto',
                component: require('./components/control-presupuesto/solicitud-cambio/Index').default,
                children: [
                    {
                        path: '/',
                        name: 'solicitud-cambio-presupuesto',
                        component: require('./components/control-presupuesto/solicitud-cambio/Index').default,
                        meta: {
                            title: 'Control de Cambios al Presupuesto',
                            breadcrumb: {parent: 'control_presupuesto', name: 'CONTROL DE CAMBIOS'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_variacion_volumen','registrar_variacion_volumen']
                        }
                    },
                ]
            },
            {
                path: 'variacion-volumen',
                component: require('./components/control-presupuesto/variacion-volumen/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'variacion-volumen',
                        component: require('./components/control-presupuesto/variacion-volumen/Index').default,
                        meta: {
                            title: 'Variación de Volumen (Aditivas / Deductivas)',
                            breadcrumb: {parent: 'control_presupuesto', name: 'VARIACIÓN DE VOLUMEN'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_variacion_volumen','registrar_variacion_volumen']
                        }
                    },
                    {
                        path: 'create',
                        name: 'variacion-volumen-create',
                        component: require('./components/control-presupuesto/variacion-volumen/Create').default,
                        props: true,
                        meta: {
                            title: 'Registrar Variación de Volumen',
                            breadcrumb: {name: 'REGISTRAR', parent: 'variacion-volumen'},
                            middleware: [auth, context, permission],
                            permission: ['registrar_variacion_volumen']
                        }
                    },
                    {
                        path: ':id',
                        name: 'variacion-volumen-show',
                        component: require('./components/control-presupuesto/variacion-volumen/Show').default,
                        props: true,
                        meta: {
                            title: 'Variación de Volumen',
                            breadcrumb: {name: 'VER', parent: 'variacion-volumen'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_variacion_volumen']
                        }
                    },
                    {
                        path: ':id/autorizar',
                        name: 'variacion-volumen-autorizar',
                        component: require('./components/control-presupuesto/variacion-volumen/Autorizar').default,
                        props: true,
                        meta: {
                            title: 'Autorizar Variación de Volumen',
                            breadcrumb: {name: 'VER', parent: 'variacion-volumen'},
                            middleware: [auth, context, permission],
                            permission: ['autorizar_variacion_volumen','rechazar_variacion_volumen']
                        }
                    },
                ]
            },
            {
                path: 'extraordinario',
                component: require('./components/control-presupuesto/extraordinario/Layout').default,
                children: [
                    {
                        path: '/',
                        name: 'extraordinario',
                        component: require('./components/control-presupuesto/extraordinario/Index').default,
                        meta: {
                            title: 'Concepto Extraordinario',
                            breadcrumb: {parent: 'control_presupuesto', name: 'EXTRAORDINARIO'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_extraordinario','registrar_extraordinario']
                        }
                    },
                    {
                        path: 'create',
                        name: 'extraordinario-create',
                        component: require('./components/control-presupuesto/extraordinario/Create').default,
                        props: true,
                        meta: {
                            title: 'Registrar Concepto Extraordinario',
                            breadcrumb: {name: 'REGISTRAR', parent: 'extraordinario'},
                            middleware: [auth, context, permission],
                            permission: ['registrar_extraordinario']
                        }
                    },
                    {
                        path: ':id',
                        name: 'extraordinario-show',
                        component: require('./components/control-presupuesto/extraordinario/Show').default,
                        props: true,
                        meta: {
                            title: 'Concepto Extraordinario',
                            breadcrumb: {name: 'VER', parent: 'extraordinario'},
                            middleware: [auth, context, permission],
                            permission: ['consultar_extraordinario']
                        }
                    },
                    {
                        path: ':id/autorizar',
                        name: 'extraordinario-autorizar',
                        component: require('./components/control-presupuesto/extraordinario/Autorizar').default,
                        props: true,
                        meta: {
                            title: 'Autorizar Extraordinario',
                            breadcrumb: {name: 'VER', parent: 'extraordinario'},
                            middleware: [auth, context, permission],
                            permission: ['autorizar_extraordinario','rechazar_extraordinario']
                        }
                    },
                ]
            },
        ]
    },
    {
        path: '*',
        name: 'notFound',
        component: require('./components/pages/NotFound.vue').default,
    }
];
