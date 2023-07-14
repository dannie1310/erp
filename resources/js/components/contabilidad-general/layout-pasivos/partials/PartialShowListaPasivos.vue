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
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(partida, i) in layout.partidas.data">
                                <td>{{ i + 1 }}</td>
                                <td>{{partida.obra}}</td>
                                <td>{{partida.bbdd_contpaq}}</td>
                                <td>{{partida.rfc_empresa}}</td>
                                <td>{{partida.empresa}}</td>
                                <td>{{partida.rfc_proveedor}}</td>
                                <td>{{partida.proveedor}}</td>
                                <td>{{partida.fecha_factura}}</td>
                                <td>{{partida.folio_factura}}</td>
                                <td style="text-align: right">{{partida.importe_factura}}</td>
                                <td>{{partida.moneda_factura}}</td>
                                <td style="text-align: right">{{partida.tc_factura}}</td>
                                <td style="text-align: right">{{partida.importe_mxn}}</td>
                                <td style="text-align: right">{{partida.saldo}}</td>
                                <td style="text-align: right">{{partida.tc_saldo}}</td>
                                <td style="text-align: right">{{partida.saldo_mxn}}</td>
                                <td>
                                    <CFDI v-if="partida.factura" v-bind:txt="partida.factura.uuid" v-bind:id="partida.factura.id" @click="partida.factura.id" ></CFDI>
                                    <span v-else>
                                        {{partida.uuid}}
                                    </span>
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

export default {
    name: "layout-partial-show-lista-pasivos",
    components: {CFDI},
    props : ['id','layout_parametro'],
    data(){
        return {
            cargando:false,
        }
    },

    computed: {
        layout(){
            return this.$store.getters['contabilidadGeneral/layout-pasivo/currentLayout'];
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
