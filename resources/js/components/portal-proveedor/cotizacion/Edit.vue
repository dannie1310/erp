<template>
    <span>
        <div class="row" v-if="cargando">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" >
                            <div class="col-md-12">
                                <div class="spinner-border text-success" role="status">
                                   <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-12" v-if="invitacion">
                <div class="card">
                    <form role="form" @submit.prevent="validate">
                        <div class="card-body">
                            <div class="modal-body" v-if="invitacion.cotizacionCompra">
                                <div class="row">
                                    <div class="col-md-12">
                                        <encabezado-cotizacion-compra-proveedor v-bind:cotizacion_compra="invitacion.cotizacionCompra"></encabezado-cotizacion-compra-proveedor>
                                    </div>
                                </div>

                                 <div class="row">
                                    <div class="col-md-2">
                                        <label>Fecha:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Pago en Parcialidades (%):</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Anticipo (%):</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Crédito (días):</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Tiempo de entrega (días):</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Vigencia (días):</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <datepicker v-model = "invitacion.cotizacionCompra.fecha"
                                                    id = "fecha"
                                                    name = "fecha"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    :class="{'is-invalid': errors.has('fecha')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>

                                    </div>
                                    <div class="col-md-2">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="pago"
                                            v-model="invitacion.cotizacionCompra.complemento.parcialidades"
                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="form-control"
                                            id="pago"
                                            style="text-align: right"
                                            :class="{'is-invalid': errors.has('pago')}">
                                    </div>
                                    <div class="col-md-2">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="anticipo"
                                            v-model="invitacion.cotizacionCompra.complemento.anticipo"
                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="form-control"
                                            id="anticipo"
                                            style="text-align: right"
                                            :class="{'is-invalid': errors.has('anticipo')}">
                                    </div>
                                    <div class=" col-md-2">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="credito"
                                            v-model="invitacion.cotizacionCompra.complemento.dias_credito"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="form-control"
                                            id="credito"
                                            :class="{'is-invalid': errors.has('credito')}">
                                    </div>
                                    <div class="col-md-2">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="tiempo"
                                            v-model="invitacion.cotizacionCompra.complemento.entrega"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="form-control"
                                            id="tiempo"
                                            :class="{'is-invalid': errors.has('tiempo')}">
                                    </div>
                                    <div class="col-md-2" >
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="vigencia"
                                            v-model="invitacion.cotizacionCompra.complemento.vigencia"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="form-control"
                                            id="vigencia"
                                            :class="{'is-invalid': errors.has('vigencia')}">
                                    </div>
                                </div>

                                <hr />
                                <div class="row" v-if="invitacion">
                                    <div  class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-sm tabla-resumen-monedas">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th style="width:110px;">No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th class="index">¿Cotizar? No/Si
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" id="toggleCotizar" v-model="toggleCotizar" checked value="1">
                                                            <label class="custom-control-label" for="toggleCotizar"></label>
                                                        </div>
                                                    </th>
                                                    <th >Cantidad</th>
                                                    <th class="cantidad_input">Precio Unitario</th>
                                                    <th class="cantidad_input">% Descuento</th>
                                                    <th class="cantidad_input">Precio Total</th>
                                                    <th class="cantidad_input">Moneda</th>
                                                    <th class="cantidad_input"  v-if="multiples_monedas">Precio Total Pesos (MXN)</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="invitacion.cotizacionCompra.partidasEdicion">
                                                    <tr v-for="(partida, i) in invitacion.cotizacionCompra.partidasEdicion.data">
                                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                        <td>{{(partida.material) ? partida.material.numero_parte : '----'}}</td>
                                                        <td>{{(partida.material) ? partida.material.descripcion : '----'}}</td>
                                                        <td >{{(partida.material) ? partida.material.unidad : '----'}}</td>
                                                         <td style="text-align:center; vertical-align:inherit;">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" :id="`no_cotizado[${i}]`" v-model="partida.enable" v-on:change="calcular" checked>
                                                                <label class="custom-control-label" :for="`no_cotizado[${i}]`"></label>
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">{{partida.cantidad_format}}</td>
                                                        <td>
                                                            <input type="text"
                                                                   v-on:keyup="calcular"
                                                                   class="form-control"
                                                                   :disabled="!partida.enable"
                                                                   :name="`precio[${i}]`"
                                                                   data-vv-as="Precio"
                                                                   v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                   :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                                   style="text-align: right"
                                                                   v-model="partida.precio_unitario"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                   :disabled="!partida.enable"
                                                                   class="form-control"
                                                                   :name="`descuento[${i}]`"
                                                                   v-on:keyup="calcular"
                                                                   data-vv-as="Descuento(%)"
                                                                   v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                   :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                                   style="text-align: right"
                                                                   v-model="partida.descuento"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$' + parseFloat((partida.cantidad) * partida.precio_unitario).formatMoney(2,'.',',')}}</td>
                                                        <td style="width:120px;" >
                                                            <select
                                                                type="text"
                                                                v-on:change="calcular"
                                                                :name="`moneda[${i}]`"
                                                                data-vv-as="Moneda"
                                                                :disabled="!partida.enable"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                id="moneda"
                                                                v-model="partida.id_moneda"
                                                                :class="{'is-invalid': errors.has(`moneda[${i}]`)}">
                                                                    <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;"  v-if="multiples_monedas">{{getPrecioTotal(partida.cantidad * partida.precio_unitario, partida.id_moneda)}}</td>
                                                        <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      :disabled="!partida.enable"
                                                                      v-validate="{}"
                                                                      :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                      v-model="partida.observacion"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                        </td>
                                                    </tr>
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
                                                        <td style="border: none"> <input
                                                            :disabled="cargando"
                                                            type="text"
                                                            name="descuento_cot"
                                                            v-model="descuento_cot"
                                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                            class="form-control"
                                                            id="descuento_cot"
                                                            style="text-align: right; padding-right: 4px"
                                                            :class="{'is-invalid': errors.has('descuento_cot')}">
                                                        </td>
                                                    </tr>
                                                    <!--  -->
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
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-sm" id="tabla-resumen-monedas">
                                                <thead>
                                                    <tr>
                                                        <td class="encabezado" colspan="8"><b>Exclusiones</b></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="index_corto">#</th>
                                                        <th>Descripción</th>
                                                        <th>Unidad</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio Unitario</th>
                                                        <th>Moneda</th>
                                                        <th class="cantidad_input">Precio Total</th>
                                                        <th class="icono">
                                                            <button type="button" class="btn btn-sm btn-outline-success" @click="agregarExclusion" :disabled="cargando">
                                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                                <i class="fa fa-plus" v-else></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                <tbody>
                                                    <tr v-for="(exclusion, i) in invitacion.cotizacionCompra.exclusiones.data">
                                                        <td class="index_corto">{{ i + 1 }}</td>
                                                        <td v-if="exclusion.id == undefined">
                                                            <input class="form-control"
                                                                   :name="`nombre[${i}]`"
                                                                   :data-vv-as="`'Nombre ${i + 1}'`"
                                                                   v-model="exclusion.descripcion"
                                                                   :class="{'is-invalid': errors.has(`nombre[${i}]`)}"
                                                                   v-validate="{ required: true}"
                                                                   :id="`nombre[${i}]`"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`nombre[${i}]`)">{{ errors.first(`nombre[${i}]`) }}</div>
                                                        </td>
                                                        <td v-else>{{exclusion.descripcion}}</td>
                                                        <td v-if="exclusion.id == undefined">
                                                            <select
                                                                type="text"
                                                                :name="`unidad[${i}]`"
                                                                :data-vv-as="`Unidad[${i}]`"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                :id="`unidad[${i}]`"
                                                                v-model="exclusion.unidad"
                                                                :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                    <option value>--Unidad--</option>
                                                                    <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
                                                        </td>
                                                        <td v-else>{{exclusion.unidad}}</td>
                                                        <td v-if="exclusion.id == undefined">
                                                            <input class="form-control"
                                                                   :name="`cantidad[${i}]`"
                                                                   :data-vv-as="`'Cantidad ${i + 1}'`"
                                                                   style="text-align: right"
                                                                   v-model="exclusion.cantidad"
                                                                   :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                   v-validate="{ required: true, min_value:0.01, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                   :id="`cantidad[${i}]`"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                        </td>
                                                        <td class="cantidad_input" v-else>{{exclusion.cantidad_format}}</td>
                                                        <td v-if="exclusion.id == undefined">
                                                            <input type="text"
                                                                   class="form-control"
                                                                   :name="`precio[${i}]`"
                                                                   style="text-align: right"
                                                                   :data-vv-as="`'Precio ${i + 1}'`"
                                                                   v-validate="{required: true, min_value:0.01, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                   :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                                   v-model="exclusion.precio_unitario"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                        </td>
                                                        <td class="cantidad_input" v-else>{{exclusion.precio_format}}</td>
                                                        <td v-if="exclusion.id == undefined">
                                                            <select
                                                                type="text"
                                                                :name="`moneda[${i}]`"
                                                                :data-vv-as="`'Moneda ${i + 1}'`"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                :id="`moneda[${i}]`"
                                                                v-model="exclusion.id_moneda"
                                                                :class="{'is-invalid': errors.has(`moneda[${i}]`)}">
                                                                <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                        </td>
                                                        <td v-else>{{exclusion.moneda}}</td>
                                                        <td style="text-align:right;" v-if="exclusion.id == undefined">{{getTotalExclusion(i)}}</td>
                                                        <td style="text-align:right;" v-else>{{exclusion.total_format}}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarExclusion(i)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="observaciones" class="col-form-label">Observaciones: </label>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <textarea
                                            name="observaciones"
                                            id="observaciones"
                                            class="form-control"
                                            v-model="invitacion.cotizacionCompra.observaciones"
                                            data-vv-as="Observaciones"
                                            :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>
                                Regresar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    import DatosCotizacionCompra from "./partials/DatosCotizacionCompra";
    import EncabezadoCotizacionCompraProveedor from "./partials/EncabezadoCotizacion";
    export default {
        name: "cotizacion-proveedor-edit",
        components: {
            EncabezadoCotizacionCompraProveedor,
            DatosCotizacionCompra, Datepicker, ModelListSelect},
        props: ['id', 'xls'],
        data() {
            return {
                cargando: false,
                es:es,
                fechasDeshabilitadas:{},
                invitacion : [],
                unidades: [],
                descuento_cot : '0.00',
                monedas: [],
                pesos: 0,
                dolares: 0,
                euros: 0,
                libras: 0,
                dolar:0,
                euro:0,
                libra:0,
                observaciones : '',
                pago: 0,
                anticipo: 0,
                credito: 0,
                tiempo: 0,
                vigencia: 0,
                pesos_con_descuento: 0,
                dolares_con_descuento: 0,
                euros_con_descuento: 0,
                libras_con_descuento: 0,
                toggleCotizar : 1,
                multiples_monedas : false,
                libra_seleccionado : false,
                dolar_seleccionado : false,
                euro_seleccionado : false,
                peso_seleccionado : true,
                ancho_tabla_detalle: '330',
            }
        },
        mounted() {
            this.$validator.reset();
            this.find();
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            salir() {
                this.$router.go(-1);
            },
            find() {
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id,
                    params:{ include: ['cotizacionCompra.complemento','cotizacionCompra.empresa','cotizacionCompra.sucursal','cotizacionCompra.exclusiones','cotizacionCompra.partidasEdicion'], scope: ['invitadoAutenticado']}
               }).then(data => {

                    if(data.con_cotizacion){
                        this.descuento_cot = data.cotizacionCompra.complemento.descuento;
                        this.invitacion = data;
                        this.dolar = data.cotizacionCompra.complemento ? parseFloat(data.cotizacionCompra.complemento.tc_usd).formatMoney(4, '.', '') : 0;
                        this.euro = data.cotizacionCompra.complemento ? parseFloat(data.cotizacionCompra.complemento.tc_eur).formatMoney(4, '.', '') : 0;
                        this.libra = data.cotizacionCompra.complemento ? parseFloat(data.cotizacionCompra.complemento.tc_libra).formatMoney(4, '.', '') : 0;
                        if(this.xls != null)
                        {
                            data.cotizacionCompra.complemento.anticipo = this.xls.anticipo;
                            data.cotizacionCompra.complemento.dias_credito = this.xls.credito;
                            data.cotizacionCompra.complemento.descuento = this.xls.descuento_cot;
                            data.cotizacionCompra.fecha = this.xls.fecha_cotizacion;
                            data.cotizacionCompra.observaciones = this.xls.observaciones_generales;
                            data.cotizacionCompra.parcialidades = this.xls.pago_parcialidades;
                            this.euro = this.xls.tc_eur;
                            this.libra = this.xls.tc_libra;
                            this.dolar = this.xls.tc_usd;
                            data.cotizacionCompra.complemento.entrega = this.xls.tiempo_entrega;
                            data.cotizacionCompra.complemento.vigencia = this.xls.vigencia;
                            for(var i = 0; i < data.cotizacionCompra.partidasEdicion.data.length; i++)
                            {
                                for(var x = 0; x < this.xls.partidas.length; x++)
                                {
                                    if(data.cotizacionCompra.partidasEdicion.data[i].material.id == this.xls.partidas[x].id_material)
                                    {
                                        data.cotizacionCompra.partidasEdicion.data[i].descuento = this.xls.partidas[x].descuento;
                                        data.cotizacionCompra.partidasEdicion.data[i].id_moneda = this.xls.partidas[x].id_moneda;
                                        data.cotizacionCompra.partidasEdicion.data[i].observacion = this.xls.partidas[x].observaciones;
                                        data.cotizacionCompra.partidasEdicion.data[i].precio_unitario = this.xls.partidas[x].precio_unitario;
                                        data.cotizacionCompra.partidasEdicion.data[i].unidad = this.xls.partidas[x].unidad;
                                        if(this.xls.partidas[x].precio_unitario>0)
                                        {
                                            data.cotizacionCompra.partidasEdicion.data[i].enable = 1;
                                        }
                                    }
                                }
                            }
                        }
                        this.getMonedas(data.base_datos);
                        this.getUnidades(data.base_datos);
                        this.calcular()
                    } else {
                        swal("La invitación "+data.numero_folio_format + " no tiene una cotización disponible para editar, debe registrarla primero", {
                            icon: "error",
                            closeOnClickOutside: false,
                            buttons: {
                                confirm: {
                                    text: 'Entendido',
                                }
                            }
                        }).then((value) => {
                            if(value) {
                                this.$router.push({name: 'cotizacion-proveedor-create', params: {id: data.id}});
                                swal.close();
                            }
                        });
                    }
                })
            },
            getPrecioTotal(precio, moneda) {
                if(moneda == undefined)
                {
                    return '$1.00'
                }
                if(moneda === 1)
                {
                    return '$'+parseFloat(precio != undefined ? precio : 1).formatMoney(2,'.',',')
                }
                if(moneda === 2)
                {
                    return '$'+parseFloat(precio != undefined ? precio * this.dolar : this.dolar).formatMoney(2,'.',',')
                }
                if(moneda === 3)
                {
                    return '$'+parseFloat(precio != undefined ? precio * this.euro : this.euro).formatMoney(2,'.',',')
                }
                if(moneda === 4)
                {
                    return '$'+parseFloat(precio != undefined ? precio * this.libra : this.libra).formatMoney(2,'.',',')
                }
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

                this.invitacion.cotizacionCompra.partidasEdicion.data.forEach(function (partida, i) {
                    if(partida.enable) {
                        partida.calculo_precio_total = partida.cantidad * (partida.precio_unitario - ((partida.precio_unitario * partida.descuento)/100));
                        if(partida.id_moneda == 1)
                        {
                            pesos += partida.calculo_precio_total;
                            _self.peso_seleccionado = true;
                        }
                        if(partida.id_moneda == 2)
                        {
                            dolares += partida.calculo_precio_total;
                            _self.dolar_seleccionado = true;
                        }
                        if(partida.id_moneda == 3)
                        {
                            euros += partida.calculo_precio_total;
                            _self.euro_seleccionado = true;
                        }
                        if(partida.id_moneda == 4)
                        {
                            libras += partida.calculo_precio_total;
                            _self.libra_seleccionado = true;
                        }
                    }
                })
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
            getMonedas(base){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/monedasBase', {
                    params: {sort: 'id_moneda', order: 'asc'},
                    base : base
                }).then(data => {
                    this.monedas = data.data;
                    if(this.dolar == 0) {
                        this.dolar = parseFloat(this.monedas[1].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    }
                    if(this.euro == 0) {
                        this.euro = parseFloat(this.monedas[2].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    }
                    if(this.libra == 0) {
                        this.libra = parseFloat(this.monedas[3].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    }
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.editar();
                    }
                });
            },
            editar() {

                if(this.total == 0)
                {
                    swal('Error', 'No puede ingresar una cotización donde todas las partidas tengan precio $0.00, favor de corregir para continuar', 'error');
                }
                else
                {
                    var datos = {
                        'id_invitacion' : this.invitacion.id,
                        'fecha' : this.invitacion.cotizacionCompra.fecha,
                        'descuento' :this.descuento_cot,
                        'pesos' : this.pesos,
                        'dolares' : this.dolares,
                        'euros' : this.euros,
                        'libras' : this.libras,
                        'observaciones' : this.invitacion.cotizacionCompra.observaciones,
                        'pago' : this.invitacion.cotizacionCompra.complemento.parcialidades,
                        'anticipo' : this.invitacion.cotizacionCompra.complemento.anticipo,
                        'credito' : this.invitacion.cotizacionCompra.complemento.dias_credito,
                        'tiempo' : this.invitacion.cotizacionCompra.complemento.entrega,
                        'vigencia' : this.invitacion.cotizacionCompra.complemento.vigencia,
                        'importe' : this.total_mxn,
                        'impuesto' : this.iva_mxn,
                        'tc_eur' : this.euro,
                        'tc_usd' : this.dolar,
                        'tc_libra' : this.libra,
                        'partidas' : this.invitacion.cotizacionCompra.partidasEdicion,
                        'exclusiones' : this.invitacion.cotizacionCompra.exclusiones
                    }

                    return this.$store.dispatch('compras/cotizacion/updateCotizacionProveedor', {
                    id: this.invitacion.cotizacionCompra.id,
                    cotizacion: datos
                    })
                    .then((data) => {
                        this.salir();
                    });
                }
            },
            agregarExclusion(){
                var array = {
                    'descripcion' : '',
                    'unidad' : '',
                    'cantidad' : 0,
                    'precio_unitario' : 0,
                    'id_moneda' : 1,
                    'moneda' : ''
                }
                this.invitacion.cotizacionCompra.exclusiones.data.push(array);
            },
            quitarExclusion(index){
                this.invitacion.cotizacionCompra.exclusiones.data.splice(index, 1);
            },
            getUnidades(base) {
                return this.$store.dispatch('cadeco/unidad/porBase', {
                    params: {sort: 'unidad',  order: 'asc'},
                    base : base
                })
                .then(data => {
                    this.unidades= data.data;
                })
            },
            getTotalExclusion(i){
                var moneda = this.invitacion.cotizacionCompra.exclusiones.data[i]['id_moneda'];
                var precio_total = 0;
                if(this.invitacion.cotizacionCompra.exclusiones.data[i]['cantidad'] != 0 && this.invitacion.cotizacionCompra.exclusiones.data[i]['precio_unitario'] != 0) {
                    var precio_total = this.invitacion.cotizacionCompra.exclusiones.data[i]['cantidad'] * this.invitacion.cotizacionCompra.exclusiones.data[i]['precio_unitario']
                    if (moneda == 1) {
                        return '$' + parseFloat(precio_total).formatMoney(2, '.', ',');
                    }
                    if (moneda == 2) {
                        return '$' + parseFloat(precio_total * this.dolar).formatMoney(2, '.', ',');
                    }
                    if (moneda == 3) {
                        return '$' + parseFloat(precio_total * this.euro).formatMoney(2, '.', ',');
                    }
                    if (moneda == 4) {
                        return '$' + parseFloat(precio_total * this.libra).formatMoney(2, '.', ',');
                    }
                }
                return  '$' + parseFloat(precio_total).formatMoney(2, '.', ',')
            }
        },
        watch: {
            toggleCotizar: {
                handler(toggleCotizar) {
                    if(toggleCotizar){
                        this.invitacion.cotizacionCompra.partidasEdicion.data.forEach(partida => {
                            partida.enable = true;
                            partida.no_cotizado = false;
                        })
                    }else {
                        this.invitacion.cotizacionCompra.partidasEdicion.data.forEach(partida => {
                            partida.enable = false;
                            partida.no_cotizado = true;
                        })
                    }
                    this.calcular();
                },
            }
        },
        computed: {
            colspan(){
                if(this.multiples_monedas)
                {
                    return 8;
                }else{
                    return 6;
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
table.tabla-resumen-monedas {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}

table.tabla-resumen-monedas th, table.tabla-resumen-monedas td {
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

table.tabla-resumen-monedas td.sin_borde {
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
table.tabla-resumen-monedas table tbody th
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

