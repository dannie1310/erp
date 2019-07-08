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
                                            <option v-for="rem in remesas" :value="rem.id">Año {{rem.año}}, Semana {{rem.semana}} Remesa {{rem.tipo}} ({{ rem.folio }})</option>
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

                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-9">
                                                <h3>Documentos Liberados de la Remesa</h3>
                                            </div>
                                            <div class="col-3">
                                                <h6 align="right">Total: {{count}}</h6>
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
                                                <th>Beneficiario</th>
                                                <th>Importe Moneda Original</th>
                                                <th>Moneda</th>
                                                <th>Importe en Pesos</th>
                                                <th>Importe a Pagar en Pesos</th>
                                                <th>Cuenta Abono</th>
                                                <th>Cuenta Cargo</th>
                                                <th>Seleccionar</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(doc, i) in documentos" v-if="doc.disponible == 1">
                                                    <td>{{i+1}}</td>
                                                    <td>{{doc.concepto}}</td>
                                                    <td v-if="doc.empresa">{{doc.empresa.razon_social}}</td>
                                                    <td class="text-danger" v-else>No registrado en cátalogo de Empresas SAO</td>
                                                    <td class="text-right">{{doc.monto_total_format}}</td>
                                                    <td>{{doc.moneda.abreviatura}}</td>
                                                    <td class="text-right">{{doc.saldo_moneda_nacional_format}}</td>
                                                    <td class="text-right">${{parseFloat(doc.importe_total).formatMoney(2, '.', ',') }}</td>
                                                    <td v-if = "doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length > 0 " style="width: 15%;">
                                                        <select class="form-control"
                                                              :name="`id_cuenta_abono[${i}]`"
                                                              v-model="doc.id_cuenta_abono"
                                                              v-validate="{required: doc.selected == true ? true:false}"
                                                              data-vv-as="Cuenta Abono"
                                                              :class="{'is-invalid': errors.has(`id_cuenta_abono[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in doc.empresa.cuentasBancariasProveedor.data" :value="cuenta.id">{{cuenta.banco.complemento.nombre_corto}} {{ cuenta.cuenta }}</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                            v-show="errors.has(`id_cuenta_abono[${i}]`)">{{ errors.first(`id_cuenta_abono[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td class="text-danger" style="width: 15%;" v-else-if="doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length == 0">Beneficiario sin cuentas bancarias registradas</td>
                                                    <td class="text-danger"  style="width: 15%;" v-else>Beneficiario no registrado en cátalogo de Empresas SAO</td>
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

                                                    <td class="text-center" v-if="doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length > 0 && doc.tipo_cambio === 1"><input type="checkbox" :value="doc.id" v-model="doc.selected"></td>
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
                                                <div class="row" align="right">
                                                    <div class="table-responsive col-md-12">
                                                        <div class="col-6">
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <tbody>
                                                                        <tr v-for="(moneda) in monedas">
                                                                            <th style="width:50%" class="bg-gray-light">Subtotal Remesa ({{ moneda.abreviatura.replace(' ', '')}}):</th>
                                                                            <td class="bg-gray-light" align="right"><b>$&nbsp; {{(parseFloat(subtotal_x_moneda[moneda.id])).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>

                                                                        <tr>
                                                                            <th style="width:50%" class="bg-gray-light">Subtotal Remesa (MXP):</th>
                                                                            <td class="bg-gray-light" align="right"><b>$&nbsp; {{(parseFloat(subtotal_x_moneda[1])).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th style="width:50%" class="bg-gray-light">Total Remesa (MXP):</th>
                                                                            <td class="bg-gray-light" align="right"><b>$&nbsp; {{(parseFloat(sumaImporteTotal)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Documentos Seleccionados a Pagar (MXP):</th>
                                                                            <td align="right"> <b>$&nbsp;{{(parseFloat(sumaSeleccionImportes)).formatMoney(2,'.',',')}}</b></td>
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
                subtotal_x_moneda : [],
                original : null,
                documentos : null,
                cuenta_cargo: [],
                total_selecionado: 0,
                total: 0,
                count:0,
                cargando: false
            }
        },
        mounted() {
            this.getRemesas();
            this.getMonedas();
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
            sumaImporteTotal() {
                let result = 0;
                let count = 0;
                this.documentos.forEach(function (doc, i) {
                       result += parseFloat(doc.monto_total_solicitado);
                       if(doc.disponible == 1) {
                           count += 1;
                       }
                })
                this.total = result;
                this.count = count;
                return result
            },
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
            },
        },
        methods: {
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
                        scope: 'paraTraspaso'
                    }
                })
                    .then(data => {
                        this.cuenta_cargo = data.data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },

            getImporte() {
                let result = 0;
                this.documentos.forEach(function (doc, i) {
                    doc.importe_total = 0
                    if (doc.tipo_cambio == 1.0) {
                        doc.importe_total = parseFloat(doc.monto_total);
                    } else {
                        doc.importe_total = parseFloat(doc.monto_total *  doc.moneda.tipo_cambio);
                    }
                })
                return result
            },

            getDocumentos(){
                this.documentos = [];
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/remesa/find',{
                    id: self.id_remesa,
                    params: {
                        include: ['documento', 'documento.empresa.cuentasBancariasProveedor.banco', 'documento.moneda']
                    }
                })
                    .then(data => {
                        this.original = JSON.parse(JSON.stringify(data.documento.data));
                        this.documentos = JSON.parse(JSON.stringify(data.documento.data));
                        this.getImporte();
                    })
                    .finally(() => {
                        this.cargando = false;
                        this.getCuentaCargo();
                        this.sumaSubtotalPorMoneda();
                    });
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
            sumaSubtotalPorMoneda(){
                let self = this;
                self.subtotal_x_moneda =[];
                self.subtotal_x_moneda[1] = 0;
                self.monedas.forEach(function (moneda, i) {
                    self.subtotal_x_moneda[moneda.id] = 0;
                });
                self.documentos.forEach(function (doc, i) {
                    self.subtotal_x_moneda[doc.id_moneda] += parseFloat(doc.monto_total_solicitado);
                })
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
