<template>
    <span>

        <div class="row">
            <div class="col-12">
                <impresion-informe-r-e-p-faltante></impresion-informe-r-e-p-faltante>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
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
        </div>
        <!-- /.col -->
    </span>
</template>

<script>
import DateRangePicker from "../../../globals/DateRangePicker"
import CfdiRepPendienteXls from "./partials/CFDIREPPendienteXLS";
import ImpresionInformeREPFaltante from "../partials/ImpresionInformeREPFaltante";

export default {
    name: "cfdi-rep-pendiente-proveedor-index",
    components:{ImpresionInformeREPFaltante, CfdiRepPendienteXls, DateRangePicker},

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
                { title: 'RFC Proveedor', field: 'rfc_proveedor',thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Proveedor', field: 'proveedor', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: ' # CFDI Emitidos', field: 'cantidad_cfdi',tdClass: 'td_money',thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Monto CFDI', field: 'total_cfdi',tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Monto REP', field: 'total_rep',tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Pendiente REP', field: 'pendiente_rep',tdClass: 'td_money', thComp: require('../../../globals/th-Filter').default, sortable: true},
                { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtonsPorProveedor').default},
            ],
            data: [],
            total: 0,
            query: {
                include: [],
                sort: 'pendiente_rep',  order: 'desc', limit: '40'
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
            return this.$store.dispatch('fiscal/proveedor-rep/paginate', {params: this.query})
                .then(data=>{

                })
                .finally(()=>{
                    this.cargando=false;
                })
        },

    },
    computed: {
        proveedores_rep(){
            return this.$store.getters['fiscal/proveedor-rep/proveedores_rep'];
        },
        meta(){
            return this.$store.getters['fiscal/proveedor-rep/meta']
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        proveedores_rep: {
            handler(proveedores_rep) {
                let self = this
                self.$data.data = []
                proveedores_rep.forEach(function (proveedor_rep, i) {
                    self.$data.data.push({
                        index: (i + 1) + self.query.offset,
                        proveedor: proveedor_rep.proveedor,
                        rfc_proveedor: proveedor_rep.rfc_proveedor,
                        cantidad_cfdi: proveedor_rep.cantidad_cfdi,
                        total_cfdi: proveedor_rep.total_cfdi,
                        total_rep: proveedor_rep.total_rep,
                        pendiente_rep: proveedor_rep.pendiente_rep,
                        es_empresa_hermes: proveedor_rep.es_empresa_hermes,

                        buttons: $.extend({}, {
                            id: proveedor_rep.id,
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
