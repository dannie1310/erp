<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Recursos Autorizados
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="id_remesa">Remesas Autorizada: </label>

                                        <select
                                                type="text"
                                                name="id_remesa"
                                                data-vv-as="Remesa Autorizada"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_remesa"
                                                v-model="id_remesa"
                                                :class="{'is-invalid': errors.has('id_remesa')}"
                                        >
                                            <option value>-- Seleccione una Remesa --</option>
                                            <option v-for="rem in remesas" :value="rem.id">Año {{rem.año}}, Semana {{rem.semana}} Remesa {{rem.tipo}} ({{ rem.folio }}) {{rem.proyecto_descripcion}}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_remesa')">{{ errors.first('id_remesa') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tipos de Cambio</label>
                                        <h5>{{tiposCambio}}</h5>
                                    </div>
                                </div>

                            </div>

                            <div class="row" v-if="documentos">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-9">
                                                <h6>Documentos Liberados de la Remesa</h6>
                                            </div>
                                            <div class="col-3">
                                                <h6 align="right">Total: {{documentos.length}}</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                <div  class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Concepto</th>
                                                <th></th>
                                                <th>Beneficiario</th>
                                               <!-- <th>Importe Moneda Original</th>
                                                <th>Moneda</th>
                                                <th>Importe en Pesos</th>-->
                                                <th>Importe a Pagar en Pesos</th>
                                                <th>Cuenta Cargo</th>
                                                <th>Cuenta Abono</th>
                                                <th>Seleccionar</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(doc, i) in documentos">
                                                    <td>{{i+1}}</td>
                                                    <td>{{doc.concepto}}</td>
                                                     <td v-if="doc.factura" >
                                                        <span v-if="doc.factura.empresa.efos" v-html="doc.factura.empresa.efos.alert_icon"></span>
                                                    </td>
                                                    <td v-else></td>
                                                    <td v-if="doc.beneficiario != null">{{doc.beneficiario}}</td>
                                                    <td class="text-danger" v-else>No registrado</td>
                                                    <!--<td class="text-right">{{doc.monto_total_format}}</td>-->
                                                    <!--<td>{{doc.moneda.abreviatura}}</td>-->
                                                    <!--<td class="text-right">{{doc.saldo_moneda_nacional_format}}</td>-->
                                                    <td class="text-right" v-if="doc.importe_total > 0">${{parseFloat(doc.importe_total).formatMoney(2, '.', ',') }}</td>
                                                    <td class="text-right" v-else><i class="fa fa-exclamation-triangle" style="color: red" title="Importes autorizados exceden el importe total solicitado"></i></td>
                                                    <td style="width: 15%;">
                                                        <select
                                                                class="form-control"
                                                                :name="`id_cuenta_cargo[${i}]`"
                                                                v-model="doc.id_cuenta_cargo"
                                                                v-validate="{required: doc.selected == true ? true : false}"
                                                                data-vv-as="Cuenta Cargo"
                                                                :class="{'is-invalid': errors.has(`id_cuenta_cargo[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in cuenta_cargo" :value="cuenta.id">{{ cuenta.abreviatura }} ({{cuenta.numero}})</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                             v-show="errors.has(`id_cuenta_cargo[${i}]`)">{{ errors.first(`id_cuenta_cargo[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td v-if = "doc.tipo_documento != 12 && doc.empresa && getCuentasActivas(doc.empresa.cuentas_bancarias.data).length > 0" style="width: 15%;">
                                                        <select class="form-control"
                                                                :name="`id_cuenta_abono[${i}]`"
                                                                v-model="doc.id_cuenta_abono"
                                                                v-validate="{required: doc.selected == true ? true:false}"
                                                                data-vv-as="Cuenta Abono"
                                                                :class="{'is-invalid': errors.has(`id_cuenta_abono[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in getCuentasActivas(doc.empresa.cuentas_bancarias.data)" :value="cuenta.id">{{getCuentaAbono(cuenta)}}</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                             v-show="errors.has(`id_cuenta_abono[${i}]`)">{{ errors.first(`id_cuenta_abono[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td v-else-if="doc.tipo_documento == 12 && doc.fondo && doc.fondo.empresa && getCuentasActivas(doc.fondo.empresa.cuentas_bancarias.data).length > 0 " style="width: 15%;">
                                                        <select class="form-control"
                                                                :name="`id_cuenta_abono[${i}]`"
                                                                v-model="doc.id_cuenta_abono"
                                                                v-validate="{required: doc.selected == true ? true:false}"
                                                                data-vv-as="Cuenta Abono"
                                                                :class="{'is-invalid': errors.has(`id_cuenta_abono[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in getCuentasActivas(doc.fondo.empresa.cuentas_bancarias.data)" :value="cuenta.id">{{getCuentaAbono(cuenta)}}</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                             v-show="errors.has(`id_cuenta_abono[${i}]`)">{{ errors.first(`id_cuenta_abono[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td class="text-danger" style="width: 15%;" v-else-if="doc.tipo_documento != 12 && doc.empresa && getCuentasActivas(doc.empresa.cuentas_bancarias.data).length == 0">Beneficiario sin cuentas bancarias registradas</td>
                                                    <td class="text-danger" style="width: 15%;" v-else-if="doc.tipo_documento == 12 && doc.fondo && doc.fondo.empresa && getCuentasActivas(doc.fondo.empresa.cuentas_bancarias.data).length == 0">Beneficiario de fondo sin cuentas bancarias registradas</td>
                                                    <td class="text-danger"  style="width: 15%;" v-else>Beneficiario no registrado en cátalogo de Empresas SAO</td>

                                                    <td class="text-center" v-if="doc.empresa && getCuentasActivas(doc.empresa.cuentas_bancarias.data).length > 0 && doc.tipo_cambio == 1 || doc.fondo && doc.fondo.empresa && getCuentasActivas(doc.fondo.empresa.cuentas_bancarias.data).length > 0 && doc.tipo_cambio == 1 "><input type="checkbox" :value="doc.id" v-model="doc.selected"></td>
                                                    <td class="text-center" v-else-if="doc.tipo_cambio != 1"><i class="fa fa-exclamation-triangle" style="color: orange" title="Partida en moneda extranjera no seleccionable por el momento."></i></td>
                                                    <td class="text-center" v-else><i class="fa fa-exclamation-triangle" style="color: red" title="No seleccionable por datos faltantes."></i></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                                        <div  v-if="documentos">
                                            <form role="form" @submit.prevent="validate">
                                                <div class="row" align="left">
                                                    <div class="table-responsive col-md-12">
                                                        <div class="col-12">
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <tbody>
                                                                        <tr>
                                                                            <th style="width:25%" class="bg-gray-light">Remesa Autorizada (MXN):</th>
                                                                            <td style="width:15%" class="bg-gray-light" align="right"><b>$&nbsp; {{(parseFloat(monto_total_remesa)).formatMoney(2,'.',',')}}</b></td>

                                                                            <th style="width:10%"></th>
                                                                            <td style="width:10%"></td>

                                                                            <th>Documentos Seleccionados a Pagar (MXN):</th>
                                                                            <td style="width:15%" align="right"> <b>$&nbsp;{{(parseFloat(sumaSeleccionImportes)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="width:20%">Dispersiones Anteriores (MXN):</th>
                                                                            <td style="width:15%" align="right"><b>$&nbsp; {{(parseFloat(monto_distribuido_anteriormente)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="bg-gray-light">Dispersión Actual (MXN):</th>
                                                                            <td align="right" class="bg-gray-light"> <b>$&nbsp;{{(parseFloat(sumaSeleccionImportes)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Restante por Dispersar (MXN):</th>
                                                                            <td align="right"> <b>$&nbsp;{{(parseFloat(monto_total_remesa-(sumaSeleccionImportes + monto_distribuido_anteriormente)).formatMoney(2,'.',','))}}</b></td>
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
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || !cambio || sumaSeleccionImportes == 0">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "distribuir-recurso-remesa-create",
        data() {
            return {
                id_remesa : '',
                remesas : [],
                monedas : [],
                original : null,
                documentos : null,
                monto_distribuido_anteriormente : 0,
                monto_total_remesa : 0,
                cuenta_cargo: [],
                total_selecionado: 0,
                cargando: false
            }
        },
        mounted() {
            this.getRemesas();
            this.getMonedas();
        },
        computed: {
            sumaSeleccionImportes() {
                let result = 0;
                this.documentos.forEach(function (doc, i) {
                    if(doc.selected == true) {
                        result += parseFloat(doc.importe_total);
                    }
                })
                this.total_selecionado = result;
                return result
            },

            diff() {
                return diff(this.documentos, this.original)
            },

            cambio() {
                return JSON.stringify(this.documentos) != JSON.stringify(this.original) || this.nuevosMovimientos
            },

            tiposCambio(){
                let txt = "";
                this.monedas.forEach(function (mon, i) {
                    txt += mon.abreviatura + " : $" + parseFloat(mon.tipo_cambio).formatMoney(2, '.', ',') + "   ";
                });
                return txt;
            },

            nuevosDistribucion() {
                return !!this.original.documentos.find(doc => {
                    return !doc .id
                })
            }
        },
        methods: {
            getCuentasActivas(cuentas){
                return cuentas.filter(function (value, index,arr) {
                    return parseInt(value.estado) === 1
                })
            },
            getRemesas() {
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/remesa/index', {
                    params: {
                        scope: 'liberada',
                        sort: 'FechaHoraRegistro',
                        order: 'DESC'
                    }
                })
                    .then(data => {
                        this.remesas = data.data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },

            getMonedas(){
                this.cargando = true;
                let self = this;
                return self.$store.dispatch('cadeco/moneda/index', {
                    params: {
                        scope: 'monedaExtranjera'
                    }
                })
                    .then(data => {
                        self.monedas = data.data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },

            getCuentaCargo() {
                this.cargando = true;
                let self = this
                return self.$store.dispatch('cadeco/cuenta/index', {
                    params: {
                        scope: ['paraTraspaso', 'pagadora'],
                    }
                })
                    .then(data => {
                        this.cuenta_cargo = data.data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },

            getDocumentos(){
                this.documentos = null;
                this.original = null;
                this.monto_distribuido_anteriormente = 0;
                this.monto_total_remesa = 0;
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/remesa/find',{
                    id: self.id_remesa,
                    params: {
                        include: ['documentosDisponibles',
                            'documentosDisponibles.empresa.cuentas_bancarias.banco.ctgBanco',
                            'documentosDisponibles.moneda',
                            'documentosDisponibles.montoProcesado',
                            'remesaLiberada',
                            'documentosDisponibles.fondo.empresa.cuentas_bancarias.banco', 'documentosDisponibles.empresa.efos'],
                    }
                })
                    .then(data => {
                        if(data.documentosDisponibles.data != 0) {
                            this.documentos = [];
                            this.original = [];
                            this.monto_distribuido_anteriormente = data.remesaLiberada.monto_distribuido;
                            this.monto_total_remesa = data.remesaLiberada.monto_total_remesa;
                            this.original = JSON.parse(JSON.stringify(data.documentosDisponibles.data));
                            this.documentos = JSON.parse(JSON.stringify(data.documentosDisponibles.data));
                        }else{
                            swal("La remesa seleccionada no tiene documentos disponibles para generar una distribución de recursos.", {
                                icon: "warning",
                                buttons: {
                                    confirm: {
                                        text: 'Aceptar'
                                    }
                                }
                            })
                        }
                    })
                    .finally(() => {
                        this.cargando = false;
                        this.getCuentaCargo();
                    });
            },

            getCuentaAbono(cuenta){
                if(cuenta.banco.ctgBanco){
                    return cuenta.banco.ctgBanco.nombre_corto+" "+ cuenta.cuenta;
                }
                return  "----- "+ cuenta.cuenta;
            },

            store() {
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'distribuir-recurso-remesa'});
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

            salir(){
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/salir')
                    .then(() => {
                        this.$router.push({name: 'distribuir-recurso-remesa'});
                    });
            }
        },
        watch: {
            id_remesa(value){
                if(value){
                    this.getDocumentos();
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
