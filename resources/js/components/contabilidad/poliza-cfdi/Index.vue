<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="form-row">
                            <div class="col">
                                <DateRangePicker class="form-control" placeholder="Rango de Fechas" v-model="$data.daterange"/>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-switch" :disabled="ver_asociados">
                                    <input type="checkbox" class="custom-control-input" id="ver_pendientes" v-model="ver_pendientes" :disabled="ver_asociados">
                                    <label class="custom-control-label" for="ver_pendientes" :disabled="ver_asociados">Ver únicamente pendientes de CFDI</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch" :disabled="ver_pendientes">
                                    <input type="checkbox" class="custom-control-input" id="ver_asociados" v-model="ver_asociados" :disabled="ver_pendientes">
                                    <label class="custom-control-label" for="ver_asociados" :disabled="ver_pendientes">Ver únicamente asociados con CFDI</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
                <!-- /.col -->
        </div>
        <div class="row">
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
    </span>

</template>

<script>
    import DateRangePicker from "../../globals/DateRangePicker";
    export default {
        name: "lista-empresas-index",
        components: {DateRangePicker},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'BD CTPQ', field: 'base_datos_ctpq', thComp: require('../../globals/th-Filter').default},
                    { title: 'Empresa', field: 'empresa_ctpq', thComp: require('../../globals/th-Filter').default},
                    { title: 'Ejercicio', field: 'ejercicio',tdClass: 'td_money', thComp: require('../../globals/th-Filter').default},
                    { title: 'Periodo', field: 'periodo',tdClass: 'td_money', thComp: require('../../globals/th-Filter').default},
                    { title: 'Tipo Póliza', field: 'tipo_poliza',tdClass: 'td_money', thComp: require('../../globals/th-Filter').default},
                    { title: 'Folio Póliza', field: 'folio_poliza',tdClass: 'td_money', thComp: require('../../globals/th-Filter').default},
                    { title: 'Fecha Póliza', field: 'fecha_poliza',tdClass: 'td_money',thComp: require('../../globals/th-Date').default, sortable: true},
                    { title: 'CFDI', field: 'cfdi',tdClass: 'td_money', tdComp: require('./partials/ListaCFDI').default },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    scope : 'paraProyecto',
                    include: ['empresa','asociacion_cfdi.cfdi'],
                    sort: 'fecha',  order: 'desc'
                },
                daterange: null,
                search: '',
                cargando: false,
                sincronizando : false,
                ver_pendientes: false,
                ver_asociados: false,

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
                return this.$store.dispatch('contabilidad/poliza-cfdi/paginate', { params: this.query })
                    .then(data => {
                        this.$store.commit('contabilidad/poliza-cfdi/SET_POLIZAS', data.data);
                        this.$store.commit('contabilidad/poliza-cfdi/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
            },
        },

        computed: {
            polizas(){
                return this.$store.getters['contabilidad/poliza-cfdi/polizas'];
            },
            meta(){
                return this.$store.getters['contabilidad/poliza-cfdi/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },

        watch: {
            polizas: {
                handler(polizas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = polizas.map((poliza, i) => ({
                        index: (i + 1) + self.query.offset,
                        base_datos_ctpq: poliza.base_datos,
                        empresa_ctpq: poliza.empresa.nombre,
                        ejercicio: poliza.ejercicio,
                        periodo: poliza.periodo,
                        tipo_poliza: poliza.tipo,
                        folio_poliza: poliza.folio,
                        fecha_poliza: poliza.fecha_format,
                        monto: poliza.monto_format,
                        cfdi: poliza.asociacion_cfdi.data,
                        buttons: $.extend({}, {
                            id: poliza.id_poliza_contpaq,
                            id_empresa: poliza.empresa.id,
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
                handler () {
                    this.paginate()
                },
                deep: true
            },
            'daterange.startDate': {
                handler(sd) {
                    this.query.startDate = sd.format('YYYY-MM-DD')
                    this.query.offset = 0;
                    this.paginate()
                },
                deep: true
            },
            'daterange.endDate': {
                handler(ed) {
                    this.query.endDate = ed.format('YYYY-MM-DD')
                    this.query.offset = 0;
                    this.paginate()
                },
                deep: true
            },
            ver_pendientes:{
                handler(vp) {
                    this.query.solo_pendientes = vp
                    this.query.offset = 0;
                    this.paginate()
                },
            },
            ver_asociados:{
                handler(va) {
                    this.query.solo_asociados = va
                    this.query.offset = 0;
                    this.paginate()
                },
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

</style>
