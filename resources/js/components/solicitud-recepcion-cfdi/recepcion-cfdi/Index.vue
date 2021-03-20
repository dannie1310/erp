<template>
    <span>
        <div class="row" >
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
        </div>
            <!-- /.col -->
    </span>
</template>

<script>

    export default {
        name: "cfd-sat-index",

        data() {
            return {
                cargando: false,

                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Fecha de Registro', tdClass: 'fecha_hora', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date').default},
                    { title: 'Folio', field: 'folio', tdClass: 'td_numero_folio', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Cliente', field: 'cliente'},
                    { title: 'Proyecto', field: 'proyecto', thClass: 'th_c150',},
                    { title: 'UUID', tdClass: 'td_c280', field: 'uuid', tdComp: require('../../fiscal/cfd/cfd-sat/UUID').default, thComp: require('../../globals/th-Filter').default},
                    { title: 'Ti Com', field: 'tipo_comprobante'},
                    { title: 'Moneda', field: 'moneda'},
                    { title: 'TC', field: 'tipo_cambio'},
                    /*{ title: 'Subtotal', field: 'subtotal', tdClass: 'td_money'},
                    { title: 'Impuestos Trasladados', field: 'impuestos_trasladados', tdClass: 'td_money'},
                    { title: 'Impuestos Retenidos', field: 'impuestos_retenidos', tdClass: 'td_money'},*/
                    { title: 'Monto', field: 'monto', tdClass: 'td_money'},
                    { title: 'Estado', field: 'estado', tdClass: 'td_money'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    include: ['empresa','cfdi', 'obra'],
                    scope : ['porProyecto'],
                    sort: 'id',  order: 'desc'
                },
                daterange: null,
            }
        },
        mounted(){
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            paginate(){
                this.cargando=true;
                return this.$store.dispatch('recepcion-cfdi/solicitud-recepcion-cfdi/paginate', {params: this.query})
                    .then(data=>{

                    })
                    .finally(()=>{
                        this.cargando=false;
                    })
            },
        },
        computed: {
            solicitudes(){
                return this.$store.getters['recepcion-cfdi/solicitud-recepcion-cfdi/solicitudes'];
            },
            meta(){
                return this.$store.getters['recepcion-cfdi/solicitud-recepcion-cfdi/meta']
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
                    solicitudes.forEach(function (solicitud, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            fecha: solicitud.fecha_registro,
                            folio: solicitud.numero_folio,
                            cliente: solicitud.empresa.razon_social,
                            proyecto: solicitud.obra.nombre,
                            moneda: solicitud.cfdi.moneda,
                            monto: solicitud.cfdi.total_format,
                            subtotal: solicitud.cfdi.subtotal_format,
                            impuestos_trasladados: solicitud.cfdi.impuestos_trasladados_format,
                            impuestos_retenidos: solicitud.cfdi.impuestos_retenidos_format,
                            tipo_cambio: solicitud.cfdi.tipo_cambio,
                            tipo_comprobante: solicitud.cfdi.tipo_comprobante,
                            estado: solicitud.estado_format,
                            uuid: $.extend({}, {
                                id: solicitud.cfdi.id,
                                uuid: solicitud.cfdi.uuid,
                            }),
                            buttons: $.extend({}, {
                                id: solicitud.id,
                                aprobar : solicitud.estado == 0 ? true : false,
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
            },
        },
    }
</script>

<style scoped>

</style>
