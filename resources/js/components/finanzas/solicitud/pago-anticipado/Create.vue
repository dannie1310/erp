<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_solicitud_pago_anticipado')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Solicitud de Pago Anticipado
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR SOLICITUD DE PAGO ANTICIPADO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha de Solicitud</b></label>
                                                <datepicker v-model = "fecha_solicitud_1"
                                                            name = "fecha_solicitud_1"
                                                            :language = "es"
                                                            :format = "formatoFecha"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :class="{'is-invalid': errors.has('fecha_solicitud_1')}"
                                                ></datepicker>
                                             <div class="invalid-feedback" v-show="errors.has('fecha_solicitud_1')">{{ errors.first('fecha_solicitud_1') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Limite de Pago</b></label>
                                                <datepicker v-model = "fecha_limite_1"
                                                            name = "fecha_limite_1"
                                                            :language = "es"
                                                            :format = "formatoFecha"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :class="{'is-invalid': errors.has('fecha_limite_1')}"
                                                ></datepicker>
                                             <div class="invalid-feedback" v-show="errors.has('fecha_limite_1')">{{ errors.first('fecha_limite_1') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        <label for="tipo">Tipo de Cuenta</label>
                                        <select
                                                type="text"
                                                name="tipo"
                                                data-vv-as="Tipo Transacción"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="tipo"
                                                v-model="tipo"
                                                :class="{'is-invalid': errors.has('tipo')}"
                                        >
                                            <option value>--- Tipo de Transacción ---</option>
                                            <option value="19">Orden de Compra</option>
                                            <option value="51">Subcontrato</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group error-content">
                                        <label for="id_antecedente">Transacción</label>
                                        <select
                                                :disabled="!tipo"
                                                type="text"
                                                name="id_antecedente"
                                                data-vv-as="Transacción"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_antecedente"
                                                v-model="id_antecedente"
                                                :class="{'is-invalid': errors.has('id_antecedente')}"
                                        >
                                            <option value>-- Seleccione Transacción --</option>
                                            <option v-for="tran in transacciones" :value="tran.id">{{ tran.numero_folio_format }}--({{ tran.referencia }}-)</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_antecedente')">{{ errors.first('id_antecedente') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <h3>Información de la Transacción</h3>
                                            </div>
                                            <div class="col-6">
                                                <h6 align="right">Fecha: {{ transaccion.fecha_format }}</h6>
                                            </div>
                                        </div>
                                        <form role="form" @submit.prevent="validate">
                                            <div class="row">
                                                <div class="table-responsive col-md-12">
                                                    <table class="table table-striped">
                                                        <tbody>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Número de Folio</b><br>
                                                                {{ transaccion.numero_folio_format}}
                                                            </td>
                                                            <td class="bg-gray-light"><b>Requisición</b><br>
                                                                {{ transaccion.referencia}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-gray-light" v-if="transaccion.empresa"><b>Empresa</b><br>
                                                                {{ transaccion.empresa.razon_social }}
                                                            </td>
                                                            <td class="bg-gray-light"><b>Referencia</b><br>
                                                               {{ transaccion.referencia}}
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row" align="right">
                                                <div class="table-responsive col-md-12">
                                                    <div class="col-6">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th style="width:50%" class="bg-gray-light">Subtotal:</th>
                                                                        <td class="bg-gray-light">{{ transaccion.subtotal_format}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>IVA:</th>
                                                                        <td>{{ transaccion.impuesto_format }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="bg-gray-light">Total:</th>
                                                                        <td class="bg-gray-light">{{ transaccion.total_format }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Observaciones -->
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="observaciones">Observaciones</label>
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
                            <div class="row">
                                <!-- Costos -->
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_costo" class="col-sm-2 col-form-label">Costos</label>
                                        <div class="col-sm-10">
                                            <costo-select
                                                    name="id_costo"
                                                    data-vv-as="Costo"
                                                    scope="costoFinanza"
                                                    v-validate="{required: true}"
                                                    id="id_costo"
                                                    v-model="id_costo"
                                                    :error="errors.has('id_costo')"
                                                    ref="costoSelect"
                                                    :disableBranchNodes="false"
                                            ></costo-select>
                                            <div class="error-label" v-show="errors.has('id_costo')">{{ errors.first('id_costo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale'
    import CostoSelect from "../../../cadeco/costo/Select";
    export default {
        name: "solicitud-pago-anticipado-create",
        components: {CostoSelect, Datepicker},
        data() {
            return {
                es: es,
                fecha_solicitud_1: '',
                fecha_limite_1: '',
                cumplimiento: '',
                vencimiento: '',
                tipo: 0,
                transacciones: [],
                id_antecedente: '',
                cargando: false,
                transaccion: [],
                observaciones: '',
                id_costo: '',
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },
        methods: {
            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    this.cargando = true;
                    $(this.$refs.modal).modal('show');
                    this.fecha_solicitud_1 = '';
                    this.fecha_limite_1 = '';
                    this.cumplimiento = '';
                    this.vencimiento = '';
                    this.tipo = 0;
                    this.transacciones = [];
                    this.id_antecedente = '';
                    this.transaccion = [];
                    this.id_costo = '';
                    this.observaciones = '';
                    this.$validator.reset()
                    this.cargando = false;
                }
            },
            formatoFecha(date){
                return moment(date).format('YYYY-MM-DD');
            },
            getOrdenes() {
                return this.$store.dispatch('compras/orden-compra/index',{
                    config: {
                        params: {
                            scope: 'sinPagoAnticipado'
                        }
                    }
                }).then(data => {
                        this.transacciones = data;
                    })
            },
            getSubcontratos() {
                return this.$store.dispatch('contratos/subcontrato/index',{
                    config: {
                        params: {
                            scope: 'sinPagoAnticipado'
                        }
                    }
                }).then(data => {
                        this.transacciones = data;
                })
            },
            getTransaccion(){
                this.transaccion = [];
                if(this.tipo == 19)
                {
                    return this.$store.dispatch('compras/orden-compra/find', {
                        id: this.id_antecedente,
                        params: {
                            include: ['empresa']
                        }
                    })
                        .then(data => {
                            this.transaccion = data;
                        })
                }
                if(this.tipo == 51)
                {
                    return this.$store.dispatch('contratos/subcontrato/find', {
                        id: this.id_antecedente,
                        params: {
                            include: ['empresa']
                        }
                    })
                        .then(data => {
                            this.transaccion = data;
                        })
                }
            },
            store() {
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/store', this.$data)
                    .then(data => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                       if(this.fecha_limite < this.fecha_solicitud){
                            swal('¡Error!', 'La fecha limite no puede ser antes de la fecha de solicitud.', 'error')
                        }else {
                           this.store()
                       }
                    }
                });
            },
        },
        watch: {
            tipo(value){
                this.transacciones = [];
                this.transaccion = [];
                if(value){
                    if(value == 19){
                        this.getOrdenes();
                    }
                    if(value == 51) {
                        this.getSubcontratos();
                    }
                }
            },
            id_antecedente(value){
                if(value){
                    this.getTransaccion();
                }
            },
            fecha_limite_1(value){
                var d = 0;
                var m = 0;
                var y = 0;
                if(value){
                    if(value < this.fecha_solicitud_1){
                        swal('¡Error!', 'La fecha limite no puede ser antes de la fecha de solicitud.', 'error')
                    }else{
                        var date =  new Date (value);
                        d = date.getDate();
                        m = date.getMonth() + 1;
                        y = date.getFullYear();
                        if (d < 10) {
                            d = '0' + d;
                        }
                        if (m < 10) {
                            m = '0' + m;
                        }
                        this.vencimiento = y+'-'+ m+'-'+d;
                    }
                }
            },
            fecha_solicitud_1(value) {
                var d = 0;
                var m = 0;
                var y = 0;

                if(value){
                   var date =  new Date (value);
                   d = date.getDate();
                   m = date.getMonth() + 1;
                   y = date.getFullYear();
                    if (d < 10) {
                        d = '0' + d;
                    }
                    if (m < 10) {
                        m = '0' + m;
                    }
                    this.cumplimiento = y+'-'+ m+'-'+d;
                }
            },


        }
    }
</script>
<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>