<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_solicitud_compra')" class="btn btn-app btn-info pull-right">
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
        name: "solicitud-compra-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio', sortable: true},
                    { title: 'Fecha Requerido', field: 'fecha', sortable: true },
                    { title: 'Fecha / Hora Registro', field: 'fecha_registro', tdClass: 'money', thClass: 'th_money', sortable: false },
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    { title: 'Estatus', field: 'estado', sortable: true, tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
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

                    })
            },

            getEstado(estado) {

                let val = parseInt(estado);
                switch (val) {
                    case 0:
                        return {
                            color: '#f39c12',
                            descripcion: 'Registrada'
                        }
                    case 1:
                        return {
                            color: '#00a65a',
                            descripcion: 'Aprobada'
                        }
                    case 2:
                        return {
                            color: '#7889d6',
                            descripcion: 'Tercer caso'
                        }
                    default:
                        return {
                            color: '#d2d6de',
                            descripcion: 'Desconocido'
                        }
                }
            },
            create() {
                this.$router.push({name: 'solicitud-compra-create'});
            },
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
                        fecha: solicitud.fecha_format,
                        fecha_registro: solicitud.fecha_registro,
                        observaciones: solicitud.observaciones,
                        estado: this.getEstado(solicitud.estado),
                        buttons: $.extend({}, {
                            show: true,
                            aprobar: (self.$root.can('aprobar_solicitud_compra') && (solicitud.estado == 0)) ? true : false,
                            delete: self.$root.can('eliminar_solicitud_compra') ? true : false,
                            edit: (self.$root.can('editar_solicitud_compra') && (solicitud.estado == 0)) ? false : false,
                            id: solicitud.id,
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
