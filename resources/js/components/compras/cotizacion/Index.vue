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
                    { title: 'Núm de Folio', field: 'numero_folio', tdClass: 'folio', sortable: true},
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Proveedor', field: 'empresa', sortable: false },
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    { title: 'Importe', field: 'importe', tdClass: 'money', sortable: false },
                    { title: 'Estatus', field: 'estado', sortable: false, tdClass: 'folio', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Núm de Folio de la Solicitud', tdClass: 'folio', field: 'solicitud',  tdComp: require('../solicitud-compra/partials/ActionButtons').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'numero_folio', order: 'DESC', include: ['solicitud', 'empresa']},
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
                            color: '#f39c12',
                            descripcion: 'Por Autorizar'
                        }
                    case 1:
                        return {
                            color: '#00a65a',
                            descripcion: 'Autorizada'
                        }
                    case 2:
                        return {
                            color: '#7889d6',
                            descripcion: 'Tercer caso'
                        }
                    case 8:
                        return {
                            color: '#e3f702',
                            descripcion: 'Pendiente de cotización'
                        }    
                    default:
                        return {
                            color: '#d2d6de',
                            descripcion: 'Sin solicitud'
                        }
                }
            },
            create() {
                this.$router.push({name: 'cotizacion-create'});
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
                        estado: this.getEstado((cotizacion.estado === 0) ? 8 :((cotizacion.solicitud) ? cotizacion.solicitud.estado : null)),
                        solicitud: $.extend({}, {
                            show: (cotizacion.solicitud) ? true : false,
                            id: (cotizacion.solicitud) ? cotizacion.solicitud.id : null,
                            cotizacion: (cotizacion.solicitud) ? cotizacion.solicitud : null
                        }),
                        buttons: $.extend({}, {
                            show: true,
                            id: cotizacion.id,
                            delete: self.$root.can('eliminar_cotizacion_compra')  ? true : false,
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
