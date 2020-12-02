<template>
    <span>
        <div class="row">
            <div class="col-12">
                <RegistroMasivo/>
                <ProcesaDirectorio/>
                <button @click="descargar" class="btn btn-app btn-secondary float-right" title="Descargar">
                    <i class="fa fa-download"></i> Descargar
                </button>
            </div>
        </div>
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
    import RegistroMasivo from './RegistroMasivo'
    import ProcesaDirectorio from './ProcesaDirectorio'
    import DateRangePicker from "../../../globals/DateRangePicker"

    export default {
        name: "cfd-sat-index",
        components:{RegistroMasivo,ProcesaDirectorio, DateRangePicker},

        data() {
            return {
                cargando: false,
                descargando: false,
                id_empresa: '',
                empresas: [],
                empresa_seleccionada: [],
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Fecha', field: 'fecha',thComp: require('../../../globals/th-Date').default, sortable: true},
                    { title: 'Serie', field: 'serie',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Folio', field: 'folio',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Tipo', field: 'tipo_comprobante',thComp: require('../../../globals/th-Filter').default, sortable: false},
                    { title: 'UUID', field: 'uuid',thComp: require('../../../globals/th-Filter').default, sortable: false},
                    { title: 'RFC Receptor', field: 'rfc_receptor',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Receptor', field: 'receptor',thComp: require('../../../globals/th-Filter').default, sortable: false},
                    { title: 'RFC Emisor', field: 'rfc_emisor',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Emisor', field: 'emisor', thComp: require('../../../globals/th-Filter').default, sortable: false},
                    { title: 'Subtotal', field: 'subtotal', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Descuento', field: 'descuento', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Impuestos Retenidos', field: 'impuestos_retenidos', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Impuestos Trasladados', field: 'impuestos_trasladados', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Total', field: 'total',tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    include: ['empresa','proveedor'],
                    sort: 'fecha',  order: 'desc'
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
                return this.$store.dispatch('fiscal/cfd-sat/paginate', {params: this.query})
                    .then(data=>{

                    })
                    .finally(()=>{
                        this.cargando=false;
                    })
            },
            descargar(){
                this.descargando = true;
                return this.$store.dispatch('fiscal/cfd-sat/descargar',
                    {
                        params: this.query,
                    })
                    .then(data => {
                        this.$emit('success');
                    }).finally(() => {
                        this.descargando = false;
                    });
            },
        },
        computed: {
            cfdi(){
                return this.$store.getters['fiscal/cfd-sat/CFDSAT'];
            },
            meta(){
                return this.$store.getters['fiscal/cfd-sat/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            cfdi: {
                handler(cfdi) {
                    let self = this
                    self.$data.data = []
                    cfdi.forEach(function (ccfdi, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            fecha: ccfdi.fecha_format,
                            serie: ccfdi.serie,
                            folio: ccfdi.folio,
                            rfc_emisor: ccfdi.rfc_emisor,
                            rfc_receptor: ccfdi.rfc_receptor,
                            emisor: ccfdi.proveedor.razon_social,
                            tipo_comprobante: ccfdi.tipo_comprobante,
                            receptor: ccfdi.empresa.razon_social,
                            total: ccfdi.total_format,
                            subtotal: ccfdi.subtotal_format,
                            descuento: ccfdi.descuento_format,
                            impuestos_retenidos: ccfdi.impuestos_retenidos_format,
                            impuestos_trasladados: ccfdi.impuestos_trasladados_format,
                            uuid: ccfdi.uuid,
                            buttons: $.extend({}, {
                                id: ccfdi.id,
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
        },
    }
</script>

<style scoped>

</style>
