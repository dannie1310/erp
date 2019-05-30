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
                                                <th>Número Folio</th>
                                                <th>Referencia</th>
                                                <th>Importe</th>
                                                <th>Tipo Cambio</th>
                                                <th>Moneda</th>
                                                <th>Tipo Cambio Actual</th>
                                                <th>Importe Pesos</th>
                                                <th>Seleccionar</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(doc, key) in documentos">
                                                    <td>{{key+1}}</td>
                                                    <td>{{doc.numero_folio}}</td>
                                                    <td>{{doc.referencia}}</td>
                                                    <td>{{doc.monto_total}}</td>
                                                    <td>{{doc.tipo_cambio}}</td>
                                                    <td>{{doc.moneda}}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><input type="checkbox" :value="doc.id" v-model="checkedDocumentos"></td>
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
                                                                        <td align="right">0 <span>Checked names: {{ checkedDocumentos }}</span></td>
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
                documentos : [],
                checkedDocumentos: [],
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
            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    this.cargando = true;
                    $(this.$refs.modal).modal('show');

                    this.id_remesa = '';
                    this.remesas = [];
                    this.documentos = [];

                    this.$validator.reset()
                    this.cargando = false;
                    this.getRemesas();
                }
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

            getDocumentos(){
                this.documentos = [];
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/remesa/find',{
                    id: self.id_remesa,
                    params: {
                        include: 'documento'
                    }
                })
                    .then(data => {
                        this.documentos = data.documento.data;
                    })
                    .finally(() => {
                        this.cargando = false;
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