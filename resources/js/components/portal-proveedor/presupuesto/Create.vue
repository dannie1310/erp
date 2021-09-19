<template>
    <span>
        <div class="card" v-if="cargando">
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
        <div class="row" v-if="!cargando">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body" v-if="contrato">
                            <div class="row">
                                <div class="col-md-12">
                                    <tabla-datos-solicitud v-bind:solicitud="contrato" />
                                </div>
                            </div>
                            <hr />

                            <div class="row">
                                <div class="col-md-2">
                                    <label>Fecha:</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Anticipo (%):</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Crédito (días):</label>
                                </div>
                                <div class="col-md-2">
                                    <label>Vigencia (días):</label>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <datepicker v-model = "fecha"
                                                id="fecha"
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
                                        name="anticipo"
                                        v-model="contrato.anticipo"
                                        v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                        class="form-control"
                                        id="anticipo"
                                        style="text-align: right"
                                        :class="{'is-invalid': errors.has('anticipo')}">
                                    <div class="invalid-feedback" v-show="errors.has('anticipo')">{{ errors.first('anticipo') }}</div>
                                </div>
                                <div class=" col-md-2">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="credito"
                                        v-model="contrato.credito"
                                        v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                        class="form-control"
                                        id="credito"
                                        :class="{'is-invalid': errors.has('credito')}">
                                    <div class="invalid-feedback" v-show="errors.has('credito')">{{ errors.first('credito') }}</div>
                                </div>

                                <div class="col-md-2" >
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="vigencia"
                                        v-model="contrato.vigencia"
                                        v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                        class="form-control"
                                        id="vigencia"
                                        :class="{'is-invalid': errors.has('vigencia')}">
                                    <div class="invalid-feedback" v-show="errors.has('vigencia')">{{ errors.first('vigencia') }}</div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-1">
                                   <label>Proveedor:</label>
                                </div>
                                <div class="col-md-5">
                                    <h6>{{contrato.razon_social}} [{{contrato.rfc}}]</h6>
                                </div>
                                <div class="col-md-1">
                                   <label>Sucursal:</label>
                                </div>
                                <div class="col-md-2">
                                    <h6>{{contrato.sucursal}}</h6>
                                </div>
                                <div class="col-md-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input button" id="cotizacion" v-model="pendiente" >
                                        <label class="custom-control-label" for="cotizacion">Dejar pendiente la captura de precios</label>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="row" v-if="id != '' && !pendiente">
                                <div  class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm tabla">
                                            <thead>
                                            <tr>
                                                <th class="index_corto">#</th>
                                                <th>Descripción</th>
                                                <th class="unidad">Unidad</th>
                                                <th class="index">¿Cotizar? No/Si
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="toggleCotizar" v-model="toggleCotizar" checked value="1">
                                                        <label class="custom-control-label" for="toggleCotizar"></label>
                                                    </div>
                                                </th>
                                                <th >Cantidad </th>
                                                <th class="cantidad_input">Precio Unitario</th>
                                                <th class="cantidad_input">% Descuento</th>
                                                <th class="cantidad_input">Precio Total</th>
                                                <th class="cantidad_input">Moneda</th>
                                                <th class="cantidad_input" v-if="multiples_monedas">Precio Total Pesos (MXN)</th>
                                                <th >Observaciones</th>
                                            </tr>
                                            </thead>
                                            <tbody v-if="contrato">
                                                <tr v-for="(partida, i) in contrato.conceptos.data">
                                                    <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                    <td style="text-align:left;" v-html="partida.descripcion"></td>
                                                    <td>{{partida.unidad}}</td>
                                                    <td style="text-align:center; vertical-align:inherit;">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="partida.enable" >
                                                            <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align:right;">{{partida.cantidad_presupuestada_format}}</td>
                                                    <td>
                                                        <input
                                                            type="text"  v-on:keyup="calcular"
                                                            :disabled="partida.enable == false"
                                                            class="form-control"
                                                            :name="`precio[${i}]`"
                                                            :data-vv-as="`'Precio ${i + 1}'`"
                                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d{2})?$/}"
                                                            :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                            v-model="partida.precio_cot"
                                                            style="text-align: right"
                                                        />
                                                        <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                        <input type="text"  v-on:keyup="calcular"
                                                            :disabled="partida.enable == false"
                                                            class="form-control"
                                                            :name="`descuento[${i}]`"
                                                            :data-vv-as="`'Descuento(%) ${i + 1}'`"
                                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d{2})?$/}"
                                                            :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                            v-model="partida.descuento_cot"
                                                               style="text-align: right"
                                                        />
                                                        <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                    </td>
                                                    <td style="text-align:right;">{{getPrecio(partida)}}</td>
                                                    <td style="width:120px;" >
                                                        <select
                                                            v-on:change="calcular"
                                                            type="text"
                                                            :name="`moneda[${i}]`"
                                                            :data-vv-as="`'Moneda ${i + 1}'`"
                                                            :disabled="partida.enable == false"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            id="moneda"
                                                            v-model="partida.moneda_seleccionada"
                                                            :class="{'is-invalid': errors.has(`moneda[${i}]`)}">
                                                                <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.abreviatura }}</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                    </td>
                                                    <td style="text-align:right;" v-if="multiples_monedas">{{getPrecioTotal(partida.calculo_precio_total, partida.moneda_seleccionada)}}</td>
                                                    <td style="width:200px;">
                                                        <textarea class="form-control"
                                                                :name="`observaciones[${i}]`"
                                                                :data-vv-as="`'Observaciones ${i + 1}'`"
                                                                :disabled="partida.enable == false"
                                                                v-validate="{}"
                                                                :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                v-model="partida.observaciones_cot"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                    </td>
                                                </tr>
                                                <tr style="border: none">
                                                    <td :colspan="colspan" rowspan="21" style="border: none; padding-top: 0.75rem">

                                                        <div class="card" :style="{'max-width': ancho_tabla_detalle+'px'}" v-if="multiples_monedas == true || dolar_seleccionado == true || euro_seleccionado == true || libra_seleccionado == true ">
                                                            <div class="card-header">
                                                                <h6><i class="fa fa-coins" ></i>Detalle por Moneda</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table table-sm" id="tabla-resumen-monedas" >
                                                                    <tr style="border: none">
                                                                        <td style="width: 150px; border: none">

                                                                        </td>
                                                                        <th style="border: none" v-if="peso_seleccionado">

                                                                        </th>
                                                                        <th style="padding-top: 0.75rem; border: none" v-if="dolar_seleccionado" colspan="2">
                                                                            T.C. Dólar:
                                                                        </th>

                                                                        <th style="border: none" v-if="euro_seleccionado" colspan="2">
                                                                            T.C. Euro:
                                                                        </th>

                                                                        <th style="border: none" v-if="libra_seleccionado" colspan="2">
                                                                            T.C. Libra:
                                                                        </th>

                                                                    </tr>
                                                                    <tr style="border: none">
                                                                        <td style="width: 150px; border: none">

                                                                        </td>
                                                                        <th style="border: none" v-if="peso_seleccionado">

                                                                        </th>

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
                                                                        <td style="width: 150px; border: none">

                                                                        </td>
                                                                        <th style="background-color: #f2f4f5; width: 150px" v-if="peso_seleccionado">
                                                                            Partidas Cotizadas  <br>en Pesos (MXN)
                                                                        </th>
                                                                        <th colspan="2" style="background-color: #f2f4f5; max-width: 150px" v-if="dolar_seleccionado">
                                                                            Partidas Cotizadas  <br>en Dólares (USD)
                                                                        </th>
                                                                        <th colspan="2" style="background-color: #f2f4f5; max-width: 150px" v-if="euro_seleccionado">
                                                                            Partidas Cotizadas  <br>en Euros (EUR)
                                                                        </th>
                                                                        <th colspan="2" style="background-color: #f2f4f5; max-width: 150px" v-if="libra_seleccionado">
                                                                            Partidas Cotizadas  <br>en Libras (GBL)
                                                                        </th>
                                                                        <th style="background-color: #f2f4f5; max-width: 150px">
                                                                            Valor de Cotización en <br>Pesos MXN
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th style="text-align: right; background-color: #f2f4f5">
                                                                            Subtotal:
                                                                        </th>
                                                                        <td style="text-align: right" v-if="peso_seleccionado">
                                                                            <span v-if="pesos>0">
                                                                                ${{(parseFloat(pesos_con_descuento)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="dolar_seleccionado">
                                                                            <span v-if="dolares>0">
                                                                                ${{(parseFloat(dolares_con_descuento)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="euro_seleccionado">
                                                                            <span v-if="euros>0">
                                                                                ${{(parseFloat(euros_con_descuento)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="libra_seleccionado">
                                                                            <span v-if="libras>0">
                                                                                ${{(parseFloat(libras_con_descuento)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
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
                                                                            <span v-else>
                                                                                -
                                                                            </span>

                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="dolar_seleccionado">
                                                                            <span v-if="dolares>0">
                                                                                ${{(parseFloat(dolares_con_descuento*.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="euro_seleccionado">
                                                                            <span v-if="euros>0">
                                                                                ${{(parseFloat(euros_con_descuento*.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="libra_seleccionado">
                                                                            <span v-if="libras>0">
                                                                                ${{(parseFloat(libras_con_descuento*.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right">
                                                                            ${{(parseFloat(iva_mxn)).formatMoney(2,'.',',')}}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th style="text-align: right; background-color: #f2f4f5">
                                                                            Total:
                                                                        </th>
                                                                        <td style="text-align: right" v-if="peso_seleccionado">
                                                                            <span v-if="pesos>0">
                                                                                ${{(parseFloat(pesos_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="dolar_seleccionado">
                                                                            <span v-if="dolares>0">
                                                                                ${{(parseFloat(dolares_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="euro_seleccionado">
                                                                            <span v-if="euros>0">
                                                                                ${{(parseFloat(euros_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right" colspan="2" v-if="libra_seleccionado">
                                                                            <span v-if="libras>0">
                                                                                ${{(parseFloat(libras_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right">
                                                                            ${{(parseFloat(total_mxn)).formatMoney(2,'.',',')}}
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <th style="text-align: right; background-color: #f2f4f5">
                                                                            Total en Pesos (MXN):
                                                                        </th>
                                                                        <td style="text-align: right" v-if="peso_seleccionado">
                                                                            <span v-if="pesos>0">
                                                                                ${{(parseFloat(pesos_con_descuento*1.16)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right; width: 80px" v-if="dolar_seleccionado">
                                                                            T.C.: ${{(parseFloat(dolar)).formatMoney(2,'.',',')}}
                                                                        </td>
                                                                        <td style="text-align: right" v-if="dolar_seleccionado">
                                                                            <span v-if="dolares>0">
                                                                                ${{(parseFloat(dolares_con_descuento*1.16*dolar)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right; width: 80px" v-if="euro_seleccionado">
                                                                           T.C.: ${{(parseFloat(euro)).formatMoney(2,'.',',')}}
                                                                        </td>
                                                                        <td style="text-align: right" v-if="euro_seleccionado">
                                                                            <span v-if="euros>0">
                                                                                ${{(parseFloat(euros_con_descuento *1.16 * euro)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right; width: 80px" v-if="libra_seleccionado">
                                                                            T.C.: ${{(parseFloat(libra)).formatMoney(2,'.',',')}}
                                                                        </td>
                                                                        <td style="text-align: right" v-if="libra_seleccionado">
                                                                            <span v-if="libras>0">
                                                                                ${{(parseFloat(libras_con_descuento*1.16 * libra)).formatMoney(2,'.',',')}}
                                                                            </span>
                                                                            <span v-else>
                                                                                -
                                                                            </span>
                                                                        </td>
                                                                        <td style="text-align: right">
                                                                            ${{(parseFloat(total_mxn)).formatMoney(2,'.',',')}}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Subtotal antes de descuento<span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                                    <td style="border: none; text-align: right; padding-top: 0.75rem;">
                                                        ${{(parseFloat(subtotal_antes_descuento)).formatMoney(2,'.',',')}}
                                                    </td>
                                                </tr>
                                                <tr style="border: none">
                                                    <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Descuento Global(%):</b></td>
                                                    <td style="border: none; text-align: right"> <input
                                                        v-on:keyup="calcular"
                                                        :disabled="cargando"
                                                        type="text"
                                                        name="descuento_cot"
                                                        v-model="descuento_cot"
                                                        v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                        class=" form-control"
                                                        id="descuento_cot"
                                                        style="text-align: right; padding-right: 4px"
                                                        :class="{'is-invalid': errors.has('descuento_cot')}">
                                                    </td>
                                                </tr>
                                                <!--  -->
                                                <tr style="border: none">
                                                    <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Subtotal<span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                                    <td style="border: none; text-align: right; padding-top: 0.75rem">
                                                        ${{(parseFloat(subtotal)).formatMoney(2,'.',',')}}
                                                    </td>
                                                </tr>
                                                <tr style="border: none">
                                                    <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>IVA<span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                                    <td style="border: none; text-align: right; padding-top: 0.75rem">
                                                        ${{(parseFloat(iva)).formatMoney(2,'.',',')}}
                                                    </td>
                                                </tr>
                                                <tr style="border: none">
                                                    <td colspan="2" style="border: none; text-align: right; padding-top: 0.75rem"><b>Total<span v-if="multiples_monedas"> Pesos (MXN)</span>:</b></td>
                                                    <td style="border: none; text-align: right; padding-top: 0.75rem">
                                                        ${{(parseFloat(total)).formatMoney(2,'.',',')}}
                                                    </td>
                                                </tr>
                                                <template  v-if="multiples_monedas == true || dolar_seleccionado == true || euro_seleccionado == true || libra_seleccionado == true ">
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
                            <br />
                            <div class="row">
                                <div class="col-md-12">
                                    <div >
                                        <table class="table table-sm tabla">
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
                                                    <th class="icono">
                                                        <button type="button" class="btn btn-sm btn-outline-success" @click="agregarExclusion" :disabled="cargando">
                                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                            <i class="fa fa-plus" v-else></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                            <tbody>
                                                <tr v-for="(extension, i) in exclusiones">
                                                    <td class="index_corto">{{ i + 1 }}</td>
                                                    <td>
                                                        <input class="form-control"
                                                               :name="`nombre[${i}]`"
                                                               :data-vv-as="`'Nombre ${i + 1}'`"
                                                               v-model="extension.descripcion"
                                                               :class="{'is-invalid': errors.has(`nombre[${i}]`)}"
                                                               v-validate="{ required: true}"
                                                               :id="`nombre[${i}]`"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`nombre[${i}]`)">Campo obligatorio</div>
                                                    </td>
                                                    <td>
                                                        <select
                                                            type="text"
                                                            :name="`unidad[${i}]`"
                                                            :data-vv-as="`'Unidad ${i + 1}'`"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            :id="`unidad[${i}]`"
                                                            v-model="extension.unidad"
                                                            :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                <option value>--Unidad--</option>
                                                                <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">Campo obligatorio</div>
                                                    </td>
                                                    <td>
                                                        <input class="form-control"
                                                               :name="`cantidad[${i}]`"
                                                               :data-vv-as="`'Cantidad ${i + 1}'`"
                                                               style="text-align: right"
                                                               v-model="extension.cantidad"
                                                               :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                               v-validate="{ required: true, min_value:0.01, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                               :id="`cantidad[${i}]`"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">Campo obligatorio</div>
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                               class="form-control"
                                                               :name="`precio_exclusión[${i}]`"
                                                               style="text-align: right"
                                                               :data-vv-as="`'Precio de Exclusión ${i + 1}'`"
                                                               v-validate="{required: true, min_value:0.01, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                               :class="{'is-invalid': errors.has(`precio_exclusión[${i}]`)}"
                                                               v-model="extension.precio_unitario"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`precio_exclusión[${i}]`)">Campo obligatorio</div>
                                                    </td>
                                                    <td>
                                                        <select
                                                            type="text"
                                                            :name="`moneda[${i}]`"
                                                            :data-vv-as="`'Moneda ${i + 1}'`"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            :id="`moneda[${i}]`"
                                                            v-model="extension.id_moneda"
                                                            :class="{'is-invalid': errors.has(`moneda[${i}]`)}">
                                                            <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">Campo obligatorio</div>
                                                    </td>
                                                    <td style="text-align:right;">{{getTotalExclusion(i)}}</td>
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
                                            v-model="contrato.observaciones_cot"
                                            v-validate="{required: true}"
                                            data-vv-as="Observaciones"
                                            :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>
                                Regresar</button>
                            <button type="submit" class="btn btn-primary" v-if="contrato">
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
    import TablaDatosSolicitud from "../cotizacion/partials/TablaDatosSolicitud";
    export default {
        name: "presupuesto-proveedor-create",
        props: ['id'],
        components: {Datepicker, TablaDatosSolicitud, es },
        data() {
            return {
                cargando: false,
                pendiente: false,
                contrato : null,
                es:es,
                fechasDeshabilitadas:{},
                descuento_cot : '0.00',
                fecha : '',
                monedas: [],
                unidades: [],
                pesos: 0,
                dolares: 0,
                euros: 0,
                libras:0,
                pesos_sd: 0,
                dolares_sd: 0,
                euros_sd: 0,
                libras_sd:0,
                pesos_con_descuento: 0,
                dolares_con_descuento: 0,
                euros_con_descuento: 0,
                libras_con_descuento: 0,
                dolar:0,
                euro:0,
                libra:0,
                observaciones : '',
                anticipo: 0,
                credito: 0,
                vigencia: 0,
                exclusiones : [],
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
            this.find();
            this.fecha = new Date();
            this.$validator.reset();
            this.fechasDeshabilitadas.from= new Date();
            this.fechasDeshabilitadas.to= new Date();
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            find() {
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/getSolicitud', {
                    id: this.id,
                    params:{}
                }).then(data => {
                    this.contrato = data
                    this.getMonedas(data.base_datos);
                    this.getUnidades(data.base_datos);
                })
            },
            getMonedas(base){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/monedasBase', {
                    params: {sort: 'id_moneda', order: 'asc'},
                    base : base
                }).then(data => {
                    this.monedas = data.data;
                    this.dolar = parseFloat(this.monedas[1].tipo_cambio_cadeco.cambio).formatMoney(2, '.', '');
                    this.euro = parseFloat(this.monedas[2].tipo_cambio_cadeco.cambio).formatMoney(2, '.', '');
                    this.libra = parseFloat(this.monedas[3].tipo_cambio_cadeco.cambio).formatMoney(2, '.', '');
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            getPrecioMoneda(i) {
                var precio = 0;
                if (this.contrato.conceptos.data[i]['moneda_seleccionada'] == 1) {
                    precio = this.contrato.conceptos.data[i]['precio_unitario']
                }
                if (this.contrato.conceptos.data[i]['moneda_seleccionada'] == 2) {
                    precio = this.contrato.conceptos.data[i]['precio_cot'] != undefined ? (this.contrato.conceptos.data[i]['precio_cot'] * this.dolar) - (this.contrato.conceptos.data[i]['precio_cot'] * this.dolar * this.contrato.descuento_cot/100) :  this.dolar
                }
                if (this.contrato.conceptos.data[i]['moneda_seleccionada'] == 3) {
                    precio = this.contrato.conceptos.data[i]['precio_cot'] != undefined ? (this.contrato.conceptos.data[i]['precio_cot'] * this.euro) - (this.contrato.conceptos.data[i]['precio_cot'] * this.euro * this.contrato.descuento_cot/100) : this.euro
                }
                if (this.contrato.conceptos.data[i]['moneda_seleccionada'] == 4) {
                    precio = this.contrato.conceptos.data[i]['precio_cot'] != undefined ? (this.contrato.conceptos.data[i]['precio_cot'] * this.libra) - (this.contrato.conceptos.data[i]['precio_cot'] * this.libra * this.contrato.descuento_cot/100) : this.libra
                }
                this.contrato.conceptos.data[i]['precio_unitario_moneda_conversion'] = precio;
                return  '$' + parseFloat(precio).formatMoney(2, '.', ',')
            },
            getPrecioTotalAntesDesc(i){
                var total = this.contrato.conceptos.data[i]['precio_cot'] != undefined ? this.contrato.conceptos.data[i]['precio_cot'] * this.contrato.conceptos.data[i]['cantidad_presupuestada'] : 0;
                this.contrato.conceptos.data[i]['precio_total_antes_desc'] = total;
                return  '$' + parseFloat(total).formatMoney(2, '.', ',')
            },
            getPrecioUnitario(i){
                var precio_unitario = this.contrato.conceptos.data[i]['precio_cot'] != undefined ? this.contrato.conceptos.data[i]['precio_cot'] - ((this.contrato.conceptos.data[i]['precio_cot'] * this.contrato.conceptos.data[i]['descuento_cot']) / 100) : 0;
                this.contrato.conceptos.data[i]['precio_unitario'] = precio_unitario;
                return  '$' + parseFloat(precio_unitario).formatMoney(2, '.', ',')
            },
            getPrecioTotal1(i){
                var precio_total = this.contrato.conceptos.data[i]['precio_total_antes_desc'] - ((this.contrato.conceptos.data[i]['precio_total_antes_desc'] * this.contrato.conceptos.data[i]['descuento_cot'])/100);
                this.contrato.conceptos.data[i]['precio_total'] = precio_total;
                return  '$' + parseFloat(precio_total).formatMoney(2, '.', ',')
            },
            getPrecioTotal(precio, moneda) {
                if(moneda == undefined)
                {
                    return '$0.00'
                }
                if(moneda === 1)
                {
                    return '$'+parseFloat(precio != undefined ? precio : 0).formatMoney(2,'.',',')
                }
                if(moneda === 2)
                {
                    return '$'+parseFloat(precio != undefined ? precio * this.dolar : 0).formatMoney(2,'.',',')
                }
                if(moneda === 3)
                {
                    return '$'+parseFloat(precio != undefined ? precio * this.euro : 0).formatMoney(2,'.',',')
                }
                if(moneda === 4)
                {
                    return '$'+parseFloat(precio != undefined ? precio * this.libra : 0).formatMoney(2,'.',',')
                }
            },
            getPrecio(partida){
                if(partida.precio_cot){
                    return '$' + parseFloat(partida.precio_cot * partida.cantidad_presupuestada - (partida.precio_cot * partida.cantidad_presupuestada * (partida.descuento_cot ? partida.descuento_cot : 0) / 100)).formatMoney(2,'.',',');
                }
                return '$0.00';
            },
            salir(){
                 this.$router.push({name: 'cotizacion-proveedor'});
            },
            calcular1() {
                var pesos = 0;
                var dolares = 0;
                var euros = 0;
                var libras = 0;
                var pesos_sd = 0;
                var dolares_sd = 0;
                var euros_sd = 0;
                var libras_sd = 0;
                var descuento = this.contrato.descuento_cot
                var suma = 0;
                var suma_desc = 0;
                this.contrato.conceptos.data.forEach(function (partida, i)
                {
                    if (partida.enable === true)
                    {
                        if(partida.moneda_seleccionada != undefined && partida.precio_cot != undefined)
                        {
                            suma = partida.cantidad_presupuestada * (partida.precio_cot - (partida.precio_cot * (partida.descuento_cot + descuento - (partida.descuento_cot * descuento) / 100))/100);
                            suma_desc = partida.cantidad_presupuestada * (partida.precio_cot - ((partida.precio_cot * partida.descuento_cot) / 100));
                            if(partida.moneda_seleccionada == 1)
                            {
                                pesos += suma;
                                pesos_sd += suma_desc;
                            }
                            if(partida.moneda_seleccionada == 2)
                            {
                                dolares += suma;
                                dolares_sd += suma_desc;
                            }
                            if(partida.moneda_seleccionada == 3)
                            {
                                euros += suma;
                                euros_sd += suma_desc;
                            }
                            if(partida.moneda_seleccionada == 4)
                            {
                                libras += suma;
                                libras_sd += suma_desc;
                            }
                        }
                    }
                });
                this.pesos = pesos;
                this.dolares = dolares;
                this.euros = euros;
                this.libras = libras;

                this.pesos_sd = pesos_sd;
                this.dolares_sd = dolares_sd;
                this.euros_sd = euros_sd;
                this.libras_sd = libras_sd;
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

                var estado = (this.contrato.estado == 0)
                this.contrato.conceptos.data.forEach(function (partida, i) {
                    if(partida.enable === true) {
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
                        if(partida.moneda_seleccionada != undefined && partida.precio_cot != undefined)
                        {
                            partida.calculo_precio_total = partida.cantidad_presupuestada
                                * (partida.precio_cot - (partida.precio_cot * (partida.descuento_cot ? partida.descuento_cot : 0))/100);
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
            validate() {

                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                if(this.total == 0 && this.pendiente === false)
                {
                    swal('Error', "No puede ingresar una cotización donde todas las partidas tengan precio $0.00. \n  \n Favor de ingresar los precios o seleccionar la opción de dejar pendiente la carga de precios.", 'error');
                }
                else
                {
                    this.contrato.pendiente = this.pendiente;
                    this.contrato.fecha_cot = this.fecha;
                    this.contrato.descuento_cot = this.descuento_cot;
                    this.contrato.pesos = this.pesos;
                    this.contrato.dolares = this.dolares;
                    this.contrato.euros = this.euros;
                    this.contrato.libras = this.libras;
                    this.contrato.anticipo = this.anticipo;
                    this.contrato.credito = this.credito;
                    this.contrato.vigencia = this.vigencia;
                    this.contrato.subtotal = this.subtotal_mxn;
                    this.contrato.total = this.total_mxn;
                    this.contrato.impuesto = this.iva_mxn;
                    this.contrato.tc_eur = this.euro;
                    this.contrato.tc_usd = this.dolar;
                    this.contrato.tc_libra = this.libra;
                    this.contrato.exclusiones = this.exclusiones;
                    return this.$store.dispatch('contratos/presupuesto/registrarPresupuestoProveedor', this.contrato)
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
                this.exclusiones.push(array);
            },
            quitarExclusion(index){
                this.exclusiones.splice(index, 1);
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
                var moneda = this.exclusiones[i]['id_moneda'];
                var precio_total = 0;
                if(this.exclusiones[i]['cantidad'] != 0 && this.exclusiones[i]['precio_unitario'] != 0) {
                    var precio_total = this.exclusiones[i]['cantidad'] * this.exclusiones[i]['precio_unitario']
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
                        this.contrato.conceptos.data.forEach(partida => {
                            partida.enable = true;
                        })

                    }else {
                        this.contrato.conceptos.data.forEach(partida => {
                            partida.enable = false;
                        })
                    }
                    this.calcular();
                },
            }
        },
        computed: {
            /*subtotal()
            {
                return (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra) );
            },
            subtotal_antes_descuento()
            {
                return (this.pesos_sd + (this.dolares_sd * this.dolar) + (this.euros_sd * this.euro) + (this.libras_sd * this.libra) );
            },
            iva()
            {
                return this.subtotal * 0.16;
            },
            total()
            {
                return this.subtotal + this.iva;
            }*/
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
            iva() {
                return this.subtotal * 0.16;
            },
            total() {
                return this.subtotal + this.iva;
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
