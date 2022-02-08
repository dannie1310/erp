<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <descarga-solicitud-pago v-bind:query="query"></descarga-solicitud-pago>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 9px'" />
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
                <!-- /.col -->
        </div>
    </span>
</template>

<script>

    import DescargaSolicitudPago from "./DescargaExcel";
    export default {
        name: "finanzas-general-indicador-solicitud-pago-index",
        components: {DescargaSolicitudPago},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'index_corto', sortable: false },
                    { title: 'Base de Datos', field: 'base_datos',sortable: true,  thClass:'th_c120', thComp: require('../../../globals/th-Filter').default},
                    { title: 'Obra', field: 'nombre_obra', thClass:'th_c100', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha de Solicitud', tdClass:'center', field: 'fecha_solicitud', sortable: true, thClass:'th_c60', },
                    { title: 'Folio de Solicitud', tdClass:'center', field: 'numero_folio', sortable: true, thClass:'th_c60', },
                    { title: 'Proveedor', field: 'razon_social',  sortable: true},
                    { title: 'Usuario Registro Solicitud', field: 'usuario_registro',  sortable: true},
                    { title: 'Monto de Solicitud', field: 'monto', tdClass: 'td_money80', thClass: 'th_c80', sortable: true},
                    { title: 'Remesa Relacionada', field: 'remesa_relacionada', thClass: 'th_c70',  sortable: true},
                    { title: 'Monto Autorizado en Remesa', field: 'monto_autorizado_remesa', tdClass: 'td_money80', thClass: 'th_c80',  sortable: true},
                    { title: 'Monto Pagado', field: 'monto_pagado', tdClass: 'td_money80', thClass: 'th_c80',  sortable: true},
                    { title: 'Monto Aplicado', field: 'monto_aplicado',  tdClass: 'td_money80', thClass: 'th_c80', sortable: true},
                    { title: 'Saldo', field: 'pendiente', tdClass: 'td_money80', thClass: 'th_c80', sortable: true},
                ],
                data: [],
                total: 0,
                query: {scope:['pendientes','registrosActivos'], sort: 'fecha_solicitud', order: 'desc', limit:'20'},
                cargando: false,
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
                        this.$store.commit('finanzas-general/solicitud-pago/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
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
                        base_datos: solicitud.base_datos,
                        nombre_obra: solicitud.nombre_obra,
                        fecha_solicitud: solicitud.fecha_solicitud_format,
                        numero_folio: solicitud.numero_folio_format,
                        monto: solicitud.monto_format,
                        remesa_relacionada: solicitud.remesa_relacionada,
                        monto_autorizado_remesa: solicitud.monto_autorizado_format,
                        monto_pagado: solicitud.monto_pagado_format,
                        monto_aplicado: solicitud.monto_aplicado_format,
                        pendiente: solicitud.pendiente_format,
                        razon_social : solicitud.razon_social,
                        usuario_registro : solicitud.usuario_registro,
                    }));
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

