<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_relacion_gastos_recursos', true)" class="btn btn-app pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'"  />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</template>

<script>
    export default {
        name: "relacion-gasto-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                    { title: 'Serie', field: 'idserie', thClass: 'th_c60', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Empleado', field: 'idempleado', thClass: 'th_c250', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Empresa', field: 'idempresa', thClass: 'th_c250', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Inicio', thClass: 'th_c80', field: 'fecha_inicio', sortable: true,thComp: require('../../../globals/th-Date').default},
                    { title: 'Folio', field: 'folio',sortable: true,thClass: 'th_c80',  thComp: require('../../../globals/th-Filter').default},
                    { title: 'Proyecto', field: 'idproyecto',sortable: true,thComp: require('../../../globals/th-Filter').default},
                    { title: 'Total', field: 'total', thClass :'th_c200', tdClass: 'right', sortable: true},
                    { title: 'Moneda', field: 'idmoneda',thClass: 'th_c100',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estatus', field: 'idestado', sortable: true, thClass:'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {include: [], sort: 'numero_folio', order: 'desc'},
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
                return this.$store.dispatch('controlRecursos/relacion-gasto/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('controlRecursos/relacion-gasto/SET_RELACIONES', data.data);
                        this.$store.commit('controlRecursos/relacion-gasto/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'relacion-gasto-create'});
            },
            getEstado(estado, color) {
                return {
                    color: color,
                    descripcion: estado
                }
            },
        },
        computed: {
            relaciones(){
                return this.$store.getters['controlRecursos/relacion-gasto/relaciones'];
            },
            meta(){
                return this.$store.getters['controlRecursos/relacion-gasto/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            relaciones: {
                handler(relaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = relaciones.map((relacion, i) => ({
                            index: (i + 1) + self.query.offset,
                            fecha_inicio: relacion.fecha_inicio_format,
                            idempleado: relacion.empleado_descripcion,
                            idproyecto: relacion.proyecto_descripcion,
                            idempresa: relacion.empresa_descripcion,
                            folio: relacion.folio,
                            total: relacion.total_format,
                            idmoneda: relacion.moneda,
                            idserie: relacion.serie,
                            idestado: this.getEstado(relacion.estado_descripcion, relacion.estado_color),
                            buttons: $.extend({}, {
                                show: true,
                                id: relacion.id,
                                id_documento: relacion.id_documento,
                                edit : self.$root.can('editar_relacion_gastos_recursos', true) && (relacion.estado == 1 ||  relacion.estado == 2)? true : false,
                                cerrar: (self.$root.can('cerrar_relacion_gastos_recursos', true) && relacion.estado == 2) ? true : false,
                                abrir: (self.$root.can('abrir_relacion_gastos_recursos', true) && relacion.estado == 5) ? true : false,
                                pdf: relacion.estado == 5 ? true : false,
                                delete: self.$root.can('eliminar_relacion_gastos_recursos', true) && (relacion.estado == 1 || relacion.estado == 2) ? true : false,
                                reembolso_x_solicitud: self.$root.can('eliminar_relacion_gastos_recursos', true) && relacion.estado == 6 ? true : false,
                                solicitar_reembolso: self.$root.can('eliminar_relacion_gastos_recursos', true) && relacion.estado == 5 ? true : false,
                                solicitar_reembolso_caja: self.$root.can('eliminar_relacion_gastos_recursos', true) && relacion.estado == 5 ? true : false,
                                reembolso_x_solicitud_caja: self.$root.can('eliminar_relacion_gastos_recursos', true) && relacion.estado == 60 ? true : false,
                            })
                    }))
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

</style>
