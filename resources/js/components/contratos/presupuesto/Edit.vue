<template>
    <span>
        <nav>
            <div class="row" v-if="presupuesto != ''">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group error-content">
                                            <label for="fecha" class="col-form-label">Fecha:</label>
                                            <datepicker v-model="presupuesto.fecha"
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
                                <div class="table-responsive col-md-12">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="bg-gray-light" align="center" colspan="6"><b>{{(presupuesto.empresa) ? presupuesto.empresa.razon_social : '----------'}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                    <td class="bg-gray-light">{{(presupuesto.sucursal) ? presupuesto.sucursal.descripcion : '----------'}}</td>
                                                    <td class="bg-gray-light"><b>TC USD:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_usd_format}}</td>
                                                    <td class="bg-gray-light"><b>TC EURO:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_euro_format}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Direccion:</b></td>
                                                    <td class="bg-gray-light">{{(presupuesto.sucursal) ? presupuesto.sucursal.direccion : '----------'}}</td>
                                                    <td class="bg-gray-light"><b>Folio:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.numero_folio}}</td>
                                                    <td class="bg-gray-light"><b>Importe:</b></td>
                                                    <td class="bg-gray-light">{{importe}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <hr />
                                
                                <div class="row" v-if="presupuesto.partidas">
                                    <div  class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th>Descripción</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th></th>
                                                    <th class="money">Cantidad Solicitada</th>
                                                    <th class="money">Cantidad Aprobada</th>                                                  
                                                    <th class="cantidad_input">Precio Unitario</th>
                                                    <th class="money">Precio Total Antes Descto.</th>
                                                    <th class="money">% Descuento</th>
                                                    <th class="money">Precio Unitario</th>
                                                    <th class="money">Precio Total</th>
                                                    <th class="money">Moneda</th>
                                                    <th class="money">Precio Unitario Moneda Conversión</th>
                                                    <th class="money">Precio Total Moneda Conversión</th>
                                                    <th style="width:10%;">Observaciones</th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="presupuesto.partidas">
                                                    <tr v-for="(partida, i) in presupuesto.partidas.data">
                                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                        <td v-html="partida.concepto.descripcion_formato" v-if="partida.concepto"></td>
                                                        <td v-else>-----------</td>
                                                        <td style="text-align:center;">{{(partida.concepto) ? partida.concepto.unidad : '----'}}</td>
                                                         <td style="text-align:center; vertical-align:inherit;">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="enable[i]" checked>
                                                                <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                            </div>
                                                        </td>                                                        
                                                        <td style="text-align:center;">{{(partida.concepto) ? partida.concepto.cantidad_original_format : '----------'}}</td>
                                                        <td style="text-align:center;">{{(partida.concepto) ? partida.concepto.cantidad_presupuestada_format : '----------'}}</td>
                                                        <td>
                                                            <input type="number"
                                                                   min="0.01"
                                                                   step=".01"
                                                                   class="form-control"
                                                                   :disabled="enable[i] == false"
                                                                   :name="`precio[${i}]`"
                                                                   v-model="precio[i]"
                                                                   data-vv-as="Precio"
                                                                   v-validate="{required: true}"
                                                                   :class="{'is-invalid': errors.has(`precio[${i}]`)}"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$ ' + parseFloat(precio[i] * ((partida.concepto) ? partida.concepto.cantidad_presupuestada : 0)).formatMoney(2, '.', ',')}}</td>
                                                        <td>
                                                            <input type="number"
                                                                   min="0.00"
                                                                   max="100"
                                                                   step=".01"
                                                                   :disabled="enable[i] == false"
                                                                   class="form-control"
                                                                   :name="`descuento[${i}]`"
                                                                   v-model="descuento[i]"
                                                                   data-vv-as="Descuento(%)"
                                                                   v-validate="{required: true}"
                                                                   :class="{'is-invalid': errors.has(`descuento[${i}]`)}"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$ ' + parseFloat(precio[i] - ((descuento[i] > 0) ? ((precio[i] * descuento[i]) / 100) : 0)).formatMoney(2, '.', ',')}}</td>
                                                        <td style="text-align:right;">{{'$ ' + parseFloat((precio[i] * ((partida.concepto) ? partida.concepto.cantidad_presupuestada : 0)) - ((descuento[i] > 0) ? (((precio[i] * ((partida.concepto) ? partida.concepto.cantidad_presupuestada : 0)) * descuento[i]) / 100) : 0)).formatMoney(2, '.', ',')}}</td>
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
                                                                :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                    <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.abreviatura }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$ ' + parseFloat((precio[i] * ((moneda_input[i] > 1) ? ((moneda_input[i] == 2) ? presupuesto.tc_usd : presupuesto.tc_euro) : 1)) - ((descuento[i] > 0) ? (((precio[i] * ((moneda_input[i] > 1) ? ((moneda_input[i] == 2) ? presupuesto.tc_usd : presupuesto.tc_euro) : 1)) * descuento[i]) / 100) : 0)).formatMoney(2, '.', ',')}}</td>
                                                        <td style="text-align:right;">{{'$ ' + parseFloat(((precio[i] * ((moneda_input[i] > 1) ? ((moneda_input[i] == 2) ? presupuesto.tc_usd : presupuesto.tc_euro) : 1)) * ((partida.concepto) ? partida.concepto.cantidad_presupuestada : 0)) - ((descuento[i] > 0) ? ((((precio[i] * ((moneda_input[i] > 1) ? ((moneda_input[i] == 2) ? presupuesto.tc_usd : presupuesto.tc_euro) : 1)) * ((partida.concepto) ? partida.concepto.cantidad_presupuestada : 0)) * descuento[i]) / 100) : 0)).formatMoney(2, '.', ',')}}</td>
                                                       <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      :disabled="enable[i] == false"
                                                                      v-validate="{required: true}"
                                                                      :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                      v-model="partida.observaciones"/>
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
                                                                min="0"
                                                                name="descuento_cot"
                                                                v-model="descuento_cot"
                                                                v-validate="{required: true,}"
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
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(dolares)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Precios EURO:</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(euros)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">TC USD:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">{{dolar}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">TC EURO:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">{{euro}}</label>
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
                                        <label class="col-sm-2 col-form-label">% Anticipo:</label>                                        
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step=".01"
                                                                max="100"
                                                                min="0"
                                                                name="anticipo"
                                                                v-model="anticipo"
                                                                v-validate="{required: true,}"
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
                                                                min="0"
                                                                name="credito"
                                                                v-model="credito"
                                                                v-validate="{required: true,}"
                                                                class="col-sm-6 form-control"
                                                                id="credito"
                                                                :class="{'is-invalid': errors.has('credito')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Vigencia( días):</label>                                        
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step="1"
                                                                min="0"
                                                                name="vigencia"
                                                                v-model="vigencia"
                                                                v-validate="{required: true,}"
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
                                                v-model="presupuesto.observaciones"
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
                                    <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Registrar</button>
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
        name: "presupuesto-edit",

        components: {Datepicker, ModelListSelect},
        props: ['id'],
        data() {
            return {
                cargando: false,
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                descuento_cot : '0.00',
                tipo_cambio: [],
                id_tipo : '',
                monedas: [],
                pesos: 0,
                dolares: 0,
                euros: 0,
                presupuesto: '',
                moneda_input:[],
                observaciones_inputs:[],
                observaciones : '',
                precio: [],
                x: 0,
                post: {
                    id_presupuesto: '',
                    fecha: '',
                    moneda: [],
                    partidas: [],
                    precio: [],
                    enable: [],
                    descuento: [],
                    descuento_cot: '',
                    anticipo: '',
                    credito: '',
                    vigencia: '',
                    subtotal: '',
                    impuesto: '',
                    observaciones: '',
                    tipo_cambio: []
                },
                anticipo: 0,
                credito: 0,
                vigencia: 0,
                descuento: [],
                enable: []
                             
            }
        },
        mounted() {
            this.getMonedas();
            this.enable = [];
            this.precio = [];
            this.moneda_input = [];
            this.observaciones_inputs = [];
            this.descuento = [];
            this.find();            
            this.$validator.reset();
            
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getMonedas(){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                }).then(data => {
                    this.monedas = data.data;
                    this.monedas.pop();
                }).finally(()=>{

                })
            },
            salir()
            {
                 this.$router.push({name: 'presupuesto'});
                
            },
            find() {
                
                this.cargando = true;
                this.$store.commit('contratos/presupuesto/SET_PRESUPUESTO', null);
                return this.$store.dispatch('contratos/presupuesto/find', {
                    id: this.id,
                    params:{include: [
                        'empresa',
                        'sucursal',
                        'partidas.concepto'
                    ]}
                }).then(data => {         
                    this.presupuesto = data;                               
                    this.fecha = data.fecha;                    
                    this.ordenar();
                    this.cargando = false;                    
                })
            },
            calcular()
            {
                this.x = 0;
                this.pesos = 0;
                this.dolares = 0;
                this.euros = 0;
                while(this.x < this.presupuesto.partidas.data.length)
                {                    
                    if(this.moneda_input[this.x] !== '' && this.moneda_input[this.x] !== null && this.moneda_input[this.x] !== undefined && this.enable[this.x] !== false)
                    {                                           
                        if(this.moneda_input[this.x] == 1 && this.precio[this.x] != undefined)
                        {
                            this.pesos = (this.pesos + parseFloat(this.presupuesto.partidas.data[this.x].concepto.cantidad_presupuestada * this.precio[this.x] - 
                            ((this.presupuesto.partidas.data[this.x].concepto.cantidad_presupuestada * this.precio[this.x] * this.descuento[this.x]) / 100)));
                        }
                        if(this.moneda_input[this.x] == 2 && this.precio[this.x] != undefined)
                        {
                            this.dolares = (this.dolares + parseFloat(this.presupuesto.partidas.data[this.x].concepto.cantidad_presupuestada * this.precio[this.x] - 
                            ((this.presupuesto.partidas.data[this.x].concepto.cantidad_presupuestada * this.precio[this.x] * this.descuento[this.x]) / 100)));
                        }
                        if(this.moneda_input[this.x] == 3 && this.precio[this.x] != undefined)
                        {
                            this.euros = (this.euros + parseFloat(this.presupuesto.partidas.data[this.x].concepto.cantidad_presupuestada * this.precio[this.x] - 
                            ((this.presupuesto.partidas.data[this.x].concepto.cantidad_presupuestada * this.precio[this.x] * this.descuento[this.x]) / 100)));
                        }                       
                    }
                    this.x ++;                    
                }                     
            },
            ordenar()
            {                
                this.x = 0;
                while(this.x < this.presupuesto.partidas.data.length)
                {
                        this.enable[this.x] = this.presupuesto.partidas.data[this.x].presupuesto;
                        this.precio[this.x] = this.presupuesto.partidas.data[this.x].precio_unitario_convert;
                        this.moneda_input[this.x] = (this.presupuesto.partidas.data[this.x].id_moneda != 0) ? this.presupuesto.partidas.data[this.x].id_moneda : 1;
                        this.descuento[this.x] = (this.presupuesto.partidas.data[this.x].descuento > 0) ? this.presupuesto.partidas.data[this.x].descuento : 0;
                    this.x ++;                    
                }
                    this.anticipo = (this.presupuesto.anticipo > 0) ? this.presupuesto.anticipo : 0;
                    this.credito = (this.presupuesto.dias_credito > 0) ? this.presupuesto.dias_credito : 0;
                    this.vigencia = (this.presupuesto.dias_vigencia > 0) ? this.presupuesto.dias_vigencia : 0;
                    this.descuento_cot = (this.presupuesto.descuento > 0) ? this.presupuesto.descuento : 0;
                
                    this.tipo_cambio[0] = null;
                    this.tipo_cambio[1] = 1;
                    this.tipo_cambio[2] = (this.presupuesto.tc_usd) ? this.presupuesto.tc_usd : this.monedas[1].tipo_cambio_igh;
                    this.tipo_cambio[3] = (this.presupuesto.tc_euro) ? this.presupuesto.tc_euro : this.monedas[2].tipo_cambio_igh;

                    this.calcular();                
            },
            validate() {
                
                this.$validator.validate().then(result => {
                    if (result) {
                        this.post.partidas = this.presupuesto.partidas.data;
                        this.post.id_presupuesto = this.id;
                        this.post.fecha = this.presupuesto.fecha;
                        this.post.moneda = this.moneda_input;
                        this.post.precio = this.precio;
                        this.post.enable = this.enable;
                        this.post.descuento = this.descuento;
                        this.post.descuento_cot = this.descuento_cot;
                        this.post.anticipo = this.anticipo;
                        this.post.credito = this.credito;
                        this.post.vigencia = this.vigencia;
                        this.post.subtotal = this.subtotal;
                        this.post.impuesto = this.iva;
                        this.post.observaciones = this.presupuesto.observaciones;
                        this.post.tipo_cambio = this.tipo_cambio;
                        this.save()
                    }                    
                });
            },
            save() {
                
                if(this.total == 0)
                {
                    swal('¡Error!', 'Favor de ingresar partidas a cotizar', 'error');
                }
                else
                {                    
                    return this.$store.dispatch('contratos/presupuesto/update', {
                        id: this.id,
                        post: this.post
                        })
                    .then((data) => {
                        this.$router.push({name: 'presupuesto'});
                    });                
                }
            },
        },
        computed: {
            subtotal()
            {
                return ((this.pesos + (this.dolares * this.tipo_cambio[2]) + (this.euros * this.tipo_cambio[3])) - 
                ((this.descuento_cot * (this.pesos + (this.dolares * this.tipo_cambio[2]) + (this.euros * this.tipo_cambio[3]))) / 100 ));
            },
            iva()
            {
                return this.subtotal * 0.16;
            },
            total()
            {
                return this.subtotal + this.iva;
            },
            dolar()
            {
                return '$ ' + parseFloat(this.tipo_cambio[2]).formatMoney(4,'.',',');
            },
            euro()
            {
                return '$ ' + parseFloat(this.tipo_cambio[3]).formatMoney(4,'.',',');
            },
            importe()
            {
                return '$ ' + (parseFloat(this.presupuesto.subtotal) + parseFloat(this.presupuesto.impuesto)).formatMoney(2,'.',',');
            }
        },
        watch: {
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
