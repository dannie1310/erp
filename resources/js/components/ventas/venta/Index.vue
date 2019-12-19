<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_venta')"  :disabled="cargando">
            <button @click="create" class="btn btn-app btn-info float-right" >
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
    export default {
        name: "venta-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'folio',sortable: true},
                    { title: 'Fecha', field: 'fecha_format', tdClass: 'td_money',sortable: true},
                    { title: 'Observaciones', field: 'observaciones'},
                    { title: 'Estado', field: 'estado',  tdComp: require('./partials/VentaEstatus').default},
                    { title: 'Monto', field: 'monto', tdClass: 'td_money',sortable: false},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include:'estado'},
                estado: "",
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
                return this.$store.dispatch('ventas/venta/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('ventas/venta/SET_VENTAS', data.data);
                        this.$store.commit('ventas/venta/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create(){
                this.$router.push({name: 'venta-create'});
            }
        },
        computed: {
            ventas(){
                return this.$store.getters['ventas/venta/ventas'];
            },
            meta(){
                return this.$store.getters['ventas/venta/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            ventas: {
                handler(ventas) {
                    let self = this
                    self.$data.data = []
                    ventas.forEach(function (venta, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            folio: venta.folio_format,
                            fecha_format: venta.fecha_format,
                            observaciones: venta.observaciones,
                            monto: venta.monto,
                            estado: $.extend({},{
                                estado: venta.estado
                            }),
                            buttons: $.extend({}, {
                                show: true,
                                pagina: self.query.offset,
                                id: venta.id,
                                borrar: venta.estado !== -1 ? true : false,
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
