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
                                        <datos-cotizacion-compra v-bind:cotizacion_compra="invitacion.cotizacionCompra"></datos-cotizacion-compra>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group error-content">
                                            <label for="fecha" class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "invitacion.cotizacionCompra.fecha"
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
                                <div class="row" v-if="invitacion">
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
                                                <tbody v-if="invitacion.cotizacionCompra.partidas">
                                                    <tr v-for="(partida, i) in invitacion.cotizacionCompra.partidas.data">
                                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                        <td>{{(partida.material) ? partida.material.numero_parte : '----'}}</td>
                                                        <td>{{(partida.material) ? partida.material.descripcion : '----'}}</td>
                                                        <td >{{(partida.material) ? partida.material.unidad : '----'}}</td>
                                                         <td style="text-align:center; vertical-align:inherit;">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" :id="`no_cotizado[${i}]`" v-model="partida.no_cotizado" v-on:change="calcular" checked>
                                                                <label class="custom-control-label" :for="`no_cotizado[${i}]`"></label>
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center;">{{partida.cantidad_format}}</td>
                                                        <td>
                                                            <input type="text"
                                                                   v-on:change="calcular"
                                                                   class="form-control"
                                                                   :disabled="partida.no_cotizado == false"
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
                                                                   :disabled="partida.no_cotizado == false"
                                                                   class="form-control"
                                                                   :name="`descuento[${i}]`"
                                                                   v-on:change="calcular"
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
                                                                :disabled="partida.no_cotizado == false"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                id="moneda"
                                                                v-model="partida.id_moneda"
                                                                :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                    <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.abreviatura }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:right;">{{getPrecioTotal(partida.cantidad * partida.precio_unitario, partida.id_moneda)}}</td>
                                                        <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      :disabled="partida.no_cotizado == false"
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
                                            v-on:change="getPrecioTotal"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-md-6 form-control"
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
                                            v-on:change="getPrecioTotal"
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
                                            v-on:change="getPrecioTotal"
                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                            class="col-sm-6 form-control"
                                            id="tc_libra"
                                            style="text-align: right"
                                            :class="{'is-invalid': errors.has('tc_libra')}">
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-4 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">${{(parseFloat(subtotal)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">IVA:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">${{(parseFloat(iva)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Total:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">${{(parseFloat(total)).formatMoney(2,'.',',')}}</label>
                                    </div>
                                    <div class=" col-md-10" align="right">
                                        <label class="col-md-4 col-form-label">Pago en Parcialidades(%):</label>
                                    </div>
                                    <div class=" col-md-2 p-1" align="right">
                                        <input
                                            :disabled="cargando"
                                            type="text"
                                            name="pago"
                                            v-model="invitacion.cotizacionCompra.complemento.parcialidades"
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
                                            v-model="invitacion.cotizacionCompra.complemento.anticipo"
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
                                            v-model="invitacion.cotizacionCompra.complemento.dias_credito"
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
                                            v-model="invitacion.cotizacionCompra.complemento.entrega"
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
                                            v-model="invitacion.cotizacionCompra.complemento.vigencia"
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
    import invitacion from "../../../store/modules/padronProveedores/invitacion";
    export default {
        name: "cotizacion-proveedor-edit",
        components: {DatosCotizacionCompra, Datepicker, ModelListSelect},
        props: ['id', 'xls'],
        data() {
            return {
                cargando: false,
                es:es,
                fechasDeshabilitadas:{},
                invitacion : [],
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
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            find() {
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id,
                    params:{ include: ['cotizacionCompra.complemento','cotizacionCompra.empresa','cotizacionCompra.sucursal','cotizacionCompra.partidas'], scope: ['invitadoAutenticado']}
                }).then(data => {
                    this.invitacion = data;
                    this.getMonedas(data.base_datos);
                    this.calcular()
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
                this.invitacion.cotizacionCompra.partidas.data.forEach(function (partida, i) {
                    if(partida.no_cotizado === true) {
                        partida.calculo_precio_total = partida.cantidad * (partida.precio_unitario - ((partida.precio_unitario * partida.descuento)/100));
                        if(partida.id_moneda == 1)
                        {
                            pesos += partida.calculo_precio_total;
                        }
                        if(partida.id_moneda == 2)
                        {
                            dolares += partida.calculo_precio_total;
                        }
                        if(partida.id_moneda == 3)
                        {
                            euros += partida.calculo_precio_total;
                        }
                        if(partida.id_moneda == 4)
                        {
                            libras += partida.calculo_precio_total;
                        }
                    }
                })
                this.pesos = pesos;
                this.dolares = dolares;
                this.euros = euros;
                this.libras = libras;
            },
            getMonedas(base){
                this.cargando = true;
                this.$store.commit('cadeco/moneda/SET_MONEDAS', null);
                return this.$store.dispatch('cadeco/moneda/monedasBase', {
                    params: {sort: 'id_moneda', order: 'asc'},
                    base : base
                }).then(data => {
                    this.monedas = data.data;
                    this.dolar = parseFloat(this.monedas[1].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    this.euro = parseFloat(this.monedas[2].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
                    this.libra = parseFloat(this.monedas[3].tipo_cambio_cadeco.cambio).formatMoney(4, '.', '');
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
                    swal('¡Error!', 'Favor de ingresar partidas a cotizar', 'error');
                }
                else
                {
                    var datos = {
                        'id_invitacion' : this.invitacion.id,
                        'fecha' : this.invitacion.cotizacionCompra.fecha,
                        'descuento' :this.invitacion.cotizacionCompra.complemento.descuento,
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
                        'importe' : this.total,
                        'impuesto' : this.iva,
                        'tc_eur' : this.euro,
                        'tc_usd' : this.dolar,
                        'tc_libra' : this.libra,
                        'partidas' : this.invitacion.cotizacionCompra.partidas
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
        },
        computed: {
            subtotal()
            {
                return ((this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra)) -
                    ((this.descuento_cot * (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra))) / 100 ));
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
                console.log(this.invitacion)
                console.log(this.xls)
                return (this.xls) ? this.xls : false;
            }
        },
    }
</script>
