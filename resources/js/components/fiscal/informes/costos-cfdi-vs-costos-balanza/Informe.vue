<template>
    <span>
        <div class="row" v-if="!informe">
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
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm">
                            <tr>
                                <td class="sin_borde"><b>Empresas:</b></td>
                                <td class="c90 sin_borde"  ><b>Año:</b></td>
                                <td class="c90 sin_borde"></td>
                                <td class="c150 sin_borde"></td>
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

                                <td class="sin_borde">
                                    <select id="dob"
                                    class="form-control"
                                    v-model="anio_input"
                                    >
                                      <option v-for="year in years" :value="year">{{ year }}</option>
                                    </select>
                                </td>
                                <td class="sin_borde" style="padding-top: 3px;">
                                    <button type="button" class="btn btn-secondary" v-on:click="getInforme" :disabled="cargando" >
                                        <i class="fa fa-filter" v-if="!cargando"></i>
                                        <i class="fa fa-spinner fa-spin" v-else></i>
                                        Filtrar
                                    </button>
                                </td>
                                <td class="sin_borde">
                                    <carga-cuentas-balanza></carga-cuentas-balanza>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr />
                <div class="row" v-if="!cargando">
                    <div class="col-md-12 table-responsive" style="overflow-y: auto;height: 600px;">
                        <div class="row">
                            <div class="col-md-7">
                                <span style="font-size: 12px"><b>{{this.empresa_sat_razon_social}}</b></span>
                            </div>
                            <div class="col-md-5">
                                <small style="color: #5a6268; font-style: italic; text-align: right" class="pull-right">Última verificación de vigencia {{informe.ultima_verificacion.ultima_fecha_verificacion}} sobre CFDI del {{informe.ultima_verificacion.fecha_inicial_cfdi}} al {{informe.ultima_verificacion.fecha_final_cfdi}}</small>
                            </div>
                        </div>

                        <table class="table table-sm" id="sticky">
                            <thead >
                                <tr>
                                    <th class="index_corto" rowspan="2">#</th>
                                    <th class="c100" rowspan="2">Mes</th>

                                    <th class="c100">Costos y Gastos Deducibles <br>(según Balanza)</th>

                                    <th class="c100">CFDI Recibidos Tipo I <br>Totales</th>
                                    <th class="c100">(-) CFDI TIPO I <br>Sustitución de Ejercicios Anteriores</th>
                                    <th class="c100">(-) CFDI por Compraventa <br>de Moneda Extranjera</th>
                                    <th class="c100">(-) CFDI por Dispersión <br>de Vales</th>
                                    <th class="c100">Neto de Recibidos <br> Tipo I</th>

                                    <th class="c100">CFDI Recibidos Tipo E <br>Totales</th>
                                    <th class="c100">(-) CFDI TIPO E <br>Relacionados a un ejercicio anterior</th>

                                    <th class="c100">Neto de Recibidos <br> Tipo E</th>

                                    <th class="c100">CFDI recibidos <br>por mes (Neto)</th>

                                    <th class="c100">Diferencia Vs. Balanza</th>

                                    <th class="c100">(-) CFDI TIPO E <br>Recibidos en un ejercicio posterior</th>

                                    <th class="c100">Diferencia Real</th>
                                </tr>
                            <tr>

                                <th class="c100">A</th>

                                <th class="c100">B1</th>
                                <th class="c100">B2</th>
                                <th class="c100">B3</th>
                                <th class="c100">B4</th>
                                <th class="c100">B5= B1-B2-B3-B4</th>

                                <th class="c100">C1</th>
                                <th class="c100">C2</th>

                                <th class="c100">C3= C1-C2</th>

                                <th class="c100">D = B5-C3</th>

                                <th class="c110">E = D - A</th>

                                <th class="c100">F</th>

                                <th class="c100">G = E - F</th>


                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(partida, i) in informe.partidas">
                                    <td>{{i+1}}</td>
                                    <td>{{partida.mes}}</td>

                                    <td_informe v-bind:value="partida.costo_balanza_sf" />

                                    <td_informe v-bind:value="partida.costo_cfdi_i_sf" />
                                    <td_informe v-bind:value="partida.sustitucion_ejercicios_anteriores_sf" />
                                    <td_informe v-bind:value="partida.compraventa_divisas_sf" />
                                    <td_informe v-bind:value="partida.dispersion_vales_sf" />
                                    <td_informe v-bind:value="partida.neto_tipo_i_sf" />


                                    <td_informe v-bind:value="partida.costo_cfdi_e_sf" />
                                    <td_informe v-bind:value="partida.relacion_ejercicios_anteriores_sf" />
                                    <td_informe v-bind:value="partida.neto_tipo_e_sf" />

                                    <td style="text-align: right; " :style="partida.costo_cfdi != '-'?`text-decoration: underline; cursor: pointer`:``" v-on:click="verCFDI(partida)">{{partida.costo_cfdi}}</td>
                                    <td_informe v-bind:value="partida.diferencia_sf" />

                                    <td_informe_link_cfdi @ver-cfdi="verCFDI" v-bind:value="partida.relacion_ejercicios_posteriores_sf" v-bind:partida="partida" v-bind:tipo="9"></td_informe_link_cfdi>

                                    <td_informe v-bind:value="partida.diferencia_real_sf" />
                                </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2"><b>Suma:</b></td>
                                <td_informe v-bind:value="informe.sumatorias.suma_costos_balanza_sf" />

                                <td_informe v-bind:value="informe.sumatorias.suma_costos_cfdi_i_sf" />
                                <td_informe v-bind:value="informe.sumatorias.suma_sustitucion_ejercicios_anteriores_sf" />
                                <td_informe v-bind:value="informe.sumatorias.suma_compraventa_divisas_sf" />
                                <td_informe v-bind:value="informe.sumatorias.suma_dispersion_vales_sf" />
                                <td_informe v-bind:value="informe.sumatorias.suma_neto_tipo_i_sf" />

                                <td_informe v-bind:value="informe.sumatorias.suma_costos_cfdi_e_sf" />
                                <td_informe v-bind:value="informe.sumatorias.suma_relacion_ejercicios_anteriores_sf" />
                                <td_informe v-bind:value="informe.sumatorias.suma_neto_tipo_e_sf" />

                                <td_informe v-bind:value="informe.sumatorias.suma_costos_cfdi_sf" />
                                <td_informe v-bind:value="informe.sumatorias.suma_diferencia_sf" />

                                <td_informe v-bind:value="informe.sumatorias.suma_relacion_ejercicios_posteriores_sf" />

                                <td_informe v-bind:value="informe.sumatorias.suma_diferencia_real_sf" />
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
        <div class="modal fade" ref="modal_cfdi_posteriores" tabindex="-1" role="dialog">
                 <div class="modal-dialog modal-xl" >
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
                                                <th class="encabezado">CFDI Relacionado</th>
                                                <th class="encabezado">Fecha CFDI Relacionado</th>

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
                                                <CFDI v-if="cfdi.id_relacionado >0" v-bind:txt="cfdi.cfdi_relacionado" v-bind:id="cfdi.id_relacionado" @click="cfdi.id_relacionado" ></CFDI>
                                            </td>
                                            <td>
                                                <CFDI v-if="cfdi.id_relacionado >0" v-bind:txt="cfdi.fecha_relacionado" v-bind:id="cfdi.id_relacionado" @click="cfdi.id_relacionado" ></CFDI>
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
                                                <template v-if="total_cfdi_sf<0">(</template>
                                                    <span v-if="total_cfdi_sf!=0">${{  parseFloat(Math.abs(total_cfdi_sf)).formatMoney(2,'.',',') }}</span>
                                                    <span v-else>-</span>
                                                <template v-if="total_cfdi_sf<0">)</template>
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
import Td_informe from "../../../globals/td_informe";
import Td_informe_link_cfdi from "./td_informe_link_cfdi";
import CargaCuentasBalanza from "./CargaCuentasBalanza.vue";

export default {
    name: "Informe",
    components: {
        CargaCuentasBalanza,
        Td_informe_link_cfdi,
        Td_informe, PolizaShowModal, PDFPoliza, DescargaCFDI, CFDI, Datepicker, ModelListSelect},
    data() {
        return {
            informe : null,
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
            empresa_sat : "1",
            empresas_sat:[],
            empresa_sat_seleccionada:'',
            sin_proveedor : {},
            abriendo_modal : 0,
            total_cfdi_sf : '',
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
                return this.$store.dispatch('fiscal/cfd-sat/getListaCFDICostosCFDIBalanza', {
                    empresa_sat: this.empresa_sat_seleccionada,
                    mes: partida.id_mes,
                    anio: this.anio_input,
                    tipo: tipo
                })
                    .then(data => {
                        this.lista_cfdi = data.informe;
                        this.razon_social = partida.razon_social;
                        this.rfc = partida.rfc;
                        this.total_cfdi = data.total_format;
                        this.total_cfdi_sf = data.total;
                    })
                    .finally(() => {
                        if(tipo ==9)
                        {
                            $(this.$refs.modal_cfdi_posteriores).appendTo('body')
                            $(this.$refs.modal_cfdi_posteriores).modal('show');
                            this.abriendo_modal = 0;
                        }else{
                            $(this.$refs.modal_cfdi).appendTo('body')
                            $(this.$refs.modal_cfdi).modal('show');
                            this.abriendo_modal = 0;
                        }

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
table.table-sm{
    font-size: 12px;
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
