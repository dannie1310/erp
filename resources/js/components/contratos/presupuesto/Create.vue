<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group error-content">
                                            <label for="fecha" class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "fecha"
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
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_proveedor">Proveedores/Contratistas</label>
                                            <model-list-select
                                                id="id_proveedor"
                                                name="id_proveedor"
                                                option-value="id"
                                                v-model="id_proveedor"
                                                :custom-text="razonSocialRFC"
                                                :list="proveedores"
                                                :placeholder="!cargando?'Seleccionar o buscar por RFC o razón social':'Cargando...'">
                                            </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proveedor')">{{ errors.first('id_proveedor') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 offset-1" v-if="sucursal && id_proveedor">
                                        <div class="form-group">
                                            <label for="id_sucursal">Sucursal</label>
                                            <select class="form-control"
                                                    name="id_sucursal"
                                                    data-vv-as="Sucursal"
                                                    v-model="id_sucursal"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_sucursal')"
                                                    id="id_sucursal">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="sucursal in sucursales" :value="sucursal.id" >{{ sucursal.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_sucursal')">{{ errors.first('id_sucursal') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 offset-1" v-else-if="id_proveedor">
                                        <div class="form-group">
                                            <label for="id_sucursal">Sucursal</label>
                                            <select class="form-control"
                                                    name="id_sucursal"
                                                    :disabled="true"
                                                    v-model="id_sucursal"
                                                    :error="errors.has('id_sucursal')"
                                                    id="id_sucursal">
                                                <option value>-- Sin Sucursal--</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 offset-1">
                                        <div class="custom-control custom-switch" style="top:40%">
                                            <input type="checkbox" class="custom-control-input button" id="cotizacion" v-model="pendiente" >
                                            <label class="custom-control-label" for="cotizacion">Dejar pendiente captura de precios</label>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <div class="row" v-if="id_contrato != '' && !pendiente">
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
                                                                <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="enable[i]" >
                                                                <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">{{partida.cantidad_original_format}}</td>
                                                        <td style="text-align:center;">{{partida.cantidad_presupuestada_format}}</td>
                                                        <td>
                                                            <input type="text"
                                                                   :disabled="enable[i] == false"
                                                                   v-on:change="calcular()"
                                                                   class="form-control"
                                                                   :name="`precio[${i}]`"
                                                                   data-vv-as="Precio"
                                                                   v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                   :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                                   v-model="precio[i]"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$' + parseFloat(precio[i] * partida.cantidad_presupuestada).formatMoney(2, '.', ',')}}</td>
                                                        <td>
                                                            <input type="text"
                                                                   :disabled="enable[i] == false"
                                                                   v-on:change="calcular()"
                                                                   class="form-control"
                                                                   :name="`descuento[${i}]`"
                                                                   data-vv-as="Descuento(%)"
                                                                   v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                   :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                                   v-model="descuento[i]"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$' + parseFloat(precio[i] - ((precio[i] * descuento[i]) / 100)).formatMoney(2,'.',',')}}</td>
                                                        <td style="text-align:right;">{{'$' + parseFloat(partida.cantidad_presupuestada * precio[i] - ((partida.cantidad_presupuestada * precio[i] * descuento[i]) / 100)).formatMoney(2,'.',',')}}</td>
                                                        <td style="width:120px;" >
                                                            <select
                                                                type="text"
                                                                :name="`moneda[${i}]`"
                                                                v-on:change="calcular()"
                                                                data-vv-as="Moneda"
                                                                :disabled="enable[i] == false"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                id="moneda"
                                                                v-model="moneda_input[i]"
                                                                :class="{'is-invalid': errors.has(`moneda[${i}]`)}">
                                                                    <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.abreviatura }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$' + parseFloat(getPrecioUnitarioMC(i,precio[i])).formatMoney(2,'.',',')}}</td>
                                                        <td style="text-align:right;">{{'$' + parseFloat(partida.cantidad_presupuestada * getPrecioUnitarioMC(i,precio[i])).formatMoney(2,'.',',')}}</td>
                                                        <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      :disabled="enable[i] == false"
                                                                      v-validate="{}"
                                                                      :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                      v-model="observaciones_inputs[i]"/>
                                                             <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Antes de Descuento:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">${{(parseFloat(subtotal_antes_descuento)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Descuento (%):</label>
                                    </div>
                                    <div class=" col-md-2" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            v-on:change="calcular()"
                                            name="descuento_cot"
                                            v-model="descuento_cot"
                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="descuento_cot"
                                            :class="{'is-invalid': errors.has('descuento_cot')}">
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
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="tc_usd"
                                            :class="{'is-invalid': errors.has('tc_usd')}">
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
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="tc_eur"
                                            :class="{'is-invalid': errors.has('tc_eur')}">
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
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="tc_libra"
                                            :class="{'is-invalid': errors.has('tc_libra')}">
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(subtotal)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-md-2 col-form-label">TASA DE IVA:</label>
                                    </div>
                                    <div class="col-md-2 p-1" align="right">  
                                        <input
                                            type="text"
                                            name="tasa_iva"
                                            v-model="tasa_iva"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*$/}"
                                            class="col-md-6 form-control"
                                            id="tasa_iva"
                                            :class="{'is-invalid': errors.has('tasa_iva')}">
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">IVA:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(iva)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Total:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(total)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Anticipo(%):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="anticipo"
                                            v-model="anticipo"
                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="anticipo"
                                            :class="{'is-invalid': errors.has('anticipo')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Crédito (días):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="credito"
                                            v-model="credito"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="credito"
                                            :class="{'is-invalid': errors.has('credito')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Vigencia (días):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="vigencia"
                                            v-model="vigencia"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="vigencia"
                                            :class="{'is-invalid': errors.has('vigencia')}">
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
                                                v-model="observaciones"
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
                                    <button type="submit" :disabled="id_contrato == ''" class="btn btn-primary">
                                        <i class="fa fa-save"></i>
                                        Guardar
                                    </button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "presupuesto-create",
        props: ['id_contrato'],
        components: {Datepicker, ModelListSelect},
        data() {
            return {
                cargando: false,
                pendiente: false,
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                descuento_cot : '0.00',
                proveedores : [],
                sucursales: [],
                id_sucursal: '',
                id_proveedor : '',
                id_tipo : '',
                concepto : '',
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
                moneda_input:[],
                sucursal: true,
                observaciones_inputs:[],
                observaciones : '',
                precio: [],
                x: 0,
                j: 0,
                post: {
                    id_contrato: '',
                    fecha: '',
                    id_proveedor: '',
                    id_sucursal: '',
                    sucursal: '',
                    observaciones: [],
                    observacion: '',
                    moneda: [],
                    subtotal: '',
                    pendiente: '',
                    precio: [],
                    enable: [],
                    descuento: [],
                    partidas: [],
                    descuento_cot: '',
                    pago: '',
                    anticipo: '',
                    credito: '',
                    tiempo: '',
                    vigencia: '',
                    total:''
                },
                anticipo: 0,
                credito: 0,
                vigencia: 0,
                descuento: [],
                enable: [],
                tasa_iva: 16
            }
        },
        mounted() {
            this.find();
            this.fecha = new Date();
            this.$validator.reset();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}] ---- [ ${item.referencia} ]`;
            },
            razonSocialRFC (item)
            {
                return `[${item.razon_social}] - [ ${item.rfc} ]`;
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getProveedores() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'tipoEmpresa:2,3', include: 'sucursales' }
                })
                    .then(data => {
                        this.proveedores = data.data;
                    }).finally(()=>{
                        this.getMonedas();
                    })
            },
            getMonedas(){
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                    params: {sort: 'id_moneda', order: 'asc'}
                }).then(data => {
                    this.monedas = data.data;
                    this.dolar = parseFloat(this.monedas[1].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    this.euro = parseFloat(this.monedas[2].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    this.libra = this.monedas[3] ? parseFloat(this.monedas[3].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '') : 0;
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            getPrecioUnitarioMC(i, precio) {
                var suma_total = 0;
                if(this.moneda_input.length != 0) {
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 1) {
                       return  suma_total = precio != undefined ? precio - (precio * this.descuento[i]/100) : '1.00'
                    }
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 2) {
                        return suma_total = precio != undefined ? (precio * this.dolar) - (precio * this.dolar * this.descuento[i]/100) :  this.dolar
                    }
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 3) {
                        return suma_total = precio != undefined ? (precio * this.euro) - (precio * this.euro * this.descuento[i]/100) : this.euro
                    }
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 4) {
                        return suma_total = precio != undefined ? (precio * this.libra) - (precio * this.libra * this.descuento[i]/100) : this.libra
                    }
                }
            },
            salir()
            {
                 this.$router.push({name: 'presupuesto-selecciona-contrato-proyectado'});

            },
            find() {
                this.cargando = true;
                if(this.$store.getters['contratos/contrato-proyectado/currentContrato'] == null){
                    this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
                    return this.$store.dispatch('contratos/contrato-proyectado/find', {
                        id: this.id_contrato,
                        params:{include: ['conceptos']}
                    }).then(data => {
                        this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', data);
                        this.asigna();
                    }).finally(()=>{
                        this.getProveedores();
                    });
                } else {
                    this.enable = [];
                    this.precio = [];
                    this.pendiente = false;
                    this.moneda_input = [];
                    this.observaciones_inputs = [];
                    this.descuento = [];
                    this.getProveedores();
                    this.asigna();

                }
            },
            asigna()
            {
                this.j = 0;
                while(this.j < this.contrato.conceptos.data.length)
                {
                    this.enable[this.j] = true;
                    this.descuento[this.j] = 0;
                    this.moneda_input[this.j] = 1;
                    this.j ++;
                }
            },
            calcular()
            {
                this.x = 0;
                this.pesos = 0;
                this.dolares = 0;
                this.euros = 0;
                this.libras = 0;

                this.pesos_sd = 0;
                this.dolares_sd = 0;
                this.euros_sd = 0;
                this.libras_sd = 0;
                while(this.x < this.contrato.conceptos.data.length)
                {
                    if(this.moneda_input[this.x] !== '' && this.enable[this.x] !== false)
                    {
                        if(this.moneda_input[this.x] == 1 && this.precio[this.x] != undefined)
                        {

                            this.pesos = (this.pesos + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                             * (Number(this.precio[this.x]) - ((Number(this.precio[this.x]) *
                                    ( Number(this.descuento[this.x]) + Number(this.descuento_cot) - (Number(this.descuento[this.x]) * Number(this.descuento_cot)/100) )
                                )/100)  )
                            ));


                            this.pesos_sd = (this.pesos_sd + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                                * (this.precio[this.x] - ((this.precio[this.x] * this.descuento[this.x])/100)  )));
                        }
                        if(this.moneda_input[this.x] == 2 && this.precio[this.x] != undefined)
                        {
                            this.dolares = (this.dolares + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                             * (Number(this.precio[this.x]) - ((Number(this.precio[this.x]) *
                                    ( Number(this.descuento[this.x]) + Number(this.descuento_cot) - (Number(this.descuento[this.x]) * Number(this.descuento_cot)/100) )
                                )/100)  )
                            ));

                            this.dolares_sd = (this.dolares_sd + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                                * (this.precio[this.x] - ((this.precio[this.x] * this.descuento[this.x])/100)  )));
                        }
                        if(this.moneda_input[this.x] == 3 && this.precio[this.x] != undefined)
                        {
                            this.euros = (this.euros + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                               * (Number(this.precio[this.x]) - ((Number(this.precio[this.x]) *
                                    ( Number(this.descuento[this.x]) + Number(this.descuento_cot) - (Number(this.descuento[this.x]) * Number(this.descuento_cot)/100) )
                                )/100)  )
                            ));

                            this.euros_sd = (this.euros_sd + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                                * (this.precio[this.x] - ((this.precio[this.x] * this.descuento[this.x])/100)  )));
                        }
                        if(this.moneda_input[this.x] == 4 && this.precio[this.x] != undefined)
                        {
                            this.libras = (this.libras + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                                * (Number(this.precio[this.x]) - ((Number(this.precio[this.x]) *
                                    ( Number(this.descuento[this.x]) + Number(this.descuento_cot) - (Number(this.descuento[this.x]) * Number(this.descuento_cot)/100) )
                                )/100)  )
                            ));

                            this.libras_sd = (this.libras_sd + parseFloat(this.contrato.conceptos.data[this.x].cantidad_presupuestada
                                * (this.precio[this.x] - ((this.precio[this.x] * this.descuento[this.x])/100)  )));
                        }
                    }
                    this.x ++;
                }
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(!this.id_proveedor >0)
                        {
                            swal('¡Error!', 'Debe seleccionar un contratista', 'error')
                        } else
                        if(!this.id_sucursal >0)
                        {
                            swal('¡Error!', 'Debe seleccionar una sucursal', 'error')
                        } else {
                            this.post.partidas = this.contrato.conceptos.data;
                            this.post.id_contrato = this.id_contrato;
                            this.post.id_proveedor = this.id_proveedor;
                            this.post.sucursal = this.sucursal;
                            this.post.id_sucursal = this.id_sucursal;
                            this.post.observaciones = this.observaciones_inputs;
                            this.post.moneda = this.moneda_input;
                            this.post.observacion = this.observaciones;
                            this.post.precio = this.precio;
                            this.post.enable = this.enable;
                            this.post.descuento = this.descuento;
                            this.post.descuento_cot = this.descuento_cot;
                            this.post.anticipo = this.anticipo;
                            this.post.credito = this.credito;
                            this.post.vigencia = this.vigencia;
                            this.post.fecha = this.fecha;
                            this.post.subtotal = this.subtotal;
                            this.post.total = this.total;
                            this.post.impuesto = this.iva;
                            this.post.pendiente = this.pendiente;
                            this.post.tc_eur = this.euro;
                            this.post.tc_usd = this.dolar;
                            this.post.tc_libra = this.libra;
                            this.store()
                        }
                    }
                });
            },
            store() {
                if(this.total == 0 && this.pendiente === false)
                {
                    swal('¡Error!', 'Favor de ingresar partidas a cotizar', 'error');
                }
                else
                {   return this.$store.dispatch('contratos/presupuesto/store', this.post)
                    .then((data) => {
                        this.$router.push({name: 'presupuesto'});
                    });
                }
            },
        },
        computed: {
            contrato(){
                return this.$store.getters['contratos/contrato-proyectado/currentContrato'];
            },
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
                return this.subtotal * this.tasa_iva;
            },
            total()
            {
                return this.subtotal + this.iva;
            }
        },
        watch: {
            id_proveedor(value){
                this.id_sucursal = '';
                if(value !== '' && value !== null && value !== undefined){
                    var busqueda = this.proveedores.find(x=>x.id === value);
                    this.sucursales = busqueda.sucursales.data;
                    this.sucursal = (busqueda.sucursales.data.length) ? true : false;
                }
            },
        }
    }
</script>

<style scoped>

</style>
