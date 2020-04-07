<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="" class="btn btn-app btn-info pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
        </div>
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
        name: "cotizacion-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio', sortable: true},
                    { title: 'Fecha Requerido', field: 'fecha', sortable: true },
                    { title: 'Fecha / Hora Registro', field: 'fecha_registro', tdClass: 'money', thClass: 'th_money', sortable: false },
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    // { title: 'Estatus', field: 'estado', sortable: true, tdComp: require('./partials/EstatusLabel').default},
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'numero_folio', order: 'DESC'},
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
                return this.$store.dispatch('compras/cotizacion/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('compras/cotizacion/SET_COTIZACIONES', data.data);
                        this.$store.commit('compras/cotizacion/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;

                    })
            },

            // getEstado(estado) {

            //     let val = parseInt(estado);
            //     switch (val) {
            //         case 0:
            //             return {
            //                 color: '#f39c12',
            //                 descripcion: 'Registrada'
            //             }
            //         case 1:
            //             return {
            //                 color: '#00a65a',
            //                 descripcion: 'Aprobada'
            //             }
            //         case 2:
            //             return {
            //                 color: '#7889d6',
            //                 descripcion: 'Tercer caso'
            //             }
            //         default:
            //             return {
            //                 color: '#d2d6de',
            //                 descripcion: 'Desconocido'
            //             }
            //     }
            // },
            create() {
                this.$router.push({name: 'solicitud-compra-create'});
            },
        },
        computed: {
            cotizaciones(){
                return this.$store.getters['compras/cotizacion/cotizaciones'];
            },
            meta(){
                return this.$store.getters['compras/cotizacion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            cotizaciones: {
                handler(cotizaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = cotizaciones.map((cotizacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: cotizacion.folio,
                        fecha: cotizacion.fecha_format,
                        fecha_registro: cotizacion.fecha_format,
                        observaciones: cotizacion.observaciones,
                        // estado: this.getEstado(cotizacion.estado),
                        // buttons: $.extend({}, {
                        //     show: true,
                        //     aprobar: (cotizacion.estado == 0) ? true : false,
                        //     id: cotizacion.id,
                        // })
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
    .money
    {
        text-align: right;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>
