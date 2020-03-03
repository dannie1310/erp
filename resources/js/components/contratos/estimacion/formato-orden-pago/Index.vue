<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form role="form" @submit.prevent="validate">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h6 class="box-title">Datos de Consulta</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Contratista:</label>
                                        <select
                                                type="text"
                                                name="id_empresa"
                                                data-vv-as="Empresa"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_empresa"
                                                v-model="id_empresa"
                                                :class="{'is-invalid': errors.has('id_empresa')}"
                                        >
                                            <option value>-- Contratista --</option>
                                            <option v-for="c in empresas" :value="c.id">{{ c.razon_social }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_subcontrato">Subcontrato:</label>
                                        <select
                                                :disabled="!id_empresa"
                                                type="text"
                                                name="id_subcontrato"
                                                data-vv-as="Subcontrato"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_subcontrato"
                                                v-model="id_subcontrato"
                                                :class="{'is-invalid': errors.has('id_subcontrato')}"
                                        >
                                            <option value>-- Subcontrato --</option>
                                            <option v-for="c in subcontratos" :value="c.id">{{ c.referencia }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_subcontrato')">{{ errors.first('id_subcontrato') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_estimacion">Estimación:</label>
                                        <select
                                                :disabled="!id_subcontrato"
                                                type="text"
                                                name="id_estimacion"
                                                data-vv-as="Estimacion"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_estimacion"
                                                v-model="id_estimacion"
                                                :class="{'is-invalid': errors.has('id_estimacion')}"
                                        >
                                            <option value>-- Estimación --</option>
                                            <option v-for="tipo in estimaciones" :value="tipo.id">{{ tipo.numero_folio }} - {{tipo.observaciones }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_estimacion')">{{ errors.first('id_estimacion') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer btn-group" v-if="id_estimacion">
                            <PDF v-bind:id="id_estimacion" @click="validate"></PDF>
                        </div>
                        <div class="modal-footer btn-group" v-else>
                            <button type="submit" class="btn btn-primary" @click="validate">Ver Formato</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</template>

<script>
    import PDF from "./FormatoOrdenPago";
    export default {
        name: "formato-orden-pago-index",
        components: {PDF},
        data() {
            return {
                cargando: false,
                id_empresa: '',
                id_subcontrato:  '',
                id_estimacion: '',
                empresas: [],
                subcontratos: [],
                estimaciones: [],
                ok : false,
                pdf_datos: ""
            }
        },
        mounted() {
            this.getEmpresas();
        },
        computed: {
        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');

                this.id_empresa = '';
                this.id_subcontrato = '';
                this.id_estimacion = '';

                this.$validator.reset()
                this.cargando = false;
            },
            getEmpresas() {
                this.id_empresa= '';
                this.id_subcontrato=  '';
                this.id_estimacion= '';
                this.empresas= [];
                this.subcontratos= [];
                this.estimaciones= [];
                this.$store.commit('cadeco/empresa/SET_EMPRESAS', []);
                this.cargando = true;
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {scope: 'paraSubcontratistas'}
                })
                    .then(data => {
                        this.empresas = data.data
                        if(this.empresas.length){
                            $(this.$refs.modal).appendTo('body')
                            $(this.$refs.modal).modal('show');
                        }
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getSubcontrato() {
                this.id_subcontrato=  '';
                this.id_estimacion= '';
                this.subcontratos= [];
                this.estimaciones= [];
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id_empresa,
                    params: { include: 'subcontratos' }
                }).then(data => {
                    this.subcontratos = data.subcontratos.data;
                    if(this.subcontratos.length){
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');

                    }
                })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstimaciones(){
                this.id_estimacion= '';
                this.estimaciones= [];
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id_subcontrato,
                    params: { include: 'estimaciones' }
                })
                    .then(data => {
                        this.estimaciones = data.estimaciones.data;
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {

                    }
                });
            }
        },
        watch: {
            id_empresa(value) {
                this.subcontratos = []
                if (value) {
                    this.getSubcontrato();
                }
            },
            id_subcontrato(value){
                this.estimaciones = []
                if(value){
                    this.getEstimaciones();
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
