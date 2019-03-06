<template>
    <div class="row">
        <div class="col-md-12">
            <solicitud-movimiento-fondo-garantia-create :tipo_boton="1"></solicitud-movimiento-fondo-garantia-create>
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
    import SolicitudMovimientoFondoGarantiaCreate from "./Create";
    import ActionButtonsSmfg from "./partials/ActionButtons";
    export default {
        components: {SolicitudMovimientoFondoGarantiaCreate},
        name: "solicitud-movimiento-fondo-garantia-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', sortable: false },
                    { title: 'Folio', field: 'id', thComp: require('../../../globals/th-Filter'), sortable: true },
                    { title: 'Tipo', field: 'tipo_solicitud__descripcion', thComp: require('../../../globals/th-Filter'), },
                    { title: 'Fecha', field: 'fecha', thClass: 'th_fecha', sortable: true },
                    { title: 'Referencia', field: 'referencia', thComp: require('../../../globals/th-Filter')},
                    { title: 'Folio Subcontrato', field: 'subcontrato__numero_folio', thClass: 'th_folio', thComp: require('../../../globals/th-Filter'), sortable: true },
                    { title: 'Importe', field: 'importe', tdClass: 'money', thClass: 'th_money'},
                    { title: 'Observaciones', field: 'observaciones'},
                    { title: 'Estatus', field: 'ctg_tipos_mov_sol__estado_resultante_desc', thComp: require('../../../globals/th-Filter'), sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                }
            }
        },
        mounted() {
        },
        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('contratos/solicitud-movimiento-fg/paginate', payload)
            }
        },
        computed: {
            solicitudes(){
                return this.$store.getters['contratos/solicitud-movimiento-fg/solicitudes'];
            },
            meta(){
                return this.$store.getters['contratos/solicitud-movimiento-fg/meta'];
            },
        },
        watch: {
            solicitudes: {
                handler(solicitudes) {
                    let self = this
                    self.$data.data = []
                    solicitudes.forEach(function (solicitud, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            id: solicitud.numero_folio_format,
                            importe: solicitud.importe,
                            referencia: solicitud.referencia,
                            fecha: solicitud.fecha_format,
                            observaciones: solicitud.observaciones,
                            ctg_tipos_mov_sol__estado_resultante_desc: solicitud.estado_desc,
                            subcontrato__numero_folio: solicitud.fondo_garantia.subcontrato.numero_folio_format,
                            tipo_solicitud__descripcion: solicitud.tipo_solicitud.descripcion,
                            buttons: $.extend({}, {
                                show: self.$root.can('consultar_cuenta_almacen') ? true : undefined,
                                cancelar: self.$root.can('consultar_cuenta_almacen') && solicitud.estado == 0 ? true : undefined,
                                autorizar: self.$root.can('consultar_cuenta_almacen') && solicitud.estado == 0 ? true : undefined,
                                rechazar: self.$root.can('consultar_cuenta_almacen') && solicitud.estado == 0 ? true : undefined,
                                revertir_autorizacion: self.$root.can('consultar_cuenta_almacen') && solicitud.estado == 1 ? true : undefined,
                                id: solicitud.id,
                            })
                        })
                    });
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
                    this.paginate(query)
                },
                deep: true
            }
        },
    }
</script>