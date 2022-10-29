<template>
    <span>

        <div class="row">
            <div class="col-12">
                <cfdi-rep-pendiente-xls v-bind:query="query" />
                <button @click="descargarComunicados" class="btn btn-app btn-secondary float-right" title="Descargar">
                    <i class="fa fa-file-pdf"></i> Comunicados
                </button>
                <!--<button @click="descargar" class="btn btn-app btn-secondary float-right" title="Descargar">
                    <i class="fa fa-download"></i> Descargar XLS
                </button>
                <button @click="actualizarPolizas"  class="btn btn-app btn-secondary float-right" title="Actualizar Relación con Pólizas">
                    <i class="fa fa-sync"></i> Descargar Comunicados
                </button>-->
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
                </div>
                <!-- /.card -->
            </div>
        </div>
        <div class="row">
            <div class="card">

                <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'" />
                        </div>
                    </div>
                <!-- /.card-body -->
                </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </span>
</template>

<script>
import DateRangePicker from "../../../globals/DateRangePicker"
import CfdiRepPendienteXls from "./partials/CFDIREPPendienteXLS";

export default {
    name: "cfdi-rep-pendiente-index",
    components:{CfdiRepPendienteXls, DateRangePicker},

    data() {
        return {
            cargando: false,
            descargando: false,
            id_empresa: '',
            empresas: [],
            empresa_seleccionada: [],
            detalle_descarga :[],
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
                { title: 'Subtotal', field: 'subtotal', tdClass: 'td_money',  thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Descuento', field: 'descuento', tdClass: 'td_money',  thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Impuestos Retenidos', field: 'impuestos_retenidos', tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Impuestos Trasladados', field: 'impuestos_trasladados', tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Total', field: 'total',tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Moneda', field: 'moneda', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'TC', field: 'tipo_cambio',tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'BD SAO', field: 'base_datos', thComp: require('../../../globals/th-Filter').default},
                { title: 'Proyecto', field: 'obra', thComp: require('../../../globals/th-Filter').default},
                { title: 'Fecha Carga Proyecto', field: 'fecha_carga_proyecto'},
                { title: 'BD CTPQ', field: 'base_datos_ctpq',thComp: require('../../../globals/th-Filter').default},
                { title: 'Ejercicio', field: 'ejercicio',tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default},
                { title: 'Periodo', field: 'periodo', thComp: require('../../../globals/th-Filter').default},
                { title: 'Tipo Póliza', field: 'tipo_poliza', thComp: require('../../../globals/th-Filter').default},
                { title: 'Folio Póliza', field: 'folio_poliza', thComp: require('../../../globals/th-Filter').default},
                { title: 'Fecha Póliza', field: 'fecha_poliza',thComp: require('../../../globals/th-Date').default, sortable: true},
                { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: {
                include: ['empresa','proveedor', 'factura_repositorio', "poliza_cfdi"],
                scope:'repPendiente', sort: 'cfd_sat.fecha',  order: 'desc'
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
        descargarComunicados()
        {

            this.descargando = true;
            return this.$store.dispatch('fiscal/cfd-sat/descargarComunicados',
                {
                    params: this.query,
                })
                .then(data => {
                    this.$emit('success');
                }).finally(() => {
                    this.descargando = false;
                });

        }
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
                        moneda: ccfdi.moneda,
                        tipo_cambio: ccfdi.tipo_cambio,
                        estado: ccfdi.estado,
                        base_datos: ccfdi.factura_repositorio?ccfdi.factura_repositorio.base_datos:'',
                        obra: ccfdi.factura_repositorio?ccfdi.factura_repositorio.obra:'',
                        fecha_carga_proyecto: ccfdi.factura_repositorio?ccfdi.factura_repositorio.fecha_hora_carga_format:'',
                        uuid: ccfdi.uuid,
                        base_datos_ctpq: ccfdi.poliza_cfdi?ccfdi.poliza_cfdi.base_datos:'',
                        ejercicio: ccfdi.poliza_cfdi?ccfdi.poliza_cfdi.ejercicio:'',
                        periodo: ccfdi.poliza_cfdi?ccfdi.poliza_cfdi.periodo:'',
                        tipo_poliza: ccfdi.poliza_cfdi?ccfdi.poliza_cfdi.tipo:'',
                        folio_poliza: ccfdi.poliza_cfdi?ccfdi.poliza_cfdi.folio:'',
                        fecha_poliza: ccfdi.poliza_cfdi?ccfdi.poliza_cfdi.fecha_format:'',
                        monto: ccfdi.poliza_cfdi?ccfdi.poliza_cfdi.monto_format:'',
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
        ver_asociados_contabilidad:{
            handler(vac) {
                this.query.solo_asociados_contabilidad = vac
                this.query.offset = 0;
                this.paginate()
            },
        },
        ver_no_asociados_contabilidad:{
            handler(vnac) {
                this.query.solo_no_asociados_contabilidad = vnac
                this.query.offset = 0;
                this.paginate()
            },
        }
    },
}
</script>

<style scoped>

</style>
