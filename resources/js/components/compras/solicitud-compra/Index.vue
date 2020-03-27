<template>
    <div class="row">
        <!-- <div class="col-12">
            <button @click="create_solicitud" v-if="" class="btn btn-app btn-info pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
        </div> -->
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
        name: "solicitud-compra-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: true},
                    { title: 'Fecha', field: 'observaciones', sortable: true },
                    { title: 'Tipo', field: 'id_empresa',  sortable: true  },
                    { title: 'Requerido', field: 'subtotal', tdClass: 'money', thClass: 'th_money', sortable: false },
                    { title: 'Estado', field: 'impuesto', tdClass: 'money', thClass: 'th_money', sortable: true },
                    { title: 'Observaciones', field: 'monto', tdClass: 'money', thClass: 'th_money', sortable: true },
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
                return this.$store.dispatch('compras/solicitud-compra/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('compras/solicitud-compra/SET_SOLICITUDES', data.data);
                        this.$store.commit('compras/solicitud-compra/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                        console.log('solicitudes', this.solicitudes);
                        
                    })
            },

            getEstado(estado) {
                
                let val = parseInt(estado);
                switch (val) {
                    case 0:
                        return {
                            color: '#f39c12',
                            descripcion: 'Registrado'
                        }
                    case 1:
                        return {
                            color: '#0073b7',
                            descripcion: 'Estimado Parcial'
                        }
                    case 2:
                        return {
                            color: '#00a65a',
                            descripcion: 'Estimado Total'
                        }
                    default:
                        return {
                            color: '#d2d6de',
                            descripcion: 'Desconocido'
                        }
                }
            }
        },
        computed: {
            solicitudes(){
                return this.$store.getters['compras/solicitud-compra/solicitudes'];
            },
            meta(){
                return this.$store.getters['compras/solicitud-compra/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            solicitudes: {
                handler(solicitudes) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = solicitudes.map((solicitud, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: solicitud.numero_folio_format,
                        observaciones: solicitud.observaciones,
                        id_empresa: solicitud.empresa,
                        estado: this.getEstado(solicitud.estado),
                        monto: solicitud.monto_format,
                        impuesto:solicitud.impuesto_format,
                        subtotal: solicitud.subtotal_format,
                        // buttons: $.extend({}, {
                        //     show: true,
                        //     id: solicitud.id,
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
