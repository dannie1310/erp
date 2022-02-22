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
        <div class="card" v-if="!cargando">
            <form role="form" @submit.prevent="validate">
                <div class="card-body">
                    <div class="col-md-12">
                        <datos-documento v-bind:solicitud="solicitud" />
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="fecha">Fecha:</label>
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
                            <div class="col-md-3" v-if="cuentas">
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_cuenta">Cuenta Bancaria:</label>
                                            <input type="text"
                                                   name="id_cuenta"
                                                   id="id_cuenta"
                                                   @click="abrirModal"
                                                   v-validate="{required: true}"
                                                   class="form-control"
                                                   data-vv-as="Cuenta"
                                                   :class="{'is-invalid': errors.has('id_cuenta')}"
                                                   v-model="id_cuenta" />
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('id_cuenta') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" v-if="cuenta != ''">
                               <div class="form-group error-content">
                                   <label>Moneda:</label>
                                   <label>{{ cuenta.moneda.abreviatura }}</label>
                               </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="referencia">Referencia:</label>
                                    <input type="text"
                                           name="referencia"
                                           id="referencia"
                                           class="form-control"
                                           data-vv-as="Referencia"
                                           :class="{'is-invalid': errors.has('referencia')}"
                                           v-model="referencia" />
                                    <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <th class="encabezado">
                                            Folio
                                        </th>
                                        <th class="encabezado">
                                            Autorizado de Remesa
                                        </th>
                                        <th class="encabezado">
                                            A Pagar
                                        </th>
                                        <th class="encabezado">
                                            Tipo Cambio
                                        </th>
                                        <th class="encabezado">
                                            Total
                                        </th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center">
                                            {{solicitud.numero_folio}}
                                        </td>
                                        <td style="text-align: right" >
                                            {{solicitud.monto_autorizado_remesa_format}}
                                        </td>
                                        <td v-if="solicitud.tipo_transaccion == 65">
                                            <input
                                                 type="text" @change="calcular"
                                                 class="form-control"
                                                 name="monto_pagar"
                                                 style="text-align: right"
                                                 data-vv-as="Monto a Pagar"
                                                 v-model="monto_pagar"
                                                 v-validate="{max_value:solicitud.monto_autorizado, min_value:0}"
                                                 :class="{'is-invalid': errors.has('monto_pagar')}"
                                                 id="monto_pagar">
                                            <div class="invalid-feedback" v-show="errors.has('monto_pagar')">{{ errors.first('monto_pagar') }}</div>
                                        </td>
                                        <td v-if="solicitud.tipo_transaccion == 72" style="text-align: right">
                                            {{ solicitud.saldo_format }}
                                        </td>
                                        <td v-if="cuenta != '' && cuenta.moneda.tipo_cambio != 1 && solicitud.tipo_transaccion == 65 && tipo_cambio_actual != 1" style="text-align: right">
                                            <input
                                                type="text" @change="calcular"
                                                class="form-control"
                                                name="tipo_cambio"
                                                style="text-align: right"
                                                data-vv-as="Tipo de Cambio"
                                                v-model="tipo_cambio"
                                                v-validate="{max_value: tipo_cambio_actual, min_value:0}"
                                                :class="{'is-invalid': errors.has('tipo_cambio')}"
                                                id="tipo_cambio">
                                            <div class="invalid-feedback" v-show="errors.has('tipo_cambio')">{{ errors.first('tipo_cambio') }}</div>
                                        </td>
                                        <td style="text-align: right" v-else-if="cuenta != ''">
                                            {{ parseFloat(tipo_cambio_actual).formatMoney(2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right" v-else>0.00</td>
                                        <td style="text-align: right">
                                            {{ parseFloat(monto_calculado).formatMoney(2, '.', ',') }}
                                        </td>
                                    </tr>
                                </table>
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
        <div class="modal fade" id="exampleModalCenter"  ref="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered sortable">
                                <thead>
                                    <tr>
                                        <th class="encabezado">Cuenta</th>
                                        <th class="encabezado">Moneda</th>
                                        <th class="encabezado">Banco</th>
                                        <th class="encabezado">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="c in cuentas">
                                        <th @click="seleccionarCuenta(c)" style="text-align: left">{{c.numero}}</th>
                                        <td @click="seleccionarCuenta(c)">{{c.moneda.nombre}}</td>
                                        <td @click="seleccionarCuenta(c)">{{c.empresa.razon_social}}</td>
                                        <td @click="seleccionarCuenta(c)" style="text-align: right" >{{c.saldo_format_cadeco}}</td>
                                    </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                        <button type="button" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import DatosDocumento from "./partials/DatosDocumento";
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "registrar-pago",
        props: ['id'],
        components : { DatosDocumento, Datepicker, es },
        data() {
            return {
                es: es,
                cargando: false,
                fechasDeshabilitadas:{},
                fecha : '',
                solicitud : [],
                cuentas : [],
                cuenta : '',
                referencia : '',
                monto_pagar : 0,
                tipo_cambio : 0,
                tipo_cambio_actual : 0,
                id_cuenta : '',
                monto_calculado : 0
            }
        },
        mounted() {
            this.fecha = new Date();
            this.fechasDeshabilitadas.from= new Date();
            this.monto_pagar = 0;
            this.tipo_cambio = 0;
            this.tipo_cambio_actual = 0;
            this.monto_calculado = 0;
            this.referencia = '';
            this.cuenta = '';
            this.solicitud = [];
            this.cuentas = [];
            this.$validator.reset();
            this.find();
        },
        methods : {
            init() {
                this.fecha = new Date();
                this.fechasDeshabilitadas.from= new Date();
                this.monto_pagar = 0;
                this.tipo_cambio = 0;
                this.referencia = '';
                this.cuenta = '';
                this.solicitud = [];
                this.cuentas = [];
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            find() {
                this.cargando = true
                return this.$store.dispatch('finanzas/pago/documentoParaPagar', {
                    id: this.id,
                }).then(data => {
                    if(data['error'])
                    {
                        swal({
                            title: "Atención",
                            text: data['error'],
                            icon: "warning",
                            buttons: {
                                confirm: {
                                    text: 'OK',
                                    closeModal: true,
                                }
                            },
                            dangerMode: true,
                        })
                            .then((value) => {
                                this.salir()
                            });
                    }else {
                        this.solicitud = data;
                        this.monto_pagar = data.monto_autorizado
                        this.getCuentas()
                    }
                });
            },
            getCuentas() {
                return this.$store.dispatch('cadeco/cuenta/index', {
                    params: { include: ['moneda','empresa'] }
                }).then(data => {
                    this.cuentas = data.data;
                    this.cargando = false;
                });
            },
            abrirModal()
            {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            seleccionarCuenta(item)
            {
                $(this.$refs.modal).modal('hide');
                this.cuenta = item
                this.tipo_cambio = item.moneda.tipo_cambio
                this.tipo_cambio_actual = item.moneda.tipo_cambio
                this.id_cuenta = item.numero
                this.calcular();
            },
            calcular()
            {
                if(this.cuenta != '') {
                    this.monto_calculado = this.monto_pagar * this.tipo_cambio
                    return this.monto_pagar * this.tipo_cambio
                }
            },
            salir(){
                this.$router.push({name: 'registro-pago'});
            },
            validate(){
                this.calcular()
                this.$validator.validate().then(result => {
                    if (result) {
                        this.solicitud.fecha_pago = this.fecha;
                        this.solicitud.tipo_cambio = this.tipo_cambio;
                        this.solicitud.id_cuenta_cargo = this.cuenta.id;
                        this.solicitud.referencia_pago = this.referencia;
                        this.solicitud.id_moneda_cuenta_cargo = this.cuenta.moneda.id
                        this.solicitud.monto_pagado = this.monto_calculado;
                        this.solicitud.monto_pagado_transaccion = this.monto_pagar
                        this.guardar()
                    }
                });
            },
            guardar() {
                return this.$store.dispatch('finanzas/pago/store', {
                    id: this.id,
                    solicitud: this.solicitud
                }).then((data) => {
                    this.$router.push({name: 'pago'});
                });
            },
        },
        watch : {
            monto_pagar (value){
                if(value){
                    this.calcular();
                }
            },
            tipo_cambio (value){
                if(value){
                    this.calcular();
                }
            }
        }
    }
</script>

<style scoped>
    .sortable tr {
        cursor: pointer;
    }
</style>
