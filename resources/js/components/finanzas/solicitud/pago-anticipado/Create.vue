<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_solicitud_pago_anticipado')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
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
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha de Solicitud:</b></label>
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
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Limite de Pago:</b></label>
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
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <div class="col-md-12">
                                            <div class="form-group error-content">
                                                <label for="id_empresa">Beneficiario</label>
                                                <empresa
                                                        name="id_empresa"
                                                        placeholder="-- Empresa --"
                                                        id="id_empresa"
                                                        data-vv-as="Empresa"
                                                        v-validate="{required: true}"
                                                        v-model="id_empresa"
                                                        scope="proveedorContratista"
                                                        :error="errors.has('id_empresa')">
                                                ></empresa>
                                                <div class="error-label" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="tipo">Tipo de Transacción:</label>
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
                                <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="id_antecedente">Transacción: </label>
                                        <select
                                                :disabled="!bandera_transaccion"
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
                                            <option v-for="tran in transacciones" :value="tran.id">{{ tran.numero_folio_format }} ({{ tran.dato_transaccion }})</option>
                                        </select>
                                        <div class="error-label" v-show="errors.has('id_antecedente')">{{ errors.first('id_antecedente') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="iniciar == 1">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <h6>Información de la Transacción</h6>
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
                                                            <td class="bg-gray-light"><b>Número de Folio:</b><br>
                                                                {{ transaccion.numero_folio_format}}
                                                            </td>
                                                             <td class="bg-gray-light" v-if="transaccion.empresa"><b>Empresa:</b><br>
                                                                {{ transaccion.empresa.razon_social }}
                                                            </td>
                                                            <td class="bg-gray-light"><b>Referencia:</b><br>
                                                               {{ transaccion.referencia}}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div>
                                                <form role="form" @submit.prevent = "validate">
                                                    <div class="row" align="left">
                                                        <div class="table-responsive col-md-12">
                                                            <div class="col-12">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            <tr>
                                                                                <th class="bg-gray-light">Subtotal:</th>
                                                                                <td class="bg-gray-light" align="right"><b>{{ transaccion.subtotal_format}}</b></td>
                                                                                <th></th>
                                                                                <th style="width: 10%" for="importe">Importe a Solicitar:</th>
                                                                                <td>
                                                                                    <div class="col-12">
                                                                                        <div class="form-group error-content">
                                                                                            <input
                                                                                                    :disabled="!iniciar"
                                                                                                    type="number"
                                                                                                    step="any"
                                                                                                    name="importe"
                                                                                                    data-vv-as="Importe"
                                                                                                    v-validate="{required: true, min_value:0.1, max_value: disponible, decimal:2}"
                                                                                                    class="form-control"
                                                                                                    id="importe"
                                                                                                    placeholder="Importe"
                                                                                                    v-model="importe"
                                                                                                    :class="{'is-invalid': errors.has('importe')}">
                                                                                            <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>IVA:</th>
                                                                                <td align="right">{{ transaccion.impuesto_format }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th class="bg-gray-light">Total:</th>
                                                                                <td class="bg-gray-light" align="right">{{ transaccion.total_format }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" align="left">
                                                        <div class="table-responsive col-md-12">
                                                            <div class="col-12">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <tbody>
                                                                            <tr>
                                                                                <th>Monto Facturado:</th>
                                                                                <td align="right" v-model="facturado">$ {{ (parseFloat(facturado)).formatMoney(2,'.',',') }}</td>
                                                                                <th class="bg-gray-light">Monto en otras Solicitudes:</th>
                                                                                <td align="right" class="bg-gray-light" v-model="solicitado">$ {{ (parseFloat(solicitado)).formatMoney(2,'.',',') }}</td>
                                                                                <th class="bg-gray">Monto Disponible:</th>
                                                                                <td class="bg-gray" align="right" v-model="disponible">$ {{ (parseFloat(disponible)).formatMoney(2,'.',',')}}</td>
                                                                                <th class="bg-gray-light">Monto Restante:</th>
                                                                                <td class="bg-gray-light" align="right" v-model="restante">$ {{ (parseFloat(restante)).formatMoney(2,'.',',')}}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="row col-12">
                                    <!-- Observaciones -->
                                    <div class="col-md-12">
                                        <div class="form-group error-content">
                                            <label for="observaciones">Motivo:</label>
                                            <textarea
                                                    name="observaciones"
                                                    id="observaciones"
                                                    class="form-control"
                                                    v-model="observaciones"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Motivo"
                                                    :class="{'is-invalid': errors.has('observaciones')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-12">
                                    <!-- Costos -->
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <label for="id_costo" class="col-sm-2 col-form-label">Costos:</label>
                                            <div class="col-sm-10">
                                                <costo-select
                                                        name="id_costo"
                                                        data-vv-as="Costo"
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
                        </div>
                         <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 || id_antecedente=='' ">Registrar</button>
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
    import Empresa from "../../../cadeco/empresa/Select";
    export default {
        name: "solicitud-pago-anticipado-create",
        components: {CostoSelect, Datepicker,Empresa},
        data() {
            return {
                es: es,
                fecha_solicitud_1: '',
                fecha_limite_1: '',
                cumplimiento: '',
                vencimiento: '',
                id_empresa: '',
                empresas: [],
                tipo: '',
                transacciones: [],
                id_antecedente: '',
                cargando: false,
                transaccion: [],
                observaciones: '',
                id_costo: '',
                bandera_transaccion: 0,
                importe : '',
                disponible : 0,
                solicitado : 0,
                facturado : 0,
                iniciar : 0,
                restante : 0
            }
        },
        computed: {

        },
        methods: {
            init() {
                    this.cargando = true;
                    $(this.$refs.modal).modal('show');
                    this.fecha_solicitud_1 = '';
                    this.fecha_limite_1 = '';
                    this.cumplimiento = '';
                    this.vencimiento = '';
                    this.id_empresa = '';
                    this.tipo = '';
                    this.transacciones = [];
                    this.id_antecedente = '';
                    this.transaccion = [];
                    this.id_costo = '';
                    this.observaciones = '';
                    this.$validator.reset();
                    this.cargando = false;
                    this.importe = '';
                    this.disponible = 0;
                    this.solicitado = 0;
                    this.facturado = 0;
                    this.iniciar = 0;
                    this.restante = 0;
                    this.bandera_transaccion = 0;
            },
            formatoFecha(date){
                return moment(date).format('YYYY-MM-DD');
            },
            getEmpresas(){
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        sort: 'razon_social', order: 'asc',
                        scope: 'proveedorContratista'
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })
            },
            getOrdenes() {
                return this.$store.dispatch('compras/orden-compra/index',{
                    config: {
                        params: {
                            scope: 'ordenCompraDisponible:'+this.id_empresa
                        }
                    }
                }).then(data => {
                    this.transacciones = data;
                    this.bandera_transaccion = 1;
                })
            },
            getSubcontratos() {
                return this.$store.dispatch('contratos/subcontrato/index',{
                    config: {
                        params: {
                            scope: 'subcontratosDisponible:'+this.id_empresa
                        }
                    }
                }).then(data => {
                    this.transacciones = data;
                    this.bandera_transaccion = 1;
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
                            this.iniciar = 1;
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
                            this.iniciar = 1;
                        })
                }
            },
            store() {
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/store', this.$data)
                    .then(data => {
                       this.$emit('created', data);
                       $(this.$refs.modal).modal('hide');
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
            id_empresa(value){
                this.transacciones = [];
                this.bandera_transaccion = 0;
                if(value != '')
                {
                    if(this.tipo == 19){
                        this.getOrdenes();
                    }
                    if(this.tipo == 51) {
                        this.getSubcontratos();
                    }
                }
            },
            tipo(value){
                this.transacciones = [];
                this.transaccion = [];
                this.bandera_transaccion = 0;
                this.iniciar = 0;
                this.id_costo = '';
                this.observaciones = '';

                if(value){
                    if(value == 19 && this.id_empresa !=''){
                        this.getOrdenes();
                    }
                    if(value == 51 && this.id_empresa !='') {
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
            transaccion(value){
                this.disponible = 0;
                this.solicitado = 0;
                this.facturado = 0;
                this.restante = 0;
                this.importe = '';

                if(value.length != 0) {
                    this.disponible = parseFloat(value.saldo - (value.monto_facturado_ea + value.monto_facturado_oc + value.monto_solicitado)).toFixed(2);
                    this.solicitado = value.monto_solicitado;
                    this.facturado = value.monto_facturado_ea + value.monto_facturado_oc;
                    this.restante = parseFloat(value.saldo - (value.monto_facturado_ea + value.monto_facturado_oc + value.monto_solicitado)).toFixed(2);
                }
            },
            importe(value){
                if(value){
                    this.restante = this.disponible - value;
                }
            }
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
