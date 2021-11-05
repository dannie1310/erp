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
        name: "comparativa-cotizacion-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:"index_corto", sortable: false },
                    { title: 'Folio', field: 'numero_folio', thClass: 'th_c120', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha', field: 'fecha', thComp: require('../../globals/th-Date').default, thClass: 'th_fecha', sortable: false },
                    { title: 'No. Invitaciones', tdClass: 'td_money', field: 'numero_invitaciones'},
                    { title: 'No. Cotizaciones', tdClass: 'td_money', field: 'numero_cotizaciones'},
                    { title: 'Concepto', field: 'concepto', sortable: false, thComp: require('../../globals/th-Filter').default },
                    { title: 'Estatus', field: 'estado_solicitud', sortable: false, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: '', field: 'buttons', thClass: 'icono', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {scope: ['areasCompradorasAsignadasParaSolicitudes','cotizadaOConInvitacion'], sort: 'numero_folio', order: 'DESC', include: ['complemento']},
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
            getEstado(estado, color) {
                return {
                    color: color,
                    descripcion: estado
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
                        observaciones: solicitud.observaciones,
                        concepto: solicitud.concepto,
                        numero_invitaciones: solicitud.numero_invitaciones,
                        numero_cotizaciones: solicitud.numero_cotizaciones,
                        numero_folio_compuesto: solicitud.numero_folio_compuesto,
                        estado_solicitud: this.getEstado(solicitud.complemento ? solicitud.complemento.descripcion_estado : '', solicitud.complemento ? solicitud.complemento.color : ''),
                        buttons: $.extend({}, {
                            show: true,
                            solicitud_consulta : false,
                            aprobar: (self.$root.can('aprobar_solicitud_compra') && (solicitud.estado == 0) && (solicitud.autorizacion_requerida == 1)) ? true : false,
                            delete: (self.$root.can('eliminar_solicitud_compra') && (solicitud.estado == 0) && solicitud.complemento) ? true : false,
                            edit: (self.$root.can('editar_solicitud_compra') && solicitud.estado == 0  && solicitud.complemento) ? true : false,
                            id: solicitud.id,
                            transaccion: {id:solicitud.id, tipo:17},
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
