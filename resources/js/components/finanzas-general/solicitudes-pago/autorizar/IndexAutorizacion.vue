<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'" />
                        </div>
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
    import ActionButtons from "./partials/ActionButtons";
    export default {
        name: "autorizar-solicitud-pago-index-autorizacion",
        components: {ActionButtons},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', tdClass: 'td_index_corto', sortable: false },
                    { title: 'Proyecto', field: 'proyecto',sortable: false},
                    { title: 'Número Folio', field: 'numero_folio', tdClass:'center', thClass: 'th_c80', sortable: false},
                    { title: 'Fecha', tdClass:'center', field: 'fecha', thClass: 'th_c80', sortable: false},
                    { title: 'Proveedor', field: 'razon_social', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'RFC', field: 'rfc', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Moneda', field: 'moneda', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Monto', field: 'monto',  tdClass: 'money', thClass: 'th_c100', sortable: true},
                    { title: 'Acciones', field: 'buttons',  thClass:'th_c100', tdClass:'center', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: { sort: '', order: '', scope:'autorizacionPendiente'},
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
                return this.$store.dispatch('finanzas-general/solicitud-pago/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas-general/solicitud-pago/SET_SOLICITUDES', data.data);
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
        },
        computed: {
            solicitudes(){
                return this.$store.getters['finanzas-general/solicitud-pago/solicitudes'];
            },
            meta(){
                return this.$store.getters['finanzas-general/solicitud-pago/meta'];
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
                        proyecto: solicitud.proyecto,
                        numero_folio: solicitud.numero_folio,
                        fecha: solicitud.fecha,
                        razon_social: solicitud.razon_social,
                        rfc: solicitud.rfc,
                        referencia: solicitud.referencia,
                        moneda: solicitud.moneda,
                        monto: solicitud.monto,
                        buttons: $.extend({}, {
                            id : solicitud.id,
                            autorizar: self.$root.can('autorizar_rechazar_solicitud_pago', true) && solicitud.estado == 0 || solicitud.estado == 1 ? true : false,
                            rechazar: self.$root.can('autorizar_rechazar_solicitud_pago', true) && solicitud.estado == 0 || solicitud.estado == 1 ? true : false,
                        })
                    }));
                },
                deep: true
            },

            meta: {
                handler(meta) {
                    /*let total = meta.pagination.total
                    this.$data.total = total*/
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
