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
                                <td class="c90 sin_borde" style="padding-top: 0.75rem" ><b>Fecha Inicial:</b></td>
                                <td class="c130 sin_borde"><datepicker
                                    id = "fechaInicial"
                                    v-model = "fecha_inicial_input"
                                    name = "fecha_inicio"
                                    :format = "formatoFecha"
                                    :language = "es"
                                    :bootstrap-styling = "true"
                                    class = "form-control"
                                    style="font-size: 10px"
                                    v-validate="{required: true}"
                                    :disabled-dates="fechasDeshabilitadas"
                                    :class="{'is-invalid': errors.has('fecha_inicio')}"
                                ></datepicker></td>
                                <td class="c90 sin_borde" style="padding-top: 0.75rem" ><b>Fecha Final:</b></td>
                                <td class="c130 sin_borde"><datepicker
                                    id = "fechaFinal"
                                    v-model = "fecha_final_input"
                                    name = "fecha_fin"
                                    :format = "formatoFecha"
                                    :language = "es"
                                    :bootstrap-styling = "true"
                                    class = "form-control"
                                    v-validate="{required: true}"
                                    :disabled-dates="fechasDeshabilitadas"
                                    :class="{'is-invalid': errors.has('fecha_fin')}"
                                ></datepicker></td>
                                <td class="sin_borde" style="padding-top: 6px">
                                    <button type="button" class="btn btn-secondary" v-on:click="getInforme" ><i class="fa fa-filter"></i>Filtrar</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr />
                <div class="row" >
                    <div class="col-md-12 table-responsive">
                        <table class="table table-sm table-fs-sm">
                            <thead >
                                <tr>
                                    <th rowspan="2" class="index_corto">#</th>
                                    <th rowspan="2" class="c100">RFC</th>
                                    <th rowspan="2">Razón Social</th>
                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="2">CFDI Tipo I</th>

                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="2">CFDI Tipo E</th>

                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="2">Neto CFDI</th>

                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="3">Contabilidad GHI</th>
                                </tr>
                                <tr>

                                    <th class="c80">Neto Tipo I</th>
                                    <th class="c80">Total Con IVA</th>

                                    <th class="c80">Neto Tipo E</th>
                                    <th class="c80">Total Con IVA</th>

                                    <th class="c80">Neto CFDI</th>
                                    <th class="c80">Total Con IVA</th>

                                    <th class="c80">Cantidad Cuentas Relacionadas</th>
                                    <th class="c80">Pasivos Registrados</th>
                                    <th class="c80">Diferencias</th>

                                </tr>

                            </thead>
                            <tbody>
                            <template v-for="(partida, i) in informe.partidas">
                                <tr class="sin_borde">
                                <td>
                                    {{i + 1}}
                                </td>
                                <td  v-on:click="verCFDI(partida)" style="text-decoration: underline; cursor: pointer">
                                    {{partida.rfc}}
                                </td>
                                <td>
                                    {{partida.razon_social}}
                                </td>
                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_subtotal_i) != 0">
                                        ${{parseFloat(partida.neto_subtotal_i).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_total_i) != 0">
                                        ${{parseFloat(partida.neto_total_i).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_subtotal_e) != parseFloat('0')">
                                        ${{parseFloat(partida.neto_subtotal_e).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_total_e) != 0">
                                        ${{parseFloat(partida.neto_total_e).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.neto_subtotal_sat).formatMoney(2,".",",") }}
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.neto_total_sat).formatMoney(2,".",",") }}
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right; text-decoration: underline" :style="parseFloat(partida.cantidad_cuentas)>0?`cursor : pointer`:``" v-on:click="verCuentas(partida)" v-if="parseFloat(partida.cantidad_cuentas)>0">
                                     {{parseFloat(partida.cantidad_cuentas) }}
                                </td>
                                 <td style="text-align: right" v-else>
                                    -
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.importe_movimientos_pasivo).formatMoney(2,".",",") }}
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.diferencia).formatMoney(2,".",",") }}
                                </td>

                            </tr>

                            </template>
                            </tbody>
                            <tfoot>
                            <tr class="sin_borde" v-if="1 == 0">
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_subtotal_i) != 0">
                                        ${{parseFloat(partida.neto_subtotal_i).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_total_i) != 0">
                                        ${{parseFloat(partida.neto_total_i).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_subtotal_e) != parseFloat('0')">
                                        ${{parseFloat(partida.neto_subtotal_e).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_total_e) != 0">
                                        ${{parseFloat(partida.neto_total_e).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.neto_subtotal_sat).formatMoney(2,".",",") }}
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.neto_total_sat).formatMoney(2,".",",") }}
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right; text-decoration: underline" :style="parseFloat(partida.cantidad_cuentas)>0?`cursor : pointer`:``" v-on:click="verCuentas(partida)" v-if="parseFloat(partida.cantidad_cuentas)>0">
                                     {{parseFloat(partida.cantidad_cuentas) }}
                                </td>
                                 <td style="text-align: right" v-else>
                                    -
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.importe_movimientos_pasivo).formatMoney(2,".",",") }}
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.diferencia).formatMoney(2,".",",") }}
                                </td>
                            </tr>

                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Cuentas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm table-fs-sm">
                                        <thead>
                                        <tr>
                                            <th class="index_corto">#</th>
                                            <th>Cuenta</th>
                                            <th>Monto</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(cuenta, i ) in cuentas">
                                                <td>{{i+1}}</td>
                                                <td>{{cuenta.codigo_cuenta}}</td>
                                                <td style="text-align: right">${{parseFloat(cuenta.importe_movimiento).formatMoney(2,".",",") }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                             <tr>
                                                <td style="text-align: right" colspan="2" class="sin_borde"><b>Total:</b></td>
                                                <td style="text-align: right" class="sin_borde">${{parseFloat(importe_cuentas).formatMoney(2,".",",") }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i>Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" ref="modal_cfdi" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
                 <div class="modal-dialog modal-xl" id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-file-invoice-dollar"></i> Lista de CFDI</h4>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                        </div>
                        <div class="modal-body " ref="body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm table-fs-sm">
                                        <tr>
                                            <td colspan="3" style="border: none"><h6>{{rfc}}</h6></td>
                                            <td colspan="5" style="border: none"><h6>{{razon_social}}</h6></td>
                                            <td colspan="7" style="border: none; text-align: right"><h6>${{ parseFloat(neto_total_sat).formatMoney(2,".",",") }}</h6></td>
                                        </tr>
                                        <tr>
                                            <th class="index_corto encabezado">#</th>
                                            <th class="encabezado">Serie</th>
                                            <th class="encabezado">Folio</th>
                                            <th class="encabezado">Tipo</th>
                                            <th class="encabezado">Fecha</th>
                                            <th class="encabezado">Moneda</th>
                                            <th class="encabezado">TC</th>
                                            <th class="encabezado">Total</th>
                                            <th class="encabezado">Total MXN</th>
                                            <th class="encabezado">Fue Reemplazado</th>
                                            <th class="encabezado">Fecha CFDI Reemplazo</th>
                                            <th class="encabezado">Es reemplazo</th>
                                            <th class="encabezado">Fecha CFDI Original</th>
                                            <th class="encabezado">Obra SAO</th>
                                            <th class="encabezado"></th>
                                        </tr>
                                        <tr v-for="(cfdi, i) in lista_cfdi">
                                            <td>{{i+1}}</td>
                                            <td>{{cfdi.serie}}</td>
                                            <td>{{cfdi.folio}}</td>
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
                                            <td style="width: 90px">
                                                <CFDI v-bind:id="cfdi.id" @click="cfdi.id" ></CFDI>
                                                <DescargaCFDI v-bind:id="cfdi.id"></DescargaCFDI>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" style="border: none">

                                            </td>
                                            <td style="text-align: right; border: none" >
                                                <b>${{ parseFloat(neto_total_sat).formatMoney(2,".",",") }}</b>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times-circle"  ></i>
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
import CFDI from "../cfd/cfd-sat/CFDI";
import DescargaCFDI from "../cfd/cfd-sat/DescargaCFDI";

export default {
    name: "Informe",
    components: {DescargaCFDI, CFDI, Datepicker},
    data() {
        return {
            informe : [],
            cuentas : [],
            cargando: false,
            importe_cuentas : 0,
            fechasDeshabilitadas:{},
            fecha_inicial : new Date("2020/01/01"),
            fecha_final : new Date("2020/12/31"),

            fecha_inicial_input : new Date("2020/01/01"),
            fecha_final_input : new Date("2020/12/31"),
            es:es,
            razon_social : '',
            rfc : '',
            neto_total_sat : '',
            lista_cfdi : []
        }
    },
    mounted() {
        this.getInforme();
        this.fechasDeshabilitadas.to = new Date("2020/01/01");
        this.fechasDeshabilitadas.from = new Date("2020/12/31");
    },
    props: ['id'],
    methods: {
        getInforme() {
            this.cargando = true;
            this.fecha_inicial = this.fecha_inicial_input;
            this.fecha_final = this.fecha_final_input;
            return this.$store.dispatch('fiscal/cfd-sat/obtenerInformeSATLP2020', {
                id:this.id,
                fecha_inicial : this.fecha_inicial,
                fecha_final : this.fecha_final,
            })
            .then(data => {
                this.informe = data.informe;
                //this.getMovimientos(this.informe.data[0])
            })
            .finally(() => {
                this.cargando = false;
            });
        },
        verCFDI(partida)
        {
            return this.$store.dispatch('fiscal/cfd-sat/getListaCFDI', {
                id_proveedor_sat: partida.id_proveedor_sat,
                fecha_inicial : this.fecha_inicial,
                fecha_final : this.fecha_final,
            })
            .then(data => {
                this.lista_cfdi = data;
                this.razon_social = partida.razon_social;
                this.rfc = partida.rfc;
                this.neto_total_sat = partida.neto_total_sat;
            })
            .finally(() => {
                $(this.$refs.modal_cfdi).appendTo('body')
                $(this.$refs.modal_cfdi).modal('show');
            });
        },
        getMovimientos(item)
        {
            this.$router.push({name: 'cuenta-saldo-negativo-detalle-movimientos', params: {aniomes: item.anio+'-'+item.mes}});
        },
        verCuentas(partida)
        {
            return this.$store.dispatch('fiscal/cfd-sat/obtenerCuentasInformeSATLP2020', {
                id: partida.id_proveedor_sat,
                fecha_inicial : this.fecha_inicial,
                fecha_final : this.fecha_final,
            })
            .then(data => {
                this.cuentas = data.informe;
                this.importe_cuentas = partida.importe_movimientos_pasivo;
            })
            .finally(() => {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            });
        },
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
    },
    computed: {
        anio_seleccionado(){
            return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/anioSeleccionado'];
        },

        mes_seleccionado(){
            return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/mesSeleccionado'];
        },
    },
}
</script>

<style scoped>
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
