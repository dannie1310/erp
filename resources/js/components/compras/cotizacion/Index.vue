<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_cotizacion_compra')" class="btn btn-app btn-info pull-right">
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
        <router-view ></router-view>
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
                    { title: 'Folio', field: 'numero_folio', tdClass: 'td_c80', sortable: true},
                    { title: 'Solicitud', tdClass: 'td_c80', field: 'solicitud'},
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Proveedor', field: 'empresa', sortable: false },
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    { title: 'Importe', field: 'importe', tdClass: 'money', sortable: false },
                    { title: 'Estatus', field: 'estado', sortable: false, tdClass: 'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_m200', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {scope: 'areasCompradorasAsignadas', sort: 'numero_folio', order: 'DESC', include: ['solicitud', 'empresa', 'relaciones']},
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

            getEstado(estado) {

                let val = parseInt(estado);
                switch (val) {
                    case 0:
                        return {
                            color: '#ff0000',
                            descripcion: 'Precios Pendientes'
                        }
                    case 1:
                        return {
                            color: '#f39c12',
                            descripcion: 'Registrada'
                        }
                    case 2:
                        return {
                            color: '#4f9b34',
                            descripcion: 'En Asignación'
                        }
                }
            },
            create1() {
                this.$router.push({name: 'cotizacion-create'});
            },
            create() {
                this.$router.push({name: 'cotizacion-selecciona-solicitud-compra'});
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
                        numero_folio: cotizacion.folio_format,
                        fecha: cotizacion.fecha_format,
                        empresa: (cotizacion.empresa) ? cotizacion.empresa.razon_social : '----- Proveedor Desconocido -----',
                        observaciones: cotizacion.observaciones,
                        importe: cotizacion.importe,
                        estado: this.getEstado(cotizacion.estado),
                        solicitud: cotizacion.solicitud.numero_folio_format,
                        buttons: $.extend({}, {
                            show: true,
                            id: cotizacion.id,
                            delete: self.$root.can('eliminar_cotizacion_compra') && !cotizacion.asignada ? true : false,
                            edit: (cotizacion.asignada) ? false : true,
                            transaccion: {id:cotizacion.id, tipo:18},
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
