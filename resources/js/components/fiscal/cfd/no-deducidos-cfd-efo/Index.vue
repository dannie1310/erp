<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_no_deducido_cfd_efo', true)" :disabled="cargando">
            <button @click="create" class="btn btn-app btn-info float-right">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
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
    import Create from './Create'
    export default {
        name: "no-deducidos-cfd-efos-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Proveedor', field: 'id_proveedor_sat', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha', field: 'fecha_hora_registro', sortable: true},
                    { title: 'Cantidad de CFD', field:'cantidad_partidas',  tdClass: 'td_money', sortable: false},
                    { title: 'Estatus', field: 'estado', tdComp: require('./partials/EstatusLabel').default, sortable: false},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: ['proveedor'], sort: 'id', order: 'desc'},
                estado: "",
                cargando: false,
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
            create() {
                this.$router.push({name: 'no-deducidos-cfd-efos-create'});
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('fiscal/no-deducido/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('fiscal/no-deducido/SET_NODEDUCIDOS', data.data);
                        this.$store.commit('fiscal/no-deducido/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            noDeducidos(){
                return this.$store.getters['fiscal/no-deducido/noDeducidos'];
            },
            meta(){
                return this.$store.getters['fiscal/no-deducido/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            noDeducidos: {
                handler(noDeducidos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = noDeducidos.map((noDeducido, i) => ({
                        index: (i + 1) + self.query.offset,
                        id_proveedor_sat: noDeducido.proveedor.razon_social,
                        fecha_hora_registro: noDeducido.fecha,
                        cantidad_partidas: noDeducido.cantidad_partidas,
                        estado: noDeducido.estatus,
                        buttons: $.extend({}, {
                            id: noDeducido.id,
                            show : self.$root.can('consultar_no_deducido_cfd_efo', true) ? true : false
                        })
                    }));
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

<style scoped>

</style>
