<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_cotizacion_proveedor',true)" class="btn btn-app btn-info pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
        </div>
        <div class="col-12">
            <div class="card">
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
                    { title: 'Folio', field: 'numero_folio', tdClass: 'folio', sortable: true},
                    { title: 'Solicitud', tdClass: 'folio', field: 'solicitud'},
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Proveedor', field: 'empresa', sortable: false },
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    { title: 'Importe', field: 'importe', tdClass: 'money', sortable: false },
                    { title: 'Estatus', field: 'estado', sortable: false, tdClass: 'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_m200', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: 'transaccion', scope: ['cotizacionRealizada','invitadoAutenticado'], sort: '', order: ''},
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
                return this.$store.dispatch('padronProveedores/invitacion/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('padronProveedores/invitacion/SET_INVITACIONES', data.data);
                        this.$store.commit('padronProveedores/invitacion/SET_META', data.meta);
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
            create() {
                this.$router.push({name: 'cotizacion-proveedor-seleccionar-solicitud'});
            },
        },
        computed: {
            invitaciones(){
                return this.$store.getters['padronProveedores/invitacion/invitaciones'];
            },
            meta(){
                return this.$store.getters['padronProveedores/invitacion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            invitaciones: {
                handler(invitaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = invitaciones.map((invitacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: invitacion.cotizacion.numero_folio_format,
                        solicitud: invitacion.transaccion.numero_folio_format,
                        fecha: invitacion.cotizacion.fecha_format,
                        empresa: invitacion.razon_social,
                        observaciones: invitacion.cotizacion.observaciones,
                        importe: invitacion.cotizacion.monto,
                        estado: this.getEstado(invitacion.cotizacion.estado),
                        buttons: $.extend({}, {
                            show: self.$root.can('consultar_cotizacion_proveedor') ? true : false,
                            id: invitacion.id,
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
