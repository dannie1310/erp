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
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_solicitud">Buscar Solicitud:</label>
                                                 <model-list-select
                                                     id="id_solicitud"
                                                     name="id_solicitud"
                                                     option-value="id"
                                                     v-model="id_solicitud"
                                                     :custom-text="idFolioObservaciones"
                                                     :list="solicitudes"
                                                     :placeholder="!cargando?'Seleccionar o buscar solicitud de compra por número de folio, concepto u observaciones':'Cargando...'">
                                                 </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="id_proveedor">Proveedor:</label>
                                                <model-list-select
                                                    id="id_proveedor"
                                                    name="id_proveedor"
                                                    option-value="id"
                                                    v-model="id_proveedor"
                                                    :custom-text="razonSocialRFC"
                                                    :list="proveedores"
                                                    :placeholder="!cargando?'Seleccionar o busca proveedor por razón social o RFC':'Cargando...'">
                                                 </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proveedor')">{{ errors.first('id_proveedor') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 offset-1" v-if="id_proveedor">
                                        <div class="form-group">
                                            <label for="id_sucursal">Sucursal:</label>
                                            <select class="form-control"
                                                    name="id_sucursal"
                                                    data-vv-as="Sucursal"
                                                    v-model="id_sucursal"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_sucursal')"
                                                    id="id_sucursal">
                                                <option value >-- Seleccionar--</option>
                                                <option v-for="sucursal in sucursales" :value="sucursal.id" >{{ sucursal.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_sucursal')">{{ errors.first('id_sucursal') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 offset-1">
                                        <div class="custom-control custom-switch" style="top:40%">
                                            <input type="checkbox" class="custom-control-input button" id="cotizacion" v-model="pendiente" >
                                            <label class="custom-control-label" for="cotizacion">Dejar pendiente la captura de precios</label>
                                        </div>
                                    </div>
                                </div>

                                <hr />
                                <div class="row" v-if="id_solicitud != '' && !pendiente">
                                    <div  class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th style="width:110px;">No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th class="index_corto"></th>
                                                    <th class="cantidad_input">Cantidad Solicitada</th>
                                                    <th class="cantidad_input">Cantidad Aprobada</th>
                                                    <th class="cantidad_input">Precio Unitario</th>
                                                    <th class="cantidad_input">% Descuento</th>
                                                    <th class="money">Precio Total</th>
                                                    <th class="money">Moneda</th>
                                                    <th class="money">Precio Total Moneda Conversión</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="solicitud">
                                                    <tr v-for="(partida, i) in solicitud.partidas.data">
                                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                        <td style="text-align:center;">{{partida.material.numero_parte}}</td>
                                                        <td>{{partida.material.descripcion}}</td>
                                                        <td style="text-align:center;">{{partida.material.unidad}}</td>
                                                        <td style="text-align:center; vertical-align:inherit;">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="enable[i]" checked>
                                                                <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">{{partida.cantidad_original_num}}</td>
                                                        <td style="text-align:center;">{{(solicitud.estado === 1) ? partida.cantidad : '0.0'}}</td>
                                                        <td>
                                                            <input type="number"
                                                                   min="0.01"
                                                                   step=".01"
                                                                   :disabled="enable[i] == false"
                                                                   class="form-control"
                                                                   :name="`precio[${i}]`"
                                                                   data-vv-as="Precio"
                                                                   v-validate="{required: true}"
                                                                   :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                                   v-model="precio[i]"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                        </td>
                                                        <td>
                                                            <input type="number"
                                                                   min="0.00"
                                                                   max="100"
                                                                   step=".01"
                                                                   :disabled="enable[i] == false"
                                                                   class="form-control"
                                                                   :name="`descuento[${i}]`"
                                                                   data-vv-as="Descuento(%)"
                                                                   v-validate="{required: true}"
                                                                   :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                                   v-model="descuento[i]"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{(precio[i]) ? '$ ' + parseFloat(((solicitud.estado === 0) ? partida.cantidad_original_num : partida.cantidad) * precio[i]).formatMoney(2,'.',',') : '$ 0.00'}}</td>
                                                        <td style="width:120px;" >
                                                            <select
                                                                type="text"
                                                                :name="`moneda[${i}]`"
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
                                                        <td style="text-align:right;">{{'$ '+parseFloat(getPrecioTotal(i,partida.calculo_precio_total)).formatMoney(2,'.',',')}}</td>
                                                        <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      :disabled="enable[i] == false"
                                                                      v-validate="{required: true}"
                                                                      :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                      v-model="observaciones_inputs[i]"/>
                                                             <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">% Descuento:</label>
                                    </div>
                                    <div class=" col-md-2" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="number"
                                            step=".01"
                                            max="100"
                                            name="descuento_cot"
                                            v-model="descuento_cot"
                                            v-validate="{required: true}"
                                            class="col-sm-6 form-control"
                                            id="descuento_cot"
                                            :class="{'is-invalid': errors.has('descuento_cot')}">
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Precios Peso (MXP)</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(pesos)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Precios Dolar (USD):</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(dolares)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Precios EURO:</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(euros)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Precios Libra:</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(libras)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">TC USD:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[1].tipo_cambio_cadeco.cambio)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">TC EURO:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[2].tipo_cambio_cadeco.cambio)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">TC LIBRA:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[3].tipo_cambio_cadeco.cambio)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Moneda Conversión (MXP):</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(subtotal)).formatMoney(4,'.',',')}}</label>
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
                                        <label class="col-sm-2 col-form-label">Pago en Parcialidades (%):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="number"
                                            step="1"
                                            max="100"
                                            name="pago"
                                            v-model="pago"
                                            v-validate="{required: true}"
                                            class="col-sm-6 form-control"
                                            id="pago"
                                            :class="{'is-invalid': errors.has('pago')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">% Anticipo:</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="number"
                                            step=".01"
                                            max="100"
                                            name="anticipo"
                                            v-model="anticipo"
                                            v-validate="{required: true}"
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
                                            type="number"
                                            step="1"
                                            name="credito"
                                            v-model="credito"
                                            v-validate="{required: true}"
                                            class="col-sm-6 form-control"
                                            id="credito"
                                            :class="{'is-invalid': errors.has('credito')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Tiempo de Entrega (días):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="number"
                                            step="1"
                                            name="tiempo"
                                            v-model="tiempo"
                                            v-validate="{required: true}"
                                            class="col-sm-6 form-control"
                                            id="tiempo"
                                            :class="{'is-invalid': errors.has('tiempo')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Vigencia (días):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="number"
                                            step="1"
                                            name="vigencia"
                                            v-model="vigencia"
                                            v-validate="{required: true}"
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
                                    <button type="submit" :disabled="id_solicitud == ''" class="btn btn-primary">
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
        name: "cotizacion-create",
        components: {Datepicker, ModelListSelect},
        data() {
            return {
                cargando: false,
                pendiente: false,
                id_solicitud: '',
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                descuento_cot : '0.00',
                proveedores : [],
                sucursales: [],
                id_sucursal: '',
                id_proveedor : '',
                id_tipo : '',
                solicitudes : [],
                concepto : '',
                monedas: [],
                pesos: 0,
                dolares: 0,
                euros: 0,
                libras: 0,
                moneda_input:[],
                sucursal: true,
                observaciones_inputs:[],
                observaciones : '',
                precio: [],
                x: 0,
                pago: 0,
                post: {
                    id_solicitud: '',
                    fecha: '',
                    id_proveedor: '',
                    id_sucursal: '',
                    sucursal: '',
                    observaciones: [],
                    observacion: '',
                    moneda: [],
                    importe: '',
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
                    vigencia: ''
                },
                anticipo: 0,
                credito: 0,
                tiempo: 0,
                vigencia: 0,
                descuento: [],
                enable: []

            }
        },
        mounted() {
            this.fecha = new Date();
            this.$validator.reset();
            this.getProveedores();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}] - [ ${item.concepto} ] - [ ${item.observaciones} ]`;
            },
            razonSocialRFC (item)
            {
                return `[${item.razon_social}] - [ ${item.rfc} ]`;
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getProveedores() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'tipoEmpresa:1,3', include: 'sucursales' }
                })
                    .then(data => {
                        this.proveedores = data.data;
                    })
                .finally(()=>{
                    this.getMonedas();
                })
            },
            getMonedas(){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                    params: {sort: 'id_moneda', order: 'asc',}
                    }).then(data => {
                    this.monedas = data.data;
                }).finally(()=>{
                    this.getSolicitudes();
                })
            },
            salir()
            {
                 this.$router.push({name: 'cotizacion'});
            },
            find() {
                this.enable = [];
                this.precio = [];
                this.pendiente = false;
                this.moneda_input = [];
                this.observaciones_inputs = [];
                this.descuento = [];
                this.cargando = true;
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id_solicitud,
                    params:{include: [
                            'complemento',
                            'partidas.complemento',
                            'partidas.entrega',
                            'cotizaciones']}
                }).then(data => {
                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);
                    this.cargando = false;
                })
            },
            calcular()
            {
                this.x = 0;
                this.pesos = 0;
                this.dolares = 0;
                this.euros = 0;
                this.libras = 0;
                while(this.x < this.solicitud.partidas.data.length)
                {
                    if(this.moneda_input[this.x] !== '' && this.moneda_input[this.x] !== null && this.moneda_input[this.x] !== undefined && this.enable[this.x] !== false)
                    {
                        if(this.moneda_input[this.x] == 1 && this.precio[this.x] != undefined)
                        {
                            this.pesos = (this.pesos + parseFloat(((this.solicitud.estado === 0) ?
                            this.solicitud.partidas.data[this.x].cantidad_original_num :
                            this.solicitud.partidas.data[this.x].cantidad) * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ?
                            this.descuento[this.x] : 0))/100))));
                            this.solicitud.partidas.data[this.x].calculo_precio_total = (this.solicitud.estado === 0 ?
                                this.solicitud.partidas.data[this.x].cantidad_original_num : this.solicitud.partidas.data[this.x].cantidad)
                                * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ? this.descuento[this.x] : 0))/100));
                        }
                        if(this.moneda_input[this.x] == 2 && this.precio[this.x] != undefined)
                        {
                            this.dolares = (this.dolares + parseFloat(((this.solicitud.estado === 0) ?
                            this.solicitud.partidas.data[this.x].cantidad_original_num :
                            this.solicitud.partidas.data[this.x].cantidad) * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ?
                            this.descuento[this.x] : 0))/100))));
                            this.solicitud.partidas.data[this.x].calculo_precio_total = ((this.solicitud.estado === 0 ?
                                this.solicitud.partidas.data[this.x].cantidad_original_num : this.solicitud.partidas.data[this.x].cantidad)
                                * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ? this.descuento[this.x] : 0))/100)));
                        }
                        if(this.moneda_input[this.x] == 3 && this.precio[this.x] != undefined)
                        {
                            this.euros = (this.euros + parseFloat(((this.solicitud.estado === 0) ?
                            this.solicitud.partidas.data[this.x].cantidad_original_num :
                            this.solicitud.partidas.data[this.x].cantidad) * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ?
                            this.descuento[this.x] : 0))/100))));
                            this.solicitud.partidas.data[this.x].calculo_precio_total = ((this.solicitud.estado === 0 ?
                                this.solicitud.partidas.data[this.x].cantidad_original_num : this.solicitud.partidas.data[this.x].cantidad)
                                * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ? this.descuento[this.x] : 0))/100)));
                        }
                        if(this.moneda_input[this.x] == 4 && this.precio[this.x] != undefined)
                        {
                            this.libras = (this.libras + parseFloat(((this.solicitud.estado === 0) ?
                                this.solicitud.partidas.data[this.x].cantidad_original_num :
                                this.solicitud.partidas.data[this.x].cantidad) * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ?
                                this.descuento[this.x] : 0))/100))));
                            this.solicitud.partidas.data[this.x].calculo_precio_total = ((this.solicitud.estado === 0 ?
                                this.solicitud.partidas.data[this.x].cantidad_original_num : this.solicitud.partidas.data[this.x].cantidad)
                                * (this.precio[this.x] - ((this.precio[this.x] * ((this.descuento[this.x]) ? this.descuento[this.x] : 0))/100)));
                        }
                    }
                    this.x ++;
                }
            },
            getSolicitudes() {
                this.solicitudes = [];
                this.cargando = true;
                return this.$store.dispatch('compras/solicitud-compra/index', {
                    params: {
                        scope: ['conItems','areasCompradorasAsignadas','conAutorizacion'],
                        order: 'DESC',
                        sort: 'numero_folio'
                    }
                })
                .then(data => {
                    this.solicitudes = data.data;
                })
                .finally(()=>{
                    this.cargando = false;
                })
            },
            validate() {

                this.$validator.validate().then(result => {
                    if (result) {
                        this.post.partidas = this.solicitud.partidas.data;
                        this.post.id_solicitud = this.id_solicitud;
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
                        this.post.pago = this.pago;
                        this.post.anticipo = this.anticipo;
                        this.post.credito = this.credito;
                        this.post.tiempo = this.tiempo;
                        this.post.vigencia = this.vigencia;
                        this.post.fecha = this.fecha;
                        this.post.importe = this.total;
                        this.post.impuesto = this.iva;
                        this.post.pendiente = this.pendiente;
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
                {   return this.$store.dispatch('compras/cotizacion/store', this.post)
                    .then((data) => {
                        this.$router.push({name: 'cotizacion'});
                    });
                }
            },
            getPrecioTotal(i, precio) {
                var suma_total = 0;
                if(this.moneda_input.length != 0) {
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 1) {
                       return  suma_total = precio != undefined ? precio : '1.00'
                    }
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 2) {
                        return suma_total = precio != undefined ? precio * this.monedas[1].tipo_cambio_cadeco.cambio : this.monedas[1].tipo_cambio_cadeco.cambio
                    }
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 3) {
                        return suma_total = precio != undefined ? precio * this.monedas[2].tipo_cambio_cadeco.cambio : this.monedas[2].tipo_cambio_cadeco.cambio
                    }
                    if (this.moneda_input[i] != undefined && this.moneda_input[i] == 4) {
                        return suma_total = precio != undefined ? precio * this.monedas[3].tipo_cambio_cadeco.cambio : this.monedas[3].tipo_cambio_cadeco.cambio
                    }
                }
            }
        },
        computed: {
            solicitud(){
                return this.$store.getters['compras/solicitud-compra/currentSolicitud'];
            },
            subtotal()
            {
                return (this.pesos + (this.dolares * this.monedas[1].tipo_cambio_cadeco.cambio) + (this.euros * this.monedas[2].tipo_cambio_cadeco.cambio) + (this.libras * this.monedas[3].tipo_cambio_cadeco.cambio) -
                    ((this.descuento_cot > 0) ? (((this.pesos + (this.dolares * this.monedas[1].tipo_cambio_cadeco.cambio) + (this.euros *
                        this.monedas[2].tipo_cambio_cadeco.cambio) + (this.libras * this.monedas[3].tipo_cambio_cadeco.cambio)) * parseFloat(this.descuento_cot)) / 100) : 0));
            },
            iva() {
                return this.subtotal * 0.16;
            },
            total() {
                return this.subtotal + this.iva;
            },

        },
        watch: {
            id_solicitud(value)
            {
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.find();
                }
            },
            id_proveedor(value){
                this.id_sucursal = '';
                if(value !== '' && value !== null && value !== undefined){
                    var busqueda = this.proveedores.find(x=>x.id === value);
                    this.sucursales = busqueda.sucursales.data;
                    this.sucursal = (busqueda.sucursales.data.length) ? true : false;
                    if(this.sucursales.length == 1){
                        this.id_sucursal = this.sucursales[0].id;
                    }
                }
            },
            moneda_input()
            {
                if(this.moneda_input.length > 0)
                {
                    this.calcular();
                }
            },
            precio()
            {
                if(this.precio.length > 0)
                {
                    this.calcular();
                }
            },
            descuento()
            {
                if(this.descuento.length > 0)
                {
                    this.calcular();
                }
            },
            enable()
            {
                if(this.enable.length > 0)
                {
                    this.calcular();
                }
            }

        }
    }
</script>

<style scoped>

</style>
