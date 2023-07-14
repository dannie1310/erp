<template>
    <span>
        <div v-if="layout">
            <div class="row">
                <div class="col-md-12 table-responsive" style="overflow-y: auto;max-height: 600px;">
                    <table class="table table-sm table-fs-sm">
                        <thead>
                            <tr>
                                <th class="index_corto">#</th>
                                <th >Obra</th>
                                <th >BBDD Contpaq</th>
                                <th >RFC Empresa</th>
                                <th >Empresa</th>
                                <th >RFC Proveedor</th>
                                <th >Proveedor</th>
                                <th >Fecha Factura</th>
                                <th >Folio Factura</th>
                                <th >Importe</th>
                                <th >Moneda</th>
                                <th >TC</th>
                                <th >Importe MXN</th>
                                <th >Saldo</th>
                                <th >TC Saldo</th>
                                <th >Saldo MXN</th>
                                <th >UUID Correspondiente</th>
                                <th ></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(partida, i) in pasivos">
                                <td>{{ i + 1 }}</td>
                                <td>{{partida.obra}}</td>
                                <td>{{partida.bbdd_contpaq}}</td>
                                <td>{{partida.rfc_empresa}}
                                    <i class="fa fa-file-invoice" style="color: red" v-if="partida.coincide_rfc_empresa == false"></i>
                                    <i class="fa fa-file-invoice" style="color: green" v-else-if="partida.coincide_rfc_empresa == true"></i>
                                </td>
                                <td>{{partida.empresa}}</td>
                                <td>{{partida.rfc_proveedor}}
                                    <i class="fa fa-file-invoice" style="color: red" v-if="partida.coincide_rfc_proveedor == false"></i>
                                    <i class="fa fa-file-invoice" style="color: green" v-else-if="partida.coincide_rfc_proveedor == true"></i>
                                </td>
                                <td>{{partida.proveedor}}</td>
                                <td>{{partida.fecha_factura}}
                                    <i class="fa fa-file-invoice" style="color: red" v-if="partida.coincide_fecha == false"></i>
                                    <i class="fa fa-file-invoice" style="color: green" v-else-if="partida.coincide_fecha == true"></i>
                                </td>
                                <td>{{partida.folio_factura}}
                                    <i class="fa fa-file-invoice" style="color: red" v-if="partida.coincide_folio == false"></i>
                                    <i class="fa fa-file-invoice" style="color: green" v-else-if="partida.coincide_folio == true"></i>
                                </td>
                                <td style="text-align: right">{{partida.importe_factura}}
                                    <i class="fa fa-file-invoice" style="color: red" v-if="partida.coincide_importe == false"></i>
                                    <i class="fa fa-file-invoice" style="color: green" v-else-if="partida.coincide_importe == true"></i>
                                </td>
                                <td>{{partida.moneda_factura}}
                                    <i class="fa fa-file-invoice" style="color: red" v-if="partida.coincide_moneda == false"></i>
                                    <i class="fa fa-file-invoice" style="color: green" v-else-if="partida.coincide_moneda == true"></i>
                                </td>
                                <td style="text-align: right">{{partida.tc_factura}}</td>
                                <td style="text-align: right">{{partida.importe_mxn}}</td>
                                <td style="text-align: right">{{partida.saldo}}</td>
                                <td style="text-align: right">{{partida.tc_saldo}}</td>
                                <td style="text-align: right">{{partida.saldo_mxn}}
                                    <i class="fa fa-times-circle" style="color: red" v-if="partida.inconsistencia_saldo == true"></i>
                                    <i class="fa fa-check-circle" style="color: green" v-else-if="partida.inconsistencia_saldo == false"></i>
                                </td>
                                <td>
                                    <CFDI v-if="partida.factura" v-bind:txt="partida.factura.uuid" v-bind:id="partida.factura.id" @click="partida.factura.id"
                                    v-bind:cancelado="partida.factura.cancelado"></CFDI>
                                    <span v-else>
                                        {{partida.uuid}}
                                    </span>
                                </td>
                                <td>
                                    <pasivos-edit
                                    v-bind:pasivo_parametro="partida"></pasivos-edit>
                                    <!--
                                    <button type="button" class="btn btn-sm btn-outline-success" title="Buscar CFDI" @click="buscaCFDI(partida, $event)" :disabled="actualizando">
                                        <i class="fa fa-file-invoice-dollar"></i>
                                    </button>
                                    -->

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </span>

</template>

<script>

import CFDI from "../../../fiscal/cfd/cfd-sat/CFDI.vue";
import PasivosEdit from "../Edit.vue";

export default {
    name: "layout-partial-show-lista-pasivos-validar",
    components: {PasivosEdit, CFDI},
    props : ['id','layout_parametro'],
    data(){
        return {
            cargando:false,
        }
    },
    methods:{
        buscaCFDI(partida_pasivo, event) {

            let _self = this;

            return this.$store.dispatch('contabilidadGeneral/layout-pasivo/findCFDI',
                {
                    id_pasivo: partida_pasivo.id,
                    data: {id_pasivo: partida_pasivo.id},
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {

                }).finally(() => {

                });

        },
    },

    computed: {
        layout(){
            return this.$store.getters['contabilidadGeneral/layout-pasivo/currentLayout'];
        },
        pasivos(){
            return this.$store.getters['contabilidadGeneral/layout-pasivo-partida/pasivos'];
        },
        actualizando() {
            return this.$store.getters['contabilidadGeneral/layout-pasivo/actualizando'];
        },
    },
}
</script>

<style scoped>

tr.hover td{
    background-color: #b8daa9;
}

tr.click td{
    background-color: #50b920;
}

.form-control {
    font-size: 10px !important;
}
table {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}
table.table-fs-sm{
    font-size: 10px;
}

table th,  table td {
    border: 1px solid #dee2e6;
}

table thead th
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: bold;
    color: black;
    overflow: hidden;
    text-align: center;

    position: sticky;
    position: -webkit-sticky;
    top: 0;
    z-index: 2;
}

table thead th.no_negrita
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: normal;
    color: black;
    overflow: hidden;
    text-align: center;
}

table td.sin_borde {
    border: none;
    padding: 2px 5px;
}

table thead th {
    text-align: center;
}
table tbody tr
{
    border-width: 0 1px 1px 1px;
    border-style: none solid solid solid;
    border-color: white #CCCCCC #CCCCCC #CCCCCC;
}
table tbody td,
table tbody th
{
    border-right: 1px solid #ccc;
    color: #242424;
    line-height: 20px;
    overflow: hidden;
    padding: 2px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}

.encabezado{
    text-align: center;
    background-color: #f2f4f5;
    font-weight: bold;
}

.sin_borde{
    border:none; background-color:#FFF
}
</style>
