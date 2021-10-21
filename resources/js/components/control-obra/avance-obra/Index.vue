<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_avance_obra')" class="btn btn-app pull-right">
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
    export default {
        name: "avance-obra-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha', field: 'fecha', thComp: require('../../globals/th-Date').default, sortable: true },
                    { title: 'Concepto Padre', field: 'id_concepto', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Observaciones', field: 'observaciones', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Estatus', field: 'estado', sortable: false, tdClass: 'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: [], sort: 'id_transaccion',  order: 'desc'},
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
                return this.$store.dispatch('controlObra/avance-obra/paginate', {
                    params: this.query
                })
                .then(data => {
                    this.$store.commit('controlObra/avance-obra/SET_AVANCES', data.data);
                    this.$store.commit('controlObra/avance-obra/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
            },
            getEstado(color, descripcion) {
                return {
                    color: color,
                    descripcion: descripcion
                }
            },
            create() {
                this.$router.push({name: 'avance-obra-create'});
            },
        },
        computed: {
            avances(){
                return this.$store.getters['controlObra/avance-obra/avances'];
            },

            meta(){
                return this.$store.getters['controlObra/avance-obra/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            avances: {
                handler(avances) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = avances.map((avance, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: avance.numero_folio_format,
                        fecha: avance.fecha_format,
                        observaciones: avance.observaciones,
                        estado: this.getEstado(avance.color_estado, avance.descripcion_estado),
                        id_concepto: avance.concepto_descripcion,
                        buttons: $.extend({}, {
                            id: avance.id,
                            show: true,
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
                handler () {
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

<style scoped>

</style>
