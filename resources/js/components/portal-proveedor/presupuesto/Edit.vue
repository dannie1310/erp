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
            <div class="col-12" v-if="invitacion.presupuesto_proveedor">
                <div class="card">
                    <form role="form" @submit.prevent="validate">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <datos-presupuesto v-bind:presupuesto="presupuesto" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="fecha" class="col-form-label">Fecha:</label>
                                        <datepicker v-model="presupuesto.fecha"
                                                    name = "fecha"
                                                    id="fecha"
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
                            <div class="row" v-if="presupuesto.contratos">
                                <div  class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
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
                                            <tbody v-for="(partida, i) in presupuesto.contratos">
                                                <tr v-if="partida.unidad">
                                                    <td>{{partida.descripcion}}</td>
                                                    <td style="text-align:center;">{{partida.unidad}}</td>
                                                     <td style="text-align:center; vertical-align:inherit;">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" :id="`enable[${i}]`" v-model="partida.partida_activa" checked>
                                                            <label class="custom-control-label" :for="`enable[${i}]`"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align:center;">{{partida.cantidad_original_format}}</td>
                                                    <td style="text-align:center;">{{partida.cantidad_presupuestada_format}}</td>
                                                    <td>
                                                        <input
                                                            v-on:change="calcular"
                                                            type="text"
                                                            class="form-control"
                                                            :disabled="partida.partida_activa == false"
                                                            :name="`precio[${i}]`"
                                                            :data-vv-as="`'Precio ${i + 1}'`"
                                                            v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d{2})?$/}"
                                                            v-model="partida.precio_unitario"
                                                            :class="{'is-invalid': errors.has(`precio[${i}]`)}"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                    </td>
                                                    <td style="text-align:right;">{{getPrecioTotalAntesDesc(i)}}</td>
                                                    <td>
                                                        <input v-on:change="calcular"
                                                               type="text"
                                                               :disabled="partida.partida_activa == false"
                                                               class="form-control"
                                                               :name="`descuento[${i}]`"
                                                               v-model="partida.descuento"
                                                               :data-vv-as="`'Descuento(%) ${i + 1}'`"
                                                               v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d{2})?$/}"
                                                               :class="{'is-invalid': errors.has(`descuento[${i}]`)}"/>
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
                                                            :disabled="partida.partida_activa == false"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            id="moneda"
                                                            v-model="partida.IdMoneda"
                                                            :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.abreviatura }}</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-show="errors.has(`moneda[${i}]`)">{{ errors.first(`moneda[${i}]`) }}</div>
                                                    </td>
                                                    <td style="text-align:right;">{{getPrecioMoneda(i)}}</td>
                                                    <td style="text-align:right;">{{getPrecioTotalMoneda(partida)}}</td>
                                                   <td style="width:200px;">
                                                        <textarea class="form-control"
                                                                  :name="`observaciones[${i}]`"
                                                                  :data-vv-as="`'Observaciones ${i + 1}'`"
                                                                  :disabled="partida.partida_activa == false"
                                                                  :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                  v-model="partida.observaciones"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Antes de Descuento:</label>
                                    <label class="col-sm-2 col-form-label money" style="text-align: right">$&nbsp;{{(parseFloat(subtotal_antes_descuento)).formatMoney(4,'.',',')}}</label>
                                </div>
                                <div class=" col-md-10" align="right">
                                    <label class="col-sm-2 col-form-label">% Descuento:</label>
                                </div>
                                <div class=" col-md-2" align="right">
                                    <input
                                        v-on:change="calcular"
                                        :disabled="cargando"
                                        type="number"
                                        step=".01"
                                        max="100"
                                        min="0"
                                        name="descuento_cot"
                                        v-model="presupuesto.descuento"
                                        v-validate="{required: true}"
                                        class="col-sm-6 form-control"
                                        id="descuento_cot"
                                        :class="{'is-invalid': errors.has('descuento_cot')}">
                                </div>
                                 <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal Precios Peso (MXN):</label>
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
                                        type="number"
                                        step="any"
                                        name="tc_usd"
                                        v-model="dolar"
                                        v-validate="{required: true}"
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
                                        type="number"
                                        step="any"
                                        name="tc_eur"
                                        v-model="euro"
                                        v-validate="{required: true}"
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
                                        type="number"
                                        step="any"
                                        name="tc_libra"
                                        v-model="libra"
                                        v-validate="{required: true}"
                                        class="col-sm-6 form-control"
                                        id="tc_libra"
                                        :class="{'is-invalid': errors.has('tc_libra')}">
                                </div>
                                <div class=" col-md-12" align="right">
                                    <label class="col-sm-2 col-form-label">Subtotal:</label>
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
                                        v-model="presupuesto.anticipo"
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
                                        min="0"
                                        name="credito"
                                        v-model="presupuesto.dias_credito"
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
                                        v-model="presupuesto.dias_vigencia"
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
                        <div class="card-footer">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>
                                    Regresar</button>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
                                    Guardar</button>
                            </div>
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
    import DatosPresupuesto from "./partials/DatosPresupuesto";
    export default {
        name: "presupuesto-edit",
        components: {  Datepicker, ModelListSelect, DatosPresupuesto},
        props: ['id', 'xls'],
        data() {
            return {
                cargando: false,
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                monedas: [],
                presupuesto: [],
                invitacion : [],
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
            }
        },
        mounted() {
            this.find();
            this.$validator.reset();
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            find() {
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id,
                    params: {
                        include: ['presupuesto_proveedor'],
                        scope: ['invitadoAutenticado']
                    }
                }).then(data => {
                    this.presupuesto = data.presupuesto_proveedor
                    this.invitacion = data
                    this.getMonedas(data.base_datos);
                    this.dolar = parseFloat(this.presupuesto.tc_usd).formatMoney(2, '.', ',')
                    this.euro = parseFloat(this.presupuesto.tc_euro).formatMoney(2, '.', ',')
                    this.libra = parseFloat(this.presupuesto.tc_libra).formatMoney(2, '.', ',')
                    if(this.xls != null)
                    {
                        this.presupuesto.anticipo = this.xls.anticipo;
                        this.presupuesto.dias_credito = this.xls.credito;
                        this.presupuesto.descuento = this.xls.descuento;
                        this.presupuesto.observaciones = this.xls.observaciones;
                        this.presupuesto.dias_vigencia = this.xls.vigencia;
                        this.euro = this.xls.tc_euro;
                        this.libra = this.xls.tc_libra;
                        this.dolar = this.xls.tc_usd;
                        for(var i = 0; i < this.presupuesto.contratos.length; i++)
                        {
                            for(var x = 0; x < this.xls.contratos.length; x++)
                            {
                                if(this.presupuesto.contratos[i].id_concepto == this.xls.contratos[x].id_concepto)
                                {
                                    this.presupuesto.contratos[i].descuento = this.xls.contratos[x].descuento;
                                    this.presupuesto.contratos[i].IdMoneda = this.xls.contratos[x].id_moneda;
                                    this.presupuesto.contratos[i].observaciones = this.xls.contratos[x].observaciones;
                                    this.presupuesto.contratos[i].precio_unitario = this.xls.contratos[x].precio_unitario;
                                    this.presupuesto.contratos[i].partida_activa = this.xls.contratos[x].partida_activa;
                                }
                            }
                        }
                    }
                    this.calcular();
                    this.cargando = false;
                })
            },
            getMonedas(base){
                this.cargando = true;
                return this.$store.dispatch('cadeco/moneda/monedasBase', {
                    params: {sort: 'id_moneda', order: 'asc'},
                    base : base
                }).then(data => {
                    this.monedas = data.data;
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            salir() {
                this.$router.go(-1);
            },
            getPrecioTotalAntesDesc(i){
                var total = this.presupuesto.contratos[i]['precio_unitario'] != undefined ? this.presupuesto.contratos[i]['precio_unitario'] * this.presupuesto.contratos[i]['cantidad_presupuestada'] : 0;
                this.presupuesto.contratos[i]['precio_total_antes_desc'] = total;
                return  '$' + parseFloat(total).formatMoney(2, '.', ',')
            },
            getPrecioUnitario(i){
                var precio_unitario = this.presupuesto.contratos[i]['precio_unitario'] != undefined ? this.presupuesto.contratos[i]['precio_unitario'] - ((this.presupuesto.contratos[i]['precio_unitario'] * this.presupuesto.contratos[i]['descuento']) / 100) : 0;
                this.presupuesto.contratos[i]['precio_unitario_calculado'] = precio_unitario;
                return  '$' + parseFloat(precio_unitario).formatMoney(2, '.', ',')
            },
            getPrecioTotal(i){
                var precio_total = this.presupuesto.contratos[i]['precio_total_antes_desc'] - ((this.presupuesto.contratos[i]['precio_total_antes_desc'] * this.presupuesto.contratos[i]['descuento'])/100);
                this.presupuesto.contratos[i]['precio_total'] = precio_total;
                return  '$' + parseFloat(precio_total).formatMoney(2, '.', ',')
            },
            getPrecioMoneda(i) {
                var precio = 0;
                if (this.presupuesto.contratos[i]['IdMoneda'] == 1) {
                    precio = this.presupuesto.contratos[i]['precio_unitario_calculado']
                }
                if (this.presupuesto.contratos[i]['IdMoneda'] == 2) {
                    precio = this.presupuesto.contratos[i]['precio_unitario'] != undefined ? (this.presupuesto.contratos[i]['precio_unitario'] * this.dolar) - (this.presupuesto.contratos[i]['precio_unitario'] * this.dolar * this.presupuesto.descuento/100) :  this.dolar
                }
                if (this.presupuesto.contratos[i]['IdMoneda'] == 3) {
                    precio = this.presupuesto.contratos[i]['precio_unitario'] != undefined ? (this.presupuesto.contratos[i]['precio_unitario'] * this.euro) - (this.presupuesto.contratos[i]['precio_unitario'] * this.euro * this.presupuesto.descuento/100) : this.euro
                }
                if (this.presupuesto.contratos[i]['IdMoneda'] == 4) {
                    precio = this.presupuesto.contratos[i]['precio_unitario'] != undefined ? (this.presupuesto.contratos[i]['precio_unitario'] * this.libra) - (this.presupuesto.contratos[i]['precio_unitario'] * this.libra * this.presupuesto.descuento/100) : this.libra
                }
                this.presupuesto.contratos[i]['precio_unitario_moneda_conversion'] = precio;
                return  '$' + parseFloat(precio).formatMoney(2, '.', ',')
            },
            getPrecioTotalMoneda(partida){
                return '$' + parseFloat(partida.cantidad_presupuestada * partida.precio_unitario_moneda_conversion).formatMoney(2,'.',',')
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
                var descuento = parseFloat(this.presupuesto.descuento)
                var partida_descuento = 0
                var suma = 0;
                var suma_desc = 0;
                this.presupuesto.contratos.forEach(function (partida, i)
                {
                    if (partida.partida_activa === true)
                    {
                        if(partida.IdMoneda != undefined && partida.precio_unitario != undefined)
                        {
                            partida_descuento = parseFloat(partida.descuento)
                            suma = partida.cantidad_presupuestada * (partida.precio_unitario - ((partida.precio_unitario * (partida_descuento + descuento - (partida_descuento * descuento / 100)))/100));
                            suma_desc = partida.cantidad_presupuestada * (partida.precio_unitario - ((partida.precio_unitario * partida.descuento) / 100));
                            if(partida.IdMoneda == 1)
                            {
                                pesos += suma;
                                pesos_sd += suma_desc;
                            }
                            if(partida.IdMoneda == 2)
                            {
                                dolares += suma;
                                dolares_sd += suma_desc;
                            }
                            if(partida.IdMoneda == 3)
                            {
                                euros += suma;
                                euros_sd += suma_desc;
                            }
                            if(partida.IdMoneda == 4)
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
                        this.presupuesto.id_invitacion = this.id;
                        this.presupuesto.subtotal = this.subtotal;
                        this.presupuesto.monto = this.total;
                        this.presupuesto.impuesto = this.iva;
                        this.presupuesto.tcUsd = this.dolar;
                        this.presupuesto.tdEuro = this.euro;
                        this.presupuesto.tcLibra = this.libra;
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
                    return this.$store.dispatch('contratos/presupuesto/updatePresupuestoProveedor', {
                        id: this.presupuesto.id,
                        presupuesto: this.presupuesto
                        })
                    .then((data) => {
                        this.salir()
                    });
                }
            },
        },
        computed: {
            subtotal(){
                return (this.pesos + (this.dolares * this.dolar) + (this.euros * this.euro) + (this.libras * this.libra) );
            },
            subtotal_antes_descuento(){
                return (this.pesos_sd + (this.dolares_sd * this.dolar) + (this.euros_sd * this.euro) + (this.libras_sd * this.libra) );
            },
            iva(){
                return this.subtotal * 0.16;
            },
            total(){
                return this.subtotal + this.iva;
            },
            carga(){
                return (this.xls) ? this.xls : false;
            }
        }
    }
</script>

<style scoped>

</style>
