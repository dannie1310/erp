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
                                <div class="col-md-1">
                                    <label>Fecha:</label>
                                </div>
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
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group error-content">
                                        <label class="col-form-label"><h6><b>Portal-Proveedor:</b></h6></label>
                                        <h6>{{contrato.razon_social}} [{{contrato.rfc}}]</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label class="col-form-label"><h6><b>Sucursal:</b></h6></label>
                                        <h6>{{contrato.sucursal}}</h6>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input button" id="cotizacion" v-model="pendiente" >
                                        <label class="custom-control-label" for="cotizacion">Dejar pendiente la captura de precios</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="id != '' && !pendiente">
                                <div  class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                            <tr>
                                                <th class="index_corto">#</th>
                                                <th>Descripción</th>
                                                <th class="unidad">Unidad</th>
                                                <th></th>
                                                <th >Cantidad Solicitada</th>
                                                <th >Cantidad Aprobada</th>
                                                <th class="cantidad_input">Precio Unitario</th>
                                                <th >Precio Total Antes Descto.</th>
                                                <th class="cantidad_input">% Descuento</th>
                                                <th >Precio Unitario</th>
                                                <th >Precio Total</th>
                                                <th >Moneda</th>
                                                <th >Precio Unitario Moneda Conversión</th>
                                                <th >Precio Total Moneda Conversión</th>
                                                <th >Observaciones</th>
                                            </tr>
                                            </thead>
                                            <tbody v-if="contrato">
                                                <tr v-for="(partida, i) in contrato.conceptos.data">
                                                    <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                    <td style="text-align:left;" v-html="partida.descripcion_formato"></td>
                                                    <td>{{partida.unidad}}</td>
                                                    <td style="text-align:center; vertical-align:inherit;">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="partida.enable" >
                                                            <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align:right;">{{partida.cantidad_original_format}}</td>
                                                    <td style="text-align:right;">{{partida.cantidad_presupuestada_format}}</td>
                                                    <td>
                                                        <input type="text" v-on:change="calcular"
                                                            :disabled="partida.enable == false"
                                                            class="form-control"
                                                            :name="`precio[${i}]`"
                                                            :data-vv-as="`'Precio ${i + 1}'`"
                                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d{2})?$/}"
                                                            :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                            v-model="partida.precio_cot"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                    </td>
                                                    <td style="text-align:right;">{{getPrecioTotalAntesDesc(i)}}</td>
                                                    <td>
                                                        <input type="text" v-on:change="calcular"
                                                            :disabled="partida.enable == false"
                                                            class="form-control"
                                                            :name="`descuento[${i}]`"
                                                            :data-vv-as="`'Descuento(%) ${i + 1}'`"
                                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d{2})?$/}"
                                                            :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                            v-model="partida.descuento_cot"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                    </td>
                                                    <td style="text-align:right;">{{getPrecioUnitario(i)}}</td>
                                                    <td style="text-align:right;">{{getPrecioTotal(i)}}</td>
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
                                                            :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.abreviatura }}</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                    </td>
                                                    <td style="text-align:right;">{{getPrecioMoneda(i)}}</td>
                                                    <td style="text-align:right;">{{'$' + parseFloat(partida.cantidad_presupuestada * partida.precio_unitario_moneda_conversion).formatMoney(2,'.',',')}}</td>
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
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Antes de Descuento:</label>
                                    <label class="col-sm-2 col-form-label money" style="text-align: right">${{(parseFloat(subtotal_antes_descuento)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">Descuento (%):</label>
                                </div>
                                <div class=" col-md-2" align="right">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        v-on:change="calcular"
                                        name="descuento_cot"
                                        v-model="contrato.descuento_cot"
                                        v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d{2})?$/}"
                                        class="col-sm-6 form-control"
                                        id="descuento_cot"
                                        :class="{'is-invalid': errors.has('descuento_cot')}">
                                    <div class="invalid-feedback" v-show="errors.has('descuento_cot')">{{ errors.first('descuento_cot') }}</div>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Precios Peso (MXN)</label>
                                    <label class="col-sm-2 col-form-label" style="text-align: right">${{(parseFloat(pesos)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Precios Dolar (USD):</label>
                                    <label class="col-sm-2 col-form-label" style="text-align: right">${{(parseFloat(dolares)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Precios EURO:</label>
                                    <label class="col-sm-2 col-form-label" style="text-align: right">${{(parseFloat(euros)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Precios LIBRA:</label>
                                    <label class="col-sm-2 col-form-label" style="text-align: right">${{(parseFloat(libras)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">TC USD:</label>
                                </div>
                                <div class=" col-md-2 p-1" align="right">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="tc_usd"
                                        v-model="dolar"
                                        v-validate="{required: true, min_value:0}"
                                        class="col-sm-6 form-control"
                                        id="tc_usd"
                                        :class="{'is-invalid': errors.has('tc_usd')}">
                                    <div class="invalid-feedback" v-show="errors.has('tc_usd')">{{ errors.first('tc_usd') }}</div>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">TC EURO:</label>
                                </div>
                                <div class=" col-md-2 p-1" align="right">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="tc_eur"
                                        v-model="euro"
                                        v-validate="{required: true, min_value:0}"
                                        class="col-sm-6 form-control"
                                        id="tc_eur"
                                        :class="{'is-invalid': errors.has('tc_eur')}">
                                    <div class="invalid-feedback" v-show="errors.has('tc_eur')">{{ errors.first('tc_eur') }}</div>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">TC LIBRA:</label>
                                </div>
                                <div class=" col-md-2 p-1" align="right">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="tc_libra"
                                        v-model="libra"
                                        v-validate="{required: true, min_value:0 }"
                                        class="col-sm-6 form-control"
                                        id="tc_libra"
                                        :class="{'is-invalid': errors.has('tc_libra')}">
                                    <div class="invalid-feedback" v-show="errors.has('tc_libra')">{{ errors.first('tc_libra') }}</div>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                    <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(subtotal)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">IVA:</label>
                                    <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(iva)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Total:</label>
                                    <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(total)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">Anticipo(%):</label>
                                </div>
                                <div class=" col-md-2 p-1" align="right">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="anticipo"
                                        v-model="contrato.anticipo"
                                        v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                        class="col-sm-6 form-control"
                                        id="anticipo"
                                        :class="{'is-invalid': errors.has('anticipo')}">
                                    <div class="invalid-feedback" v-show="errors.has('anticipo')">{{ errors.first('anticipo') }}</div>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">Crédito (días):</label>
                                </div>
                                <div class=" col-md-2 p-1" align="right">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="credito"
                                        v-model="contrato.credito"
                                        v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                        class="col-sm-6 form-control"
                                        id="credito"
                                        :class="{'is-invalid': errors.has('credito')}">
                                    <div class="invalid-feedback" v-show="errors.has('credito')">{{ errors.first('credito') }}</div>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">Vigencia (días):</label>
                                </div>
                                <div class=" col-md-2 p-1" align="right">
                                    <input
                                        :disabled="cargando"
                                        type="text"
                                        name="vigencia"
                                        v-model="contrato.vigencia"
                                        v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                        class="col-sm-6 form-control"
                                        id="vigencia"
                                        :class="{'is-invalid': errors.has('vigencia')}">
                                    <div class="invalid-feedback" v-show="errors.has('vigencia')">{{ errors.first('vigencia') }}</div>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <td colspan="8" style="text-align: center;"><b>Exclusiones</b></td>
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
                                                        <div class="invalid-feedback" v-show="errors.has(`nombre[${i}]`)">{{ errors.first(`nombre[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                        <select
                                                            type="text"
                                                            :name="`unidad[${i}]`"
                                                            :data-vv-as="`Unidad[${i}]`"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            :id="`unidad[${i}]`"
                                                            v-model="extension.unidad"
                                                            :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                <option value>--Unidad--</option>
                                                                <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
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
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                               class="form-control"
                                                               :name="`precio[${i}]`"
                                                               style="text-align: right"
                                                               :data-vv-as="`'Precio ${i + 1}'`"
                                                               v-validate="{required: true, min_value:0.01, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                               :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                               v-model="extension.precio_unitario"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
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
                                                        <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
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
                fecha : '',
                monedas: [],
                pesos: 0,
                dolares: 0,
                euros: 0,
                libras:0,
                pesos_sd: 0,
                dolares_sd: 0,
                euros_sd: 0,
                libras_sd:0,
                dolar:0,
                euro:0,
                libra:0,
                observaciones : '',
                anticipo: 0,
                credito: 0,
                vigencia: 0,
                exclusiones : []
            }
        },
        mounted() {
            this.find();
            this.fecha = new Date();
            this.$validator.reset();
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
            getPrecioTotal(i){
                var precio_total = this.contrato.conceptos.data[i]['precio_total_antes_desc'] - ((this.contrato.conceptos.data[i]['precio_total_antes_desc'] * this.contrato.conceptos.data[i]['descuento_cot'])/100);
                this.contrato.conceptos.data[i]['precio_total'] = precio_total;
                return  '$' + parseFloat(precio_total).formatMoney(2, '.', ',')
            },
            salir(){
                 this.$router.push({name: 'cotizacion-proveedor'});
            },
            calcular() {
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
                    swal('¡Error!', 'Favor de ingresar partidas a cotizar', 'error');
                }
                else
                {
                    this.contrato.pendiente = this.pendiente;
                    this.contrato.fecha_cot = this.fecha;
                    this.contrato.pesos = this.pesos;
                    this.contrato.dolares = this.dolares;
                    this.contrato.euros = this.euros;
                    this.contrato.libras = this.libras;
                    this.contrato.anticipo = this.anticipo;
                    this.contrato.credito = this.credito;
                    this.contrato.vigencia = this.vigencia;
                    this.contrato.subtotal = this.subtotal;
                    this.contrato.total = this.total;
                    this.contrato.impuesto = this.iva;
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
        computed: {
            subtotal()
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
            }
        },
    }
</script>

<style>

</style>
