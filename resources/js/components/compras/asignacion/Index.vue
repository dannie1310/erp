<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" class="btn btn-app btn-default pull-right" v-if="$root.can('registrar_asignacion_proveedor')" :disabled="cargando">
                <i class="fa fa-plus"></i> Registrar
            </button>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
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
    </div>
</template>

<script>
    import Layout from "./CargaLayout";
    export default {
        name: "asignacion-proveedor-index",
        components: {Layout},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'id', tdClass: 'th_numero_folio', sortable: true},
                    { title: 'Folio SAO Solicitud', field: 'solicitud', tdClass: 'th_c120', tdComp: require('../solicitud-compra/partials/ActionButtonsConsulta').default,sortable: true},
                    { title: 'Concepto de Solicitud', field: 'concepto'},
                    { title: 'Fecha / Hora de Registro', field: 'fecha_format',sortable: true},
                    { title: 'Estado', field: 'estado', thClass:'th_c120', sortable: true},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_c150', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {scope: 'areasCompradorasAsignadas', sort: 'id', order: 'desc', include: ['solicitudCompra']},
                estado: "",
                cargando: false
            }
        },

        mounted() {
            this.cargando = true;
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                    this.cargando = false;
                })
        },

        methods: {
            create(){
                this.$router.push({name: 'seleccionar-solicitud-compra'});
            },
            paginate() {
                return this.$store.dispatch('compras/asignacion/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('compras/asignacion/SET_ASIGNACIONES', data.data);
                        this.$store.commit('compras/asignacion/SET_META', data.meta);
                    })
            }
        },
        computed: {
            asignaciones(){
                return this.$store.getters['compras/asignacion/asignaciones'];
            },
            meta(){
                return this.$store.getters['compras/asignacion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            asignaciones: {
                handler(asignaciones) {
                    let self = this
                    self.$data.data = []
                    asignaciones.forEach(function (asignacion, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            folio_solicitud: asignacion.folio_solicitud_format,
                            id: asignacion.folio_asignacion_format,
                            concepto: asignacion.concepto,
                            estado: asignacion.estado_format,
                            fecha_format: asignacion.fecha_format,
                            solicitud: $.extend({}, {
                                show: (asignacion.solicitud) ? true : false,
                                id: (asignacion.solicitud) ? asignacion.solicitud.id : null,
                                numero_folio: (asignacion.solicitud) ? asignacion.solicitud.numero_folio_format : null
                            }),
                            buttons: $.extend({}, {
                                id:asignacion.id,
                                estado:asignacion.estado,
                                eliminar: (self.$root.can('eliminar_asignacion_proveedor') && ! asignacion.aplicada) ? true : false,
                                editar: self.$root.can('registrar_orden_compra') && ! asignacion.aplicada ? true : false
                            })
                        })
                    });
                },
                deep: true
            },
            meta: {
                handler(meta) {
                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler(query) {
                    this.paginate(query)
                },
                deep: true
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
    .money
    {
        text-align: right;
    }
</style>
