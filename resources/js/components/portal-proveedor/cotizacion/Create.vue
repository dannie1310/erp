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
                        <div class="modal-body">
                            <div class="row" v-if="solicitud">
                                <div class="col-md-12">
                                    <tabla-datos-solicitud v-bind:solicitud_compra="solicitud"></tabla-datos-solicitud>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-1">
                                    <h6><b>Fecha:</b></h6>
                                </div>
                                <div class="col-md-1">
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
                                <div class="col-md-1">
                                   <h6><b>Portal-Proveedor:</b></h6>
                                </div>
                                <div class="col-md-4">
                                    <h6>{{solicitud.razon_social}} [{{solicitud.rfc}}]</h6>
                                </div>
                                <div class="col-md-1">
                                   <h6><b>Sucursal:</b></h6>
                                </div>
                                <div class="col-md-2">
                                    <h6>{{solicitud.sucursal}}</h6>
                                </div>
                                <div class="col-md-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input button" id="cotizacion" v-model="pendiente" >
                                        <label class="custom-control-label" for="cotizacion">Dejar pendiente la captura de precios</label>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="row" v-if="solicitud != '' && !pendiente">
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
                                                <th >Cantidad Solicitada</th>
                                                <th >Cantidad Aprobada</th>
                                                <th class="cantidad_input">Precio Unitario</th>
                                                <th class="cantidad_input">% Descuento</th>
                                                <th >Precio Total</th>
                                                <th >Moneda</th>
                                                <th >Precio Total Moneda Conversión</th>
                                                <th>Observaciones</th>
                                            </tr>
                                            </thead>
                                            <tbody v-if="Object.keys(solicitud).length > 0">
                                                <tr v-for="(partida, i) in solicitud.partidas">
                                                    <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                    <td style="text-align:center;">{{partida.numero_parte}}</td>
                                                    <td>{{partida.material}}</td>
                                                    <td style="text-align:center;">{{partida.unidad}}</td>
                                                    <td style="text-align:center; vertical-align:inherit;">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="partida.enable" checked>
                                                            <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align:center;">{{partida.cantidad_original}}</td>
                                                    <td style="text-align:center;">{{partida.cantidad_original}}</td>
                                                    <td>
                                                        <input type="text" v-on:change="calcular"
                                                               :disabled="partida.enable == false"
                                                               class="form-control"
                                                               :name="`precio[${i}]`"
                                                               data-vv-as="Precio"
                                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                               :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                               v-model="partida.precio_cotizacion"
                                                               style="text-align: right"
                                                        />
                                                        <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                        <input type="text" v-on:change="calcular"
                                                           :disabled="partida.enable == false"
                                                           class="form-control"
                                                           :name="`descuento[${i}]`"
                                                           data-vv-as="Descuento(%)"
                                                           v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                           :class="{'is-invalid': errors.has(`descuento[${i}]`)}"
                                                           v-model="partida.descuento"
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
                                                            data-vv-as="Moneda"
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
                                                    <td style="text-align:right;">{{getPrecioTotal(partida.calculo_precio_total, partida.moneda_seleccionada)}}</td>
                                                    <td style="width:200px;">
                                                        <textarea class="form-control"
                                                                  :name="`observaciones[${i}]`"
                                                                  data-vv-as="Observaciones"
                                                                  :disabled="partida.enable == false"
                                                                  v-validate="{}"
                                                                  :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                  v-model="partida.observacion_partida"/>
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
                                    <label class="col-md-4 col-form-label">Subtotal Precios Peso (MXN)</label>
                                    <label class="col-md-2 col-form-label" style="text-align: right">${{(parseFloat(pesos)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-md-4 col-form-label">Subtotal Precios Dolar (USD):</label>
                                    <label class="col-md-2 col-form-label" style="text-align: right">${{(parseFloat(dolares)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-md-4 col-form-label">Subtotal Precios EURO:</label>
                                    <label class="col-md-2 col-form-label" style="text-align: right">${{(parseFloat(euros)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-md-4 col-form-label">Subtotal Precios Libra:</label>
                                    <label class="col-md-2 col-form-label" style="text-align: right">${{(parseFloat(libras)).formatMoney(2,'.',',')}}</label>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-md-2 col-form-label">TC USD:</label>
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
                                        style="text-align: right"
                                        :class="{'is-invalid': errors.has('tc_libra')}">
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-md-4 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                    <label class="col-md-2 col-form-label money" style="text-align: right">${{(parseFloat(subtotal)).formatMoney(4,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-md-2 col-form-label">IVA:</label>
                                    <label class="col-md-2 col-form-label money" style="text-align: right">${{(parseFloat(iva)).formatMoney(4,'.',',')}}</label>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-md-2 col-form-label">Total:</label>
                                    <label class="col-md-2 col-form-label money" style="text-align: right">${{(parseFloat(total)).formatMoney(4,'.',',')}}</label>
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
                                    <label class="col-md-4 col-form-label">Tiempo de Entrega (días):</label>
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
                                            v-model="observaciones"
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
                                <button type="button" @click="validate" :disabled="solicitud == ''" class="btn btn-primary">
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
    import TablaDatosSolicitud from "./partials/TablaDatosSolicitud";
    export default {
        name: "cotizacion-proveedor-create",
        props: ['id_solicitud'],
        components: {
            TablaDatosSolicitud,
            Datepicker, ModelListSelect},
        data() {
            return {
                cargando: false,
                pendiente: false,
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
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
                solicitud: []
            }
        },
        mounted() {
            this.find();
            this.fecha = new Date();
            this.$validator.reset();
            this.getMonedas();
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            find() {
                this.cargando = true;
                this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
                return this.$store.dispatch('padronProveedores/invitacion/getSolicitud', {
                    id: this.id_solicitud,
                    params:{}
                }).then(data => {
                    this.solicitud = data
                    this.cargando = false;
                })
            },
            getMonedas(){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/index', {
                    params: {sort: 'id_moneda', order: 'asc',}
                }).then(data => {
                    this.monedas = data.data;
                    this.dolar = parseFloat(this.monedas[1].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    this.euro = parseFloat(this.monedas[2].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    this.libra = parseFloat(this.monedas[3].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            salir()
            {
                this.$router.push({name: 'cotizacion-proveedor'});
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
            getPrecio(partida){
                if(partida.precio_cotizacion){
                    let cantidad = 0;
                    this.solicitud.estado === 0? cantidad = partida.cantidad_original_num : cantidad = partida.cantidad;
                    return '$' + parseFloat(partida.precio_cotizacion * cantidad - (partida.precio_cotizacion * cantidad * (partida.descuento ? partida.descuento : 0) / 100).formatMoney(2,'.',','));
                }
                return '$ 0.00';
            },
            calcular(){
                var pesos = 0;
                var dolares = 0;
                var euros = 0;
                var libras = 0;
                var estado = (this.solicitud.estado == 0)
                this.solicitud.partidas.forEach(function (partida, i) {
                    if(partida.enable === true) {
                        if(partida.moneda_seleccionada != undefined && partida.precio_cotizacion != undefined)
                        {
                            partida.calculo_precio_total = (estado ? partida.cantidad_original_num : partida.cantidad)
                                * (partida.precio_cotizacion - (partida.precio_cotizacion * (partida.descuento ? partida.descuento : 0))/100);
                            console.log(partida.calculo_precio_total);
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
                })
                this.pesos = pesos;
                this.dolares = dolares;
                this.euros = euros;
                this.libras = libras;
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
                    return this.$store.dispatch('compras/cotizacion/registrarCotizacionProveedor', this.$data)
                    .then((data) => {
                        this.salir();
                    });
                }
            },
        },
        computed: {
            subtotal()
            {
                return (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra) -
                    ((this.descuento_cot > 0) ? (((this.pesos + (this.dolares * this.dolar) + (this.euros *
                        this.euro) + (this.libras * this.libra)) * parseFloat(this.descuento_cot)) / 100) : 0));
            },
            iva() {
                return this.subtotal * 0.16;
            },
            total() {
                return this.subtotal + this.iva;
            },
        },
    }
</script>

<style scoped>

</style>
