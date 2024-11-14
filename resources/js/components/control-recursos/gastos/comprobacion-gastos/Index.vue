<template>
    <div class="row">
        <div class="col-12">
           <!-- <button @click="create" v-if="$root.can('registrar_relacion_gastos_recursos', true)" class="btn btn-app pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button> -->
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
        name: "comprobacion-gasto-index",
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
                    //{ title: 'Estatus', field: 'idestado', sortable: true, thClass:'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    //{ title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {include: [], sort: 'timestamp_registro', order: 'desc'},
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
                return this.$store.dispatch('controlRecursos/comprobacion-gasto/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('controlRecursos/comprobacion-gasto/SET_COMPROBACIONES', data.data);
                        this.$store.commit('controlRecursos/comprobacion-gasto/SET_META', data.meta);
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
            comprobaciones(){
                return this.$store.getters['controlRecursos/comprobacion-gasto/comprobaciones'];
            },
            meta(){
                return this.$store.getters['controlRecursos/comprobacion-gasto/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            comprobaciones: {
                handler(comprobaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = comprobaciones.map((comprobacion, i) => ({
                            index: (i + 1) + self.query.offset,
                            fecha_inicio: comprobacion.fecha_inicio_format,
                            idempleado: comprobacion.empleado_descripcion,
                            idproyecto: comprobacion.proyecto_descripcion,
                            idempresa: comprobacion.empresa_descripcion,
                            folio: comprobacion.folio,
                            total: comprobacion.total_format,
                            idmoneda: comprobacion.moneda,
                            idserie: comprobacion.serie,
                           // idestado: this.getEstado(relacion.estado_descripcion, relacion.estado_color),
                            buttons: $.extend({}, {
                                show: true,
                                id: comprobacion.id,
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
