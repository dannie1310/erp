<template>
    <span>
        <nav>
            <div  v-if="cotizacion == ''">
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
            <div class="row" v-else>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <datos-cotizacion-compra v-bind:cotizacion_compra="cotizacion"></datos-cotizacion-compra>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group error-content">
                                            <label for="fecha" class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "cotizacion.fecha"
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

                                <hr />

                                <div class="row" v-if="cotizacion.partidas">
                                    <div  class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th style="width:110px;">No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th class="index_corto"></th>
                                                    <th >Cantidad</th>
                                                    <th class="cantidad_input">Precio Unitario</th>
                                                    <th class="cantidad_input">% Descuento</th>
                                                    <th >Precio Total</th>
                                                    <th >Moneda</th>
                                                    <th >Precio Total Moneda Conversión</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="cotizacion.partidas">
                                                    <tr v-for="(partida, i) in cotizacion.partidas.data">
                                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                        <td>{{(partida.material) ? partida.material.numero_parte : '----'}}</td>
                                                        <td>{{(partida.material) ? partida.material.descripcion : '----'}}</td>
                                                        <td >{{(partida.material) ? partida.material.unidad : '----'}}</td>
                                                         <td style="text-align:center; vertical-align:inherit;">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="enable[i]" checked>
                                                                <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">{{partida.cantidad_format}}</td>
                                                        <td>
                                                            <input type="text"
                                                                   class="form-control"
                                                                   :disabled="enable[i] == false"
                                                                   :name="`precio[${i}]`"
                                                                   data-vv-as="Precio"
                                                                   v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d{0,6})?$/}"
                                                                   :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                                   style="text-align: right"
                                                                   v-model="precio[i]"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                   :disabled="enable[i] == false"
                                                                   class="form-control"
                                                                   :name="`descuento[${i}]`"
                                                                   data-vv-as="Descuento(%)"
                                                                   v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                                   :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                                   style="text-align: right"
                                                                   v-model="descuento[i]"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`descuento[${i}]`)">{{ errors.first(`descuento[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{'$' + parseFloat((partida.cantidad) * precio[i]).formatMoney(2,'.',',')}}</td>
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
                                                        <td style="text-align:right;">{{'$' + parseFloat(partida.cantidad * precio[i] * tipo_cambio[moneda_input[i]]).formatMoney(2, '.', ',')}}</td>
                                                       <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      :disabled="enable[i] == false"
                                                                      v-validate="{}"
                                                                      :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                      v-model="partida.observacion"/>
                                                             <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-sm-2 col-form-label">Descuento(%):</label>
                                    </div>
                                    <div class=" col-md-2" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="descuento_cot"
                                            v-model="descuento_cot"
                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="descuento_cot"
                                            style="text-align: right"
                                            :class="{'is-invalid': errors.has('descuento_cot')}">
                                    </div>
                                     <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Subtotal Precios Peso (MXN)</label>
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
                                        <label class="col-sm-2 col-form-label">Subtotal Precios LIBRA:</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">$&nbsp;{{(parseFloat(libras)).formatMoney(2,'.',',')}}</label>
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
                                            @change ="actualizaTC(2)"
                                            style="text-align: right"
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
                                            @change ="actualizaTC(3)"
                                            style="text-align: right"
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
                                            @change ="actualizaTC(4)"
                                            style="text-align: right"
                                            :class="{'is-invalid': errors.has('tc_libra')}">
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-4 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(subtotal)).formatMoney(4,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-md-2 col-form-label">Tasa de IVA (%):</label>
                                    </div>
                                    <div class="col-md-2 p-1" align="right">
                                        <input
                                            type="text"
                                            name="tasa_iva"
                                            v-model="cotizacion.tasa_iva_format"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*$/}"
                                            class="col-md-6 form-control"
                                            style="text-align: right"
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
                                        <label class="col-md-4 col-form-label">Pago en Parcialidades(%):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="pago"
                                            v-model="pago"
                                            v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="pago"
                                            style="text-align: right"
                                            :class="{'is-invalid': errors.has('pago')}">
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
                                            style="text-align: right"
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
                                        <label class="col-sm-2 col-form-label">Tiempo de Entrega (días):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="tiempo"
                                            v-model="tiempo"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
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
                                                v-model="cotizacion.observaciones"
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
            </div>
        </nav>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    import DatosCotizacionCompra from "./partials/DatosCotizacionCompra";
    export default {
        name: "cotizacion-edit",

        components: {DatosCotizacionCompra, Datepicker, ModelListSelect},
        props: ['id', 'xls'],
        data() {
            return {
                cargando: false,
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                descuento_cot : '0.00',
                tipo_cambio: [],
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
                libras:0,
                dolar:0,
                euro:0,
                libra:0,
                cotizacion: [],
                moneda_input:[],
                sucursal: true,
                observaciones_inputs:[],
                observaciones : '',
                precio: [],
                x: 0,
                pago: 0,
                post: {
                    id_cotizacion: '',
                    fecha: '',
                    moneda: [],
                    precio: [],
                    enable: [],
                    descuento: [],
                    descuento_cot: '',
                    pago: '',
                    anticipo: '',
                    credito: '',
                    tiempo: '',
                    vigencia: '',
                    importe: '',
                    impuesto: '',
                    observaciones: '',
                    tipo_cambio: []
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
            this.getMonedas();
            this.enable = [];
            this.precio = [];
            this.moneda_input = [];
            this.observaciones_inputs = [];
            this.descuento = [];
            this.$validator.reset();
        },
        methods : {
            actualizaTC(index){
                switch (index){
                    case 2:
                        this.tipo_cambio[index] = this.dolar;
                        break;
                    case 3:
                        this.tipo_cambio[index] = this.euro;
                        break;
                    case 4:
                        this.tipo_cambio[index] = this.libra;
                        break;
                }
                this.calcular();
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getMonedas(){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                }).then(data => {
                    this.monedas = data.data;
                    this.find();
                }).finally(()=>{

                })
            },
            salir()
            {
                 this.$router.push({name: 'cotizacion'});
            },
            find() {
                this.cargando = true;
                this.$store.commit('compras/cotizacion/SET_COTIZACION', null);
                return this.$store.dispatch('compras/cotizacion/find', {
                    id: this.id,
                    params:{include: [
                        'solicitud',
                        'empresa',
                        'sucursal',
                        'complemento',
                        'partidasEdicion.material'
                    ]}
                }).then(data => {
                    this.cotizacion = data;
                    if(this.xls)
                    {
                        this.cotizacion.tasa_iva_format = this.xls.tasa_iva_format;
                        this.cotizacion.tasa_iva = this.xls.tasa_iva / 100;
                    }
                    this.cotizacion.partidas = data.partidasEdicion;
                    this.fecha = data.fecha;
                    this.ordenar();
                    this.cargando = false;
                    if(data.partidasEdicion.data.length === 0)
                    {
                        return this.$store.dispatch('compras/solicitud-compra/find', {
                            id: data.solicitud.id,
                            params:{include: [
                                    'partidas.complemento'
                                ], order:'asc', sort:'id_item'}
                        }).then(data => {
                            this.cotizacion.partidas = data.partidas
                        })
                    }
                })
            },
            calcular()
            {
                this.x = 0;
                this.pesos = 0;
                this.dolares = 0;
                this.euros = 0;
                this.libras = 0;
                while(this.x < this.cotizacion.partidas.data.length)
                {
                    if(this.moneda_input[this.x] !== '' && this.moneda_input[this.x] !== null && this.moneda_input[this.x] !== undefined && this.enable[this.x] !== false)
                    {
                        if(this.moneda_input[this.x] == 1 && this.precio[this.x] != undefined)
                        {
                            this.pesos = (this.pesos + parseFloat(this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] -
                            ((this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] * this.descuento[this.x]) / 100)));
                        }
                        if(this.moneda_input[this.x] == 2 && this.precio[this.x] != undefined)
                        {
                            this.dolares = (this.dolares + parseFloat(this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] -
                            ((this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] * this.descuento[this.x]) / 100)));
                        }
                        if(this.moneda_input[this.x] == 3 && this.precio[this.x] != undefined)
                        {
                            this.euros = (this.euros + parseFloat(this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] -
                            ((this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] * this.descuento[this.x]) / 100)));
                        }
                        if(this.moneda_input[this.x] == 4 && this.precio[this.x] != undefined)
                        {
                            this.libras = (this.libras + parseFloat(this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] -
                                ((this.cotizacion.partidas.data[this.x].cantidad * this.precio[this.x] * this.descuento[this.x]) / 100)));
                        }
                    }
                    this.x ++;
                }
            },
            ordenar()
            {
                let sort = this.cotizacion.partidas.data.sort(function(a, b) {
                    return a.id_item_solicitud - b.id_item_solicitud;
                });

                this.cotizacion.partidas.data = sort;
                this.x = 0;
                while(this.x < this.cotizacion.partidas.data.length)
                {
                    if(!this.carga)
                    {
                        this.enable[this.x] = this.cotizacion.partidas.data[this.x].enable;
                        this.precio[this.x] = this.cotizacion.partidas.data[this.x].precio_unitario;
                        this.moneda_input[this.x] = (this.cotizacion.partidas.data[this.x].id_moneda != 0) ? this.cotizacion.partidas.data[this.x].id_moneda : 1;
                        this.descuento[this.x] = (this.cotizacion.partidas.data[this.x].descuento > 0) ? this.cotizacion.partidas.data[this.x].descuento : 0;

                    }else{
                        var busqueda = this.carga.partidas.find(x=>x.id_material == this.cotizacion.partidas.data[this.x].material.id);
                        this.cotizacion.partidas.data[this.x].observacion = busqueda.observaciones;
                        this.enable[this.x] = (busqueda.precio_unitario > 0) ? true : false;
                        this.precio[this.x] = busqueda.precio_unitario;
                        this.moneda_input[this.x] = busqueda.id_moneda;
                        this.descuento[this.x] = busqueda.descuento;
                    }
                    this.x ++;
                }
                if(!this.carga)
                {
                    this.libra = (this.cotizacion.complemento) ? this.cotizacion.complemento.tc_libra : this.monedas[3].tipo_cambio_cadeco.cambio_formato;
                    this.euro = (this.cotizacion.complemento) ? this.cotizacion.complemento.tc_eur : this.monedas[2].tipo_cambio_cadeco.cambio_formato;
                    this.dolar = (this.cotizacion.complemento) ? this.cotizacion.complemento.tc_usd : this.monedas[1].tipo_cambio_cadeco.cambio_formato;
                    this.pago = (this.cotizacion.complemento) ? this.cotizacion.complemento.parcialidades : 0;
                    this.anticipo = (this.cotizacion.complemento) ? this.cotizacion.complemento.anticipo : 0;
                    this.credito = (this.cotizacion.complemento) ? this.cotizacion.complemento.dias_credito : 0;
                    this.tiempo = (this.cotizacion.complemento) ? this.cotizacion.complemento.entrega : 0;
                    this.vigencia = (this.cotizacion.complemento) ? this.cotizacion.complemento.vigencia : 0;
                    this.descuento_cot = (this.cotizacion.complemento) ? this.cotizacion.complemento.descuento : 0;
                }else{
                    this.pago = this.carga.pago_parcialidades;
                    this.dolar = this.carga.tc_usd;
                    this.euro = this.carga.tc_eur;
                    this.libra = this.carga.tc_libra;
                    this.anticipo = this.carga.anticipo;
                    this.credito = this.carga.credito;
                    this.tiempo = this.carga.tiempo_entrega;
                    this.vigencia = this.carga.vigencia;
                    this.descuento_cot = this.carga.descuento_cot;
                    this.cotizacion.observaciones = this.carga.observaciones_generales;
                    this.cotizacion.fecha = this.carga.fecha_cotizacion;
                }
                        this.tipo_cambio[1] = 1;
                        this.tipo_cambio[2] = (this.cotizacion.complemento) ? this.cotizacion.complemento.tc_usd : this.monedas[1].tipo_cambio_cadeco.cambio_formato;
                        this.tipo_cambio[3] = (this.cotizacion.complemento) ? this.cotizacion.complemento.tc_eur : this.monedas[2].tipo_cambio_cadeco.cambio_formato;
                        this.tipo_cambio[4] =  this.monedas[3].tipo_cambio_cadeco.cambio_formato;

                    this.calcular();
            },
            validate() {

                this.$validator.validate().then(result => {
                    if (result) {

                        let self = this;
                        this.descuento.forEach(function(desc, i) {
                            self.cotizacion.partidas.data[i].enable = self.enable[i];
                            self.cotizacion.partidas.data[i].precio_unitario = self.precio[i];
                            self.cotizacion.partidas.data[i].id_moneda = self.moneda_input[i];
                            self.cotizacion.partidas.data[i].descuento = self.descuento[i];
                        });
                        this.post.partidas = this.cotizacion.partidas.data;
                        this.post.id_cotizacion = this.id;
                        this.post.fecha = this.cotizacion.fecha;
                        this.post.moneda = this.moneda_input;
                        this.post.precio = this.precio;
                        this.post.enable = this.enable;
                        this.post.descuento = this.descuento;
                        this.post.descuento_cot = this.descuento_cot;
                        this.post.pago = this.pago;
                        this.post.anticipo = this.anticipo;
                        this.post.credito = this.credito;
                        this.post.tiempo = this.tiempo;
                        this.post.vigencia = this.vigencia;
                        this.post.importe = this.total;
                        this.post.impuesto = this.iva;
                        this.post.observaciones = this.cotizacion.observaciones;
                        this.post.tipo_cambio = this.tipo_cambio;
                        this.post.tc_eur = this.euro;
                        this.post.tc_usd = this.dolar;
                        this.post.tc_libra = this.libra;
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
                {   return this.$store.dispatch('compras/cotizacion/update', {
                    id: this.id,
                    post: this.post
                    })
                    .then((data) => {
                        this.$router.push({name: 'cotizacion'});
                    });
                }
            },
        },
        computed: {
            solicitud(){
                return this.$store.getters['compras/solicitud-compra/currentSolicitud'];
            },
            subtotal()
            {
                return ((this.pesos + (this.dolares * this.tipo_cambio[2]) + (this.euros * this.tipo_cambio[3]) + (this.libras * this.tipo_cambio[4])) -
                ((this.descuento_cot * (this.pesos + (this.dolares * this.tipo_cambio[2]) + (this.euros * this.tipo_cambio[3]) + (this.libras * this.tipo_cambio[4]))) / 100 ));
            },
            iva()
            {
                return this.subtotal * (this.cotizacion.tasa_iva_format / 100);
            },
            total()
            {
                return this.subtotal + this.iva;
            },
            carga()
            {
                return (this.xls) ? this.xls : false;
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
