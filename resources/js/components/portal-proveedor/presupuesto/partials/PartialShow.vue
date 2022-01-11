<template>
    <span>
        <div class="row" v-if="!invitacion.presupuesto_proveedor">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                   <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <span v-else>
            <div class="row">
                <div class="col-md-12">
                    <DatosPresupuesto :presupuesto="presupuesto" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table id="tabla-conceptos" v-if="presupuesto">
                        <thead>
                            <tr>
                                <th >#</th>
                                <th >Clave</th>
                                <th >Concepto</th>
                                <th >Unidad</th>
                                <th  class="contratado">Cantidad</th>
                                <th class="cantidad_input">Precio Unitario</th>
                                <th class="cantidad_input">% Descuento</th>
                                <th class="cantidad_input">Precio Total</th>
                                <th class="cantidad_input">Moneda</th>
                                <th v-if="multiples_monedas" class="cantidad_input">Precio Total Pesos (MXN)</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody v-for="(concepto, i) in presupuesto.contratos">
                            <tr v-if="concepto.unidad == null">
                                <td >{{i + 1}}</td>
                                <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                                <td :title="concepto.descripcion">
                                    {{concepto.descripcion}}
                                </td>
                                <td></td>
                                <td></td>
                                <td ></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr v-else>
                                <td >{{i + 1}}</td>
                                <td :title="concepto.clave">{{ concepto.clave }}</td>
                                <td :title="concepto.descripcion">
                                    {{concepto.descripcion}}
                                </td>
                                <td >{{concepto.unidad}}</td>
                                <td style="text-align: right" >{{concepto.cantidad_presupuestada_format}}</td>
                                <td style="text-align: right" >{{concepto.precio_unitario_antes_descuento_format}}</td>
                                <td style="text-align: right" >{{concepto.descuento_format}}</td>
                                <td style="text-align: right" >{{concepto.total_despues_descuento_format}}</td>
                                <td>{{concepto.moneda}}</td>
                                <td style="text-align: right" v-if="multiples_monedas">{{concepto.total_despues_descuento_partida_mc_format}}</td>
                                <td>{{concepto.observaciones}}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="border: none">
                                                        <td :colspan="colspan" rowspan="20" style="border: none; padding-top: 0.75rem">
                                                            <div class="card" :style="{'max-width': ancho_tabla_detalle+'px'}" v-if="multiples_monedas == true || dolar_seleccionado == true || euro_seleccionado == true || libra_seleccionado == true ">
                                                                <div class="card-header">
                                                                    <h6><i class="fa fa-coins" ></i>Detalle por Moneda</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <table class="table table-sm" id="tabla-resumen-monedas" >
                                                                        <tr style="border: none">
                                                                            <td style="width: 150px; border: none"></td>
                                                                            <th style="border: none" v-if="peso_seleccionado"></th>
                                                                            <th style="padding-top: 0.75rem; border: none" v-if="dolar_seleccionado" colspan="2">T.C. Dólar:</th>
                                                                            <th style="border: none" v-if="euro_seleccionado" colspan="2">T.C. Euro:</th>
                                                                            <th style="border: none" v-if="libra_seleccionado" colspan="2">T.C. Libra:
                                                                            </th>
                                                                        </tr>
                                                                        <tr style="border: none">
                                                                            <td style="width: 150px; border: none"></td>
                                                                            <th style="border: none" v-if="peso_seleccionado"></th>
                                                                            <th style="border: none" v-if="dolar_seleccionado" colspan="2">
                                                                                <input
                                                                                    :disabled="cargando"
                                                                                    type="text"
                                                                                    name="tc_usd"
                                                                                    v-model="dolar"
                                                                                    v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                                    class="form-control"
                                                                                    id="tc_usd"
                                                                                    style="text-align: right"
                                                                                    :class="{'is-invalid': errors.has('tc_usd')}">
                                                                            </th>
                                                                            <th style="border: none" v-if="euro_seleccionado" colspan="2">
                                                                                <input
                                                                                    :disabled="cargando"
                                                                                    type="text"
                                                                                    name="tc_eur"
                                                                                    v-model="euro"
                                                                                    v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                                    class="form-control"
                                                                                    id="tc_eur"
                                                                                    style="text-align: right"
                                                                                    :class="{'is-invalid': errors.has('tc_eur')}">
                                                                            </th>
                                                                            <th style="border: none" v-if="libra_seleccionado" colspan="2">
                                                                                <input
                                                                                    :disabled="cargando"
                                                                                    type="text"
                                                                                    name="tc_libra"
                                                                                    v-model="libra"
                                                                                    v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                                    class="form-control"
                                                                                    id="tc_libra"
                                                                                    style="text-align: right"
                                                                                    :class="{'is-invalid': errors.has('tc_libra')}">
                                                                            </th>
                                                                        </tr>
                                                                        <tr style="border: none">
                                                                            <td style="border: none">&nbsp;</td>
                                                                        </tr>
                                                                        <tr style="border: none">
                                                                            <td style="width: 150px; border: none"></td>
                                                                            <th style="background-color: #f2f4f5; width: 150px" v-if="peso_seleccionado">Partidas Cotizadas  <br>en Pesos (MXN)</th>
                                                                            <th colspan="2" style="background-color: #f2f4f5; max-width: 150px" v-if="dolar_seleccionado">Partidas Cotizadas  <br>en Dólares (USD)</th>
                                                                            <th colspan="2" style="background-color: #f2f4f5; max-width: 150px" v-if="euro_seleccionado">Partidas Cotizadas  <br>en Euros (EUR)</th>
                                                                            <th colspan="2" style="background-color: #f2f4f5; max-width: 150px" v-if="libra_seleccionado">Partidas Cotizadas  <br>en Libras (GBL)</th>
                                                                            <th style="background-color: #f2f4f5; max-width: 150px">Valor de Cotización en <br>Pesos MXN</th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="text-align: right; background-color: #f2f4f5">Subtotal:</th>
                                                                            <td style="text-align: right" v-if="peso_seleccionado">
                                                                                <span v-if="pesos>0">
                                                                                    ${{(parseFloat(pesos_con_descuento)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="dolar_seleccionado">
                                                                                <span v-if="dolares>0">
                                                                                    ${{(parseFloat(dolares_con_descuento)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="euro_seleccionado">
                                                                                <span v-if="euros>0">
                                                                                    ${{(parseFloat(euros_con_descuento)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="libra_seleccionado">
                                                                                <span v-if="libras>0">
                                                                                    ${{(parseFloat(libras_con_descuento)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right">
                                                                                ${{(parseFloat(subtotal_mxn)).formatMoney(2,'.',',')}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="text-align: right; background-color: #f2f4f5">
                                                                                IVA:
                                                                            </th>
                                                                            <td style="text-align: right" v-if="peso_seleccionado">
                                                                                <span v-if="pesos>0">
                                                                                    ${{(parseFloat(pesos_con_descuento*.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="dolar_seleccionado">
                                                                                <span v-if="dolares>0">
                                                                                    ${{(parseFloat(dolares_con_descuento*.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="euro_seleccionado">
                                                                                <span v-if="euros>0">
                                                                                    ${{(parseFloat(euros_con_descuento*.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="libra_seleccionado">
                                                                                <span v-if="libras>0">
                                                                                    ${{(parseFloat(libras_con_descuento*.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right">
                                                                                ${{(parseFloat(iva_mxn)).formatMoney(2,'.',',')}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="text-align: right; background-color: #f2f4f5">Total:</th>
                                                                            <td style="text-align: right" v-if="peso_seleccionado">
                                                                                <span v-if="pesos>0">
                                                                                    ${{(parseFloat(pesos_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="dolar_seleccionado">
                                                                                <span v-if="dolares>0">
                                                                                    ${{(parseFloat(dolares_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="euro_seleccionado">
                                                                                <span v-if="euros>0">
                                                                                    ${{(parseFloat(euros_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right" colspan="2" v-if="libra_seleccionado">
                                                                                <span v-if="libras>0">
                                                                                    ${{(parseFloat(libras_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right">
                                                                                ${{(parseFloat(total_mxn)).formatMoney(2,'.',',')}}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="text-align: right; background-color: #f2f4f5">Total en Pesos (MXN):</th>
                                                                            <td style="text-align: right" v-if="peso_seleccionado">
                                                                                <span v-if="pesos>0">
                                                                                    ${{(parseFloat(pesos_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right; width: 80px" v-if="dolar_seleccionado">
                                                                                T.C.: ${{(parseFloat(dolar)).formatMoney(2,'.',',')}}
                                                                            </td>
                                                                            <td style="text-align: right" v-if="dolar_seleccionado">
                                                                                <span v-if="dolares>0">
                                                                                    ${{(parseFloat(dolares_con_descuento*1.16*dolar)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right; width: 80px" v-if="euro_seleccionado">
                                                                                T.C.: ${{(parseFloat(euro)).formatMoney(2,'.',',')}}
                                                                            </td>
                                                                            <td style="text-align: right" v-if="euro_seleccionado">
                                                                                <span v-if="euros>0">
                                                                                    ${{(parseFloat(euros_con_descuento *1.16 * euro)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right; width: 80px" v-if="libra_seleccionado">
                                                                                T.C.: ${{(parseFloat(libra)).formatMoney(2,'.',',')}}
                                                                            </td>
                                                                            <td style="text-align: right" v-if="libra_seleccionado">
                                                                                <span v-if="libras>0">
                                                                                    ${{(parseFloat(libras_con_descuento*1.16 * libra)).formatMoney(2,'.',',')}}
                                                                                </span>
                                                                                <span v-else>-</span>
                                                                            </td>
                                                                            <td style="text-align: right">
                                                                                ${{(parseFloat(total_mxn)).formatMoney(2,'.',',')}}
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Subtotal <span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                                        <td style="border: none; text-align: right; padding-top: 0.75rem;">
                                                            ${{(parseFloat(subtotal_antes_descuento)).formatMoney(2,'.',',')}}
                                                        </td>
                                                    </tr>
                            <tr style="border: none">
                                <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Descuento Global(%):</b></td>
                                <td style="border: none; text-align: right; padding-top: 0.75rem;">
                                  ${{(parseFloat(descuento_cot)).formatMoney(2,'.',',')}}
                                </td>
                            </tr>
                            <tr style="border: none">
                                <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Subtotal<span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                <td style="border: none; text-align: right; padding-top: 0.75rem;">
                                    ${{(parseFloat(subtotal)).formatMoney(2,'.',',')}}
                                </td>
                            </tr>
                            <tr style="border: none">
                                <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>IVA<span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                <td style="border: none; text-align: right; padding-top: 0.75rem;">
                                    ${{(parseFloat(iva)).formatMoney(2,'.',',')}}
                                </td>
                            </tr>
                            <tr style="border: none">
                                <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Total<span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                <td style="border: none; text-align: right; padding-top: 0.75rem;">
                                    ${{(parseFloat(total)).formatMoney(2,'.',',')}}
                                </td>
                            </tr>
                            <template v-if="multiples_monedas == true || dolar_seleccionado == true || euro_seleccionado == true || libra_seleccionado == true ">
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                    <tr style="border: none">
                                                        <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"></td>
                                                        <td style="border: none; text-align: right"></td>
                                                    </tr>
                                                </template>

                        </tfoot>

                    </table>
                </div>
            </div>
            <br />
            <div class="row" v-if="presupuesto.exclusiones.length != 0">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td  colspan="8" style="border: none;text-align: center"><h6><b>Exclusiones</b></h6></td>
                                </tr>
                                <tr>
                                    <th class="index_corto">#</th>
                                    <th>Descripción</th>
                                    <th class="c150">Unidad</th>
                                    <th class="cantidad_input">Cantidad</th>
                                    <th class="cantidad_input">Precio Unitario</th>
                                    <th class="cantidad_input">Moneda</th>
                                    <th class="cantidad_input">Precio Total</th>
                                </tr>
                                </thead>
                            <tbody>
                                <tr v-for="(exclusion, i) in presupuesto.exclusiones">
                                    <td class="index_corto">{{ i + 1 }}</td>
                                    <td>{{exclusion.descripcion}}</td>
                                    <td style="text-align: center">{{exclusion.unidad}}</td>
                                    <td style="text-align:right;">{{exclusion.cantidad_format}}</td>
                                    <td style="text-align:right;">{{exclusion.precio_format}}</td>
                                    <td style="text-align:center;">{{exclusion.moneda}}</td>
                                    <td style="text-align:right;">{{exclusion.total_format}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </span>
    </span>
</template>

<script>
    import DatosPresupuesto from './DatosPresupuesto';
    export default {
        name: "presupuesto-show",
        components: {DatosPresupuesto},
        props: ['id'],
        data(){
            return{
                cargando: false,
                presupuesto:[],
                invitacion:[],
                pesos_con_descuento: 0,
                dolares_con_descuento: 0,
                euros_con_descuento: 0,
                libras_con_descuento: 0,
                pesos: 0,
                dolares: 0,
                euros: 0,
                libras: 0,
                dolar:0,
                euro:0,
                libra:0,
                multiples_monedas : false,
                libra_seleccionado : false,
                dolar_seleccionado : false,
                euro_seleccionado : false,
                peso_seleccionado : true,
                ancho_tabla_detalle: '330',
                descuento_cot : ''
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id,
                    params: {
                        include: ['presupuesto_proveedor','formato_cotizacion', 'archivos_requeridos'],
                        scope: ['invitadoAutenticado']
                    }
                }).then(data => {
                    this.descuento_cot = data.presupuesto_proveedor.descuento;
                    this.presupuesto = data.presupuesto_proveedor
                    this.invitacion = data
                    this.dolar =  parseFloat(data.presupuesto_proveedor.tc_usd).formatMoney(4, '.', '') ;
                    this.euro = parseFloat(data.presupuesto_proveedor.tc_eur).formatMoney(4, '.', '');
                    this.libra =  parseFloat(data.presupuesto_proveedor.tc_libra).formatMoney(4, '.', '');
                    this.calcular()
                    this.$emit('created', data)
                    this.cargando = false;
                })
            },
            salir() {
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            calcular(){
                var pesos = 0;
                var dolares = 0;
                var euros = 0;
                var libras = 0;

                let _self = this;
                this.multiples_monedas = false;
                this.peso_seleccionado = false;
                this.dolar_seleccionado = false;
                this.euro_seleccionado = false;
                this.libra_seleccionado = false;
                this.ancho_tabla_detalle = 330;

                this.presupuesto.contratos.forEach(function (partida, i) {
                    if(partida.partida_activa === true) {
                        if(partida.moneda_seleccionada != undefined)
                        {
                            if(partida.moneda_seleccionada == 1)
                            {
                                _self.peso_seleccionado = true;
                            } else
                            if(partida.moneda_seleccionada == 2)
                            {
                                _self.dolar_seleccionado = true;
                            } else
                            if(partida.moneda_seleccionada == 3)
                            {
                                _self.euro_seleccionado = true;
                            } else
                            if(partida.moneda_seleccionada == 4)
                            {
                                _self.libra_seleccionado = true;
                            }
                        }
                        if(partida.moneda_seleccionada != undefined && partida.precio_unitario != undefined)
                        {
                            partida.calculo_precio_total = partida.cantidad_presupuestada
                                * (partida.precio_unitario - (partida.precio_unitario * (partida.descuento ? partida.descuento : 0))/100);
                            if(partida.moneda_seleccionada == 1)
                            {
                                pesos += partida.calculo_precio_total;
                            }
                            if(partida.moneda_seleccionada == 2)
                            {
                                dolares += partida.calculo_precio_total;
                            }
                            if(partida.moneda_seleccionada == 3)
                            {
                                euros += partida.calculo_precio_total;
                            }
                            if(partida.moneda_seleccionada == 4)
                            {
                                libras += partida.calculo_precio_total;
                            }
                        }
                    }
                });

                this.pesos = pesos;
                this.dolares = dolares;
                this.euros = euros;
                this.libras = libras;

                this.pesos_con_descuento =  this.pesos - ((this.descuento_cot > 0)?(this.pesos* parseFloat(this.descuento_cot)/100):0) ;
                this.dolares_con_descuento = this.dolares - ((this.descuento_cot > 0)?(this.dolares* parseFloat(this.descuento_cot)/100):0) ;
                this.euros_con_descuento = this.euros - ((this.descuento_cot > 0)?(this.euros* parseFloat(this.descuento_cot)/100):0) ;
                this.libras_con_descuento = this.libras - ((this.descuento_cot > 0)?(this.libras* parseFloat(this.descuento_cot)/100):0) ;

                if((this.libra_seleccionado && this.euro_seleccionado) || (this.libra_seleccionado && this.dolar_seleccionado) || (this.libra_seleccionado && this.peso_seleccionado)){
                    this.multiples_monedas = true;
                } else if((this.dolar_seleccionado && this.euro_seleccionado) || (this.libra_seleccionado && this.dolar_seleccionado) || (this.dolar_seleccionado && this.peso_seleccionado)){
                    this.multiples_monedas = true;
                } else if((this.dolar_seleccionado && this.euro_seleccionado) || (this.libra_seleccionado && this.euro_seleccionado) || (this.euro_seleccionado && this.peso_seleccionado)){
                    this.multiples_monedas = true;
                } else if((this.dolar_seleccionado && this.peso_seleccionado) || (this.libra_seleccionado && this.peso_seleccionado) || (this.euro_seleccionado && this.peso_seleccionado)){
                    this.multiples_monedas = true;
                }else{
                    this.multiples_monedas = false;
                }

                if(this.euro_seleccionado){
                    this.ancho_tabla_detalle += 150;
                }
                if(this.peso_seleccionado){
                    this.ancho_tabla_detalle += 150;
                }
                if(this.dolar_seleccionado){
                    this.ancho_tabla_detalle += 150;
                }
                if(this.libra_seleccionado){
                    this.ancho_tabla_detalle += 150;
                }
            },
        },
        computed: {
            colspan(){
                if(this.multiples_monedas)
                {
                    return 7;
                }else{
                    return 5;
                }
            },
            subtotal_antes_descuento()
            {
                if(this.multiples_monedas){
                    return (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra) );
                } else if(this.peso_seleccionado){
                    return this.pesos ;
                } else if(this.dolar_seleccionado){
                    return this.dolares ;
                }else if(this.euro_seleccionado){
                    return this.euros  ;
                }else if(this.libra_seleccionado){
                    return this.libras ;
                }
            },
            subtotal()
            {
                if(this.multiples_monedas){
                    return (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra) -
                        ((this.descuento_cot > 0) ? (((this.pesos + (this.dolares * this.dolar) + (this.euros *
                            this.euro) + (this.libras * this.libra)) * parseFloat(this.descuento_cot)) / 100) : 0));
                } else if(this.peso_seleccionado){
                    return this.pesos - ((this.descuento_cot > 0)?(this.pesos* parseFloat(this.descuento_cot)/100):0) ;
                } else if(this.dolar_seleccionado){
                    return this.dolares  - ((this.descuento_cot > 0)?(this.dolares* parseFloat(this.descuento_cot)/100):0);
                }else if(this.euro_seleccionado){
                    return this.euros  - ((this.descuento_cot > 0)?(this.euros* parseFloat(this.descuento_cot)/100):0);
                }else if(this.libra_seleccionado){
                    return this.libras  - ((this.descuento_cot > 0)?(this.libras* parseFloat(this.descuento_cot)/100):0);
                }
            },
            iva()
            {
                return this.subtotal * 0.16;
            },
            total()
            {
                return this.subtotal + this.iva;
            },
            carga()
            {
                return (this.xls) ? this.xls : false;
            },
            subtotal_mxn_antes_descuento()
            {
                return (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra));
            },
            subtotal_mxn()
            {
                return (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra) -
                    ((this.descuento_cot > 0) ? (((this.pesos + (this.dolares * this.dolar) + (this.euros *
                        this.euro) + (this.libras * this.libra)) * parseFloat(this.descuento_cot)) / 100) : 0));
            },
            iva_mxn() {
                return this.subtotal_mxn * 0.16;
            },
            total_mxn() {
                return this.subtotal_mxn + this.iva_mxn;
            },
        },
    }
</script>

<style scoped>
table#tabla-resumen-monedas, table.tabla {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}

table#tabla-resumen-monedas th, table.tabla th, table#tabla-resumen-monedas td , table.tabla td  {
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

table#tabla-resumen-monedas td.sin_borde, table.tabla td.sin_borde  {
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
table#tabla-resumen-monedas table tbody th, table.tabla table tbody th
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


table tbody td input.text
{
    border: none;
    padding: 0;
    margin: 0;
    width: 100%;
    background-color: transparent;
    font-family: inherit;
    font-size: inherit;
    font-weight: bold;
}

table .numerico
{
    text-align: right;
    padding-left: 0;
    white-space: normal;
}

.text.is-invalid {
    color: #dc3545;
}

table tbody td input.text {
    text-align: right;
}

.encabezado{
    text-align: center;
    background-color: #f2f4f5
}
</style>
