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
                            <div class="row">
                                <div class="col-md-12">
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
                                                <th>Importe</th>
                                                <th>Tipo Cambio</th>
                                                <th>Importe con TC</th>
                                                <th>Tipo Cambio Actual</th>
                                                <th>Importe Pesos</th>
                                                <th>Cuenta Abono</th>
                                                <th>Cuenta Cargo</th>
                                                <th>Seleccionar</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(doc, i) in documentos" v-if="doc.disponible == 1">
                                                    <td>{{i+1}}</td>
                                                    <td>{{doc.concepto}}</td>
                                                    <td>{{doc.empresa ? doc.empresa.razon_social : ''}}</td>
                                                    <td>{{doc.monto_total_format}}</td>
                                                    <td>{{parseFloat(doc.tipo_cambio).formatMoney(2, '.', ',') }}</td>
                                                    <td>{{doc.saldo_moneda_nacional_format}}</td>
                                                    <td>{{doc.tipoCambioActual ? parseFloat(doc.tipoCambioActual.cambio).formatMoney(2, '.', ',') : '1.00'}}</td>
                                                    <td>${{parseFloat(doc.importe_total).formatMoney(2, '.', ',') }}</td>
                                                    <td v-if = "doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length > 0 ">
                                                        <select class="form-control"
                                                              :name="`id_cuenta_abono[${i}]`"
                                                              v-model="doc.id_cuenta_abono"
                                                              v-validate="{required: doc.selected == true ? true:false}"
                                                              data-vv-as="Cuenta"
                                                              :class="{'is-invalid': errors.has(`id_cuenta_abono[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in doc.empresa.cuentasBancariasProveedor.data" :value="cuenta.id">{{cuenta.banco.complemento.nombre_corto}} {{ cuenta.cuenta }}</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                            v-show="errors.has(`id_cuenta[${i}]`)">{{ errors.first(`id_cuenta_abono[${i}]`) }}
                                                        </div>
                                                    </td>
                                                    <td v-else-if="doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length == 0">Proveedor sin cuentas bancarias registradas</td>
                                                    <td v-else>No Cuenta Con Empresa en CADECO</td>
                                                    <td >
                                                        <select
                                                                class="form-control"
                                                                :name="`id_cuenta_cargo[${i}]`"
                                                                v-model="doc.id_cuenta_cargo"
                                                                v-validate="{required: doc.selected == true ? true : false}"
                                                                data-vv-as="Cuenta"
                                                                :class="{'is-invalid': errors.has(`id_cuenta_cargo[${i}]`)}"
                                                        >
                                                             <option value>-- Selecciona una cuenta --</option>
                                                             <option v-for="cuenta in cuenta_cargo" :value="cuenta.id">{{ cuenta.abreviatura }} ({{cuenta.numero}})</option>
                                                        </select>
                                                        <div class="invalid-feedback"
                                                             v-show="errors.has(`id_cuenta_cargo[${i}]`)">{{ errors.first(`id_cuenta_cargo[${i}]`) }}
                                                        </div>
                                                    </td>

                                                    <td v-if="doc.empresa && doc.empresa.cuentasBancariasProveedor.data.length > 0"><input type="checkbox" :value="doc.id" v-model="doc.selected"></td>
                                                    <td v-else></td>
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
                                                                        <tr>
                                                                            <th style="width:50%" class="bg-gray-light">Total Remesa:</th>
                                                                            <td class="bg-gray-light" align="right"><b>$&nbsp; {{(parseFloat(sumaImporteTotal)).formatMoney(2,'.',',')}}</b></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Documentos Seleccionados:</th>
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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || !cambio">Registrar</button>
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
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
            sumaImporteTotal() {
                let result = 0;
                let count = 0;
                this.documentos.forEach(function (doc, i) {
                       result += parseFloat(doc.importe_total);
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
                        doc.importe_total = parseFloat(doc.monto_total * doc.tipoCambioActual.cambio);
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
                        include: ['documento', 'documento.empresa.cuentasBancariasProveedor.banco', 'documento.tipoCambioActual']
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
                    });
            },

            store() {
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'distribuir-recurso-remesa-index'});
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
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
