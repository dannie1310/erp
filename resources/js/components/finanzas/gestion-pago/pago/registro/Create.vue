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
                                    <label for="fecha_emision">Fecha:</label>
                                    <datepicker v-model = "fecha_emision"
                                                id="fecha_emision"
                                                name = "fecha_emision"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                v-validate="{required: true}"
                                                :disabled-dates="fechasDeshabilitadas"
                                                :class="{'is-invalid': errors.has('fecha_emision')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_emision')">{{ errors.first('fecha_emision') }}</div>
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
                                                   class="form-control"
                                                   data-vv-as="Cuenta"
                                                   :class="{'is-invalid': errors.has('id_cuenta')}"
                                                   v-model="cuenta.numero" />
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('id_cuenta') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" v-if="cuenta">
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
                                        <td style="text-align: right">
                                            {{solicitud.remesa.monto_autorizado_remesa}}
                                        </td>
                                        <td style="text-align: right" v-if="solicitud.tipo_transaccion == 72">
                                            {{solicitud.saldo}}
                                        </td>
                                        <td>
                                            <input
                                                 type="text" @change="calcular"
                                                 class="form-control"
                                                 name="monto_pagar"
                                                 style="text-align: right"
                                                 data-vv-as="Monto a Pagar"
                                                 v-model="monto_pagar"
                                                 v-validate="{max_value:solicitud.remesa.monto_autorizado_remesa, min_value:0}"
                                                 :class="{'is-invalid': errors.has('monto_pagar')}"
                                                 id="monto_pagar">
                                            <div class="invalid-feedback" v-show="errors.has('monto_pagar')">{{ errors.first('monto_pagar') }}</div>
                                        </td>
                                        <td v-if="cuenta != ''" style="text-align: right">
                                            {{ parseFloat(cuenta.moneda.tipo_cambio).formatMoney(2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right" v-else>0.00</td>
                                        <td style="text-align: right" v-if="cuenta != ''">
                                            {{ parseFloat(calcular).formatMoney(2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right" v-else>
                                            {{ parseFloat(solicitud.remesa.monto_autorizado_remesa).formatMoney(2, '.', ',') }}
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
                fecha_cobro : '',
                fecha_emision : '',
                solicitud : [],
                cuentas : [],
                cuenta : '',
                referencia : '',
                monto_pagar : 0
            }
        },
        mounted() {
            this.fecha_emision = new Date();
            this.fecha_cobro = new Date();
            this.solicitud = [];
            this.cuentas = [];
            this.$validator.reset();
            this.find();
            this.fechasDeshabilitadas.to= new Date();
        },
        methods : {
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
                        this.monto_pagar = data.remesa.monto_autorizado_remesa
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
                this.calcular();
            },
            calcular()
            {
                if(this.cuenta != '') {
                    this.solicitud.monto_calculado = this.monto_pagar * this.cuenta.moneda.tipo_cambio
                    return this.solicitud.monto_calculado
                }
            },
            salir(){
                this.$router.push({name: 'registro-pago'});
            },
            validate(){
                this.$validator.validate().then(result => {
                    if (result) {
                        this.solicitud.fecha_cobro = this.fecha_cobro;
                        this.solicitud.fecha_emision = this.fecha_emision;
                        this.solicitud.id_cuenta = this.cuenta.id;
                        this.solicitud.referencia_pago = this.referencia;
                        this.solicitud.monto_pagar = this.monto_pagar;
                        this.guardar()
                    }
                });
            },
            guardar() {
                return this.$store.dispatch('finanzas/pago/store', {
                    id: this.id,
                    solicitud: this.solicitud
                }).then((data) => {
                    this.salir();
                });
            },
        }
    }
</script>

<style scoped>
    .sortable tr {
        cursor: pointer;
    }
</style>
