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
                                                                name="id_solicitud"
                                                                option-value="id"                                                               
                                                                v-model="id_solicitud"
                                                                :custom-text="idFolioObservaciones"
                                                                :list="solicitudes"
                                                                :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'">
                                                            </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_proveedor">Proveedores</label>
                                            <select class="form-control"
                                                    name="id_proveedor"
                                                    data-vv-as="Proveedores"
                                                    v-model="id_proveedor"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_proveedor')"
                                                    id="id_proveedor">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="proveedor in proveedores" :value="proveedor.id" >{{ proveedor.razon_social}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proveedor')">{{ errors.first('id_proveedor') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 offset-1" v-if="sucursal">
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
                                    <div class="col-md-3 offset-1" v-else>
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
                                </div>
                                <!-- <div class="row">   
                                    <div class="col-md-12">
                                        <label for="concepto" class="col-form-label">Concepto: </label>
                                    </div>
                                </div> -->
                                <!-- <div class="row">   
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <textarea
                                                name="concepto"
                                                id="concepto"
                                                class="form-control"
                                                v-model="concepto"
                                                v-validate="{required: true}"
                                                data-vv-as="Concepto"
                                                :class="{'is-invalid': errors.has('concepto')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                        </div>
                                    </div>
                                </div> -->
                                <hr />
                                
                                <div class="row" v-if="id_solicitud != ''">
                                    <div  class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th style="width:110px;">No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th></th>
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
                                                        <td style="text-align:center;">{{partida.cantidad_original}}</td>
                                                        <td style="text-align:center;">{{(solicitud.estado === 1) ? partida.solicitado_cantidad : '0.0'}}</td>
                                                        <td>
                                                            <input type="number"
                                                                   min="0.01"
                                                                   step=".01"
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
                                                                   step=".01"
                                                                   class="form-control"
                                                                   :name="`descuento[${i}]`"
                                                                   data-vv-as="Descuento(%)"
                                                                   v-validate="{required: true}"
                                                                   :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                                   v-model="descuento[i]"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{(precio[i]) ? '$ ' + parseFloat(partida.cantidad_original * precio[i]).formatMoney(2,'.',',') : '$ 0.00'}}</td>
                                                        <td style="width:120px;" >
                                                            <select
                                                                type="text"
                                                                :name="`unidad[${i}]`"
                                                                data-vv-as="Unidad"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                id="unidad"
                                                                v-model="moneda_input[i]"
                                                                :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                    <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.abreviatura }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{(moneda_input[i] && precio[i]) ? '$ ' + parseFloat((partida.cantidad_original * precio[i] * monedas[moneda_input[i] - 1].tipo_cambio_igh)).formatMoney(2,'.',',') : '$ 0.00'}}</td>
                                                        <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
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
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(dolares * monedas[1].tipo_cambio_igh)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Precios EURO:</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(euros * monedas[2].tipo_cambio_igh)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">ToTC USD:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[1].tipo_cambio_igh)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">ToTC EURO:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[2].tipo_cambio_igh)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Moneda Conversión (MXP):</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[2].tipo_cambio_igh)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">IVA:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[2].tipo_cambio_igh)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Total:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(monedas[2].tipo_cambio_igh)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Pago en Parcialidades (%):</label>                                        
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step=".01"
                                                                name="descuento_cot"
                                                                v-model="descuento_cot"
                                                                v-validate="{required: true,}"
                                                                class="col-sm-6 form-control"
                                                                id="descuento_cot"
                                                                :class="{'is-invalid': errors.has('descuento_cot')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">% Anticipo:</label>                                        
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step=".01"
                                                                name="descuento_cot"
                                                                v-model="descuento_cot"
                                                                v-validate="{required: true,}"
                                                                class="col-sm-6 form-control"
                                                                id="descuento_cot"
                                                                :class="{'is-invalid': errors.has('descuento_cot')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Crédito (días):</label>                                        
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step=".01"
                                                                name="descuento_cot"
                                                                v-model="descuento_cot"
                                                                v-validate="{required: true,}"
                                                                class="col-sm-6 form-control"
                                                                id="descuento_cot"
                                                                :class="{'is-invalid': errors.has('descuento_cot')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Tiempo de Entrega (días):</label>                                        
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step=".01"
                                                                name="descuento_cot"
                                                                v-model="descuento_cot"
                                                                v-validate="{required: true,}"
                                                                class="col-sm-6 form-control"
                                                                id="descuento_cot"
                                                                :class="{'is-invalid': errors.has('descuento_cot')}">
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Vigencia( días):</label>                                        
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step=".01"
                                                                name="descuento_cot"
                                                                v-model="descuento_cot"
                                                                v-validate="{required: true,}"
                                                                class="col-sm-6 form-control"
                                                                id="descuento_cot"
                                                                :class="{'is-invalid': errors.has('descuento_cot')}">
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
                                    <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                                    <button type="submit" :disabled="id_solicitud == ''" class="btn btn-primary">Registrar</button>
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
                id_solicitud: '',
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                descuento_cot : '0.00',
                tipos : [],
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
                moneda_input:[],
                sucursal: true,
                observaciones_inputs:[],
                observaciones : '',
                precio: [],
                x: 0,
                descuento: [],
                enable: []
                             
            }
        },
        mounted() {
            this.fecha = new Date();
            this.$validator.reset();
            this.getProveedores();
            this.getMonedas();
            this.getSolicitudes();
            
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}] ---- [ ${item.observaciones} ]`;
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getProveedores() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'proveedor', include: 'sucursales' }
                })
                    .then(data => {
                        this.proveedores = data.data;
                    })
            },
            getMonedas(){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                }).then(data => {
                    this.monedas = data.data;
                }).finally(()=>{

                })
            },
            getTipos() {
                console.log('tipos', this.id_solicitud);
                
                return this.$store.dispatch('configuracion/ctg-tipo/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.tipos = data.data;
                        this.disabled = false;
                    })
            },
            salir(){
                console.log('enable', this.solicitud.partidas.data[0].cantidad);
                
                
                // this.$router.push({name: 'cotizacion'});
            },
            find() {

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
                // console.log(this.solicitud.partidas.data, this.solicitud.partidas.data.length);
                this.x = 0;
                this.pesos = 0;
                this.dolares = 0;
                this.euros = 0;
                while(this.x < this.solicitud.partidas.data.length)
                {
                    if(this.moneda_input[this.x] !== '' && this.moneda_input[this.x] !== null && this.moneda_input[this.x] !== undefined)
                    {
                        if(this.moneda_input[this.x] == 1 && this.precio[this.x] != undefined)
                        {
                            this.pesos = (this.pesos + (2 * parseFloat(this.precio[this.x])));
                        }
                        if(this.moneda_input[this.x] == 2 && this.precio[this.x] != undefined)
                        {
                            this.dolares = (this.dolares + parseFloat(this.precio[this.x]));
                        }
                        if(this.moneda_input[this.x] == 3 && this.precio[this.x] != undefined)
                        {
                            this.euros = (this.euros + parseFloat(this.precio[this.x]));
                        }

                        
                        
                        
                    }
                    this.x ++;
                    
                }
                console.log(this.pesos, this.dolares, this.euros, this.moneda_input, this.precio);
                
            },
            getSolicitudes() {
                this.solicitudes = [];
                this.cargando = true;
                return this.$store.dispatch('compras/solicitud-compra/index', {
                    params: {
                        scope: 'conItems',
                        limit: 300,
                        order: 'DESC',
                        sort: 'numero_folio'
                    }
                })
                    .then(data => {
                        this.solicitudes = data.data;
                        this.cargando = false;
                    })
            },
            validate() {
                
                this.$validator.validate().then(result => {
                    if (result) {
                        console.log('solicitud del computed', this.solicitud);
                        
                        alert('validate');
                        // this.store()
                    }
                });
            },
            store() {
                this.t = 0;
                this.m = 0;
                 while(this.t < this.partidas.length){
                     if(typeof this.partidas[this.t].clave_concepto === 'undefined' || this.partidas[this.t].clave_concepto === '')
                        {
                            this.m ++;
                            swal('¡Error!', 'Ingrese un destino válido en partida '+(this.t + 1) +'.', 'error');
                        }
                        this.t ++;
                }if(this.m == 0)
                {
                    return this.$store.dispatch('compras/requisicion/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'requisicion'});
                    });

                }
            },
        },
        computed: {
            solicitud(){
                return this.$store.getters['compras/solicitud-compra/currentSolicitud'];
            },
            peso()
            {
                return '$ ' + parseFloat(this.pesos).formatMoney(2,'.',',')
            }
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
                }
            },
            moneda_input(value)
            {
                this.calcular()                ;
            }
        }
    }
</script>

<style scoped>

</style>
