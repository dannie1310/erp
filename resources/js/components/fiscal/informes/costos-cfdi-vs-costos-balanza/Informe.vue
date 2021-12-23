<template>
    <span>
        <div class="row" v-if="cargando">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else-if="cargando== false">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm">
                            <tr>
                                <td class="sin_borde c400"><b>Empresas:</b></td>
                                <td class="c90 sin_borde"  ><b>Año:</b></td>
                                <td class="c100 sin_borde"></td>
                            </tr>
                            <tr>
                                <td class="sin_borde">
                                     <model-list-select
                                        :disabled="cargando"
                                        name="empresa_sat"
                                        :placeholder="!cargando?'Seleccionar Empresa':'Cargando...'"
                                        data-vv-as="Empresa"
                                        v-model="empresa_sat"
                                        v-validate="{required: true}"
                                        option-value="id"
                                        option-text="label"
                                        :list="empresas_sat"
                                        :isError="errors.has(`empresa_sat`)">
                                    </model-list-select>
                                </td>

                                <td class="c100 sin_borde">
                                    <select id="dob"
                                    class="form-control"
                                    v-model="anio_input"
                                    >
                                      <option v-for="year in years" :value="year">{{ year }}</option>
                                    </select>
                                </td>
                                <td class="sin_borde" style="padding-top: 3px; width: 100px">
                                    <button type="button" class="btn btn-secondary" v-on:click="getInforme" ><i class="fa fa-filter"></i>Filtrar</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr />
                <div class="row" >
                    <div class="col-md-12 table-responsive" style="overflow-y: auto;height: 600px;">
                        <span><b>{{this.empresa_sat_razon_social}}</b></span>
                        <table class="table table-sm" id="sticky">
                            <thead >
                                <tr>
                                    <th class="index_corto">#</th>
                                    <th class="c100">Mes</th>
                                    <th class="c100">Costos y Gastos Deducibles <br>(según Balanza)</th>
                                    <th class="c100">CFDI recibidos <br>por mes (Neto)</th>
                                    <th class="c100">(-) CFDI TIPO I <br>Sustitución de ejercicios anteriores</th>
                                    <th class="c100">(+) CFDI TIPO E <br>Relacionados a un ejercicio anterior</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(partida, i) in informe.partidas">
                                    <td>{{i+1}}</td>
                                    <td>{{partida.mes}}</td>
                                    <td style="text-align: right">{{partida.costo_balanza}}</td>
                                    <td style="text-align: right; text-decoration: underline; cursor: pointer" v-on:click="verCFDI(partida)">{{partida.costo_cfdi}}</td>
                                    <td style="text-align: right">{{partida.sustitucion_ejercicios_anteriores}}</td>
                                    <td style="text-align: right">{{partida.relacion_ejercicios_anteriores}}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2"><b>Suma:</b></td>
                                <td style="text-align: right">{{informe.sumatorias.suma_costos_balanza}}</td>
                                <td style="text-align: right">{{informe.sumatorias.suma_costos_cfdi}}</td>
                                <td style="text-align: right">{{informe.sumatorias.suma_sustitucion_ejercicios_anteriores}}</td>
                                <td style="text-align: right">{{informe.sumatorias.suma_relacion_ejercicios_anteriores}}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="modal_cfdi" tabindex="-1" role="dialog">
                 <div class="modal-dialog modal-xl" id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-file-invoice-dollar"></i> Lista de CFDI</h4>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                        </div>
                        <div class="modal-body " ref="body">
                            <div class="row">
                                <div class="col-md-12 table-responsive" style="overflow-y: auto;height: 600px;">
                                    <span class="pull-right"><h6>{{ total_cfdi }}</h6></span>
                                    <table class="table table-sm table-fs-sm">
                                        <thead >
                                            <tr>
                                                <th class="index_corto encabezado">#</th>
                                                <th class="encabezado">Serie y Folio</th>
                                                <th class="encabezado">Tipo</th>
                                                <th class="encabezado">Fecha</th>
                                                <th class="encabezado">Moneda</th>
                                                <th class="encabezado">TC</th>
                                                <th class="encabezado">Descuento</th>
                                                <th class="encabezado">Descuento MXN</th>
                                                <th class="encabezado">Subtotal</th>
                                                <th class="encabezado">Subtotal MXN</th>
                                                <th class="encabezado">Subtotal - Descuento MXN</th>
                                                <th class="encabezado">Total</th>
                                                <th class="encabezado">Total MXN</th>
                                                <th class="encabezado">Fue Reemplazado</th>
                                                <th class="encabezado">Fecha CFDI Reemplazo</th>
                                                <th class="encabezado">Es Reemplazo</th>
                                                <th class="encabezado">Fecha CFDI Original</th>
                                                <th class="encabezado">Obra SAO</th>
                                                <th class="encabezado">Empresa Contpaq</th>
                                                <th class="encabezado"></th>
                                            </tr>
                                        </thead>
                                        <tr v-for="(cfdi, i) in lista_cfdi">
                                            <td>{{i+1}}</td>
                                            <td>{{cfdi.serie}} {{cfdi.folio}}</td>
                                            <td>{{cfdi.tipo_comprobante}}</td>
                                            <td>{{cfdi.fecha}}</td>
                                            <td>{{cfdi.moneda}}</td>
                                            <td style="text-align: right">
                                                <span v-if="cfdi.tc_xls != null">
                                                    ${{ parseFloat(cfdi.tc_xls).formatMoney(2,".",",") }}
                                                </span>
                                                <span v-else>
                                                    ${{ parseFloat(cfdi.tipo_cambio).formatMoney(2,".",",") }}
                                                </span>
                                            </td>

                                            <td style="text-align: right">
                                                <span v-if="cfdi.descuento <0">(</span>${{ parseFloat(Math.abs(cfdi.descuento)).formatMoney(2,".",",") }}<span v-if="cfdi.descuento <0">)</span>
                                            </td>
                                            <td style="text-align: right">
                                                <span v-if="cfdi.descuento_mxn <0">(</span>${{ parseFloat(Math.abs(cfdi.descuento_mxn)).formatMoney(2,".",",") }}<span v-if="cfdi.descuento_mxn <0">)</span>
                                            </td>
                                            <td style="text-align: right">
                                                <span v-if="cfdi.subtotal <0">(</span>${{ parseFloat(Math.abs(cfdi.subtotal)).formatMoney(2,".",",") }}<span v-if="cfdi.subtotal <0">)</span>
                                            </td>
                                            <td style="text-align: right">
                                                <span v-if="cfdi.subtotal_mxn <0">(</span>${{ parseFloat(Math.abs(cfdi.subtotal_mxn)).formatMoney(2,".",",") }}<span v-if="cfdi.subtotal_mxn <0">)</span>
                                            </td>
                                            <td style="text-align: right">
                                                <span v-if="cfdi.subtotal_a_sumar <0">(</span>${{ parseFloat(Math.abs(cfdi.subtotal_a_sumar)).formatMoney(2,".",",") }}<span v-if="cfdi.subtotal_a_sumar <0">)</span>
                                            </td>

                                            <td style="text-align: right">
                                                <span v-if="cfdi.total_xls != null">
                                                    <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total_xls).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                </span>
                                                <span v-else>
                                                    <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                </span>

                                            </td>


                                            <template v-if="cfdi.moneda !='MXN'">
                                                 <td style="text-align: right" v-if="cfdi.tc_xls != null">
                                                    <span v-if="cfdi.total_xls != null">
                                                        <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total_xls * cfdi.tc_xls).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                    </span>
                                                    <span v-else>
                                                        <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total * cfdi.tc_xls).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                    </span>
                                                </td>
                                                <td style="text-align: right" v-else>
                                                    <span v-if="cfdi.total_xls != null">
                                                        <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total_xls * cfdi.tipo_cambio).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                    </span>
                                                    <span v-else>
                                                        <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total * cfdi.tipo_cambio).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                    </span>
                                                </td>
                                            </template>
                                            <td style="text-align: right" v-else>
                                                <span v-if="cfdi.total_xls != null">
                                                    <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total_xls).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                </span>
                                                <span v-else>
                                                    <span v-if="cfdi.tipo_comprobante == 'E'">(</span>${{ parseFloat(cfdi.total).formatMoney(2,".",",") }}<span v-if="cfdi.tipo_comprobante == 'E'">)</span>
                                                </span>
                                            </td>


                                            <td style="text-align: center">
                                                <span v-if="cfdi.id_reemplaza>0"><i class="fa fa-check"></i></span>
                                            </td>
                                            <td>
                                                <CFDI v-if="cfdi.id_reemplaza >0" v-bind:txt="cfdi.fecha_reemplaza" v-bind:id="cfdi.id_reemplaza" @click="cfdi.id_reemplaza" ></CFDI>
                                            </td>
                                            <td style="text-align: center">
                                                <span v-if="cfdi.id_reemplazado>0"><i class="fa fa-check"></i></span>
                                            </td>
                                            <td>
                                                <CFDI v-if="cfdi.id_reemplazado >0" v-bind:txt="cfdi.fecha_reemplazado" v-bind:id="cfdi.id_reemplazado" @click="cfdi.id_reemplazado" ></CFDI>
                                            </td>
                                            <td>
                                                {{cfdi.obra_sao}}
                                            </td>
                                            <td>
                                                {{cfdi.empresa_contpaq}}
                                            </td>
                                            <td style="width: 90px">
                                                <CFDI v-bind:id="cfdi.id" @click="cfdi.id" ></CFDI>
                                                <DescargaCFDI v-bind:id="cfdi.id"></DescargaCFDI>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="10" style="border: none">

                                            </td>
                                            <td style="text-align: right; border: none" >
                                                <b>{{ total_cfdi }}</b>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times" ></i>
                                Cerrar
                            </button>
                        </div>
                     </div>
                 </div>
            </div>
    </span>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import {es} from "vuejs-datepicker/dist/locale";
import CFDI from "../../cfd/cfd-sat/CFDI";
import DescargaCFDI from "../../cfd/cfd-sat/DescargaCFDI";
import PDFPoliza from "../../../contabilidad-general/poliza/partials/PDFPoliza";
import {ModelListSelect} from 'vue-search-select';
import PolizaShowModal from "../../../contabilidad-general/poliza/ShowModal";

export default {
    name: "Informe",
    components: {PolizaShowModal, PDFPoliza, DescargaCFDI, CFDI, Datepicker, ModelListSelect},
    data() {
        return {
            informe : [],
            cuentas : [],
            cargando: false,
            importe_cuentas : 0,
            fechasDeshabilitadas:{},
            fecha_inicial : new Date("2020/01/01"),
            fecha_final : new Date("2020/12/31"),
            anio_input : new Date().getFullYear(),
            anio : new Date().getFullYear(),
            con2132 : 0,
            empresa_sat_razon_social : '',
            fecha_inicial_input : new Date("2020/01/01"),
            fecha_final_input : new Date("2020/12/31"),
            es:es,
            razon_social : '',
            rfc : '',
            total_cfdi : '',
            lista_cfdi : [],
            empresas_seleccionadas :[],
            empresas_seleccionadas_filtro :[],
            empresas : [],
            movimientos : [],
            importe_movimientos : 0,
            codigo_cuenta : '',
            nombre_cuenta : '',
            empresa_contpaq:'',
            empresa_sat : 1,
            empresas_sat:[],
            empresa_sat_seleccionada:'',
            sin_proveedor : {},
            abriendo_modal : 0,
        }
    },
    mounted() {
        this.getInforme();
    },
    props: ['id'],
    methods: {
        getInforme() {
            this.cargando = true;
            this.anio = this.anio_input;
            this.empresa_sat_seleccionada = this.empresa_sat;
            return this.$store.dispatch('fiscal/cfd-sat/obtenerInformeCostosCFDIvsCostosBalanza', {
                id:this.id,
                empresa_sat : this.empresa_sat_seleccionada,
                anio : this.anio,
            })
            .then(data => {
                this.informe = data.informe;
                this.empresas_sat = data.informe.empresas_sat;
                this.empresa_sat_razon_social = data.informe.empresa;
                this.anio = data.informe.anio;
            })
            .finally(() => {
                this.cargando = false;
            });
        },
        verCFDI(partida, tipo = 1)
        {
            if(this.abriendo_modal == 0) {
                this.abriendo_modal = 1;
                return this.$store.dispatch('fiscal/cfd-sat/getListaCFDIMesAnio', {
                    empresa_sat: this.empresa_sat_seleccionada,
                    mes: partida.id_mes,
                    anio: this.anio_input
                })
                    .then(data => {
                        this.lista_cfdi = data.informe;
                        this.razon_social = partida.razon_social;
                        this.rfc = partida.rfc;
                        this.total_cfdi = data.total;
                    })
                    .finally(() => {
                        $(this.$refs.modal_cfdi).appendTo('body')
                        $(this.$refs.modal_cfdi).modal('show');
                        this.abriendo_modal = 0;
                    });
            }
        },
        over(e){
            let tr = $(e.target).parent();
            tr.addClass("hover");
        },
        out(e){
            let tr = $(e.target).parent();
            tr.removeClass("hover");
        },
        click(e){
            let tr = $(e.target).parent();
            if(tr.hasClass("click")){
                tr.removeClass("click");
            }else {
                tr.addClass("click");
            }
        }

    },
    computed: {
        years () {
            const year = new Date().getFullYear();
            let anios = Array.from({length: year - 2000}, (value, index) => 2000 + index);
            anios.push(year);
            return anios;
        }
    },
    watch: {
        empresa_sat(value) {
            if(value !== '' && value !== null && value !== undefined){
                var busqueda = this.empresas_sat.find(x=>x.id === value);
                if(busqueda != undefined)
                {
                    /*this.fecha_inicial = new Date(busqueda.fecha_inicial);
                    this.fecha_final = new Date(busqueda.fecha_final);
                    this.fecha_inicial_input = this.fecha_inicial;
                    this.fecha_final_input = this.fecha_final;
                    this.fechasDeshabilitadas.to = this.fecha_inicial_input;
                    this.fechasDeshabilitadas.from = this.fecha_final_input;*/
                }
            }
        },
    }
}
</script>

<style scoped>
tr.sin_proveedor td {
    color: #e50c25;
    font-weight: bold;
}
tr.hover td{
    background-color: #b8daa9;
}

tr.click td{
    background-color: #50b920;
}

.form-control {
    font-size: 12px !important;
}
.btn {
    font-size: 12px;
    padding: 0.25rem 0.75rem;
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
    font-size: 8px;
}

.ui.dropdown, .ui.dropdown input {
    font-size: 12px;
}

table th,  table td {
    border: 1px solid #dee2e6;
}
table tfoot td{
    text-align: right;
    border-bottom: 2px #000 solid;
    border-top: 1px #000 solid;
    background-color: #f2f4f5;
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
