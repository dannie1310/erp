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
                            <div class="col-md-4" v-if="cuentas">
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_cuenta">Cuenta Bancaria:</label>
                                            <model-list-select
                                                :disabled="cargando"
                                                name="id_cuenta"
                                                option-value="id"
                                                v-model="id_cuenta"
                                                v-validate="{required: true}"
                                                :custom-text="numeroCuenta"
                                                :list="cuentas"
                                                :placeholder="!cargando?'Seleccionar o buscar por número, banco o saldo':'Cargando...'"
                                                :isError="errors.has(`id_cuenta`)">
                                            </model-list-select>
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
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="fecha_emision">Fecha de Emisión:</label>
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
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="fecha_emision">Fecha de Cobro:</label>
                                    <datepicker v-model = "fecha_cobro"
                                                id="fecha_cobro"
                                                name = "fecha_cobro"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                v-validate="{required: true}"
                                                :disabled-dates="fechasDeshabilitadas"
                                                :class="{'is-invalid': errors.has('fecha_cobro')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_cobro')">{{ errors.first('fecha_cobro') }}</div>
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
                                            Autorizado
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
                                            {{solicitud.autorizado_format}}
                                        </td>
                                        <td style="text-align: right">
                                            {{solicitud.autorizado_format}}
                                        </td>
                                        <td v-if="cuenta != ''" style="text-align: right">
                                            {{ parseFloat(cuenta.moneda.tipo_cambio).formatMoney(2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right" v-else>0.00</td>
                                        <td style="text-align: right" v-if="cuenta != ''">
                                            {{ parseFloat(solicitud.autorizado * cuenta.moneda.tipo_cambio).formatMoney(2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right" v-else>
                                            {{ parseFloat(solicitud.autorizado).formatMoney(2, '.', ',') }}
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
    </span>
</template>

<script>
    import DatosDocumento from "./partials/DatosDocumento";
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "registrar-pago",
        props: ['id'],
        components : { DatosDocumento, Datepicker, es, ModelListSelect },
        data() {
            return {
                es: es,
                cargando: false,
                fechasDeshabilitadas:{},
                fecha_cobro : '',
                fecha_emision : '',
                solicitud : [],
                cuentas : [],
                id_cuenta : '',
                cuenta : '',
                referencia : ''
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
            numeroCuenta(item){
                return `[${item.numero}] - [Moneda: ${item.moneda.nombre}]- [Banco: ${item.empresa.razon_social}] - [Saldo - ${item.saldo_real}]`
            },
            find() {
                this.cargando = true
                return this.$store.dispatch('finanzas/pago/documentoParaPagar', {
                    id: this.id,
                }).then(data => {
                    this.solicitud = data;
                    this.getCuentas()
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
            getCuenta() {
                return this.$store.dispatch('cadeco/cuenta/find', {
                    id: this.id_cuenta,
                    params: { include: ['moneda','empresa'] }
                }).then(data => {
                    this.cuenta = data;
                });
            },
            salir(){
                this.$router.push({name: 'registro-pago'});
            },
            validate(){
                this.$validator.validate().then(result => {
                    if (result) {
                        this.solicitud.fecha_cobro = this.fecha_cobro;
                        this.solicitud.fecha_emision = this.fecha_emision;
                        this.solicitud.id_cuenta = this.id_cuenta;
                        this.solicitud.referencia_pago = this.referencia;
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
        },
        watch: {
            id_cuenta(value){
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.getCuenta();
                }
            },
        }
    }
</script>

<style scoped>

</style>
