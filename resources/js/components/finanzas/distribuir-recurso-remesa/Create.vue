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
                                                <h6 align="right">Total: 15</h6>
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
                                                <tr v-for="(doc, i) in documentos">
                                                    <td>{{i+1}}</td>
                                                    <td>{{doc.concepto}}</td>
                                                    <td>{{doc.empresa.razon_social}}</td>
                                                    <td>{{doc.monto_total_format}}</td>
                                                    <td>{{doc.tipo_cambio}}</td>
                                                    <td>{{doc.moneda}}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <select class="form-control"
                                                              :name="`id_cuenta_abono[${i}]`"
                                                              v-model="doc.id_cuenta_abono"
                                                              v-validate="{required: true}"
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
                                                     <td>
                                                        <select
                                                                class="form-control"
                                                                :name="`id_cuenta_cargo[${i}]`"
                                                                v-model="doc.id_cuenta_cargo"
                                                                v-validate="{required: true}"
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

                                                    <td><input type="checkbox" :value="doc.id" v-model="doc.selected"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                                        <form role="form" @submit.prevent="validate">
                                            <div class="row" align="right">
                                                <div class="table-responsive col-md-12">
                                                    <div class="col-6">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th style="width:50%" class="bg-gray-light">Monto Total Remesa:</th>
                                                                        <td class="bg-gray-light" align="right">10000</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Documentos Seleccionados:</th>
                                                                        <td align="right">0 <span>Checked names: </span></td>
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
    export default {
        name: "distribuir-recurso-remesa-create",
        data() {
            return {
                id_remesa : '',
                remesas : [],
                original : null,
                documentos : null,
                cuenta_cargo: [],
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

            getDocumentos(){
                this.documentos = [];
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/remesa/find',{
                    id: self.id_remesa,
                    params: {
                        include: ['documento', 'documento.empresa.cuentasBancariasProveedor.banco', 'documento.tipo_cambio_actual']
                    }
                })
                    .then(data => {
                        this.original = JSON.parse(JSON.stringify(data.documento.data));
                        this.documentos = JSON.parse(JSON.stringify(data.documento.data));
                    })
                    .finally(() => {
                        this.cargando = false;
                        this.getCuentaCargo();
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