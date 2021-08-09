<template>
    <div class="row">
        <div class="col-12">
            <!--<button @click="create" v-if="$root.can('registrar_cotizacion_proveedor',true)" class="btn btn-app pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>-->
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <router-view ></router-view>
    </div>
</template>

<script>
    export default {
        name: "cotizacion-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:"th_index_corto", sortable: false },
                    { title: 'Proyecto', field: 'descripcion_obra', tdClass: 'td_c250', sortable: true},
                    { title: 'Folio de Invitación', tdClass: 'td_c100', field: 'numero_folio', sortable: true},
                    { title: 'Fecha de Registro de Invitación', tdClass: 'td_c100', field: 'fecha_hora_invitacion', sortable: true},
                    { title: 'Fecha de Cierre de Invitación', tdClass: 'td_c100', field: 'fecha_cierre_invitacion', sortable: true},
                    { title: 'Folio de Solicitud', tdClass: 'td_c100', field: 'solicitud'},
                    { title: 'Folio de Cotización', field: 'numero_folio_cotizacion', tdClass: 'td_c100', sortable: false},
                    { title: 'Fecha de Cotización', field: 'fecha_cotizacion', tdClass: 'td_c100', sortable: false },
                    { title: 'Importe', field: 'importe', tdClass: 'money', sortable: false },
                    { title: 'Estatus', field: 'estado', sortable: false, tdClass: 'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_m200', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: ['transaccion','cotizacion'], scope: ['cotizacionRealizada','invitadoAutenticado'], sort: 'id', order: 'desc'},
                search: '',
                cargando: false
            }
        },
        mounted() {

            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('padronProveedores/invitacion/SET_INVITACIONES', data.data);
                        this.$store.commit('padronProveedores/invitacion/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;

                    })
            },

            getEstado(estado) {
                let val = parseInt(estado);
                switch (val) {
                    case -2:
                        return {
                            color: '#e50c25',
                            text_color:'#f5f1f1',
                            descripcion: 'Precios Pendientes'
                        }
                    case -1:
                        return {
                            color: '#f39c12',
                            text_color:'#000000',
                            descripcion: 'Registrada'
                        }
                    case 0:
                        return {
                            color: '#396bea',
                            text_color:'#f5f1f1',
                            descripcion: 'Enviada'
                        }
                    case 1:
                        return {
                            color: '#59a153',
                            text_color:'#000000',
                            descripcion: 'Enviada'
                        }
                    case 2:
                        return {
                            color: '#59a153',
                            text_color:'#000000',
                            descripcion: 'En Asignación'
                        }
                }
            },
            create() {
                this.$router.push({name: 'cotizacion-proveedor-seleccionar-solicitud'});
            },
        },
        computed: {
            invitaciones(){
                return this.$store.getters['padronProveedores/invitacion/invitaciones'];
            },
            meta(){
                return this.$store.getters['padronProveedores/invitacion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            invitaciones: {
                handler(invitaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = invitaciones.map((invitacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: invitacion.numero_folio_format,
                        numero_folio_cotizacion: invitacion.cotizacion.numero_folio_format,
                        solicitud: invitacion.transaccion.numero_folio_format,
                        fecha_cotizacion: invitacion.cotizacion.fecha_format,
                        fecha_hora_invitacion: invitacion.fecha_format,
                        fecha_cierre_invitacion: invitacion.fecha_cierre_format,
                        empresa: invitacion.razon_social,
                        observaciones: invitacion.cotizacion.observaciones,
                        importe: invitacion.importe_cotizacion,
                        estado: this.getEstado(invitacion.cotizacion.estado),
                        descripcion_obra: invitacion.descripcion_obra,
                        buttons: $.extend({}, {
                            invitacion: invitacion,
                            show: self.$root.can('consultar_cotizacion_proveedor',true) ? true : false,
                            tipo_transaccion: invitacion.tipo_antecedente,
                            id_invitacion: invitacion.id,
                            id_cotizacion: invitacion.cotizacion.id_transaccion,
                            enviar: (self.$root.can('editar_cotizacion_proveedor',true) && invitacion.cotizacion.estado == -1)  ? true : false,
                            edit: (self.$root.can('editar_cotizacion_proveedor',true) && invitacion.cotizacion.estado < 0) ? true : false,
                            descarga_layout: (self.$root.can('descargar_layout_cotizacion_proveedor',true) && self.$root.can('editar_cotizacion_proveedor',true) && invitacion.cotizacion.estado < 0) ? true : false,
                            carga_layout: (self.$root.can('cargar_layout_cotizacion_proveedor',true) && self.$root.can('editar_cotizacion_proveedor',true) && invitacion.cotizacion.estado < 0) ? true : false,
                            delete: (self.$root.can('eliminar_cotizacion_proveedor',true) && invitacion.cotizacion.estado < 0) ? true : false,
                        })
                    }));
                },
                deep: true
            },
            meta: {
                handler (meta) {

                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler (query) {
                    this.paginate()
                },
                deep: true
            },
            search(val) {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => {

                    this.query.search = val;
                    this.query.offset = 0;
                    this.paginate();
                }, 500);
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        }
    }
</script>


<style>
    .folio
    {
        text-align: center;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>
