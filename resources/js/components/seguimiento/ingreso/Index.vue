<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Buscar" v-model="search">
                            </div>
                        </div>
                    </div>
                </div>
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
        name: "ingreso-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:"th_index_corto", sortable: false },
                    { title: 'Fecha de Ingreso', tdClass: 'td_c100', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date').default},
                    { title: 'Área', field: 'area', tdClass: 'td_c250', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Proyecto', field: 'idproyecto', tdClass: 'td_c250', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Cuenta', tdClass: 'td_c100', field: 'cuenta', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Factura', tdClass: 'td_c100', field: 'factura', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Moneda', field: 'idmoneda', tdClass: 'td_c100', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Total', field: 'total', tdClass: 'money', sortable: true, thComp: require('../../globals/th-Filter').default},
                  //  { title: 'Acciones', field: 'buttons', thClass: 'th_m200', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: [], sort: 'id_ingreso', order: 'desc'},
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
                return this.$store.dispatch('seguimiento/vw-ingreso/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('seguimiento/vw-ingreso/SET_INGRESOS', data.data);
                        this.$store.commit('seguimiento/vw-ingreso/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;

                    })
            },
        },
        computed: {
            ingresos(){
                return this.$store.getters['seguimiento/vw-ingreso/ingresos'];
            },
            meta(){
                return this.$store.getters['seguimiento/vw-ingreso/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            ingresos: {
                handler(ingresos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = ingresos.map((ingreso, i) => ({
                        index: (i + 1) + self.query.offset,
                        id: ingreso.id,

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
