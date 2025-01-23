<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_comprobacion_gastos', true)" class="btn btn-app pull-right">
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
        name: "comprobacion-gasto-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                    { title: 'Serie', field: 'idserie', thClass: 'th_c60', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Empleado', field: 'idproveedor', thClass: 'th_c250', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha', thClass: 'th_c80', field: 'fecha', sortable: true,thComp: require('../../../globals/th-Date').default},
                    { title: 'Folio', field: 'folio',sortable: true,thClass: 'th_c80',  thComp: require('../../../globals/th-Filter').default},
                    { title: 'Importe', field: 'importe', thClass :'th_c100', tdClass: 'right', sortable: true},
                    { title: 'Estatus', field: 'idestado', sortable: true, thClass:'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_c80', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: { sort: 'FechaHoraGenero', order: 'desc', scope: []},
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
                return this.$store.dispatch('controlRecursos/comprobacion/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('controlRecursos/comprobacion/SET_COMPROBACIONES', data.data);
                        this.$store.commit('controlRecursos/comprobacion/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstado(estado, color) {
                return {
                    color: color,
                    descripcion: estado
                }
            },
            create() {
                this.$router.push({name: 'comprobacion-gasto-create'});
            },
        },
        computed: {
            comprobaciones(){
                return this.$store.getters['controlRecursos/comprobacion/comprobaciones'];
            },
            meta(){
                return this.$store.getters['controlRecursos/comprobacion/meta'];
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
                            fecha: comprobacion.fecha_format,
                            idproveedor: comprobacion.empleado_descripcion,
                            importe: comprobacion.importe_format,
                            folio: comprobacion.folio_format,
                            idserie: comprobacion.serie,
                            idestado: this.getEstado(comprobacion.estado_format, comprobacion.estado_color),
                            buttons: $.extend({}, {
                                show: true,
                                id: comprobacion.id,
                                edit: (self.$root.can('editar_comprobacion_gastos', true) && comprobacion.estatus == 1)  ? true : false,
                                delete: (self.$root.can('eliminar_comprobacion_gastos', true) && (comprobacion.estatus == 0 || comprobacion.estatus == 1)) ? true : false,
                                cerrar: (self.$root.can('cerrar_comprobacion_gastos', true) && comprobacion.estatus == 1) ? true : false,
                                abrir: (self.$root.can('abrir_comprobacion_gastos', true) && comprobacion.estatus == 2) ? true : false,
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
